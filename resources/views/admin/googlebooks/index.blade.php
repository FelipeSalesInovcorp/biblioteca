<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">Google Books — Importação</h2>
    </x-slot>

    <div class="p-6 space-y-6">
        @if(session('success'))
            <div class="alert alert-success">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="GET" action="{{ route('admin.googlebooks.index') }}" class="max-w-3xl space-y-3">
            <div class="flex gap-2">
                <input
                    name="q"
                    value="{{ $q }}"
                    class="input input-bordered w-full"
                    placeholder="Pesquisar por título, autor ou ISBN..."
                />
                <button class="btn btn-primary">Pesquisar</button>
            </div>
            @error('q')
                <div class="text-error text-sm">{{ $message }}</div>
            @enderror
        </form>

        @if($results)
            <div class="flex items-center gap-2">
                <div class="badge badge-neutral">Total (API): {{ $results['totalItems'] ?? 0 }}</div>
                <div class="badge badge-outline">A mostrar: {{ count($results['items'] ?? []) }}</div>
            </div>

            @if(count($results['items'] ?? []))
                <div class="grid md:grid-cols-2 gap-4">
                    @foreach($results['items'] as $book)
                        @include('admin.googlebooks._result_card', ['book' => $book])
                    @endforeach
                </div>
            @else
                <div class="alert"><span>Nenhum resultado encontrado.</span></div>
            @endif
        @endif
    </div>
</x-app-layout>
