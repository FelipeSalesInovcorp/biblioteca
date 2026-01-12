<?php

namespace App\Http\Controllers;

use App\Actions\Stripe\Webhook\ProcessarCheckoutSessionCompleted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $secret = config('services.stripe.webhook_secret');

        if (!$secret) {
            Log::error('STRIPE_WEBHOOK_SECRET não configurado.');
            return response('Webhook secret not configured', 500);
        }

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\UnexpectedValueException $e) {
            return response('Invalid payload', 400);
        } catch (SignatureVerificationException $e) {
            return response('Invalid signature', 400);
        }

        try {
            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object;
                    app(ProcessarCheckoutSessionCompleted::class)->execute($session);
                    break;

                default:
                    // Ignora outros eventos
                    break;
            }

            return response('OK', 200);
        } catch (ValidationException $e) {
            // Não é um erro “transitório”. Loga e devolve 200 para não ficar em retry infinito.
            Log::warning('Stripe webhook validation warning', [
                'type' => $event->type ?? null,
                'errors' => $e->errors(),
            ]);

            return response('OK', 200);
        } catch (\Throwable $e) {
            // Erro real -> 500 para o Stripe tentar novamente
            Log::error('Stripe webhook failed', [
                'type' => $event->type ?? null,
                'message' => $e->getMessage(),
            ]);

            return response('Webhook handler error', 500);
        }
    }
}
