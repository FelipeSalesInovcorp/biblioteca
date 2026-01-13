<?php

namespace App\Actions\Stripe;

use App\Mail\EncomendaConfirmadaMail;
use App\Models\Carrinho;
use App\Models\Encomenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class ConfirmarPagamentoStripe
{
    /**
     * @throws ValidationException
     */
    public function execute(int $userId, string $sessionId): Encomenda
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        /** @var Session $session */
        $session = Session::retrieve($sessionId);

        // Garantir que encontramos a encomenda certa
        $encomenda = Encomenda::where('stripe_session_id', $session->id)->first();

        if (!$encomenda) {
            throw ValidationException::withMessages([
                'encomenda' => 'Encomenda não encontrada para esta sessão Stripe.',
            ]);
        }

        if ($encomenda->user_id !== $userId) {
            throw ValidationException::withMessages([
                'permissao' => 'Não tens permissão para confirmar esta encomenda.',
            ]);
        }

        // O Stripe devolve payment_status (ex.: 'paid')
        if (($session->payment_status ?? null) !== 'paid') {
            throw ValidationException::withMessages([
                'pagamento' => 'Pagamento ainda não confirmado.',
            ]);
        }

        // Se já está paga, idempotência
        if ($encomenda->estado === Encomenda::ESTADO_PAGA) {
            return $encomenda;
        }

        return DB::transaction(function () use ($encomenda) {
            $encomenda->update([
                'estado' => Encomenda::ESTADO_PAGA,
                'pago_em' => now(),
            ]);

            // ✅ Email de confirmação (1x, idempotente)
            if (is_null($encomenda->confirmacao_email_sent_at)) {
                $encomenda->loadMissing('user', 'items.livro');

                Mail::to($encomenda->user->email)
                    ->queue(new EncomendaConfirmadaMail($encomenda));

                $encomenda->forceFill([
                    'confirmacao_email_sent_at' => now(),
                ])->save();
            }

            // Fechar carrinho ativo do user (opção simples)
            $carrinho = Carrinho::where('user_id', $encomenda->user_id)
                ->where('estado', 'ativo')
                ->first();

            if ($carrinho) {
                $carrinho->update(['estado' => 'convertido']);
            }

            return $encomenda;
        });
    }
}
