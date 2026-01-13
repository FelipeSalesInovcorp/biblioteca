<?php

namespace App\Http\Controllers;

use App\Actions\Checkout\CriarEncomendaPendente;
use App\Models\Encomenda;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function moradaForm(Request $request)
    {
        // Só mostra o formulário
        return view('checkout.morada');
    }

    public function moradaSubmit(Request $request)
    {
        // Validação simples (podes evoluir para FormRequest depois)
        $dados = $request->validate([
            'nome_entrega' => ['required', 'string', 'max:255'],
            'morada' => ['required', 'string', 'max:255'],
            'codigo_postal' => ['required', 'string', 'max:20'],
            'localidade' => ['required', 'string', 'max:100'],
        ]);

        try {
            $encomenda = app(CriarEncomendaPendente::class)->execute($request->user(), $dados);

            // Guardar a encomenda em sessão para a página de confirmação
            session(['checkout_encomenda_id' => $encomenda->id]);

            return redirect()->route('checkout.confirmacao');
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível avançar no checkout.';
            return redirect()->route('carrinho.index')->with('error', $msg);
        }
    }

    public function confirmacao(Request $request)
    {
        $id = session('checkout_encomenda_id');

        if (!$id) {
            return redirect()->route('carrinho.index')->with('error', 'Não existe checkout em curso.');
        }

        $encomenda = Encomenda::with(['items.livro'])
            ->where('id', $id)
            ->where('user_id', $request->user()->id)
            ->first();

        if (!$encomenda) {
            return redirect()->route('carrinho.index')->with('error', 'Encomenda não encontrada.');
        }

        return view('checkout.confirmacao', [
            'encomenda' => $encomenda,
        ]);
    }

    public function sucesso(Encomenda $encomenda)
    {
        // segurança: só o dono ou admin pode ver
        abort_unless(auth()->id() === $encomenda->user_id || auth()->user()->isAdmin(), 403);

        return view('checkout.sucesso', compact('encomenda'));
    }

}
