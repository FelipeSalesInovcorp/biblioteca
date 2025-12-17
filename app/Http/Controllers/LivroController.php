<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Http\Request;
use App\Http\Requests\LivroStoreRequest;
use App\Http\Requests\LivroUpdateRequest;
use App\Actions\Livros\CreateLivro;
use App\Actions\Livros\UpdateLivro;
use App\Services\Exports\LivrosCsvExporter;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LivroController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Livro::class, 'livro');
    }

    public function index(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        // Pesquisa por nome ou ISBN
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        // Filtro por editora
        if ($editoraId = $request->input('editora_id')) {
            $query->where('editora_id', $editoraId);
        }

        // Ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'isbn', 'preco', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'nome';
        }

        $query->orderBy($sort, $direction);

        $livros = $query->paginate(6)->withQueryString();
        $editoras = Editora::orderBy('nome')->get();

        return view('livros.index', compact('livros', 'editoras', 'sort', 'direction'));
    }

    // Catalogo de livros
    public function catalogo(Request $request)
    {
        $this->authorize('viewAny', Livro::class);

        $query = Livro::with(['editora', 'autores', 'requisicoes']);

        // Pesquisa simples por nome ou ISBN
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
            $q->where('nome', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
        });
    }

        // Filtro por editora
        if ($editoraId = $request->input('editora_id')) {
        $query->where('editora_id', $editoraId);
    }

        // Ordenar por nome para o catálogo
        $livros = $query->orderBy('nome')->paginate(6)->withQueryString();
        $editoras = Editora::orderBy('nome')->get();

        return view('livros.catalogo', compact('livros', 'editoras'));
    }
    // Fim do catalogo de livros


    public function create()
    {
        $editoras = Editora::orderBy('nome')->get();
        $autores  = Autor::orderBy('nome')->get();

        return view('livros.create', compact('editoras', 'autores'));
    }

    // Armazenar novo livro
    public function store(LivroStoreRequest $request, CreateLivro $action)
    {
        $action->execute($request->validated());

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    // Editar livro
    public function edit(Livro $livro)
    {
        $editoras = Editora::orderBy('nome')->get();
        $autores  = Autor::orderBy('nome')->get();
        $autoresSelecionados = $livro->autores()->pluck('autores.id')->toArray();

        return view('livros.edit', compact('livro', 'editoras', 'autores', 'autoresSelecionados'));
    }
    
    // Atualizar livro
    public function update(LivroUpdateRequest $request, Livro $livro, UpdateLivro $action)
    {
        $action->execute($livro, $request->validated());

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }

    // Remover livro
    public function destroy(Livro $livro)
    {
        $this->authorize('delete', $livro); 

        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }

        $livro->autores()->detach();
        $livro->delete();

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro removido com sucesso!');
    }
    
    // Exportar livros para CSV
    public function exportCsv(LivrosCsvExporter $exporter): StreamedResponse
    {
        return $exporter->stream();
    }

    public function show(Livro $livro)
    {
        // carregar relações + histórico de requisições
        $livro->load(['editora', 'autores', 'requisicoes.user']);

       // ordenar histórico (ativas primeiro, depois mais recentes)
        $requisicoes = $livro->requisicoes()
            ->with('user')
            ->orderByRaw('data_entrega_real IS NULL DESC')
            ->orderByDesc('data_requisicao')
            ->get();

        return view('livros.show', compact('livro', 'requisicoes'));
}


}
