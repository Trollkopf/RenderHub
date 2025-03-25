<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class SettingsController extends Controller
{

    public function index()
    {
        $settings = [
            'auto_archive_days' => Setting::getValue('auto_archive_days', 10),
            'active_work_limit' => Setting::getValue('active_work_limit', 3),
            'system_open' => filter_var(Setting::getValue('system_open', true), FILTER_VALIDATE_BOOLEAN),
            'notifications' => json_decode(Setting::getValue('notifications', '{}'), true),
        ];

        $admins = User::where('role', 'admin')->select('id', 'name', 'email', 'created_at')->get();

        return Inertia::render('Admin/Settings', compact('settings', 'admins'));
    }
    public function updateAutoArchive(Request $request)
    {
        $request->validate(['days' => 'required|integer|min:1']);
        Setting::setValue('auto_archive_days', $request->days);
        return back()->with('success', 'Configuración actualizada.');
    }

    public function updateRequestLimit(Request $request)
    {
        $request->validate(['limite' => 'required|integer|min:1']);
        Setting::setValue('active_work_limit', $request->limite);
        return back()->with('success', 'Límite actualizado.');
    }

    public function updateSystemStatus(Request $request)
    {
        $request->validate(['abierto' => 'required|boolean']);
        Setting::setValue('system_open', $request->abierto);
        return back()->with('success', 'Estado del sistema actualizado.');
    }

    public function updateNotifications(Request $request)
    {
        $request->validate([
            'nuevo_trabajo' => 'required|boolean',
            'cambio_solicitado' => 'required|boolean',
            'trabajo_finalizado' => 'required|boolean',
            'trabajo_asignado' => 'required|boolean',
        ]);
        Setting::setValue('notifications', json_encode($request->only([
            'nuevo_trabajo',
            'cambio_solicitado',
            'trabajo_finalizado',
            'trabajo_asignado'
        ])));
        return back()->with('success', 'Notificaciones actualizadas.');
    }

    public function runAction(Request $request)
    {
        $request->validate(['action' => 'required|string']);
        $accion = $request->action;

        // Aquí puedes ejecutar acciones reales
        switch ($accion) {
            case 'backup':
                // lógica de backup
                break;
            case 'clean_orphaned_files':
                // lógica para limpiar archivos huérfanos
                break;
            case 'view_logs':
                // lógica para visualizar logs
                break;
        }

        return response()->json(['success' => "Acción '$accion' ejecutada correctamente."]);
    }

    public function uploadDocument(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:pdf,ppt,pptx,doc,docx,jpg,png|max:10240'
        ]);

        $path = $request->file('archivo')->store('documents', 'public');

        // Aquí puedes guardar en una tabla documents si lo necesitas

        return back()->with('success', 'Documento subido correctamente.');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => ['nullable', Password::defaults()],
            'password_confirmation' => 'nullable|same:password'
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}