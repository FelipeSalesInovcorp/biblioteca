<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\DashboardController;

Route::get('/', function () {
    return view('inicio');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    /*Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');*/

    Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');


    // Menus principais
    Route::resource('editoras', EditoraController::class)->except(['show']);
    Route::resource('autores', AutorController::class)->except(['show']);
    Route::resource('livros', LivroController::class)->except(['show']);

    // Catálogo de livros
    Route::get('/catalogo', [LivroController::class, 'catalogo'])->name('catalogo');

    // Para deixar o catálogo público, basta tirar essa rota do group e deixá-la fora do middleware.

     // Exportar livros em CSV (abre no Excel)
    Route::get('/livros/export', [LivroController::class, 'exportCsv'])
        ->name('livros.export');

});
