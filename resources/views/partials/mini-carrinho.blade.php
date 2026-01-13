<div class="p-4">
    <div class="flex items-center justify-between mb-3">
        <h3 class="font-bold text-base">Cesto de compras</h3>
        <span class="text-sm text-base-content/60">
            {{ $carrinhoCount ?? 0 }} item(s)
        </span>
    </div>

    @if(($carrinhoCount ?? 0) === 0 || empty($miniCarrinho))
        <p class="text-sm text-base-content/60">O carrinho está vazio.</p>

        <div class="mt-4">
            <a href="{{ route('catalogo') }}" class="btn btn-sm btn-outline w-full">
                Voltar ao catálogo
            </a>
        </div>
    @else
        <div class="space-y-3 max-h-72 overflow-auto pr-1">
            @foreach($miniCarrinho['items'] as $item)
                <div class="flex gap-3 items-start">
                    <div class="w-12 h-16 bg-base-200 rounded flex items-center justify-center overflow-hidden">
                        @if($item->livro?->imagem_capa)
                            <img src="{{ asset('storage/'.$item->livro->imagem_capa) }}"
                                 alt="Capa"
                                 class="w-full h-full object-cover" />
                        @else
                            <span class="text-xs text-base-content/60">Sem capa</span>
                        @endif
                    </div>

                    <div class="flex-1">
                        <div class="font-semibold text-sm leading-snug line-clamp-2">
                            {{ $item->livro?->nome ?? 'Livro' }}
                        </div>

                        <div class="text-xs text-base-content/60 mt-1">
                            Qtd: {{ $item->quantidade }}
                            ·
                            {{ number_format((float)$item->preco_unitario, 2, ',', '.') }} €
                        </div>
                    </div>

                    <div class="text-sm font-semibold">
                        {{ number_format((float)($item->preco_unitario * $item->quantidade), 2, ',', '.') }} €
                    </div>
                </div>
            @endforeach

            @if(($carrinhoCount ?? 0) > count($miniCarrinho['items']))
                <div class="text-xs text-base-content/60 pt-1">
                    + mais itens no carrinho…
                </div>
            @endif
        </div>

        <div class="border-t border-base-200 mt-4 pt-3 flex items-center justify-between">
            <span class="font-bold">Total</span>
            <span class="font-bold">
                {{ number_format((float)$miniCarrinho['total'], 2, ',', '.') }} €
            </span>
        </div>

        <div class="mt-3 grid grid-cols-2 gap-2">
            <a href="{{ route('carrinho.index') }}" class="btn btn-sm btn-outline">
                Ver carrinho
            </a>

            <a href="{{ route('checkout.morada') }}" class="btn btn-sm btn-primary">
                Checkout
            </a>
        </div>
    @endif
</div>
