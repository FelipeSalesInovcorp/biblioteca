<?php

namespace App\Actions\Stripe\Webhook;

use App\Models\Carrinho;
use App\Models\Encomenda;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session;

class ProcessarCheckoutSessionCompleted
{
    /**
     * Idempotente:
     * - Se já estiver paga, não faz nada.
        * - Se não estiver paga, marca como paga e fecha o carrinho ativo do user.
     * @throws ValidationException
     */
    public function execute(Session $session): void
    {
        $sessionId = $session->id ?? null;
        if (!$sessionId) {
            throw ValidationException::withMessages(['stripe' => 'Session ID em falta.']);
        }

        // Stripe devolve "paid" quando está realmente pago
        $paymentStatus = $session->payment_status ?? null;
        if ($paymentStatus !== 'paid') {
            throw ValidationException::withMessages(['stripe' => 'Pagamento ainda não está confirmado como paid.']);
        }

        // 1 - encontrar por stripe_session_id
        $encomenda = Encomenda::where('stripe_session_id', $sessionId)->first();

        // 2 - Fallback: metadata.encomenda_id (se existir)
        if (!$encomenda) {
            $metaId = $session->metadata->encomenda_id ?? null;
            if ($metaId) {
                $encomenda = Encomenda::find((int) $metaId);
            }
        }

        if (!$encomenda) {
            throw ValidationException::withMessages([
                'encomenda' => 'Encomenda não encontrada para esta sessão.',
            ]);
        }

        // Idempotência: se já está paga, acabou
        if ($encomenda->estado === 'paga') {
            return;
        }

        DB::transaction(function () use ($encomenda) {
            $encomenda->update([
                'estado' => 'paga',
                'pago_em' => now(),
            ]);

            // Fechar carrinho ativo do user
            $carrinho = Carrinho::where('user_id', $encomenda->user_id)
                ->where('estado', 'ativo')
                ->first();

            if ($carrinho) {
                $carrinho->update(['estado' => 'convertido']);
                // Opcional: limpar items
                // $carrinho->items()->delete();
            }
        });
    }
}
