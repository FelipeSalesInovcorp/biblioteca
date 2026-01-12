<?php

namespace App\Actions\Carrinho;

use App\Mail\CarrinhoAbandonadoMail;
use App\Models\Carrinho;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;

class NotificarCarrinhosAbandonados
{
    /**
     * Retorna quantos carrinhos foram notificados
     */
    public function execute(int $hours = 1): int
    {
        $limite = now()->subHours($hours);

        // carrinhos "ativos", sem notificação enviada, e com items
        $carrinhos = Carrinho::query()
            ->where('estado', 'ativo')
            ->whereNull('abandoned_notified_at')
            ->where('updated_at', '<=', $limite)
            ->whereHas('items')
            ->with(['user', 'items.livro'])
            ->get();

        $count = 0;

        foreach ($carrinhos as $carrinho) {
            // Segurança extra: só para cidadãos e com email
            if (!$carrinho->user || ($carrinho->user->role ?? null) !== 'cidadao') {
                continue;
            }

            Mail::to($carrinho->user->email)->send(new CarrinhoAbandonadoMail($carrinho));

            // idempotência: marca como notificado
            $carrinho->forceFill([
                'abandoned_notified_at' => now(),
            ]);
            
            if ($carrinho->save()) {
                // Só conta se salvou
                $count++;
            }  
            //$count++;
        }

        return $count;
    }
}
