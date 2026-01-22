<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-blue-800 leading-tight">
            ➕ Criar Sala
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-base-100 shadow rounded-box p-6">

                @if ($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 rounded">
                    <ul class="text-sm text-red-700 list-disc ml-6">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form method="POST" action="{{ route('chat.rooms.store') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium mb-1">Nome</label>
                        <input name="name" value="{{ old('name') }}" class="w-full border rounded p-2" required>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Referência (opcional)
                            <span class="text-gray-500 font-normal">— se vazio, será gerada automaticamente</span>
                        </label>
                        <input name="reference" value="{{ old('reference') }}" class="w-full border rounded p-2" placeholder="ex: geral">
                        <p class="text-xs text-gray-500 mt-1">Apenas letras, números, hífen e underscore.</p>
                    </div>

                    <div class="flex items-center gap-3">
                        <button class="px-4 py-2 bg-blue-600 text-white rounded" type="submit">
                            Criar
                        </button>

                        <a class="text-sm underline" href="{{ route('chat.inbox') }}">Voltar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>