<div class="card bg-base-100 shadow">
    <div class="card-body">
        <div class="flex gap-4">
            @if(!empty($book['capa_url']))
                <img src="{{ $book['capa_url'] }}" class="w-20 rounded" alt="Capa" />
            @endif

            <div class="space-y-1">
                <div class="font-bold">{{ $book['nome'] ?? 'Sem título' }}</div>

                <div class="text-sm opacity-80">
                    <span class="font-semibold">Autores:</span>
                    {{ !empty($book['autores']) ? implode(', ', $book['autores']) : '—' }}
                </div>

                <div class="text-sm opacity-80">
                    <span class="font-semibold">Editora:</span>
                    {{ $book['editora_nome'] ?? '—' }}
                </div>

                <div class="text-sm opacity-80">
                    <span class="font-semibold">ISBN:</span>
                    {{ $book['isbn'] ?? '—' }}
                </div>
            </div>
        </div>

        <div class="card-actions justify-end">
            <form method="POST" action="{{ route('admin.googlebooks.import') }}">
                @csrf
                <input type="hidden" name="google_volume_id" value="{{ $book['google_volume_id'] ?? '' }}" />
                <button class="btn btn-outline btn-sm">
                    Importar
                </button>
            </form>
        </div>
    </div>
</div>
{{-- Fim do cartão de resultado do Google Books --}}