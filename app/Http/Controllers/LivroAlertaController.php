<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\LivroAlerta;
use Illuminate\Http\Request;

class LivroAlertaController extends Controller
{
    public function store(Request $request, Livro $livro)
    {
        // se já estiver disponível, não faz sentido
        if ($livro->estaDisponivel()) {
            return back()->with('info', 'Este livro já se encontra disponível para requisição.');
        }

        LivroAlerta::updateOrCreate(
            ['livro_id' => $livro->id, 'user_id' => $request->user()->id],
            ['notificado_em' => null]
        );

        return back()->with('success', 'Vamos avisar por email quando este livro ficar disponível.');
    }
}
