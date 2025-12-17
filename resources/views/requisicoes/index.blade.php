<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-center text-base-content leading-tight text-blue-800">Requisições</h2>

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

        <div class="grid gap-4 sm:grid-cols-3 mb-6">
          <div class="stat bg-base-100 shadow rounded-box">
            <div class="stat-title">Requisições Ativas</div>
            <div class="stat-value text-primary">{{ $ativasCount }}</div>
          </div>

    <div class="stat bg-base-100 shadow rounded-box">
        <div class="stat-title">Requisições nos últimos 30 dias</div>
        <div class="stat-value">{{ $ultimos30DiasCount }}</div>
    </div>

    <div class="stat bg-base-100 shadow rounded-box">
        <div class="stat-title">Livros entregues hoje</div>
        <div class="stat-value">{{ $entreguesHojeCount }}</div>
    </div>
</div>



        <div class="overflow-x-auto">
            <table class="table w-full">
                <thead>
                    <tr>
                        <th>Número</th>
                        <th>Livro</th>
                        <th>Data Requisição</th>
                        <th>Fim Previsto</th>
                        <th>Entrega Real</th>
                        <th>Dias</th>
                        <th>Estado</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requisicoes as $r)
                    <tr>
                        <td>{{ $r->numero_sequencial }}</td>
                        <td>{{ $r->livro?->nome }}</td>
                        <td>{{ $r->data_requisicao?->format('d/m/Y') }}</td>
                        <td>{{ $r->data_prevista_fim?->format('d/m/Y') }}</td>
                        <td>{{ $r->data_entrega_real?->format('d/m/Y') ?? '—' }}</td>
                        <td>{{ $r->dias_decorridos ?? '—' }}</td>

                        <td>
                            @if($r->data_entrega_real)
                                <span class="badge badge-success">Entregue</span>
                            @else
                                <span class="badge badge-warning">Ativa</span>
                            @endif
                        </td>

        <td class="text-right">
            @if(auth()->user()->isAdmin() && !$r->data_entrega_real)
                <form method="POST" action="{{ route('requisicoes.confirmEntrega', $r) }}"
                      class="inline"
                      onsubmit="return confirm('Confirmar a boa receção do livro?');">
                    @csrf
                    <button class="btn btn-sm btn-success" type="submit">
                        Confirmar entrega
                    </button>
                </form>
            @else
                —
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
