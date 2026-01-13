<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-green-700">✅ Compra efetuada</h2>

            <a href="{{ route('catalogo') }}" class="btn btn-sm btn-ghost">
                Voltar ao catálogo
            </a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto space-y-4">
        @if (session('success'))
            <div class="alert alert-success shadow">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body space-y-3">
                <p class="text-lg font-semibold">
                    Obrigado, {{ $encomenda->nome_entrega }}!
                </p>

                <p class="text-base-content/70">
                    A tua encomenda <span class="font-semibold">#{{ $encomenda->id }}</span> foi paga com sucesso.
                </p>

                <p class="text-base-content/70">
                    Vais receber um email de confirmação em breve com os detalhes da compra.
                </p>

                <div class="divider"></div>

                <div class="text-sm space-y-1">
                    <div><span class="font-semibold">Total:</span> {{ number_format((float)$encomenda->total, 2, ',', '.') }} €</div>
                    <div><span class="font-semibold">Estado:</span> {{ $encomenda->estado }}</div>
                    <div><span class="font-semibold">Pago em:</span> {{ $encomenda->pago_em ? $encomenda->pago_em->format('d/m/Y H:i') : '—' }}</div>
                </div>

                <div class="mt-4 flex gap-2">
                    <a href="{{ route('catalogo') }}" class="btn btn-primary btn-sm">
                        Continuar a comprar
                    </a>

                    <a href="{{ route('carrinho.index') }}" class="btn btn-outline btn-sm">
                        Ver carrinho
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
