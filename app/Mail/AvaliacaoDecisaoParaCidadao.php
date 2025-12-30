<?php

namespace App\Mail;

use App\Models\Avaliacao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AvaliacaoDecisaoParaCidadao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Avaliacao $avaliacao) {}

    public function build()
    {
        $avaliacao = $this->avaliacao->loadMissing(['livro', 'user', 'requisicao']);

        return $this->subject('Decisão sobre a sua avaliação')
            ->markdown('emails.avaliacoes.decisao_para_cidadao', [
                'avaliacao' => $avaliacao,
            ]);
    }
}
