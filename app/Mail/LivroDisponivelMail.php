<?php

namespace App\Mail;

use App\Models\Livro;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LivroDisponivelMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Livro $livro) {}

    public function build()
    {
        $livro = $this->livro->loadMissing(['editora', 'autores']);

        return $this->subject('Livro disponível para requisição')
            ->markdown('emails.livros.disponivel', compact('livro'));
    }
}
