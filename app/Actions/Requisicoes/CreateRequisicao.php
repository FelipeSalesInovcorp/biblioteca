<?php

namespace App\Actions\Requisicoes;

use App\Models\Livro;
use App\Models\Requisicao;
use App\Mail\RequisicaoConfirmadaAdmin;
use App\Mail\RequisicaoConfirmadaCidadao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;

class CreateRequisicao
{
    public function execute(int $userId, int $livroId): Requisicao
    {
        return DB::transaction(function () use ($userId, $livroId) {

            // 1) Regra: máximo 3 requisições ativas por cidadão
            $ativas = Requisicao::where('user_id', $userId)
                ->whereNull('data_entrega_real')
                ->count();

            if ($ativas >= 3) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Já tem 3 livros requisitados em simultâneo.',
                ]);
            }

            // 2) Livro tem de estar disponível (sem requisição ativa)
            $livro = Livro::findOrFail($livroId);

            // 2.1) Sem stock => bloqueia requisição
            if (($livro->stock ?? 0) <= 0) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Não existe stock disponível para este livro.',
                ]);
            }

            // 2.2) Se requisições ativas >= stock => indisponível
            $ativasLivro = Requisicao::where('livro_id', $livro->id)
                ->whereNull('data_entrega_real')
                ->count();

            if ($ativasLivro >= $livro->stock) {
                throw ValidationException::withMessages([
                    'livro_id' => 'Este livro já está em processo de requisição.',
                ]);
            }

            // 3) Numeração sequencial (base)
            $ultimo = Requisicao::max('numero_sequencial') ?? 0;
            $sequencial = $ultimo + 1;

            // 4) Datas
            $hoje = now()->toDateString();
            $fimPrevisto = now()->addDays(5)->toDateString();

            $requisicao = Requisicao::create([
                'numero_sequencial' => $sequencial,
                'user_id' => $userId,
                'livro_id' => $livroId,
                'data_requisicao' => $hoje,
                'data_prevista_fim' => $fimPrevisto,
            ]);

            $requisicao->load(['user', 'livro']);

            // Email para o cidadão (queue imediato)
            Mail::to($requisicao->user->email)
                ->queue(new RequisicaoConfirmadaCidadao($requisicao));

            // Email para admins (queue com delay)
            $adminEmails = User::where('role', 'admin')->pluck('email')->toArray();

            if (!empty($adminEmails)) {
                Mail::to(config('mail.from.address'))
                    ->bcc($adminEmails)
                    ->later(
                        now()->addSeconds(15),
                        new RequisicaoConfirmadaAdmin($requisicao)
                    );
            }

            return $requisicao;


        });
    }
}
