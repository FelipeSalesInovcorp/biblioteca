<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Livro;

class DashboardController extends Controller
{
    // Exibe o dashboard com os livros recentes

    public function index()
{
    $user = auth()->user();

        // Cidadão vai  para o catálogo
        if ($user && $user->isCidadao()) {
            return redirect()->route('catalogo');
        }

        // Admin continua no dashboard
        $livros = Livro::whereNotNull('imagem_capa')
            ->orderByDesc('created_at')
            ->get();

        return view('dashboard', compact('livros'));
}

}
