<?php

namespace App\Http\Controllers;

use App\Actions\Avaliacoes\CreateAvaliacao;
use App\Models\Requisicao;
use Illuminate\Http\Request;

class AvaliacaoController extends Controller
{
    public function store(Request $request, Requisicao $requisicao, CreateAvaliacao $createAvaliacao)
    {
        $this->authorize('view', $requisicao);

        $data = $request->validate([
            'classificacao' => ['required', 'integer', 'min:1', 'max:5'],
            'comentario' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $createAvaliacao->execute(
            $requisicao,
            (int) $data['classificacao'],
            $data['comentario']
        );

        return redirect()
            ->route('requisicoes.show', $requisicao)
            ->with('success', 'Avaliação submetida. Ficará visível após validação do Admin.');
    }
}
