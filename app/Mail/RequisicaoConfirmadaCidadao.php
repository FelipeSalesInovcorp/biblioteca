<?php

namespace App\Mail;

use App\Models\Requisicao;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class RequisicaoConfirmadaCidadao extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Requisicao $requisicao) {}

    public function build()
    {
        $livro = $this->requisicao->livro;

        $mail = $this->subject('Confirmação de Requisição #'.$this->requisicao->numero_sequencial)
            ->markdown('emails.requisicoes.confirmada_cidadao', [
                'requisicao' => $this->requisicao,
                'livro' => $livro,
                'cidadao' => $this->requisicao->user,
            ]);

        if ($livro?->imagem_capa && Storage::disk('public')->exists($livro->imagem_capa)) {
            $mail->attach(
                Storage::disk('public')->path($livro->imagem_capa),
                [
                    'as' => 'capa-livro-'.$livro->id.'.png',
                    'mime' => 'image/png',
                ]
            );
        }

        return $mail;
    }
}
