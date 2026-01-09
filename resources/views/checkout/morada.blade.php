<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-blue-800">📦 Checkout — Morada</h2>
            <a href="{{ route('carrinho.index') }}" class="btn btn-ghost">Voltar ao carrinho</a>
        </div>
    </x-slot>

    <div class="py-8 max-w-3xl mx-auto space-y-4">

        @if ($errors->any())
            <div class="alert alert-error shadow">
                <div>
                    <p class="font-semibold">Corrige os campos:</p>
                    <ul class="list-disc ml-6">
                        @foreach ($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <form method="POST" action="{{ route('checkout.morada.submit') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="label"><span class="label-text">Nome (entrega)</span></label>
                        <input name="nome_entrega" value="{{ old('nome_entrega') }}"
                               class="input input-bordered w-full" required>
                    </div>

                    <div>
                        <label class="label"><span class="label-text">Morada</span></label>
                        <input name="morada" value="{{ old('morada') }}"
                               class="input input-bordered w-full" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="label"><span class="label-text">Código Postal</span></label>
                            <input name="codigo_postal" value="{{ old('codigo_postal') }}"
                                   class="input input-bordered w-full" required>
                        </div>

                        <div>
                            <label class="label"><span class="label-text">Localidade</span></label>
                            <input name="localidade" value="{{ old('localidade') }}"
                                   class="input input-bordered w-full" required>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit" class="btn btn-primary">
                            Continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-app-layout>
