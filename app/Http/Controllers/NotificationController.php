<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Muestra la lista de notificaciones del usuario autenticado.
     */
    public function index()
    {
        $notifications = Auth::user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return Inertia::render('Notifications/Index', [
            'notifications' => $notifications
        ]);
    }

    public function view($id)
    {
        $notification = Notification::findOrFail($id);

        // Marcar como leída
        $notification->update(['leido' => true]);

        // Redirigir al trabajo relacionado (o donde necesites)
        return redirect()->route('admin.works.show', $notification->work_id);
    }

    /**
     * Marca una notificación como leída.
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notification->update(['leido' => true]);

        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marca todas las notificaciones como leidas
     */
    public function markAllAsRead()
    {
        Auth::user()
            ->notifications()
            ->where('leido', false)
            ->update(['leido' => true]);

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->delete();

        return back();
    }

}
