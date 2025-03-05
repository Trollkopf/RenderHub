<?php

namespace App\Http\Controllers;

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
            'pendingWorks' => $client->works()->where('estado', 'pendiente')->orderBy('created_at', 'desc')->paginate(3),
            'inProgressWorks' => $client->works()->where('estado', 'en_progreso')->orderBy('created_at', 'desc')->paginate(3),
            'completedWorks' => $client->works()->where('estado', 'finalizado')->orderBy('created_at', 'desc')->paginate(3),
        ]);
    }


    /**
     * Muestra los detalles de un trabajo específico para el cliente.
     */
    public function show($id)
    {
        $work = Work::with('client.user')->findOrFail($id);

        // Verificar que el trabajo pertenece al cliente autenticado
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
        $works = Work::with('client.user')->get();

        return Inertia::render('Admin/Works/Index', [
            'works' => $works
        ]);
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
        $client = Auth::user()->client;

        if (!$client) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Validación de la solicitud
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'required|string',
        ]);

        // Crear nuevo trabajo con estado "pendiente"
        Work::create([
            'client_id' => $client->id,
            'titulo' => $validated['titulo'],
            'descripcion' => $validated['descripcion'],
            'estado' => 'pendiente', // Siempre inicia como pendiente
            'archivos' => json_encode([]), // Iniciamos con una lista vacía de archivos
        ]);

        return redirect()->back()->with('success', 'Trabajo solicitado correctamente.');
    }



}
