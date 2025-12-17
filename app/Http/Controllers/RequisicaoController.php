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

}
