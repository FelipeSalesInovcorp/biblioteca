<?php

namespace App\Providers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use App\Policies\LivroPolicy;
use App\Policies\AutorPolicy;
use App\Policies\EditoraPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     */
    protected $policies = [
        Livro::class   => LivroPolicy::class,
        Autor::class   => AutorPolicy::class,
        Editora::class => EditoraPolicy::class,
        \App\Models\Requisicao::class => \App\Policies\RequisicaoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
