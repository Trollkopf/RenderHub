<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Work;
use App\Models\Client;
use Illuminate\Support\Facades\Auth;

class WorkController extends Controller
{
    public function index()
    {
        $client = Auth::user()->client;
        $works = $client->works;
        return view('client.works.index', compact('works'));
    }

    public function show($id)
    {
        $work = Work::findOrFail($id);
        return view('client.works.show', compact('work'));
    }

    public function adminIndex()
    {
        $works = Work::all();
        return view('admin.works.index', compact('works'));
    }

    public function adminShow($id)
    {
        $work = Work::findOrFail($id);
        return view('admin.works.show', compact('work'));
    }

    public function uploadFile(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $files = $request->file('files');

        $paths = [];
        foreach ($files as $file) {
            $path = $file->store('renders', 'public');
            $paths[] = $path;
        }

        $work->archivos = array_merge($work->archivos ?? [], $paths);
        $work->estado = 'en_progreso';
        $work->save();

        return redirect()->back()->with('success', 'Archivos subidos correctamente.');
    }
}
