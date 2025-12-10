<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-blue-900 text-center">
            📚 Livros
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto space-y-4">

            {{-- Filtros e pesquisa --}}
            <form method="GET" class="flex flex-wrap gap-4 items-end bg-base-100 p-4 rounded-box shadow">
                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Pesquisar</span>
                    </label>
                    <input type="text" name="search" value="{{ request('search') }}"
                          placeholder="Nome ou ISBN"
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

                @if(request()->hasAny(['search','editora_id']))
                    <a href="{{ route('livros.index') }}" class="btn btn-ghost">
                        Limpar
                    </a>
                @endif
            </form>

            {{-- Tabela --}}
            <div class="bg-base-100 p-4 rounded-box shadow">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>
                                    <a href="{{ route('livros.index', array_merge(request()->all(), [
                                        'sort' => 'isbn',
                                        'direction' => ($sort === 'isbn' && $direction === 'asc') ? 'desc' : 'asc',
                                    ])) }}">
                                        ISBN
                                    </a>
                                </th>
                                <th>
                                    <a href="{{ route('livros.index', array_merge(request()->all(), [
                                        'sort' => 'nome',
                                        'direction' => ($sort === 'nome' && $direction === 'asc') ? 'desc' : 'asc',
                                    ])) }}">
                                        Nome
                                    </a>
                                </th>
                                <th>Editora</th>
                                <th>Autores</th>
                                <th>
                                    <a href="{{ route('livros.index', array_merge(request()->all(), [
                                        'sort' => 'preco',
                                        'direction' => ($sort === 'preco' && $direction === 'asc') ? 'desc' : 'asc',
                                    ])) }}">
                                        Preço
                                    </a>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($livros as $livro)
                                <tr>
                                    <td>{{ $livro->isbn }}</td>
                                    <td>{{ $livro->nome }}</td>
                                    <td>{{ $livro->editora?->nome }}</td>
                                    <td>{{ $livro->autores->pluck('nome')->join(', ') }}</td>
                                    <td>{{ number_format($livro->preco, 2, ',', '.') }} €</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-base-content/60">
                                        Nenhum livro encontrado.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $livros->links() }}
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
    Providing reliable tech since 1992
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

