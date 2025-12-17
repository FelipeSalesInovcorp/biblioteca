<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Nova Requisição</h2>
    </x-slot>

    <div class="p-6">
        @if ($errors->any())
            <div class="mb-4 alert alert-error">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('requisicoes.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block mb-2">Livro disponível</label>
                <select name="livro_id" class="select select-bordered w-full">
                    @foreach($livrosDisponiveis as $livro)
                        <option value="{{ $livro->id }}">{{ $livro->nome }} ({{ $livro->isbn }})</option>
                    @endforeach
                </select>
            </div>

            <button class="btn btn-primary" type="submit">
                Requisitar
            </button>
        </form>
    </div>
</x-app-layout>

