<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold">Nova Editora</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto bg-base-100 p-6 rounded-box shadow">
            <form method="POST" action="{{ route('editoras.store') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Nome</span>
                    </label>
                    <input type="text" name="nome" value="{{ old('nome') }}"
                           class="input input-bordered w-full" required>
                    @error('nome')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-control">
                    <label class="label">
                        <span class="label-text">Logótipo</span>
                    </label>
                    <input type="file" name="logotipo" class="file-input file-input-bordered w-full max-w-xs">
                    @error('logotipo')
                        <span class="text-error text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex justify-end gap-2">
                    <a href="{{ route('editoras.index') }}" class="btn btn-ghost">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Guardar
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
