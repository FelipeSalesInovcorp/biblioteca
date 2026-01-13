<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Requisicao;
use App\Models\User;
use App\Models\Carrinho;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Gate::define('admin', function ($user) {
            return $user->isAdmin();
        });


        View::composer('*', function ($view) {

        $count = 0;

        if (auth()->check()) {

            $query = Requisicao::whereNull('data_entrega_real');

            $count = auth()->user()->isAdmin()
                ? $query->count()
                : $query->where('user_id', auth()->id())->count();
        }

        $view->with('requisicoesAtivasCount', $count);
    });

        View::composer('*', function ($view) {
            $carrinhoCount = 0;
            $miniCarrinho = null;

            if (auth()->check() && (auth()->user()->role ?? null) === 'cidadao') {
                $carrinho = \App\Models\Carrinho::query()
                    ->where('user_id', auth()->id())
                    ->where('estado', 'ativo')
                    ->withCount('items')
                    ->with(['items.livro']) // para o mini-carrinho
                    ->first();

                $carrinhoCount = $carrinho?->items_count ?? 0;

                if ($carrinho) {
                    $miniCarrinho = [
                        'id' => $carrinho->id,
                        'items' => $carrinho->items->take(5), // limita para não ficar gigante
                        'total' => (float) $carrinho->items->sum(fn($i) => $i->preco_unitario * $i->quantidade),
                    ];
                }
            }

            $view->with('carrinhoCount', $carrinhoCount);
            $view->with('miniCarrinho', $miniCarrinho);
        });
    }
}
