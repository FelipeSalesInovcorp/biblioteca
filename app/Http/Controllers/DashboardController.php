<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // Exibe o dashboard com os livros recentes

    public function index()
{
    // Busca os 4 livros mais recentes com capa
    $livros = \App\Models\Livro::whereNotNull('imagem_capa')
                ->latest()
                //->take(4)
                ->orderByDesc('created_at')
                ->get();

    return view('dashboard', compact('livros'));
}

}
