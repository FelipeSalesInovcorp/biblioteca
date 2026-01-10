<?php

namespace App\Http\Controllers;

use App\Actions\Stripe\ConfirmarPagamentoStripe;
use App\Actions\Stripe\CriarStripeCheckoutSession;
use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StripeCheckoutController extends Controller
{
    public function start(Request $request, Encomenda $encomenda)
    {
        $user = $request->user();

        if ($encomenda->user_id !== $user->id) {
            return redirect()->route('carrinho.index')
                ->with('error', 'Não tens permissão para pagar esta encomenda.');
        }

        try {
            $session = app(CriarStripeCheckoutSession::class)->execute($encomenda);
            return redirect()->away($session->url);
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível iniciar pagamento.';
            return back()->with('error', $msg);
        }
    }

    public function success(Request $request)
    {
        $sessionId = (string) $request->query('session_id');

        if (!$sessionId) {
            return redirect()->route('carrinho.index')->with('error', 'Sessão Stripe em falta.');
        }

        try {
            app(ConfirmarPagamentoStripe::class)->execute($request->user()->id, $sessionId);
            // Voltar à confirmação (onde mostras o resumo)
            return redirect()->route('checkout.confirmacao')
                ->with('success', 'Pagamento confirmado! Encomenda paga com sucesso.');
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível confirmar o pagamento.';
            return redirect()->route('carrinho.index')->with('error', $msg);
        }
    }

    public function cancel()
    {
        return redirect()->route('carrinho.index')
            ->with('info', 'Pagamento cancelado. A encomenda mantém-se pendente.');
    }
}
