<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-blue-800">
            Requisição #{{ $requisicao->numero_sequencial }}
        </h2>
    </x-slot>

    <div class="p-6 space-y-4 max-w-5xl mx-auto">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title">Livro</h3>
                <p><b>Nome:</b> {{ $requisicao->livro?->nome ?? '—' }}</p>
                <p><b>Data requisição:</b> {{ $requisicao->data_requisicao?->format('d/m/Y') }}</p>
                <p><b>Fim previsto:</b> {{ $requisicao->data_prevista_fim?->format('d/m/Y') }}</p>
                <p><b>Entrega real:</b> {{ $requisicao->data_entrega_real?->format('d/m/Y') ?? '—' }}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h3 class="card-title">Review</h3>

                {{-- Ainda não foi entregue --}}
                @if (is_null($requisicao->data_entrega_real))
                    <p class="text-base-content/70">
                        Só podes deixar uma review depois da entrega do livro.
                    </p>

                {{-- Já existe review --}}
                @elseif ($requisicao->avaliacao)
                    <p><b>Estado:</b> {{ $requisicao->avaliacao->estado }}</p>
                    <p><b>Classificação:</b> {{ $requisicao->avaliacao->classificacao }}/5</p>
                    <p><b>Comentário:</b> {{ $requisicao->avaliacao->comentario }}</p>

                    @if ($requisicao->avaliacao->estado === 'recusada' && $requisicao->avaliacao->motivo_recusa)
                        <div class="alert alert-warning mt-2">
                            <b>Justificação:</b> {{ $requisicao->avaliacao->motivo_recusa }}
                        </div>
                    @endif

                {{-- Form para criar review --}}
                @else
                    <form method="POST" action="{{ route('avaliacoes.store', $requisicao) }}" class="space-y-3">
                        @csrf

                        <div class="form-control">
                            <label class="label"><span class="label-text">Classificação (1–5)</span></label>
                            <input type="number" name="classificacao" min="1" max="5"
                                   class="input input-bordered" required>
                        </div>

                        <div class="form-control">
                            <label class="label"><span class="label-text">Comentário</span></label>
                            <textarea name="comentario" rows="4" class="textarea textarea-bordered" required></textarea>
                        </div>

                        <button class="btn btn-primary">Submeter review</button>
                    </form>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
