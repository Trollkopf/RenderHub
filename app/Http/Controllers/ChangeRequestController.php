<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChangeRequest;
use App\Models\Work;
use Illuminate\Support\Facades\Auth;

class ChangeRequestController extends Controller
{
    public function store(Request $request, $id)
    {
        $work = Work::findOrFail($id);
        $client = Auth::user()->client;

        if ($work->changeRequests()->count() >= 3) {
            return redirect()->back()->with('error', 'No puedes solicitar más cambios.');
        }

        $data = [
            'work_id' => $work->id,
            'client_id' => $client->id,
            'descripcion' => $request->input('descripcion'),
            'estado' => 'pendiente',
        ];

        if ($request->hasFile('archivo')) {
            $data['archivo'] = $request->file('archivo')->store('change_requests', 'public');
        }

        ChangeRequest::create($data);

        return redirect()->back()->with('success', 'Cambio solicitado.');
    }

}
