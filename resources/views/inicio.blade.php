<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="corporate">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Biblioteca</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-base-200">

    {{-- NAVBAR --}}
    <div class="navbar bg-base-100 shadow px-4 min-h-16">
        <div class="flex-1">
            <!--<a href="{{ url('/') }}" class="btn btn-ghost text-xl font-bold">
                📚 Biblioteca
            </a>-->
            <a href="{{ url('/') }}" class="flex items-center gap-3">
            
            <!--<img src="{{ asset('img/logo-biblioteca.png') }}" class="h-16 w-auto">-->

            <img src="{{ asset('img/logo-biblioteca.png') }}" class="h-16 md:h-18 w-auto object-contain">

            </a>


        </div>

        <div class="flex-none gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-primary btn-sm">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline btn-sm">
                    Entrar
                </a>
                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                    Criar conta
                </a>
            @endauth
        </div>
    </div>

    {{-- CONTEÚDO PRINCIPAL --}}
    <main class="container mx-auto px-4 py-10">
        <div class="hero bg-base-100 rounded-2xl shadow-lg">
            <div class="hero-content flex-col lg:flex-row">
                <div class="flex-1">
                    <h1 class="text-4xl lg:text-5xl font-bold mb-4">
                        Gestão de Biblioteca
                    </h1>
                    <p class="py-2 text-base-content/80">
                        Organize livros, autores e editoras de forma simples e rápida.
                    </p>
                    <p class="pb-4 text-base-content/80">
                        Pesquise, filtre, exporte para Excel e mantenha toda a informação centralizada.
                    </p>

                    <div class="flex flex-wrap gap-3">
                        @auth
                            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                                Aceder ao sistema
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-primary text-white hover:brightness-110">
                                Começar agora
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline">
                                Já tenho conta
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="flex-1">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div class="card bg-base-200 shadow">
                            <div class="card-body">
                                <h2 class="card-title">📖 Livros</h2>
                                <p>Registe ISBN, capa, preço, bibliografia e mais.</p>
                            </div>
                        </div>
                        <div class="card bg-base-200 shadow">
                            <div class="card-body">
                                <h2 class="card-title">👤 Autores</h2>
                                <p>Associe vários autores ao mesmo livro.</p>
                            </div>
                        </div>
                        <div class="card bg-base-200 shadow">
                            <div class="card-body">
                                <h2 class="card-title">🏢 Editoras</h2>
                                <p>Gestão simples das editoras e respectivos logótipos.</p>
                            </div>
                        </div>
                        <div class="card bg-base-200 shadow">
                            <div class="card-body">
                                <h2 class="card-title">📊 Exportação</h2>
                                <p>Exporte os dados de livros para ficheiro Excel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- FOOTER --}}

    {{--<footer class="footer sm:footer-horizontal bg-neutral text-neutral-content p-10">--}}
    <footer class="footer sm:footer-horizontal bg-blue-600 text-white p-10">
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

</body>
</html>

