<?php

namespace App\Http\Controllers;

use App\Actions\Requisicoes\CreateRequisicao;
use App\Http\Requests\RequisicaoStoreRequest;
use App\Models\Livro;
use App\Models\Requisicao;

class RequisicaoController extends Controller
{
    public function index()
    {
        $this->authorize('viewAny', Requisicao::class);

        $query = Requisicao::with('livro')->latest();

        if (auth()->user()->isCidadao()) {
            $query->where('user_id', auth()->id());
        }

        $requisicoes = $query->paginate(10);

        return view('requisicoes.index', compact('requisicoes'));
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
}
