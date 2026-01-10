<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-blue-800">✅ Checkout — Confirmação</h2>
            <a href="{{ route('carrinho.index') }}" class="btn btn-ghost">Voltar ao carrinho</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-4">

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="font-bold text-lg mb-2">Morada de entrega</h3>
                <p><span class="font-semibold">Nome:</span> {{ $encomenda->nome_entrega }}</p>
                <p><span class="font-semibold">Morada:</span> {{ $encomenda->morada }}</p>
                <p><span class="font-semibold">Código Postal:</span> {{ $encomenda->codigo_postal }}</p>
                <p><span class="font-semibold">Localidade:</span> {{ $encomenda->localidade }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="font-bold text-lg mb-4">Itens</h3>

                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>Livro</th>
                                <th class="text-right">Preço</th>
                                <th class="text-center">Qtd</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($encomenda->items as $item)
                            <tr>
                                <td>{{ $item->livro->nome ?? '—' }}</td>
                                <td class="text-right">{{ number_format((float)$item->preco_unitario, 2, ',', '.') }} €</td>
                                <td class="text-center">{{ $item->quantidade }}</td>
                                <td class="text-right">{{ number_format((float)$item->subtotal, 2, ',', '.') }} €</td>
                            </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-right">Total</th>
                                <th class="text-right">{{ number_format((float)$encomenda->total, 2, ',', '.') }} €</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-4 flex justify-end gap-2">
                    {{-- Stripe no PASSO 4 --}}
                    <!--<button class="btn btn-primary btn-disabled" disabled title="Pagamento no próximo passo">
                        Pagar com Stripe
                    </button>-->
                    <form method="POST" action="{{ route('checkout.stripe.start', $encomenda->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Pagar com Stripe
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
</x-app-layout>
