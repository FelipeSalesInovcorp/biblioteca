<?php

namespace App\Actions\Requisicoes;

use App\Models\Requisicao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Actions\Livros\NotificarLivroDisponivel;
use App\Actions\Avaliacoes\NotificarCidadaoQuePodeAvaliar;
use App\Actions\Logs\LogActivity;

class ConfirmEntregaRequisicao
{
    public function execute(Requisicao $requisicao): Requisicao
    {
        return DB::transaction(function () use ($requisicao) {

            if ($requisicao->data_entrega_real) {
                throw ValidationException::withMessages([
                    'requisicao' => 'Esta requisição já foi marcada como entregue.',
                ]);
            }

            $entrega = now()->toDateString();

            $dias = $requisicao->data_requisicao
                ->diffInDays(now()); // Carbon

            $requisicao->update([
                'data_entrega_real' => $entrega,
                'dias_decorridos' => $dias,
            ]);

            // Log
            $requisicao->loadMissing('livro');

            LogActivity::run(
                module: 'Requisicoes',
                change: "Confirmou entrega da requisição #{$requisicao->numero_sequencial} (livro '{$requisicao->livro->nome}')",
                objectId: $requisicao->id,
                userId: $requisicao->user_id
            );

            // Notificar cidadão que pode avaliar
            DB::afterCommit(function () use ($requisicao) {
                app(NotificarCidadaoQuePodeAvaliar::class)->handle($requisicao);
            });

            //  Se após a entrega o livro ficar disponível, notificar alertas pendentes
            $livro = $requisicao->livro()->first();
            if ($livro) {
                app(NotificarLivroDisponivel::class)->handle($livro);
            }

            return $requisicao;
        });
    }
}
