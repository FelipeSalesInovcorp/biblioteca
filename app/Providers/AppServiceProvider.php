<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use App\Models\Requisicao;


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
    
    }
}
