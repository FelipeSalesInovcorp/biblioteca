<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConviteAvaliacaoCidadao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Requisicao $requisicao) {}

    public function build()
    {
        $requisicao = $this->requisicao->loadMissing(['livro', 'user']);

        return $this->subject('Já pode avaliar a sua requisição')
            ->markdown('emails.avaliacoes.convite', compact('requisicao'));
    }
}
