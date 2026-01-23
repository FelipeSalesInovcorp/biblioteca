<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-blue-800 leading-tight">
                💬 Chat
            </h2>

            <div class="flex items-center gap-3">
                <a class="text-sm underline" href="{{ route('chat.inbox') }}">
                    Novo DM
                </a>

                @can('create', \App\Models\Room::class)
                    <a class="text-sm underline" href="{{ route('chat.rooms.create') }}">
                        Criar sala
                    </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-12 gap-6">

                {{-- Sidebar --}}
                <aside class="col-span-12 md:col-span-4 lg:col-span-3">
                    @include('chat.partials.sidebar', [
                        'rooms' => $rooms,
                        'directConversations' => $directConversations,
                        'activeConversation' => $activeConversation,
                        'activeRoom' => $activeRoom,
                    ])
                </aside>

                {{-- Main panel --}}
                <main class="col-span-12 md:col-span-8 lg:col-span-9">
                    @if($activeConversation)
                        @include('chat.partials.conversation', [
                            'activeConversation' => $activeConversation,
                            'activeRoom' => $activeRoom,
                            'availableUsers' => $availableUsers ?? collect(),
                        ])
                    @else
                        @include('chat.partials.empty')
                    @endif
                </main>

            </div>
        </div>
    </div>
</x-app-layout>
