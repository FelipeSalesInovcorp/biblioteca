<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold  text-blue-800 text-center">
                📘 {{ $livro->nome }}
            </h2>

            <!--<a href="{{ url()->previous() }}" class="btn btn-ghost">Voltar
            </a>-->
            <a href="{{ route('catalogo') }}" class="btn btn-ghost">
                <i class="bi bi-arrow-left-circle mr-1"></i>
                Voltar
            </a>

        </div>
    </x-slot>

    <div class="py-8">

        <!--@if (session('success'))
        <div class="alert alert-success shadow mb-4">
            <span>{{ session('success') }}</span>
        </div>
        @endif-->

        @if (session('success'))
        <div class="alert alert-success shadow mb-4 flex items-center justify-between">
            <span>{{ session('success') }}</span>

            <a href="{{ route('carrinho.index') }}" class="btn btn-xs btn-outline">Ver carrinho</a>
        </div>
        @endif


        @if (session('info'))
        <div class="alert alert-info shadow mb-4">
            <span>{{ session('info') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-error shadow mb-4 flex items-center justify-between text-white">
            <span>{{ session('error') }}</span>
            <a href="{{ route('carrinho.index') }}" class="btn btn-xs btn-outline">Ver carrinho</a>
        </div>
        @endif

        <div class="max-w-6xl mx-auto space-y-6">

            {{-- Info do livro --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex flex-col lg:flex-row gap-6">
                        <div class="w-full lg:w-1/3">
                            @if ($livro->imagem_capa)
                            <img src="{{ asset('storage/'.$livro->imagem_capa) }}"
                                alt="Capa {{ $livro->nome }}"
                                class="rounded-box w-full object-contain max-h-80" />
                            @else
                            <div class="h-80 bg-base-200 rounded-box flex items-center justify-center">
                                <span class="text-base-content/60 text-sm">Sem capa</span>
                            </div>
                            @endif
                        </div>

                        <div class="w-full lg:w-2/3 space-y-2">
                            <p><span class="font-semibold">ISBN:</span> {{ $livro->isbn }}</p>
                            <p><span class="font-semibold">Editora:</span> {{ $livro->editora->nome ?? '—' }}</p>
                            <p>
                                <span class="font-semibold">Autores:</span>
                                @if ($livro->autores->count())
                                {{ $livro->autores->pluck('nome')->join(', ') }}
                                @else
                                <span class="text-base-content/50">Sem autores</span>
                                @endif
                            </p>

                            <p class="font-semibold mt-2">
                                <!--{{ number_format($livro->preco, 2, ',', '.') }} €-->
                                @if($livro->preco !== null)
                                {{ number_format((float)$livro->preco, 2, ',', '.') }} €
                                @else
                                <span class="badge badge-ghost">Preço não disponível</span>
                                @endif
                            </p>

                            <!-- Carrinho -->
                            @auth
                            @if(auth()->user()->role === 'cidadao')

                            @if(is_null($livro->preco))
                            <button type="button"
                                class="btn btn-sm btn-disabled"
                                disabled
                                title="Preço indisponível">
                                Adicionar ao carrinho
                            </button>
                            @else
                            <form method="POST" action="{{ route('carrinho.add', $livro->id) }}">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-secondary">
                                    Adicionar ao carrinho
                                </button>
                            </form>
                            @endif

                            @endif
                            @endauth
                            <!-- Fim Carrinho -->

                            <div class="mt-3 flex items-center gap-2">
                                @if($livro->estaDisponivel())
                                <span class="badge badge-success">Disponível</span>

                                <form method="POST" action="{{ route('requisicoes.store') }}">
                                    @csrf
                                    <input type="hidden" name="livro_id" value="{{ $livro->id }}">
                                    <button type="submit" class="btn btn-sm btn-primary">
                                        Requisitar
                                    </button>
                                </form>
                                @else
                                <span class="badge badge-error">Indisponível</span>
                                <button class="btn btn-sm btn-disabled" disabled>Requisitar</button>

                                {{-- Formulário de alerta --}}
                                <form method="POST" action="{{ route('livros.alertas.store', $livro) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline">
                                        <i class="bi bi-bell-fill"></i> Avisar-me quando estiver disponível
                                    </button>
                                </form>

                                @endif
                            </div>

                            @if ($livro->bibliografia)
                            <div class="mt-4">
                                <h3 class="font-semibold">Bibliografia</h3>
                                <p class="text-sm text-base-content/70 whitespace-pre-line">{{ $livro->bibliografia }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>


            {{-- RELACIONADOS / SUGESTÕES  --}}

            {{-- 1) Relacionados por descrição --}}
            @if(isset($livrosRelacionados) && $livrosRelacionados->count())
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="text-lg font-bold">🔎 Livros relacionados (por descrição)</h3>
                    <p class="text-sm text-base-content/60">
                        Sugestões geradas automaticamente com base em semelhança de palavras na bibliografia.
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-3">
                        @foreach($livrosRelacionados as $rel)
                        <a href="{{ route('livros.show', $rel) }}"
                            class="card bg-base-200/40 hover:shadow transition">
                            <div class="card-body">
                                <h4 class="font-semibold line-clamp-2">{{ $rel->nome }}</h4>

                                <p class="text-xs text-base-content/60">
                                    {{ $rel->editora->nome ?? '—' }}
                                </p>

                                @if($rel->bibliografia)
                                <p class="text-xs text-base-content/60 line-clamp-3 mt-1">
                                    {{ $rel->bibliografia }}
                                </p>
                                @endif

                                <div class="mt-2">
                                    @if($rel->estaDisponivel())
                                    <span class="badge badge-success">Disponível</span>
                                    @else
                                    <span class="badge badge-error">Indisponível</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 2) Fallback honesto --}}
            @elseif(isset($outrasSugestoes) && $outrasSugestoes->count())
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="text-lg font-bold">✨ Outras sugestões</h3>
                    <p class="text-sm text-base-content/60">
                        Não foram encontrados livros suficientemente semelhantes pela descrição. Aqui vão outras sugestões.
                    </p>

                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 mt-3">
                        @foreach($outrasSugestoes as $sug)
                        <a href="{{ route('livros.show', $sug) }}"
                            class="card bg-base-200/40 hover:shadow transition">
                            <div class="card-body">
                                <h4 class="font-semibold line-clamp-2">{{ $sug->nome }}</h4>

                                <p class="text-xs text-base-content/60">
                                    {{ $sug->editora->nome ?? '—' }}
                                </p>

                                {{-- ✅ aqui era o bug: estava $rel --}}
                                @if($sug->bibliografia)
                                <p class="text-xs text-base-content/60 line-clamp-3 mt-1">
                                    {{ $sug->bibliografia }}
                                </p>
                                @endif

                                <div class="mt-2">
                                    @if($sug->estaDisponivel())
                                    <span class="badge badge-success">Disponível</span>
                                    @else
                                    <span class="badge badge-error">Indisponível</span>
                                    @endif
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- FIM RELACIONADOS / SUGESTÕES  --}}



            {{-- Avaliações (apenas ativas) --}}
            <div id="avaliacoes" class="card bg-base-100 shadow">
                <div class="card-body">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">⭐ Avaliações</h3>

                        {{-- Opcional: contador --}}
                        <span class="badge badge-outline">
                            {{ $avaliacoesAtivas->count() }} {{ $avaliacoesAtivas->count() === 1 ? 'avaliação' : 'avaliações' }}
                        </span>
                    </div>

                    @if($avaliacoesAtivas->isEmpty())
                    <p class="text-base-content/60">Este livro ainda não tem avaliações aprovadas.</p>
                    @else
                    <div class="space-y-4 mt-2">
                        @foreach($avaliacoesAtivas as $a)
                        <div class="p-4 bg-base-200/40 rounded-box">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-semibold">
                                        {{ $a->user?->name ?? 'Cidadão' }}
                                    </p>
                                    <p class="text-xs text-base-content/60">
                                        {{ $a->created_at?->format('d/m/Y H:i') }}
                                    </p>
                                </div>

                                <div class="badge badge-neutral">
                                    {{ $a->classificacao }}/5
                                </div>
                            </div>

                            <p class="mt-3 text-sm text-base-content/80">
                                {{ $a->comentario }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>



            {{-- Histórico de requisições --}}
            <div class="card bg-base-100 shadow">
                <div class="card-body">
                    <h3 class="text-lg font-bold">📌 Histórico de Requisições</h3>

                    @if($requisicoes->count())
                    <div class="overflow-x-auto">
                        <table class="table w-full">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Cidadão</th>
                                    <th>Data Requisição</th>
                                    <th>Fim Previsto</th>
                                    <th>Entrega Real</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($requisicoes as $r)
                                <tr>
                                    <td>{{ $r->numero_sequencial }}</td>
                                    <td>{{ $r->user?->name ?? '—' }}</td>
                                    <td>{{ $r->data_requisicao?->format('d/m/Y') }}</td>
                                    <td>{{ $r->data_prevista_fim?->format('d/m/Y') }}</td>
                                    <td>{{ $r->data_entrega_real?->format('d/m/Y') ?? '—' }}</td>
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
                    @else
                    <p class="text-base-content/60">Ainda não existem requisições para este livro.</p>
                    @endif
                </div>
            </div>

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