<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [VisitorController::class, 'showLogin'])->name('login');
Route::post('/login', [VisitorController::class, 'authenticate']);
Route::post('/logout', [VisitorController::class, 'logout'])->name('logout');

Route::middleware(['token.auth'])->group(function () {
    Route::get('/dashboard', [VisitorController::class, 'index'])->name('dashboard');
    Route::post('/visitors/scan', [VisitorController::class, 'scan'])->name('visitors.scan');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
});

