<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RequisicaoController;


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
    /*Route::resource('editoras', EditoraController::class)->except(['show']);
    Route::resource('autores', AutorController::class)->except(['show']);
    Route::resource('livros', LivroController::class)->except(['show']);*/

    Route::resource('editoras', EditoraController::class)
    ->except(['show'])
    ->parameters(['editoras' => 'editora']);

    Route::resource('autores', AutorController::class)
    ->except(['show'])
    ->parameters(['autores' => 'autor']);

    Route::resource('livros', LivroController::class)
    ->parameters(['livros' => 'livro']);


    // Requisições de livros
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');

    Route::get('/requisicoes/create', [RequisicaoController::class, 'create'])->name('requisicoes.create');

    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');

    // Catálogo de livros
    Route::get('/catalogo', [LivroController::class, 'catalogo'])->name('catalogo');

    // Para deixar o catálogo público, basta tirar essa rota do group e deixá-la fora do middleware.

     // Exportar livros em CSV (abre no Excel)
    Route::get('/livros/export', [LivroController::class, 'exportCsv'])
        ->name('livros.export');

    Route::post('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmEntrega'])
    ->name('requisicoes.confirmEntrega');


});
