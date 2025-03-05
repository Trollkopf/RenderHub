<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Work;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Muestra el panel de administración con la lista de clientes y trabajos.
     */
    public function dashboard()
    {
        $clients = Client::with('user')->get();
        $works = Work::with('client.user')->get();

        return Inertia::render('Admin/Dashboard', [
            'clients' => $clients,
            'works' => $works
        ]);
    }

    /**
     * Lista todos los clientes en el panel de administración.
     */
    public function listClients()
    {
        $clients = Client::with('user')->get();

        return Inertia::render('Admin/Clients', [
            'clients' => $clients
        ]);
    }

    /**
     * Muestra la información de un cliente en detalle.
     */
    public function showClient($id)
    {
        $client = Client::with(['user', 'works'])->findOrFail($id);

        return Inertia::render('Admin/ClientDetail', [
            'client' => $client
        ]);
    }
}
