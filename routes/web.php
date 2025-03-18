<?php

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
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/clientes', [AdminController::class, 'listClients'])->name('admin.clients');
    Route::get('/admin/clientes/{id}', [AdminController::class, 'showClient'])->name('admin.clients.show');
    Route::delete('/admin/clientes/{id}', [AdminController::class, 'deleteClient'])->name('admin.clients.delete');
    Route::get('/admin/trabajos', [WorkController::class, 'adminIndex'])->name('admin.works');
    Route::get('/admin/trabajos/{id}', [WorkController::class, 'adminShow'])->name('admin.works.show');
    Route::put('/admin/trabajos/{id}/asignar', [WorkController::class, 'assignWork'])->name('admin.works.assign');
    Route::put('/admin/trabajos/{id}/estado', [WorkController::class, 'updateStatus'])->name('admin.works.updateStatus');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/trabajos/{id}/estado', [AdminController::class, 'updateStatus'])->name('admin.works.updateStatus');
    Route::put('/admin/trabajos/{id}/reasignar', [AdminController::class, 'reassign'])->name('admin.works.reassign');

});
