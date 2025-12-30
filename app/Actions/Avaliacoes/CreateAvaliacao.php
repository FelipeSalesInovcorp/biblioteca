<?php

namespace App\Actions\Avaliacoes;

use App\Mail\NovaAvaliacaoParaAdmin;
use App\Models\Avaliacao;
use App\Models\Requisicao;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class CreateAvaliacao
{
    /**
     * Cria uma avaliação (sempre "suspensa") e notifica admins.
     */
    public function execute(Requisicao $requisicao, int $classificacao, string $comentario): Avaliacao
    {
        // regra do documento: só após entrega
        if (is_null($requisicao->data_entrega_real)) {
            throw ValidationException::withMessages([
                'avaliacao' => 'Só é possível avaliar depois da entrega do livro.',
            ]);
        }

        // 1 avaliação por requisição
        if ($requisicao->avaliacao()->exists()) {
            throw ValidationException::withMessages([
                'avaliacao' => 'Esta requisição já tem uma avaliação associada.',
            ]);
        }

        $avaliacao = Avaliacao::create([
            'requisicao_id' => $requisicao->id,
            'livro_id' => $requisicao->livro_id,
            'user_id' => $requisicao->user_id,
            'classificacao' => $classificacao,
            'comentario' => $comentario,
            'estado' => Avaliacao::ESTADO_SUSPENSA,
        ]);

        // email para admins
        $adminEmails = User::where('role', 'admin')->pluck('email')->all();
        if (!empty($adminEmails)) {
            Mail::to(config('mail.from.address'))
                ->bcc($adminEmails)
                ->queue(new NovaAvaliacaoParaAdmin($avaliacao));
        }

        return $avaliacao;
    }
}
