<?php

namespace App\Actions\Requisicoes;

use App\Models\Livro;
use App\Models\Requisicao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateRequisicao
{
    public function execute(int $userId, int $livroId): Requisicao
    {
        return DB::transaction(function () use ($userId, $livroId) {

            // 1) Regra: máximo 3 requisições ativas por cidadão
            $ativas = Requisicao::where('user_id', $userId)
                ->whereNull('data_entrega_real')
                ->count();

            if ($ativas >= 3) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Já tem 3 livros requisitados em simultâneo.',
                ]);
            }

            // 2) Livro tem de estar disponível (sem requisição ativa)
            $livro = Livro::with('requisicoes')->findOrFail($livroId);

            $emRequisicao = $livro->requisicoes()
                ->whereNull('data_entrega_real')
                ->exists();

            if ($emRequisicao) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Este livro já está em processo de requisição.',
                ]);
            }

            // 3) Numeração sequencial (base)
            $ultimo = Requisicao::max('numero_sequencial') ?? 0;
            $sequencial = $ultimo + 1;

            // 4) Datas
            $hoje = now()->toDateString();
            $fimPrevisto = now()->addDays(5)->toDateString();

            return Requisicao::create([
                'numero_sequencial' => $sequencial,
                'user_id' => $userId,
                'livro_id' => $livroId,
                'data_requisicao' => $hoje,
                'data_prevista_fim' => $fimPrevisto,
            ]);
        });
    }
}
