<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-center text-base-content leading-tight text-blue-800">
            📖 Catálogo de Livros
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto space-y-6">

        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
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
    
            {{-- Filtros / pesquisa --}}
            <form method="GET" class="bg-base-100 p-4 rounded-box shadow flex flex-wrap gap-4 items-end">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Título ou ISBN"
                        class="input input-bordered w-full max-w-xs" />
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Editora</span>
                    </label>
                    <select name="editora_id" class="select select-bordered w-full max-w-xs">
                        <option value="">Todas</option>
                        @foreach ($editoras as $editora)
                            <option value="{{ $editora->id }}"
                                @selected(request('editora_id') == $editora->id)>
                                {{ $editora->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    Filtrar
                </button>

                @if(request()->has('search') || request()->has('editora_id'))
                    <a href="{{ route('catalogo') }}" class="btn btn-ghost">
                        Limpar
                    </a>
                @endif
            </form>

            {{-- Grelha de cards --}}
            @if ($livros->count())
                <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($livros as $livro)
                        <div class="card bg-base-100 shadow hover:shadow-lg transition flex flex-col">
                            @if ($livro->imagem_capa)
                                <figure class="px-4 pt-4">
                                    <img src="{{ asset('storage/'.$livro->imagem_capa) }}"
                                        alt="Capa {{ $livro->nome }}"
                                        class="rounded-box h-64 mx-auto object-contain" />
                                </figure>
                            @else
                                <div class="h-64 bg-base-200 rounded-box m-4 flex items-center justify-center">
                                    <span class="text-base-content/60 text-sm">Sem capa</span>
                                </div>
                            @endif

                            <div class="card-body">
                                <h3 class="card-title text-lg">
                                    {{ $livro->nome }}
                                </h3>

                                <p class="text-sm text-base-content/70">
                                    <span class="font-semibold">ISBN:</span> {{ $livro->isbn }}
                                </p>

                                <p class="text-sm text-base-content/70">
                                    <span class="font-semibold">Editora:</span>
                                    {{ $livro->editora->nome ?? '—' }}
                                </p>

                                <p class="text-sm text-base-content/70">
                                    <span class="font-semibold">Autores:</span>
                                    @if ($livro->autores->count())
                                        {{ $livro->autores->pluck('nome')->join(', ') }}
                                    @else
                                        <span class="text-base-content/50">Sem autores</span>
                                    @endif
                                </p>

                                <p class="text-sm font-semibold mt-2">
                                    {{ number_format($livro->preco, 2, ',', '.') }} €
                                </p>

                                @if ($livro->bibliografia)
                                    <p class="text-xs text-base-content/60 mt-2 line-clamp-3">
                                        {{ $livro->bibliografia }}
                                    </p>
                                @endif

                                <!--<div class="card-actions justify-end mt-4">
                                    {{-- Aqui podes no futuro colocar botão "Detalhes" --}}
                                    <span class="badge badge-outline">Disponível</span>
                                </div>-->
                                
                                <div class="card-actions justify-end items-center gap-2 mt-4">
                                    <a href="{{ route('livros.show', $livro) }}" class="btn btn-sm btn-ghost">
                                        Detalhes
                                    </a>

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
                                    <button class="btn btn-sm btn-disabled" disabled>
                                        Requisitar
                                    </button>
                                @endif

                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6">
                    {{ $livros->links() }}
                </div>
            @else
                <div class="text-center text-base-content/60">
                    Nenhum livro encontrado no catálogo.
                </div>
            @endif

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