@php
    $room = $activeRoom ?? null;
    $conversation = $activeConversation;
    $availableUsers = $availableUsers ?? collect();
@endphp

<div class="space-y-6">

    {{-- Header do painel --}}
    <div class="bg-base-100 shadow rounded-box p-4">
        @if($room)
            <h3 class="text-xl font-bold">
                🏠 {{ $room->name }}
                <span class="text-sm text-gray-500">(#{{ $room->reference }})</span>
            </h3>
            <p class="text-sm text-gray-600 mt-1">Sala</p>
        @else
            @php
                $other = $conversation->users->firstWhere('id', '!=', auth()->id());
            @endphp
            <h3 class="text-xl font-bold">
                💬 {{ $other?->name ?? 'Conversa Directa' }}
            </h3>
            <p class="text-sm text-gray-600 mt-1">DM</p>
        @endif
    </div>

    {{-- Convidar membros (só em sala e só admin autorizado) --}}
    @if($room)
        @can('invite', $room)
            <div class="bg-base-100 shadow rounded-box p-4">
                <h4 class="font-semibold">Convidar membros</h4>

                <form method="POST" action="{{ route('chat.rooms.invite', $room) }}" class="mt-3 space-y-3">
                    @csrf

                    <div class="space-y-2">
                        @forelse($availableUsers as $u)
                            <label class="flex items-center gap-2 text-sm">
                                <input type="checkbox" name="user_ids[]" value="{{ $u->id }}">
                                <span>{{ $u->name }} ({{ $u->email }})</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">Não há utilizadores para convidar.</p>
                        @endforelse
                    </div>

                    @if($availableUsers->count())
                        <button class="px-4 py-2 bg-blue-600 text-white rounded" type="submit">
                            Convidar selecionados
                        </button>
                    @endif
                </form>
            </div>
        @endcan
    @endif

    {{-- Membros --}}
    <div class="bg-base-100 shadow rounded-box p-4">
        <h4 class="font-semibold">Membros</h4>

        <ul class="list-disc ml-6 text-sm mt-2 space-y-1">
            @foreach($conversation->users as $member)
                <li class="flex items-center justify-between gap-3">
                    <span>
                        {{ $member->name }} ({{ $member->email }})
                        @if($room && $member->pivot?->role)
                            <span class="text-gray-500">— {{ $member->pivot->role }}</span>
                        @endif
                    </span>

                    {{-- Remover (só em sala, só admin autorizado, e nunca owner) --}}
                    @if($room)
                        @can('remove', $room)
                            @if(($member->pivot?->role ?? null) !== 'owner')
                                <form method="POST" action="{{ route('chat.rooms.remove', $room) }}">
                                    @csrf
                                    <input type="hidden" name="user_id" value="{{ $member->id }}">
                                    <button class="text-red-600 underline text-sm" type="submit">
                                        Remover
                                    </button>
                                </form>
                            @endif
                        @endcan
                    @endif
                </li>
            @endforeach
        </ul>
    </div>

    {{-- Mensagens --}}
    <div class="bg-base-100 shadow rounded-box p-4">
        <h4 class="font-semibold">Mensagens</h4>

        <div class="mt-3 space-y-3">
            @forelse($conversation->messages->sortBy('created_at') as $msg)
                <div class="border rounded p-3">
                    <div class="text-sm text-gray-600">
                        <strong>{{ $msg->user?->name ?? 'Utilizador removido' }}</strong>
                        <span class="text-gray-400">• {{ $msg->created_at }}</span>
                    </div>
                    <div class="mt-2 whitespace-pre-wrap">{{ $msg->content }}</div>
                </div>
            @empty
                <p class="text-sm text-gray-500">Ainda não há mensagens.</p>
            @endforelse
        </div>
    </div>

    {{-- Enviar mensagem --}}
    <div class="bg-base-100 shadow rounded-box p-4">
        <h4 class="font-semibold">Enviar mensagem</h4>

        @if ($errors->any())
            <div class="mt-3 p-3 bg-red-50 border border-red-200 rounded">
                <ul class="text-sm text-red-700 list-disc ml-6">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form class="mt-3 space-y-3" method="POST" action="{{ route('chat.messages.store', $conversation) }}">
            @csrf
            <textarea name="content" rows="3" class="w-full border rounded p-2" required>{{ old('content') }}</textarea>

            <div class="flex items-center gap-3">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Enviar</button>
            </div>
        </form>
    </div>

</div>

