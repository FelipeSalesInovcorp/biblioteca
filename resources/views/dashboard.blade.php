<x-app-layout>
    <x-slot name="header">
        <h2 class="text-3xl font-bold text-blue-900 text-center">
            {{ __('Dashboard - Biblioteca') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto">
            <div class="grid gap-6 md:grid-cols-3">

                <a href="{{ route('livros.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                    <div class="card-body">
                        <h2 class="card-title">📚 Livros</h2>
                        <p>Gestão de títulos, ISBN, autores, editoras, preço, capa, etc.</p>
                        <div class="card-actions justify-end">
                            <span class="btn btn-primary btn-sm">Aceder</span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('autores.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                    <div class="card-body">
                        <h2 class="card-title">👤 Autores</h2>
                        <p>Lista de autores e associação com os seus livros.</p>
                        <div class="card-actions justify-end">
                            <span class="btn btn-primary btn-sm">Aceder</span>
                        </div>
                    </div>
                </a>

                <a href="{{ route('editoras.index') }}" class="card bg-base-100 shadow hover:shadow-lg transition">
                    <div class="card-body">
                        <h2 class="card-title">🏢 Editoras</h2>
                        <p>Gestão das editoras, nomes e logótipos.</p>
                        <div class="card-actions justify-end">
                            <span class="btn btn-primary btn-sm">Aceder</span>
                        </div>
                    </div>
                </a>

            </div>
        </div>
    </div>
</x-app-layout>

