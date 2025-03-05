<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Work;

class AdminController extends Controller
{
    public function dashboard()
    {
        $clients = Client::all();
        $works = Work::all();
        return view('admin.dashboard', compact('clients', 'works'));
    }

    public function listClients()
    {
        $clients = Client::all();
        return view('admin.clients.index', compact('clients'));
    }

    public function showClient($id)
    {
        $client = Client::findOrFail($id);
        return view('admin.clients.show', compact('client'));
    }
}
