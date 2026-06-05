<?php

use App\Http\Controllers\VisitorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/login', [VisitorController::class, 'showLogin'])->name('login');
Route::post('/login', [VisitorController::class, 'authenticate']);
Route::post('/logout', [VisitorController::class, 'logout'])->name('logout');

Route::middleware(['token.auth'])->group(function () {
    Route::get('/dashboard', [VisitorController::class, 'index'])->name('dashboard');
    Route::get('/scanner', [VisitorController::class, 'showScanner'])->name('scanner');
    Route::post('/visitors/scan', [VisitorController::class, 'scan'])->name('visitors.scan');
    Route::get('/visitors/export', [VisitorController::class, 'export'])->name('visitors.export');
    Route::delete('/visitors/{id}', [VisitorController::class, 'destroy'])->name('visitors.destroy');
});
