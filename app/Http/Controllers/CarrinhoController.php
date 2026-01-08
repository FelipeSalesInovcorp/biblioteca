<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Actions\Carrinho\AdicionarLivroAoCarrinho;
use App\Actions\Carrinho\ObterCarrinhoAtivo;
use App\Actions\Carrinho\RemoverItemDoCarrinho;
use App\Models\CarrinhoItem;

class CarrinhoController extends Controller
{
    //
    public function index(Request $request)
    {
        try {
            $carrinho = app(ObterCarrinhoAtivo::class)->execute($request->user());

            $items = $carrinho?->items ?? collect();

            // Total (com subtotal por item)
            $total = $items->sum(fn ($item) => $item->preco_unitario * $item->quantidade);

            return view('carrinho.index', [
                'carrinho' => $carrinho,
                'items' => $items,
                'total' => $total,
            ]);
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível aceder ao carrinho.';
            return redirect()->route('livros.index')->with('error', $msg);
        }
    }

    public function removeItem(Request $request, CarrinhoItem $item)
    {
        try {
            app(RemoverItemDoCarrinho::class)->execute($request->user(), $item);
            return back()->with('success', 'Livro removido do carrinho.');
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível remover o item.';
            return back()->with('error', $msg);
        }
    }

    public function add(Request $request, int $livro)
    {
        $user = $request->user(); // auth middleware garante que existe

        try {
            app(AdicionarLivroAoCarrinho::class)->execute($user, $livro);
            return back()->with('success', 'Livro adicionado ao carrinho com sucesso!');
        } catch (ValidationException $e) {
            // Mostra a primeira mensagem de validação como feedback
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível adicionar ao carrinho.';
            return back()->with('error', $msg);
        }
    }

}