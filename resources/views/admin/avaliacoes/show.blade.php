<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Detalhe da Avaliação
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
            <div class="p-3 bg-green-100 rounded">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
            <div class="p-3 bg-red-100 rounded">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="p-4 bg-white shadow rounded space-y-2">
                <div><b>Livro:</b> {{ $avaliacao->livro->nome ?? '—' }}</div>
                <div><b>Cidadão:</b> {{ $avaliacao->user->name ?? $avaliacao->user->email }}</div>
                <div><b>Requisição:</b> #{{ $avaliacao->requisicao->numero_sequencial ?? $avaliacao->requisicao_id }}</div>
                <div><b>Classificação:</b> {{ $avaliacao->classificacao }}/5</div>
                <div><b>Comentário:</b> {{ $avaliacao->comentario }}</div>
                <div><b>Estado:</b> {{ $avaliacao->estado }}</div>

                @if ($avaliacao->estado === 'recusada' && $avaliacao->motivo_recusa)
                <div class="p-3 bg-yellow-100 rounded">
                    <b>Motivo recusa:</b> {{ $avaliacao->motivo_recusa }}
                </div>
                @endif
            </div>

            @if ($avaliacao->estado === 'suspensa')
            <div class="p-4 bg-white shadow rounded flex gap-3 items-start">

                <form method="POST" action="{{ route('admin.avaliacoes.aprovar', $avaliacao) }}">
                    @csrf
                    @method('PATCH')
                    <button class="px-4 py-2 bg-green-600 text-white rounded">
                        Aprovar
                    </button>
                </form>

                <form method="POST" action="{{ route('admin.avaliacoes.recusar', $avaliacao) }}" class="flex-1 space-y-2">
                    @csrf
                    @method('PATCH')

                    <label class="block text-sm font-medium">Motivo da recusa</label>
                    <textarea name="motivo_recusa" rows="3" class="w-full border rounded p-2" required></textarea>

                    <button class="px-4 py-2 bg-red-600 text-white rounded">
                        Recusar
                    </button>
                </form>

            </div>
            @endif

        </div>
    </div>
</x-app-layout>