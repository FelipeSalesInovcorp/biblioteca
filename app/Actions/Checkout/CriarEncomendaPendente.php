<?php

namespace App\Actions\Checkout;

use App\Models\Carrinho;
use App\Models\Encomenda;
use App\Models\EncomendaItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CriarEncomendaPendente
{
    /**
     * @throws ValidationException
     */
    public function execute(User $user, array $dadosMorada): Encomenda
    {
        if (($user->role ?? null) !== 'cidadao') {
            throw ValidationException::withMessages([
                'role' => 'Apenas cidadãos podem efetuar checkout.',
            ]);
        }

        $carrinho = Carrinho::where('user_id', $user->id)
            ->where('estado', 'ativo')
            ->with(['items.livro'])
            ->first();

        if (!$carrinho || $carrinho->items->isEmpty()) {
            throw ValidationException::withMessages([
                'carrinho' => 'O carrinho está vazio.',
            ]);
        }

        // Validar preços e (opcional) disponibilidade
        foreach ($carrinho->items as $item) {
            if (!$item->livro) {
                throw ValidationException::withMessages(['livro' => 'Existe um item inválido no carrinho.']);
            }

            if ($item->preco_unitario === null) {
                throw ValidationException::withMessages([
                    'preco' => 'Existe um livro sem preço no carrinho.',
                ]);
            }

            // Se quiseres reforçar disponibilidade no checkout:
            // if (method_exists($item->livro, 'estaDisponivel') && !$item->livro->estaDisponivel()) {
            //     throw ValidationException::withMessages([
            //         'disponibilidade' => 'Um dos livros já não está disponível. Remove-o do carrinho.',
            //     ]);
            // }
        }

        return DB::transaction(function () use ($user, $dadosMorada, $carrinho) {
            $total = $carrinho->items->sum(fn ($i) => $i->preco_unitario * $i->quantidade);

            $encomenda = Encomenda::create([
                'user_id' => $user->id,
                'estado' => 'pendente',
                'nome_entrega' => $dadosMorada['nome_entrega'],
                'morada' => $dadosMorada['morada'],
                'codigo_postal' => $dadosMorada['codigo_postal'],
                'localidade' => $dadosMorada['localidade'],
                'total' => $total,
                'stripe_session_id' => null,
                'pago_em' => null,
            ]);

            foreach ($carrinho->items as $item) {
                EncomendaItem::create([
                    'encomenda_id' => $encomenda->id,
                    'livro_id' => $item->livro_id,
                    'quantidade' => $item->quantidade,
                    'preco_unitario' => $item->preco_unitario,
                    'subtotal' => $item->preco_unitario * $item->quantidade,
                ]);
            }

            // NOTA: não convertemos o carrinho ainda (isso deve acontecer no pagamento bem-sucedido no PASSO 4)
            // Assim, se o utilizador desistir do checkout, o carrinho continua.

            /*
            Controller fica limpo
            Encomenda criada como pendente
            Itens copiados do carrinho com preços “snapshot”
            Total calculado
            */

            return $encomenda;
        });
    }
}
