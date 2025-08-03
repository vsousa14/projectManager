<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackofficeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolesController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');


Route::middleware(['auth', 'can:view-backoffice'])->group(function () {
    Route::get('/admin', [BackofficeController::class, 'admin'])->name('Backoffice.dashboard');
    Route::get('/admin/overview', [BackofficeController::class, 'getOverviewContent'])->name('Backoffice.partials.overview');
    
    Route::middleware('can:manage-users')->group(function () {
        Route::get('/admin/users-content', [BackofficeController::class, 'getUsersContent'])->name('Backoffice.partials.users');
        Route::get('/admin/users/{user}/edit', [UserController::class, 'edit'])->name('Backoffice.users.edit');
        Route::put('/admin/users/{user}', [UserController::class, 'update'])->name('Backoffice.users.update');
    });

    Route::middleware('can:manage-roles')->group(function () {
        Route::get('/admin/roles-content', [RolesController::class, 'index'])->name('Backoffice.partials.roles');
        Route::get('/admin/roles/create', [RolesController::class, 'create'])->name('Backoffice.roles.create');
        Route::post('/admin/roles', [RolesController::class, 'store'])->name('Backoffice.roles.store');
        Route::get('/admin/roles/{role}/edit', [RolesController::class, 'edit'])->name('Backoffice.roles.edit');
        Route::put('/admin/roles/{role}', [RolesController::class, 'update'])->name('Backoffice.roles.update');
        Route::delete('/admin/roles/{role}', [RolesController::class, 'destroy'])->name('Backoffice.roles.destroy');
    });
});

