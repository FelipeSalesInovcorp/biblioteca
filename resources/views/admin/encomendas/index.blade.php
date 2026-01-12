<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-blue-800">🧾 Admin — Encomendas</h2>

            <div class="flex gap-2">
                <a href="{{ route('admin.encomendas.index') }}"
                    class="btn btn-sm {{ is_null($estado) ? 'btn-primary' : 'btn-ghost' }}">
                    Todas
                </a>

                <a href="{{ route('admin.encomendas.index', ['estado' => 'pendente']) }}"
                    class="btn btn-sm {{ $estado === 'pendente' ? 'btn-primary' : 'btn-ghost' }}">
                    Pendentes
                </a>

                <a href="{{ route('admin.encomendas.index', ['estado' => 'paga']) }}"
                    class="btn btn-sm {{ $estado === 'paga' ? 'btn-primary' : 'btn-ghost' }}">
                    Pagas
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8 max-w-6xl mx-auto space-y-4">

        @if (session('success'))
        <div class="alert alert-success shadow">
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if (session('info'))
        <div class="alert alert-info shadow">
            <span>{{ session('info') }}</span>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-error shadow">
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <div class="card bg-base-100 shadow">
            <div class="card-body">
                @if($encomendas->isEmpty())
                <p class="text-base-content/70">Sem encomendas para mostrar.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="table w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Cidadão</th>
                                <th>Estado</th>
                                <th>Total</th>
                                <th>Nome entrega</th>
                                <th>Morada</th>
                                <!--<th>Stripe Session</th>-->
                                <th>Pago em</th>
                                <th>Criada em</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($encomendas as $e)
                            <tr>
                                <td>{{ $e->id }}</td>
                                <td>
                                    <div class="font-semibold">{{ $e->user?->name ?? '—' }}</div>
                                    <div class="text-xs text-base-content/60">{{ $e->user?->email ?? '' }}</div>
                                </td>

                                <td>
                                    @if($e->estado === 'paga')
                                    <span class="badge badge-success">paga</span>
                                    @elseif($e->estado === 'pendente')
                                    <span class="badge badge-warning">pendente</span>
                                    @else
                                    <span class="badge badge-ghost">{{ $e->estado }}</span>
                                    @endif
                                </td>

                                <td>{{ number_format((float)$e->total, 2, ',', '.') }} €</td>

                                <!--<td class="text-sm">
                                    <div>{{ $e->nome_entrega }}</div>
                                    <div class="text-base-content/60">{{ $e->morada }}</div>
                                    <div class="text-base-content/60">
                                        {{ $e->codigo_postal }} — {{ $e->localidade }}
                                    </div>
                                </td>-->

                                <td class="text-sm font-medium">
                                    {{ $e->nome_entrega }}
                                </td>

                                <td class="text-sm">
                                    <div class="text-base-content/60">{{ $e->morada }}</div>
                                    <div class="text-base-content/60">
                                        {{ $e->codigo_postal }} — {{ $e->localidade }}
                                    </div>
                                </td>

                                <!--<td class="text-xs">
                                    {{ $e->stripe_session_id ?? '—' }}
                                </td>-->

                                <td class="text-sm">
                                    {{ $e->pago_em ? $e->pago_em->format('d/m/Y H:i') : '—' }}
                                </td>

                                <td class="text-sm">
                                    {{ $e->created_at?->format('d/m/Y H:i') ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
            <!--Tabela - footer-->

                <div class="mt-3 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    {{-- Esquerda: info + links --}}
                    <div class="space-y-2">
                        <div class="text-sm text-gray-500">
                            A mostrar
                            <span class="font-medium">{{ $encomendas->firstItem() }}</span>
                            –
                            <span class="font-medium">{{ $encomendas->lastItem() }}</span>
                            de
                            <span class="font-medium">{{ $encomendas->total() }}</span>
                            encomendas
                        </div>

                        <div>
                            {{ $encomendas->onEachSide(1)->links() }}
                        </div>
                    </div>

                    {{-- Direita: seletor por página --}}
                    <div class="flex justify-end">
                        <form method="GET" action="{{ route('admin.encomendas.index') }}" class="flex items-center gap-2">
                            @if($estado)
                                <input type="hidden" name="estado" value="{{ $estado }}">
                            @endif

                            <label class="text-xm font-bold text-blue-800">Por página:</label>

                            <select name="per_page"
                                    class="select select-bordered select-xs w-24"
                                    onchange="this.form.submit()">
                                @foreach([6,10,15,18] as $n)
                                    <option value="{{ $n }}" {{ (int)request('per_page', 6) === $n ? 'selected' : '' }}>
                                        {{ $n }}
                                    </option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </div>

                @endif


            </div>
        </div>

    </div>
</x-app-layout>
