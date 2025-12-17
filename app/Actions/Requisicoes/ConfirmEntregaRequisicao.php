<?php

namespace App\Actions\Requisicoes;

use App\Models\Requisicao;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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

            return $requisicao;
        });
    }
}
