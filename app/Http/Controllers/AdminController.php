<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Work;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Muestra el Dashboard del Administrador con datos clave.
     */
    public function dashboard()
    {
        return Inertia::render('Admin/Dashboard', [
            'clients' => Client::with('user')->count(), // Contamos clientes
            'works' => Work::count(), // Contamos trabajos
            'recentWorks' => Work::with('client.user')->latest()->take(5)->get(), // Últimos 5 trabajos
        ]);
    }

    /**
     * Lista todos los clientes con paginación.
     */
    public function listClients(Request $request)
    {
        $search = $request->input('search');

        $clients = Client::with('user')
            ->whereHas('user', fn($query) => $query->where('role', 'cliente')) // Solo clientes
            ->when($search, fn($query) => $query->whereHas('user', fn($q) =>
                $q->where('name', 'like', "%$search%")))
            ->join('users', 'clients.user_id', '=', 'users.id') // Unir con users
            ->orderByRaw("LOWER(users.name) ASC") // Ordenar alfabéticamente sin distinción de mayúsculas
            ->select('clients.*') // Seleccionar solo los datos de clients
            ->paginate(10)
            ->appends(['search' => $search]);

        return Inertia::render('Admin/Clients', [
            'clients' => $clients
        ]);
    }



    public function deleteClient($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return redirect()->back()->with('success', 'Cliente eliminado correctamente.');
    }


    /**
     * Muestra los detalles de un cliente junto con sus trabajos.
     */
    public function showClient($id)
{
    $client = Client::with(['user', 'works'])->findOrFail($id);

    return Inertia::render('Admin/ClientDetail', [
        'client' => $client
    ]);
}

    /**
     * Muestra la configuración del administrador.
     */
    public function settings()
    {
        return Inertia::render('Admin/Settings');
    }

    /**
     * Muestra el tablero Kanban con los trabajos organizados por estado.
     */
    public function kanban()
    {
        return Inertia::render('Admin/Kanban', [
            'works' => Work::with('client.user')->get()->groupBy('estado')
        ]);
    }
}
