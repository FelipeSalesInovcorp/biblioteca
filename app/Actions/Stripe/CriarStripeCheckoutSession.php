<?php

namespace App\Actions\Stripe;

use App\Models\Encomenda;
use Illuminate\Validation\ValidationException;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CriarStripeCheckoutSession
{
    /**
     * @throws ValidationException
     */
    public function execute(Encomenda $encomenda): Session
    {
        if ($encomenda->estado !== 'pendente') {
            throw ValidationException::withMessages([
                'estado' => 'A encomenda não está pendente.',
            ]);
        }

        $encomenda->loadMissing(['items.livro']);

        if ($encomenda->items->isEmpty()) {
            throw ValidationException::withMessages([
                'itens' => 'A encomenda não tem itens.',
            ]);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $lineItems = [];
        foreach ($encomenda->items as $item) {
            $nomeLivro = $item->livro->nome ?? 'Livro';

            // Stripe exige amount em cêntimos (inteiro)
            $unitAmount = (int) round(((float)$item->preco_unitario) * 100);

            if ($unitAmount <= 0) {
                throw ValidationException::withMessages([
                    'preco' => 'Existe item com preço inválido.',
                ]);
            }

            $lineItems[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => $nomeLivro,
                    ],
                ],
                'quantity' => (int) $item->quantidade,
            ];
        }

        $session = Session::create([
            'mode' => 'payment',
            'line_items' => $lineItems,

            // devolve para o nosso site
            'success_url' => route('checkout.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.stripe.cancel'),

            // metadata útil (para webhook / debug)
            'metadata' => [
                'encomenda_id' => (string) $encomenda->id,
            ],
        ]);

        // Guardar session id na encomenda (para validar depois)
        $encomenda->update([
            'stripe_session_id' => $session->id,
        ]);

        return $session;
    }
}
