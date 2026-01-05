<?php

namespace App\Actions\Livros;

use App\Mail\LivroDisponivelMail;
use App\Models\Livro;
use App\Models\LivroAlerta;
use Illuminate\Support\Facades\Mail;

class NotificarLivroDisponivel
{
    public function handle(Livro $livro): void
    {
        // só faz sentido se o livro estiver disponível AGORA
        if (!$livro->estaDisponivel()) {
            return;
        }

        $alertas = LivroAlerta::with('user')
            ->where('livro_id', $livro->id)
            ->whereNull('notificado_em')
            ->get();

        foreach ($alertas as $alerta) {
            Mail::to($alerta->user->email)->queue(new LivroDisponivelMail($livro));
            $alerta->update(['notificado_em' => now()]);
        }
    }
}
