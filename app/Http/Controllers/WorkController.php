<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
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
                ->where('updated_at', '>=', now()->subDays(10)) // Mostrar solo los últimos 10 días
                ->orderBy('created_at', 'asc')
                ->get(),
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
        $work = Work::with('client.user')->findOrFail($id);

        return Inertia::render('Admin/Works/Show', [
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

        if ($request->input('action') === 'accept') {
            $work->estado = 'finalizado';
            $work->save();
            return back()->with('success', 'Trabajo aceptado.');
        }

        if ($request->input('action') === 'reject') {
            // **Obtener la última solicitud de cambio**
            $lastChangeRequest = ChangeRequest::where('work_id', $work->id)->orderBy('created_at', 'desc')->first();

            // dd($lastChangeRequest);

            if ($lastChangeRequest && $lastChangeRequest->change_count >= 3) {
                return back()->with('error', 'No puedes solicitar más cambios.');
            }

            $validated = $request->validate([
                'descripcion' => 'required|string|min:10',
                'archivo' => 'nullable|mimes:jpg,jpeg,png,pdf,ppt,pptx'
            ]);

            // **Guardar archivo si se sube**
            $archivoPath = null;
            if ($request->hasFile('archivo')) {
                $archivoPath = $request->file('archivo')->store('change_requests', 'public');
            }

            // **Incrementar el contador de cambios**
            $changeCount = $lastChangeRequest->change_count + 1;

            // dd($changeCount);

            // **Crear nueva solicitud de cambio**
            ChangeRequest::create([
                'work_id' => $work->id,
                'client_id' => $client->id,
                'descripcion' => $validated['descripcion'],
                'archivo' => $archivoPath,
                'estado' => 'pendiente',
                'change_count' => $changeCount
            ]);

            // **Mantener el trabajo en estado "en progreso"**
            $work->estado = 'en_progreso';
            $work->save();

            return back()->with('success', 'Trabajo rechazado y solicitud de cambio enviada.');
        }

        return back()->with('error', 'Acción no válida.');
    }

}
