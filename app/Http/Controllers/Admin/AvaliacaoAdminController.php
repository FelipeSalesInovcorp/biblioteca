<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AvaliacaoDecisaoParaCidadao;
use App\Models\Avaliacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class AvaliacaoAdminController extends Controller
{
    public function index(Request $request)
    {
        $estado = $request->query('estado', Avaliacao::ESTADO_SUSPENSA);

        $avaliacoes = Avaliacao::with(['livro','user','requisicao'])
            ->where('estado', $estado)
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.avaliacoes.index', compact('avaliacoes', 'estado'));
    }

    public function show(Avaliacao $avaliacao)
    {
        $avaliacao->loadMissing(['livro','user','requisicao']);
        return view('admin.avaliacoes.show', compact('avaliacao'));
    }

    public function aprovar(Avaliacao $avaliacao)
    {
        $avaliacao->update([
            'estado' => Avaliacao::ESTADO_ATIVA,
            'motivo_recusa' => null,
        ]);

        Mail::to($avaliacao->user->email)->queue(new AvaliacaoDecisaoParaCidadao($avaliacao));

        return back()->with('success', 'Avaliação aprovada e publicada.');
    }

    public function recusar(Request $request, Avaliacao $avaliacao)
    {
        $data = $request->validate([
            'motivo_recusa' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $avaliacao->update([
            'estado' => Avaliacao::ESTADO_RECUSADA,
            'motivo_recusa' => $data['motivo_recusa'],
        ]);

        Mail::to($avaliacao->user->email)->queue(new AvaliacaoDecisaoParaCidadao($avaliacao));

        return back()->with('success', 'Avaliação recusada. O cidadão foi notificado.');
    }
}
