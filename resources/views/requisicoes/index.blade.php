<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl">Requisições</h2>

            @can('create', \App\Models\Requisicao::class)
                <a href="{{ route('requisicoes.create') }}" class="btn btn-primary">
                    Nova Requisição
                </a>
            @endcan
        </div>
    </x-slot>

    <div class="p-6">
        @if(session('success'))
            <div class="mb-4 alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Livro</th>
                        <th>Data Requisição</th>
                        <th>Fim Previsto</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requisicoes as $r)
                    <tr>
                        <td>{{ $r->numero_sequencial }}</td>
                        <td>{{ $r->livro?->nome }}</td>
                        <td>{{ $r->data_requisicao?->format('d/m/Y') }}</td>
                        <td>{{ $r->data_prevista_fim?->format('d/m/Y') }}</td>
                        <td>
                            @if($r->data_entrega_real)
                                Entregue
                            @else
                                Ativa
                            @endif
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $requisicoes->links() }}
        </div>
    </div>
</x-app-layout>
