<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\WorkController;
use App\Http\Middleware\RoleMiddleware;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

// Página de inicio con Inertia.js
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('home');

// Rutas de autenticación de Laravel Breeze
require __DIR__ . '/auth.php';

// Redirección después del login según el rol
Route::get('/home', function () {
    if (!Auth::check()) {
        return redirect('/login');
    }

    return redirect(Auth::user()->role === 'admin' ? '/admin/dashboard' : '/dashboard');
})->name('dashboard');

// Rutas para Clientes (Panel de usuario)
Route::middleware(['auth', RoleMiddleware::class . ':cliente'])->group(function () {
    Route::get('/dashboard', [ClientController::class, 'dashboard'])->name('client.dashboard');
    Route::get('/perfil', [ClientController::class, 'editProfile'])->name('client.profile');
    Route::put('/perfil', [ClientController::class, 'updateProfile'])->name('client.profile.update');
    Route::get('/trabajos', [WorkController::class, 'index'])->name('client.works');
    Route::get('/trabajos/{id}', [WorkController::class, 'show'])->name('client.works.show');
    Route::post('/trabajos', [WorkController::class, 'store'])->name('client.works.store');
    Route::post('/cliente/trabajos/{id}/revisar', [WorkController::class, 'reviewWork'])->name('client.works.review');
});

// Rutas para Administradores (Panel de administración)
Route::middleware(['auth', RoleMiddleware::class . ':admin'])->group(function () {
    // Dashboard
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Clientes
    Route::get('/admin/clientes', [AdminController::class, 'listClients'])->name('admin.clients');
    Route::get('/admin/clientes/{id}', [AdminController::class, 'showClient'])->name('admin.clients.show');
    Route::delete('/admin/clientes/{id}', [AdminController::class, 'deleteClient'])->name('admin.clients.delete');

    // Trabajos
    Route::get('/admin/trabajos', [WorkController::class, 'adminIndex'])->name('admin.works');
    Route::get('/admin/trabajos/{id}', [WorkController::class, 'adminShow'])->name('admin.works.show');
    Route::put('/admin/trabajos/{id}/asignar', [WorkController::class, 'assignWork'])->name('admin.works.assign');
    Route::put('/admin/trabajos/{id}/estado', [WorkController::class, 'updateStatus'])->name('admin.works.updateStatus');
    Route::put('/admin/trabajos/{id}/reasignar', [AdminController::class, 'reassign'])->name('admin.works.reassign');

    // Configuración
    Route::get('/admin/settings', [SettingsController::class, 'index'])->name('settings.index');

    // Administración de información/admins
    Route::post('/admin/users', [AdminController::class, 'storeAdmin'])->name('admin.users.store');
    Route::post('/admin/profile', [SettingsController::class, 'updateProfile'])->name('admin.profile.update');
    Route::delete('/admin/users/{id}', [AdminController::class, 'destroyAdmin'])->name('admin.users.destroy');

    // Trabajos Archivados
    Route::get('/admin/archivados', [WorkController::class, 'archived'])->name('admin.works.archived');
    Route::put('/admin/trabajos/{id}/restore', [WorkController::class, 'restore'])->name('admin.works.restore');
    Route::delete('/admin/trabajos/{id}', [WorkController::class, 'destroy'])->name('admin.works.destroy');

    // Calendario
    Route::get('/admin/calendar', fn() => Inertia::render('Admin/Calendar'))->name('admin.calendar');
    Route::get('/api/calendar', [CalendarController::class, 'api']);
    Route::post('/calendar', [CalendarController::class, 'store']);
    Route::put('/calendar/{id}', [CalendarController::class, 'update']);
    Route::delete('/calendar/{id}', [CalendarController::class, 'destroy']);

});

// Rutas de Configuración
Route::middleware(['auth', RoleMiddleware::class . ':admin'])
    ->prefix('admin/settings')
    ->group(function () {
        Route::put('/auto-archive', [SettingsController::class, 'updateAutoArchive'])->name('settings.autoArchive');
        Route::put('/request-limit', [SettingsController::class, 'updateRequestLimit'])->name('settings.requestLimit');
        Route::put('/system-status', [SettingsController::class, 'updateSystemStatus'])->name('settings.systemStatus');
        Route::put('/notifications', [SettingsController::class, 'updateNotifications'])->name('settings.notifications');
        Route::post('/global-action', [SettingsController::class, 'runAction'])->name('settings.globalAction');
        Route::post('/documents/upload', [SettingsController::class, 'uploadDocument'])->name('settings.documents.upload');
    });

// Notificaciones
Route::middleware(['auth'])->group(function () {
    Route::get('/notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::put('/notificaciones/{id}/leer', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::put('/notificaciones/marcar-todas', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
    Route::get('/notificaciones/ver/{id}', [NotificationController::class, 'view'])->name('notifications.view');
    Route::delete('/notificaciones/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
});

