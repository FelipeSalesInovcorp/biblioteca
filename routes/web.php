<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LivroController;
use App\Http\Controllers\AutorController;
use App\Http\Controllers\EditoraController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\RequisicaoController;
use App\Http\Controllers\Admin\LogController;
use App\Http\Controllers\Admin\GoogleBooksController;
use App\Http\Controllers\AvaliacaoController;
use App\Http\Controllers\Admin\AvaliacaoAdminController;
use App\Http\Controllers\LivroAlertaController;
use App\Http\Controllers\CarrinhoController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\StripeCheckoutController;
use App\Http\Controllers\Admin\EncomendaAdminController;
use App\Http\Controllers\Chat\DirectMessageController;
use App\Http\Controllers\Chat\ConversationController;
use App\Http\Controllers\Chat\MessageController;
use App\Http\Controllers\Chat\InboxController;
use App\Http\Controllers\Chat\RoomController;

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

// Gestão de usuários (Admin)
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

    // Checkout (Cidadão)
    Route::get('/checkout/morada', [CheckoutController::class, 'moradaForm'])
        ->middleware('auth')
        ->name('checkout.morada');

    Route::post('/checkout/morada', [CheckoutController::class, 'moradaSubmit'])
        ->middleware('auth')
        ->name('checkout.morada.submit');

    Route::get('/checkout/confirmacao', [CheckoutController::class, 'confirmacao'])
        ->middleware('auth')
        ->name('checkout.confirmacao');
    // Fim Carrinho e Checkout

    // Stripe Checkout
    Route::post('/checkout/stripe/{encomenda}', [StripeCheckoutController::class, 'start'])
        ->middleware('auth')
        ->name('checkout.stripe.start');

    Route::get('/checkout/stripe/success', [StripeCheckoutController::class, 'success'])
        ->middleware('auth')
        ->name('checkout.stripe.success');

    Route::get('/checkout/stripe/cancel', [StripeCheckoutController::class, 'cancel'])
        ->middleware('auth')
        ->name('checkout.stripe.cancel');

    Route::get('/checkout/sucesso/{encomenda}', [CheckoutController::class, 'sucesso'])
    ->middleware('auth')
    ->name('checkout.sucesso');
    // Fim Stripe Checkout


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

        // Logs do sistema
        Route::get('/logs', [LogController::class, 'index'])->name('logs.index');
    });

    Route::post('/requisicoes/{requisicao}/avaliacoes', [AvaliacaoController::class, 'store'])
    ->name('avaliacoes.store');

    // Alertas de disponibilidade de livro (Cidadão)
    Route::post('/livros/{livro}/alertas', [LivroAlertaController::class, 'store'])
        ->name('livros.alertas.store');

    // Administração de encomendas (Admin)
    Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/encomendas', [EncomendaAdminController::class, 'index'])
            ->name('encomendas.index');
    });

    // Chat Routes
    /*Route::prefix('chat')->name('chat.')->group(function () {

        Route::post('/dm/{user}', [DirectMessageController::class, 'store'])
            ->name('dm.store');

        // página da conversa (placeholder por agora)
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])
            ->name('conversations.show');

        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])
            ->name('messages.store');
    });*/

    Route::prefix('chat')->name('chat.')->group(function () {

        Route::get('/inbox', [InboxController::class, 'index'])
            ->name('inbox');

        Route::post('/dm/{user}', [DirectMessageController::class, 'store'])
            ->name('dm.store');

        // página da conversa (placeholder por agora)
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])
            ->name('conversations.show');

        Route::post('/conversations/{conversation}/messages', [MessageController::class, 'store'])
            ->name('messages.store');

        // Rotas para salas de chat
        Route::get('/rooms/create', [RoomController::class, 'create'])->name('rooms.create');
        Route::post('/rooms', [RoomController::class, 'store'])->name('rooms.store');
        Route::get('/rooms/{room}', [RoomController::class, 'show'])->name('rooms.show');
    });

});