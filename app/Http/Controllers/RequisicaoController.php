<?php

namespace App\Http\Controllers;

use App\Actions\Requisicoes\CreateRequisicao;
use App\Http\Requests\RequisicaoStoreRequest;
use App\Models\Livro;
use App\Models\Requisicao;
use App\Actions\Requisicoes\ConfirmEntregaRequisicao;
use Illuminate\Http\Request;


class RequisicaoController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Requisicao::class);

        $user = auth()->user();

       // Base query (admin vê tudo, cidadão vê só as suas)
        $baseQuery = Requisicao::query();

        if ($user->isCidadao()) {
        $baseQuery->where('user_id', $user->id);
    }

    // Indicadores
    $ativasCount = (clone $baseQuery)->whereNull('data_entrega_real')->count();

    $ultimos30DiasCount = (clone $baseQuery)
        ->whereDate('data_requisicao', '>=', now()->subDays(30)->toDateString())
        ->count();

    $entreguesHojeCount = (clone $baseQuery)
        ->whereDate('data_entrega_real', now()->toDateString())
        ->count();

    // Lista
    $requisicoes = (clone $baseQuery)
        ->with('livro')
        ->latest()
        ->paginate(10);

    return view('requisicoes.index', compact(
        'requisicoes',
        'ativasCount',
        'ultimos30DiasCount',
        'entreguesHojeCount'
    ));


    }

    public function create()
    {
        $this->authorize('create', Requisicao::class);

        $livrosDisponiveis = Livro::whereDoesntHave('requisicoes', function ($q) {
            $q->whereNull('data_entrega_real');
        })->orderBy('nome')->get();

        return view('requisicoes.create', compact('livrosDisponiveis'));
    }

    public function store(RequisicaoStoreRequest $request, CreateRequisicao $action)
    {
        $this->authorize('create', Requisicao::class);

        $action->execute(auth()->id(), (int) $request->validated()['livro_id']);

        return redirect()->route('requisicoes.index')->with('success', 'Requisição criada com sucesso!');
    }

    public function confirmEntrega(Requisicao $requisicao, ConfirmEntregaRequisicao $action)
    {
        $this->authorize('confirmEntrega', $requisicao);

        $action->execute($requisicao);

        return redirect()->route('requisicoes.index')->with('success', 'Entrega confirmada com sucesso!');
    }

    // Minhas requisições
    public function minhas(Request $request)
    {
        $this->authorize('viewAny', Requisicao::class);

        $status = $request->query('status', 'ativas'); // default

        $query = Requisicao::with('livro')
            ->where('user_id', auth()->id())
            ->orderByRaw('data_entrega_real IS NULL DESC')
            ->orderByDesc('data_requisicao');

        if ($status === 'ativas') {
            $query->whereNull('data_entrega_real');
        }
        elseif ($status === 'entregues') {
            $query->whereNotNull('data_entrega_real');
        } // 'todas' não filtra

        $requisicoes = $query->paginate(10)->withQueryString();

        return view('requisicoes.minhas', compact('requisicoes', 'status'));
}


}
