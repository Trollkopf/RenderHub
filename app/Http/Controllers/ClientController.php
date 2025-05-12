<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Muestra el panel de cliente con sus trabajos.
     */
    public function dashboard(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Obtener parámetros de búsqueda y filtro
        $search = $request->input('search');
        $status = $request->input('status');

        // Consultar trabajos con filtros
        $works = $client->works()
            ->when($search, fn($query) => $query->where('titulo', 'like', "%$search%"))
            ->when($status, fn($query) => $query->where('estado', $status))
            ->orderBy('created_at', 'desc')
            ->paginate(5)
            ->appends(['search' => $search, 'status' => $status]);

        $notifications = Auth::user()
            ->notifications()
            ->latest()
            ->take(5)
            ->get(['id', 'mensaje', 'leido']);

        return Inertia::render('Client/Dashboard', [
            'user' => $user->load('client'),
            'notifications' => $notifications,
            'works' => $client->works()->get()
        ]);
    }

    /**
     * Muestra la vista de edición de perfil con Inertia.
     */
    public function editProfile()
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        return Inertia::render('Client/Profile', [
            'user' => $user,
            'client' => $client
        ]);
    }

    /**
     * Actualiza el perfil del cliente.
     */

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $client = $user->client;

        if (!$client) {
            abort(403, 'No tienes acceso a esta sección.');
        }

        // Validar los datos del formulario
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id, // Asegura que el email sea único
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
        ]);

        // Actualizar datos en la tabla `users`
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Actualizar datos en la tabla `clients`
        $client->update([
            'empresa' => $validated['empresa'],
            'telefono' => $validated['telefono'],
            'email' => $validated['email'], // También actualiza el email en la tabla clients
            'direccion' => $validated['direccion'],
        ]);

        return redirect()->back()->with('success', 'Perfil actualizado correctamente.');
    }

}
