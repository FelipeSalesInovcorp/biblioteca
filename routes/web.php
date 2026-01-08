<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RequisicaoController;
Use App\Http\Controllers\Admin\GoogleBooksController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\Admin\AvaliacaoAdminController;
use App\Http\Controllers\LivroAlertaController;
use App\Http\Controllers\CarrinhoController;


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

    // Exportar livros em CSV (abre no Excel)
    Route::get('/livros/export', [LivroController::class, 'exportCsv'])
        ->name('livros.export');

    Route::resource('livros', LivroController::class)
    ->parameters(['livros' => 'livro']);


    // Requisições de livros
    Route::get('/requisicoes', [RequisicaoController::class, 'index'])->name('requisicoes.index');

    Route::get('/requisicoes/create', [RequisicaoController::class, 'create'])->name('requisicoes.create');

    Route::post('/requisicoes', [RequisicaoController::class, 'store'])->name('requisicoes.store');

    // Catálogo de livros
    Route::get('/catalogo', [LivroController::class, 'catalogo'])->name('catalogo');

     // Carrinho (Cidadão)
    Route::post('/carrinho/adicionar/{livro}', [CarrinhoController::class, 'add'])
        ->name('carrinho.add');

    Route::get('/carrinho', [CarrinhoController::class, 'index'])
        ->middleware('auth')
        ->name('carrinho.index');

    Route::delete('/carrinho/item/{item}', [CarrinhoController::class, 'removeItem'])
        ->middleware('auth')
        ->name('carrinho.item.remove');

    // Para deixar o catálogo público, basta tirar essa rota do group e deixá-la fora do middleware.

    // Exportar livros em CSV (abre no Excel)
    /*Route::get('/livros/export', [LivroController::class, 'exportCsv'])
        ->name('livros.export');*/

    // Detalhes da requisição
    Route::get('/requisicoes/{requisicao}', [RequisicaoController::class, 'show'])
    ->name('requisicoes.show');

    // Confirmar entrega de requisição
    Route::post('/requisicoes/{requisicao}/confirmar-entrega', [RequisicaoController::class, 'confirmEntrega'])
    ->name('requisicoes.confirmEntrega');

    // Minhas requisições
    Route::get('/minhas-requisicoes', [RequisicaoController::class, 'minhas'])
    ->name('requisicoes.minhas');

    // Administração - apenas para Admins
    Route::middleware(['auth', 'verified', 'role:Admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/google-books', [GoogleBooksController::class, 'index'])
            ->name('googlebooks.index');

        Route::post('/google-books/import', [GoogleBooksController::class, 'import'])
            ->name('googlebooks.import');

        // Avaliações (moderação)
        Route::get('/avaliacoes', [AvaliacaoAdminController::class, 'index'])->name('avaliacoes.index');
        Route::get('/avaliacoes/{avaliacao}', [AvaliacaoAdminController::class, 'show'])->name('avaliacoes.show');
        Route::patch('/avaliacoes/{avaliacao}/aprovar', [AvaliacaoAdminController::class, 'aprovar'])->name('avaliacoes.aprovar');
        Route::patch('/avaliacoes/{avaliacao}/recusar', [AvaliacaoAdminController::class, 'recusar'])->name('avaliacoes.recusar');
    });

    Route::post('/requisicoes/{requisicao}/avaliacoes', [AvaliacaoController::class, 'store'])
    ->name('avaliacoes.store');

    // Alertas de disponibilidade de livro (Cidadão)
    Route::post('/livros/{livro}/alertas', [LivroAlertaController::class, 'store'])
        ->name('livros.alertas.store');

});
