<div class="bg-base-100 shadow rounded-box p-8 text-center">
    <div class="text-2xl mb-2">👈</div>
    <h3 class="text-lg font-semibold">Seleciona uma sala ou conversa</h3>
    <p class="text-sm text-gray-600 mt-2">
        Escolhe uma sala à esquerda, ou cria/abre um DM para começar.
    </p>

    <div class="mt-6 flex items-center justify-center gap-3">
        <a class="px-4 py-2 bg-blue-600 text-white rounded" href="{{ route('chat.inbox') }}">
            Novo DM
        </a>

        @can('create', \App\Models\Room::class)
            <a class="px-4 py-2 border rounded" href="{{ route('chat.rooms.create') }}">
                Criar sala
            </a>
        @endcan
    </div>
</div>
