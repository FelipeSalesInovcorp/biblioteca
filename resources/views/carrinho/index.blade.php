<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-blue-800">
                🛒 Meu Carrinho
            </h2>

            <a href="{{ route('catalogo') }}" class="btn btn-ghost">Continuar a procurar</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-4">

        @if (session('success'))
            <div class="alert alert-success shadow">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('info'))
            <div class="alert alert-info shadow">
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error shadow">
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (!$carrinho || $items->isEmpty())
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <p class="text-base-content/70">O teu carrinho está vazio.</p>
                </div>
            </div>
        @else
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>Livro</th>
                                    <th class="text-right">Preço</th>
                                    <th class="text-center">Qtd</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td>
                                            <div class="font-semibold">{{ $item->livro->nome ?? '—' }}</div>
                                            <div class="text-xs text-base-content/60">ISBN: {{ $item->livro->isbn ?? '—' }}</div>
                                        </td>

                                        <td class="text-right">
                                            {{ number_format((float)$item->preco_unitario, 2, ',', '.') }} €
                                        </td>

                                        <td class="text-center">
                                            {{ $item->quantidade }}
                                        </td>

                                        <td class="text-right">
                                            {{ number_format((float)($item->preco_unitario * $item->quantidade), 2, ',', '.') }} €
                                        </td>

                                        <td class="text-right">
                                            <form method="POST" action="{{ route('carrinho.item.remove', $item->id) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline btn-error">
                                                    Remover
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total</th>
                                    <th class="text-right">
                                        {{ number_format((float)$total, 2, ',', '.') }} €
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        {{-- Checkout vem no PASSO 3 --}}
                        <button class="btn btn-primary btn-disabled" disabled title="Checkout no próximo passo">
                            Avançar para Checkout
                        </button>
                    </div>
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
