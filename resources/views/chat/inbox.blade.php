<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-base-content leading-tight text-blue-800">
            💬 Chat — Inbox
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto space-y-4">

            <div class="bg-base-100 shadow rounded-box p-4">
                <h3 class="font-semibold mb-3">Utilizadores</h3>

                <ul class="list-disc ml-6 text-sm space-y-2">
                    @foreach($users as $u)
                        <li class="flex items-center gap-3">
                            <span>{{ $u->name }} ({{ $u->email }})</span>

                            <form method="POST" action="{{ route('chat.dm.store', $u) }}">
                                @csrf
                                <button class="text-blue-600 underline" type="submit">
                                    Abrir DM
                                </button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</x-app-layout>
