<?php

namespace App\Http\Controllers;

use App\Models\ChangeRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class AdminController extends Controller
{
    /**
     * Muestra el Dashboard del Administrador con datos clave.
     */
    public function dashboard()
{
    return Inertia::render('Admin/Dashboard', [
        'stats' => [
            'total' => Work::count(),
            'pendientes' => Work::where('estado', 'pendiente')->count(),
            'progreso' => Work::where('estado', 'en_progreso')->count(),
            'confirmacion' => Work::where('estado', 'esperando_confirmacion')->count(),
            'finalizados' => Work::where('estado', 'finalizado')->count(),
            'cambios' => ChangeRequest::count(),
        ],
        'latestWorks' => Work::with('client.user')->latest()->take(5)->get(),
        'latestClients' => Client::with('user')->latest()->take(5)->get(),
        'recentNotifications' => Auth::user()->notifications()->where('leido', false)->latest()->take(3)->get(),
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
        $admins = User::where('role', 'admin')->get();
        return Inertia::render('Admin/Kanban', [
            'works' => Work::with('client.user')->get()->groupBy('estado'),
            'admins' => $admins
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $work->estado = $request->input('estado');
        $work->save();

        return back()->with('success', 'Estado actualizado.');
    }

    public function reassign(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $work->assigned_to = $request->input('assigned_to');
        $work->save();

        return back()->with('success', 'Trabajo reasignado.');
    }

    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => ['required', Password::defaults()],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'admin'
        ]);

        return back()->with('success', 'Administrador creado correctamente.');
    }

    public function destroyAdmin($id)
    {
        $admin = User::where('role', 'admin')->findOrFail($id);

        // Protección básica: no permitir eliminarse a uno mismo
        if (auth()->id() === $admin->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $admin->delete();

        return back()->with('success', 'Administrador eliminado correctamente.');
    }

}
