<div class="bg-base-100 shadow rounded-box p-4 space-y-6">

    {{-- Rooms --}}
    <div>
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Salas</h3>
            @can('create', \App\Models\Room::class)
                <a class="text-xs underline" href="{{ route('chat.rooms.create') }}">+ criar</a>
            @endcan
        </div>

        <div class="mt-3 space-y-2">
            @forelse($rooms as $room)
                <a
                    href="{{ route('chat.rooms.show', $room) }}"
                    class="block px-3 py-2 rounded border hover:bg-gray-50"
                >
                    <div class="font-medium">{{ $room->name }}</div>
                    <div class="text-xs text-gray-500">#{{ $room->reference }}</div>
                </a>
            @empty
                <p class="text-sm text-gray-500">Ainda não participas em salas.</p>
            @endforelse
        </div>
    </div>

    {{-- DMs --}}
    <div>
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Conversas</h3>
            <a class="text-xs underline" href="{{ route('chat.inbox') }}">+ novo DM</a>
        </div>

        <div class="mt-3 space-y-2">
            @forelse($directConversations as $c)
                @php
                    $other = $c->users->firstWhere('id', '!=', auth()->id());
                    $label = $other?->name ?? 'DM';
                @endphp

                <a
                    href="{{ route('chat.conversations.show', $c) }}"
                    class="block px-3 py-2 rounded border hover:bg-gray-50"
                >
                    <div class="font-medium">{{ $label }}</div>
                    <div class="text-xs text-gray-500">Direct</div>
                </a>
            @empty
                <p class="text-sm text-gray-500">Ainda não tens DMs.</p>
            @endforelse
        </div>
    </div>

</div>
