<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-3xl text-center text-base-content leading-tight">
            Dashboard - Biblioteca
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto px-4 space-y-8">

            
    {{-- CARROSSEL DINÂMICO COM CAPAS REAIS --}}
@if($livros->count() > 0)
<div class="carousel w-full rounded-box shadow-lg overflow-hidden">

    @foreach($livros as $index => $livro)
        <div id="slide{{ $index+1 }}" class="carousel-item relative w-full">
            <img src="{{ asset('storage/' . $livro->imagem_capa) }}"
                class="w-full h-64 md:h-80 object-contain bg-base-200"
                alt="Capa do livro {{ $livro->nome }}">
            
            <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                <a href="#slide{{ $index === 0 ? $livros->count() : $index }}" class="btn btn-circle">❮</a>
                <a href="#slide{{ $index+1 == $livros->count() ? 1 : $index+2 }}" class="btn btn-circle">❯</a>
            </div>
        </div>
    @endforeach

</div>
@endif



            {{-- Grelha de cards principais --}}
            <div class="grid gap-6 md:grid-cols-3">

                {{-- CARD LIVROS --}}
                <div class="card bg-base-100 shadow-lg border border-base-200">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">📚</span>
                            <h3 class="card-title text-lg">Livros</h3>
                        </div>
                        <p class="text-sm text-base-content/70">
                            Gestão de títulos, ISBN, autores, editoras, preço, capa, etc.
                        </p>

                        <div class="card-actions justify-end mt-4 gap-2">
                            <a href="{{ route('livros.index') }}" class="btn btn-primary btn-sm">
                                Gerir livros
                            </a>
                            <a href="{{ route('catalogo') }}" class="btn btn-outline btn-sm btn-success">
                                Ver catálogo
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CARD AUTORES --}}
                <div class="card bg-base-100 shadow-lg border border-base-200">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">👤</span>
                            <h3 class="card-title text-lg">Autores</h3>
                        </div>
                        <p class="text-sm text-base-content/70">
                            Lista de autores e associação com os seus livros.
                        </p>

                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('autores.index') }}" class="btn btn-primary btn-sm">
                                Aceder
                            </a>
                        </div>
                    </div>
                </div>

                {{-- CARD EDITORAS --}}
                <div class="card bg-base-100 shadow-lg border border-base-200">
                    <div class="card-body">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xl">🏢</span>
                            <h3 class="card-title text-lg">Editoras</h3>
                        </div>
                        <p class="text-sm text-base-content/70">
                            Gestão das editoras, nomes e logótipos.
                        </p>

                        <div class="card-actions justify-end mt-4">
                            <a href="{{ route('editoras.index') }}" class="btn btn-primary btn-sm">
                                Aceder
                            </a>
                        </div>
                    </div>
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

