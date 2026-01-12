<?php

namespace App\Mail;

use App\Models\Carrinho;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CarrinhoAbandonadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Carrinho $carrinho)
    {
        $this->carrinho->loadMissing(['items.livro', 'user']);
    }

    public function build()
    {
        return $this->subject('⏰ Tens livros no carrinho à espera')
            ->markdown('emails.carrinho.abandonado', [
                'carrinho' => $this->carrinho,
            ]);
    }
}
