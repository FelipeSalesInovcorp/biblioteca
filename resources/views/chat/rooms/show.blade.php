<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-blue-800 leading-tight">
            🏠 Sala: {{ $room->name }}
            <span class="text-sm text-gray-500">(#{{ $room->reference }})</span>
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-6">

            <div class="bg-base-100 shadow rounded-box p-4">
                <h3 class="font-semibold">Membros</h3>
                <ul class="list-disc ml-6 text-sm mt-2">
                    @foreach($conversation->users as $member)
                        <li>
                            {{ $member->name }} ({{ $member->email }})
                            @if($member->pivot?->role)
                                <span class="text-gray-500">— {{ $member->pivot->role }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="bg-base-100 shadow rounded-box p-4">
                <h3 class="font-semibold">Mensagens</h3>

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
                        <p class="text-sm text-gray-500">Ainda não há mensagens nesta sala.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-base-100 shadow rounded-box p-4">
                <h3 class="font-semibold">Enviar mensagem</h3>

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
                        <a class="text-sm underline" href="{{ route('chat.inbox') }}">Voltar</a>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
