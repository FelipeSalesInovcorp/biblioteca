<?php

namespace App\Mail;

use App\Models\Encomenda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EncomendaConfirmadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Encomenda $encomenda)
    {
        $this->encomenda->loadMissing(['user', 'items.livro']);
    }

    public function build()
    {
        return $this->subject('✅ Encomenda confirmada #' . $this->encomenda->id)
            ->markdown('emails.encomendas.confirmada', [
                'encomenda' => $this->encomenda,
            ]);
    }
}
