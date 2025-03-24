<?php

namespace App\Http\Controllers;

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
     * Muestra la lista de trabajos del cliente autenticado.
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
     * Muestra los detalles de un trabajo específico para el cliente.
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
     * Muestra todos los trabajos en el panel de administración.
     */
    public function adminIndex()
    {

        $admins = User::where('role', 'admin')->get();

        $daysToKeep = Setting::getValue('auto_archive_days', 10);

        return Inertia::render('Admin/Works', [
            'pendingWorks' => Work::with('client.user')
                ->where('estado', 'pendiente')
                ->orderBy('created_at', 'asc')
                ->get(),

            'inProgressWorks' => Work::with('client.user')
                ->where('estado', 'en_progreso')
                ->orderBy('created_at', 'asc')
                ->get(),

            'waitingConfirmationWorks' => Work::with('client.user')
                ->where('estado', 'esperando_confirmacion')
                ->orderBy('created_at', 'asc')
                ->get(),

            'completedWorks' => Work::with('client.user')
                ->where('estado', 'finalizado')
                ->where('updated_at', '>=', now()->subDays($daysToKeep))
                ->orderBy('created_at', 'asc')
                ->get(),

            'admins' => $admins,
        ]);
    }


    public function updateStatus(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $newStatus = $request->input('estado');

        if (!in_array($newStatus, ['pendiente', 'en_progreso', 'esperando_confirmacion', 'finalizado'])) {
            return response()->json(['error' => 'Estado inválido'], 422);
        }

        $work->estado = $newStatus;
        $work->updated_at = now(); // Asegurar que se registre el cambio
        $work->save();

        return redirect()->route('admin.works')->with('success', 'Estado del trabajo actualizado.');
    }

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

        return redirect()->route('admin.works')->with('success', 'Trabajo asignado correctamente.');
    }



    /**
     * Muestra los detalles de un trabajo específico en el panel de administración.
     */
    public function adminShow($id)
    {
        $work = Work::with(['client.user', 'changeRequests', 'assignedAdmin'])->findOrFail($id);

        return Inertia::render('Admin/WorkDetail', [
            'work' => $work
        ]);
    }

    /**
     * Permite al administrador subir archivos a un trabajo.
     */
    public function uploadFile(Request $request, $id)
    {
        // Verifica que el usuario sea un administrador
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Solo los administradores pueden subir archivos.');
        }

        $work = Work::findOrFail($id);
        $files = $request->file('files');

        $paths = [];
        foreach ($files as $file) {
            $path = $file->store('renders', 'public');
            $paths[] = $path;
        }

        // Guardamos los archivos en formato JSON
        $work->archivos = array_merge($work->archivos ?? [], $paths);
        $work->estado = 'en_progreso';
        $work->save();

        return redirect()->back()->with('success', 'Archivos subidos correctamente.');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string|min:10',
        ]);

        $client = Auth::user()->client;

        // Crear el nuevo trabajo
        $work = Work::create([
            'client_id' => $client->id,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'pendiente',
            'archivos' => json_encode([]), // Inicialmente sin archivos
        ]);

        // **Crear automáticamente un ChangeRequest asociado**
        ChangeRequest::create([
            'work_id' => $work->id,
            'client_id' => $client->id,
            'descripcion' => 'Solicitud inicial de trabajo.',
            'archivo' => null, // No hay archivo al inicio
            'estado' => 'pendiente'
        ]);

        return back()->with('success', 'Trabajo solicitado con éxito.');
    }


    public function reviewWork(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $client = Auth::user()->client;

        if ($work->estado !== 'esperando_confirmacion') {
            return back()->with('error', 'No puedes modificar este trabajo.');
        }

        // **ACEPTAR EL TRABAJO**
        if ($request->input('action') === 'accept') {
            $work->estado = 'finalizado';
            $work->save();
            return back()->with('success', 'Trabajo aceptado.');
        }

        // **RECHAZAR EL TRABAJO**
        if ($request->input('action') === 'reject') {
            // **Obtener la última solicitud de cambio**
            $lastChangeRequest = ChangeRequest::where('work_id', $work->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // **Si no hay cambios previos, inicializar el contador en 1**
            $changeCount = $lastChangeRequest ? ($lastChangeRequest->change_count + 1) : 1;

            // **Verificar si se pueden hacer más cambios**
            if ($changeCount > 3) {
                return back()->with('error', 'No puedes solicitar más cambios.');
            }

            // **Validar los datos del formulario**
            $validated = $request->validate([
                'descripcion' => 'required|string|min:10',
                'archivo' => 'nullable|mimes:jpg,jpeg,png,pdf,ppt,pptx'
            ]);

            // **Guardar el archivo si se sube**
            $archivoPath = $request->hasFile('archivo')
                ? $request->file('archivo')->store('change_requests', 'public')
                : null;

            // **Crear nueva solicitud de cambio**
            ChangeRequest::create([
                'work_id' => $work->id,
                'client_id' => $client->id,
                'descripcion' => $validated['descripcion'],
                'archivo' => $archivoPath,
                'estado' => 'pendiente',
                'change_count' => $changeCount
            ]);

            // **Actualizar el estado del trabajo a 'pendiente'**
            $work->estado = 'pendiente';
            $work->save();

            return back()->with('success', 'Trabajo rechazado y solicitud de cambio enviada.');
        }

        return back()->with('error', 'Acción no válida.');
    }

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

    public function restore($id)
    {
        $work = Work::findOrFail($id);

        if ($work->estado === 'finalizado') {
            $work->estado = 'pendiente'; // O el estado que prefieras restaurar
            $work->save();
        }

        return back()->with('success', 'Trabajo restaurado correctamente.');
    }

    public function destroy($id)
    {
        $work = Work::findOrFail($id);
        $work->delete();

        return back()->with('success', 'Trabajo eliminado permanentemente.');
    }


}
