<div class="bg-base-100 shadow rounded-box p-4 space-y-6">

    {{-- Salas --}}
    <div>
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Salas</h3>

            @can('create', \App\Models\Room::class)
                <a class="text-xs underline" href="{{ route('chat.rooms.create') }}">+ criar</a>
            @endcan
        </div>

        <div class="mt-3 space-y-2">
            @forelse($rooms as $room)
                @php
                    $isActiveRoom = $activeRoom && $activeRoom->id === $room->id;

                    // última mensagem (depende do controller ter carregado messages com limit(1))
                    $lastMsg = $room->conversation->messages->first();
                    $lastText = $lastMsg?->content
                        ? \Illuminate\Support\Str::limit($lastMsg->content, 40)
                        : 'Sem mensagens';
                    $lastTime = $lastMsg?->created_at?->format('d/m H:i');

                    // pivot do user nesta conversa (no controller filtramos conversation.users para só o user atual)
                    $me = $room->conversation->users->first();
                    $lastReadAtRaw = $me?->pivot?->last_read_at;
                    $lastReadAt = $lastReadAtRaw ? \Carbon\Carbon::parse($lastReadAtRaw) : null;

                    $unread = $lastMsg && (!$lastReadAt || $lastMsg->created_at->gt($lastReadAt));
                @endphp

                <a
                    href="{{ route('chat.rooms.show', $room) }}"
                    class="block px-3 py-2 rounded border hover:bg-gray-50 {{ $isActiveRoom ? 'bg-gray-100 border-gray-300' : '' }}"
                >
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-medium">{{ $room->name }}</div>

                        <div class="flex items-center gap-2">
                            @if($unread)
                                <span title="Não lidas" class="inline-block w-2 h-2 rounded-full bg-blue-600"></span>
                            @endif
                            <div class="text-[11px] text-gray-500">{{ $lastTime }}</div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500">#{{ $room->reference }}</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $lastText }}</div>
                </a>
            @empty
                <p class="text-sm text-gray-500">Ainda não participas em salas.</p>
            @endforelse
        </div>
    </div>

    {{-- Conversas (DMs) --}}
    <div>
        <div class="flex items-center justify-between">
            <h3 class="font-semibold">Conversas</h3>
            <a class="text-xs underline" href="{{ route('chat.inbox') }}">+ novo DM</a>
        </div>

        <div class="mt-3 space-y-2">
            @forelse($directConversations as $c)
                @php
                    $isActiveConversation = $activeConversation && $activeConversation->id === $c->id;

                    $other = $c->users->firstWhere('id', '!=', auth()->id());
                    $label = $other?->name ?? 'DM';

                    // última mensagem (depende do controller ter carregado messages com limit(1))
                    $lastMsg = $c->messages->first();
                    $lastText = $lastMsg?->content
                        ? \Illuminate\Support\Str::limit($lastMsg->content, 40)
                        : 'Sem mensagens';
                    $lastTime = $lastMsg?->created_at?->format('d/m H:i');

                    // pivot do user autenticado nesta conversa
                    $me = $c->users->firstWhere('id', auth()->id());
                    $lastReadAtRaw = $me?->pivot?->last_read_at;
                    $lastReadAt = $lastReadAtRaw ? \Carbon\Carbon::parse($lastReadAtRaw) : null;

                    $unread = $lastMsg && (!$lastReadAt || $lastMsg->created_at->gt($lastReadAt));
                @endphp
                
                <!-- Link para a conversa Irei alterar aqui-->

                <!--<a
                    href="{{ route('chat.conversations.show', $c) }}"
                    class="block px-3 py-2 rounded border hover:bg-gray-50 {{ $isActiveConversation ? 'bg-gray-100 border-gray-300' : '' }}"
                >
                    <div class="flex items-center justify-between gap-2">
                        <div class="font-medium">{{ $label }}</div>

                        <div class="flex items-center gap-2">
                            @if($unread)
                                <span title="Não lidas" class="inline-block w-2 h-2 rounded-full bg-blue-600"></span>
                            @endif
                            <div class="text-[11px] text-gray-500">{{ $lastTime }}</div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500">Direct</div>
                    <div class="text-xs text-gray-600 mt-1">{{ $lastText }}</div>
                </a>-->

                <a
    href="{{ route('chat.conversations.show', $c) }}"
    class="flex items-start gap-3 px-3 py-2 rounded border hover:bg-gray-50 {{ $isActiveConversation ? 'bg-gray-100 border-gray-300' : '' }}"
>
    {{-- Avatar do outro user --}}
    <img
        src="{{ $other?->profile_photo_url }}"
        alt="{{ $label }}"
        class="w-8 h-8 rounded-full object-cover mt-0.5"
    />

    <div class="flex-1 min-w-0">
        <div class="flex items-center justify-between gap-2">
            <div class="font-medium truncate">{{ $label }}</div>

            <div class="flex items-center gap-2 shrink-0">
                @if($unread)
                    <span title="Não lidas" class="inline-block w-2 h-2 rounded-full bg-blue-600"></span>
                @endif
                <div class="text-[11px] text-gray-500">{{ $lastTime }}</div>
            </div>
        </div>

        <div class="text-xs text-gray-500">Direct</div>
        <div class="text-xs text-gray-600 mt-1 truncate">{{ $lastText }}</div>
    </div>
</a>


                <!-- Fim do link para a conversa -->


            @empty
                <p class="text-sm text-gray-500">Ainda não tens DMs.</p>
            @endforelse
        </div>
    </div>

</div>
