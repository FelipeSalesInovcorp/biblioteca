<?php

namespace App\Actions\Avaliacoes;

use App\Mail\ConviteAvaliacaoCidadao;
use App\Models\Requisicao;
use Illuminate\Support\Facades\Mail;

class NotificarCidadaoQuePodeAvaliar
{
    public function handle(Requisicao $requisicao): void
    {
        $requisicao->loadMissing(['user', 'livro', 'avaliacao']);

        // Só após entrega confirmada
        if (is_null($requisicao->data_entrega_real)) {
            return;
        }

        // Se já existe avaliação, não envia convite
        if ($requisicao->avaliacao) {
            return;
        }

        // Segurança: tem user + email
        if (!$requisicao->user?->email) {
            return;
        }

        Mail::to($requisicao->user->email)->queue(new ConviteAvaliacaoCidadao($requisicao));
    }
}
