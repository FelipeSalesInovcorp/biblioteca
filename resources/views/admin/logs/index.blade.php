<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-center text-base-content leading-tight text-blue-800">
            💻 Logs do Sistema
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow sm:rounded-lg p-6">

                <form method="GET" class="flex flex-col md:flex-row gap-3 mb-6">
                    <div class="flex-1">
                        <label class="label"><span class="label-text">Módulo</span></label>
                        <select name="module" class="select select-bordered w-full">
                            <option value="">Todos</option>
                            @foreach($modules as $m)
                                <option value="{{ $m }}" @selected(request('module') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex-1">
                        <label class="label"><span class="label-text">Utilizador (nome/email)</span></label>
                        <input type="text" name="user" value="{{ request('user') }}" class="input input-bordered w-full" placeholder="Ex: Gomes ou email@...">
                    </div>

                    <div class="flex items-end gap-2">
                        <button class="btn btn-primary">Filtrar</button>
                        <a href="{{ route('admin.logs.index') }}" class="btn btn-ghost">Limpar</a>
                    </div>
                </form>

                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Hora</th>
                                <th>User</th>
                                <th>Módulo</th>
                                <th>ID</th>
                                <th>Alteração</th>
                                <th>IP</th>
                                <th>Browser</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d/m/Y') }}</td>
                                    <td>{{ $log->created_at->format('H:i:s') }}</td>
                                    <td>
                                        @if($log->user)
                                            <div class="font-medium">{{ $log->user->name }}</div>
                                            <div class="text-xs opacity-70">{{ $log->user->email }}</div>
                                        @else
                                            <span class="badge badge-ghost">Sistema</span>
                                        @endif
                                    </td>
                                    <td><span class="badge badge-outline">{{ $log->module }}</span></td>
                                    <td>{{ $log->object_id ?? '-' }}</td>
                                    <td class="max-w-md whitespace-normal">{{ $log->change }}</td>
                                    <td>{{ $log->ip ?? '-' }}</td>
                                    <td class="max-w-md truncate" title="{{ $log->user_agent }}">
                                        {{ $log->user_agent ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center opacity-70">Sem logs.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
