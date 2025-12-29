<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-center text-base-content leading-tight text-blue-800">📝 Requisição de Livros</h2>

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
                        @if(auth()->user()->isAdmin())
                        <th>Cidadão</th>
                        @endif
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
                        
                        @if(auth()->user()->isAdmin())
                        <td>
                            <div class="flex items-center gap-3">
                                <img class="w-10 h-10 rounded-full"
                                    src="{{ $r->user?->profile_photo_url }}"
                                    alt="Foto do cidadão">
                                <div class="leading-tight">
                                    <div class="font-semibold">{{ $r->user?->name }}</div>
                                    <div class="text-xs opacity-70">{{ $r->user?->email }}</div>
                                </div>
                            </div>
                        </td>
                        @endif

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

<footer class="footer sm:footer-horizontal bg-blue-700 text-white p-10">
    <aside>
        <svg
            width="50"
            height="50"
            viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg"
            fill-rule="evenodd"
            clip-rule="evenodd"
            class="fill-current">
            <path
                d="M19 22h-14c-1.657 0-3-1.343-3-3v-14c0-1.657 1.343-3 3-3h14c1.657 0 3 1.343 3 3v14c0 1.657-1.343 3-3 3zm-11-20c-.551 0-1 .449-1 1v18c0 .552.449 1 1 1s1-.448 1-1v-18c0-.551-.449-1-1-1zm6 0c-.551 0-1 .449-1 1v18c0 .552.449 1 1 1s1-.448 1-1v-18c0-.551-.449-1-1-1zm-4 4h-2v2h2v-2zm0 4h-2v2h2v-2zm0 4h-2v2h2v-2zm4-4h-2v2h2v-2zm0 4h-2v2h2v-2zm0 4h-2v2h2v-2zm4-8h-2v2h2v-2zm0 4h-2v2h2v-2zm0 4h-2v2h2v-2z"></path>
        </svg>
        <p>
            Biblioteca Ltd.
            <br />
            Onde o conhecimento ganha vida.
        </p>
    </aside>
    <nav>
        <h6 class="footer-title">Social</h6>
        <div class="grid grid-flow-col gap-4">
            <a>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    class="fill-current">
                    <path
                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path>
                </svg>
            </a>
            <a>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    class="fill-current">
                    <path
                        d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path>
                </svg>
            </a>
            <a>
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    class="fill-current">
                    <path
                        d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                </svg>
            </a>
        </div>
    </nav>
</footer>