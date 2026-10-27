<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PortalController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Worker portal (labour.importer168.com)
|--------------------------------------------------------------------------
| Workers log in with passport number + birthday (ddmmyyyy). All data comes
| from the admin system over its /api/portal API; nothing is stored here.
*/

Route::middleware('worker.guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:20,1')->name('login.post');
});

Route::middleware('worker')->group(function () {
    Route::get('/', [PortalController::class, 'home'])->name('home');
    Route::get('/documents', [PortalController::class, 'documents'])->name('documents');
    Route::get('/documents/{key}', [PortalController::class, 'openDocument'])->where('key', '[A-Za-z0-9_-]+')->name('documents.open');
    Route::get('/profile', [PortalController::class, 'profile'])->name('profile');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
