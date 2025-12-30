<?php

namespace App\Mail;

use App\Models\Avaliacao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NovaAvaliacaoParaAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Avaliacao $avaliacao) {}

    public function build()
    {
        return $this->subject('Nova avaliação pendente')
            ->markdown('emails.avaliacoes.nova_para_admin', [
                'avaliacao' => $this->avaliacao,
            ]);
    }
}

