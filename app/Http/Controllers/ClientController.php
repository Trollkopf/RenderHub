<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class ClientController extends Controller
{
    public function dashboard()
    {
        $client = Auth::user()->client;
        $works = $client->works;
        return view('client.dashboard', compact('works'));
    }

    public function editProfile()
    {
        $client = Auth::user()->client;
        return view('client.profile', compact('client'));
    }

    public function updateProfile(Request $request)
    {
        $client = Auth::user()->client;
        $client->update($request->validate([
            'empresa' => 'nullable|string|max:255',
            'telefono' => 'required|string|max:20',
            'direccion' => 'nullable|string',
        ]));

        return redirect()->back()->with('success', 'Perfil actualizado');
    }
}
