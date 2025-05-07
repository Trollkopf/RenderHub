<?php

namespace App\Http\Controllers;

use App\Helpers\NotificationHelper;
use App\Models\ChangeRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WorkController extends Controller
{
    /**
     * Cliente: Muestra la lista de trabajos del cliente autenticado.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        return Inertia::render('Client/Works/Index', [
            'pendingWorks' => $client->works()->with('changeRequests')->where('estado', 'pendiente')->orderBy('created_at', 'desc')->paginate(3),
            'inProgressWorks' => $client->works()->with('changeRequests')->where('estado', 'en_progreso')->orderBy('created_at', 'desc')->paginate(3),
            'waitingConfirmationWorks' => $client->works()->with('changeRequests')->where('estado', 'esperando_confirmacion')->paginate(3),
            'completedWorks' => $client->works()->with('changeRequests')->where('estado', 'finalizado')->orderBy('created_at', 'desc')->paginate(3),
        ]);
    }

    /**
     * Cliente: Muestra los detalles de un trabajo.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        $work = Work::with(['client.user', 'changeRequests'])->findOrFail($id);

        if (Auth::user()->client->id !== $work->client_id) {
            abort(403, 'No tienes permiso para ver este trabajo.');
        }

        return Inertia::render('Client/Works/Show', [
            'work' => $work
        ]);
    }

    /**
     * Admin: Vista de todos los trabajos según su estado.
     *
     * @return \Inertia\Response
     */
    public function adminIndex()
    {
        $admins = User::where('role', 'admin')->get();
        $daysToKeep = Setting::getValue('auto_archive_days', 10);

        return Inertia::render('Admin/Works', [
            'pendingWorks' => Work::with('client.user')->where('estado', 'pendiente')->orderBy('created_at', 'asc')->get(),
            'inProgressWorks' => Work::with('client.user')->where('estado', 'en_progreso')->orderBy('created_at', 'asc')->get(),
            'waitingConfirmationWorks' => Work::with('client.user')->where('estado', 'esperando_confirmacion')->orderBy('created_at', 'asc')->get(),
            'completedWorks' => Work::with('client.user')->where('estado', 'finalizado')->where('updated_at', '>=', now()->subDays($daysToKeep))->orderBy('created_at', 'asc')->get(),
            'admins' => $admins,
        ]);
    }

    /**
     * Admin: Cambia el estado de un trabajo.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $newStatus = $request->input('estado');

        if (!in_array($newStatus, ['pendiente', 'en_progreso', 'esperando_confirmacion', 'finalizado'])) {
            return response()->json(['error' => 'Estado inválido'], 422);
        }

        $work->estado = $newStatus;
        $work->updated_at = now();
        $work->save();

        // Notificación al cliente

        NotificationHelper::notify($work->client->user_id, "📢 El estado de tu trabajo \"{$work->titulo}\" ha sido actualizado a \"{$newStatus}\".", $work->id);


        return redirect()->route('admin.works')->with('success', 'Estado del trabajo actualizado.');
    }

    /**
     * Admin: Asigna un trabajo a un administrador.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignWork(Request $request, $id)
    {
        $work = Work::findOrFail($id);

        $request->validate([
            'assigned_to' => 'nullable|exists:users,id',
            'due_date' => 'nullable|date'
        ]);

        $work->assigned_to = $request->input('assigned_to');
        $work->due_date = $request->input('due_date');
        $work->save();

        // Notificación al admin asignado
        if ($work->assigned_to) {
            NotificationHelper::notify($work->assigned_to, "🛠️ Se te ha asignado el trabajo \"{$work->titulo}\".", $work->id);
        }

        return redirect()->route('admin.works')->with('success', 'Trabajo asignado correctamente.');
    }

    /**
     * Admin: Muestra detalles de un trabajo.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function adminShow($id)
    {
        $work = Work::with(['client.user', 'changeRequests', 'assignedAdmin'])->findOrFail($id);

        return Inertia::render('Admin/WorkDetail', [
            'work' => $work
        ]);
    }

    /**
     * Admin: Subir archivos al trabajo y notificar al cliente.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadFile(Request $request, $id)
    {

        if (Auth::user()->role !== 'admin') {
            abort(403, 'Solo los administradores pueden subir archivos.');
        }

        $request->validate([
            'files.*' => 'required|file|mimes:jpg,jpeg,png,pdf'
        ]);

        $work = Work::findOrFail($id);
        $files = $request->file('files', );
        $paths = [];


        foreach ($files as $file) {
            $paths[] = $file->store('renders', 'public');
        }


        // dd($files);
        $work->archivos = $work->archivos ? array_merge($work->archivos, $paths) : $paths;
        $work->estado = 'esperando_confirmacion';
        $work->save();

        NotificationHelper::notify($work->client->user_id, "🎨 Tu trabajo \"{$work->titulo}\" tiene nuevas entregas.", $work->id);

        return redirect()->back()->with('success', 'Archivos subidos correctamente.');
    }

    /**
     * Cliente: Solicita un nuevo trabajo.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|min:10',
            'archivo' => 'nullable|file|mimes:jpg,jpeg,png,pdf', // Aquí validamos el archivo opcional
        ]);

        $client = Auth::user()->client;

        // Guardar el archivo si se ha subido
        $archivoPath = null;
        if ($request->hasFile('archivo')) {
            $archivoPath = $request->file('archivo')->store('change_requests', 'public');
        }

        $work = Work::create([
            'client_id' => $client->id,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'pendiente',

        ]);

        ChangeRequest::create([
            'work_id' => $work->id,
            'client_id' => $client->id,
            'descripcion' => 'Solicitud inicial de trabajo.',
            'archivo' => $archivoPath,
            'estado' => 'pendiente'
        ]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            NotificationHelper::notify($admin->id, '📥 Nuevo trabajo solicitado por ' . $client->user->name, $work->id);
        }

        return back()->with('success', 'Trabajo solicitado con éxito.');
    }


    /**
     * Cliente: Acepta o rechaza un trabajo.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reviewWork(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $client = Auth::user()->client;
        $admins = User::where('role', 'admin')->get();


        if ($work->estado !== 'esperando_confirmacion') {
            return back()->with('error', 'No puedes modificar este trabajo.');
        }

        if ($request->input('action') === 'accept') {
            $work->estado = 'finalizado';
            if ($work->assigned_to) {
                NotificationHelper::notify($work->assigned_to, "✅ El cliente ha aceptado el trabajo \"{$work->titulo}\".", $work->id);
            } else {
                foreach ($admins as $admin) {
                    NotificationHelper::notify($admin->id, "✅ El cliente ha aceptado el trabajo \"{$work->titulo}\".", $work->id);
                }
            }

            $work->save();
            return back()->with('success', 'Trabajo aceptado.');
        }

        if ($request->input('action') === 'reject') {
            $lastChangeRequest = ChangeRequest::where('work_id', $work->id)->orderBy('created_at', 'desc')->first();
            $changeCount = $lastChangeRequest ? ($lastChangeRequest->change_count + 1) : 1;

            if ($changeCount > 3) {
                return back()->with('error', 'No puedes solicitar más cambios.');
            }

            $validated = $request->validate([
                'descripcion' => 'required|string|min:10',
                'archivo' => 'nullable|mimes:jpg,jpeg,png,pdf,ppt,pptx'
            ]);

            $archivoPath = $request->hasFile('archivo')
                ? $request->file('archivo')->store('change_requests', 'public')
                : null;

            ChangeRequest::create([
                'work_id' => $work->id,
                'client_id' => $client->id,
                'descripcion' => $validated['descripcion'],
                'archivo' => $archivoPath,
                'estado' => 'pendiente',
                'change_count' => $changeCount
            ]);

            $work->estado = 'pendiente';
            $work->save();

            if ($work->assigned_to) {
                NotificationHelper::notify($work->assigned_to, "🔁 El cliente ha solicitado cambios en \"{$work->titulo}\".", $work->id);
            } else {
                foreach ($admins as $admin) {
                    NotificationHelper::notify($admin->id, "🔁 El cliente ha solicitado cambios en \"{$work->titulo}\".", $work->id);
                }
            }

            return back()->with('success', 'Trabajo rechazado y solicitud de cambio enviada.');
        }

        return back()->with('error', 'Acción no válida.');
    }

    /**
     * Admin: Ver trabajos finalizados hace más de X días.
     *
     * @return \Inertia\Response
     */
    public function archived()
    {
        $days = Setting::getValue('auto_archive_days', 10);

        $archivedWorks = Work::with(['client.user'])
            ->where('estado', 'finalizado')
            ->where('updated_at', '<', now()->subDays($days))
            ->orderBy('updated_at', 'desc')
            ->get();

        return Inertia::render('Admin/Works/Archived', [
            'archivedWorks' => $archivedWorks
        ]);
    }

    /**
     * Admin: Restaura un trabajo archivado.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore($id)
    {
        $work = Work::findOrFail($id);

        if ($work->estado === 'finalizado') {
            $work->estado = 'pendiente';
            $work->save();
        }

        return back()->with('success', 'Trabajo restaurado correctamente.');
    }

    /**
     * Admin: Elimina permanentemente un trabajo.
     *
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $work = Work::findOrFail($id);
        $work->delete();

        return back()->with('success', 'Trabajo eliminado permanentemente.');
    }
}
