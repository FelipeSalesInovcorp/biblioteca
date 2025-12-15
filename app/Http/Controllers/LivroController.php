<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Editora;
use App\Models\Autor;
use Illuminate\Http\Request;
use App\Http\Requests\LivroStoreRequest;
use App\Http\Requests\LivroUpdateRequest;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LivroController extends Controller
{
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
        $query = Livro::with(['editora', 'autores']);

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

    /*public function store(Request $request)
    {
        $data = $request->validate([
            'isbn'        => ['required', 'string', 'max:255', 'unique:livros,isbn'],
            'nome'        => ['required', 'string', 'max:255'],
            'editora_id'  => ['required', 'exists:editoras,id'],
            'bibliografia'=> ['nullable', 'string'],
            'imagem_capa' => ['nullable', 'image', 'max:2048'],
            'preco'       => ['required', 'numeric', 'min:0'],
            'autores'     => ['nullable', 'array'],
            'autores.*'   => ['exists:autores,id'],
        ]);

        // Upload da capa
        if ($request->hasFile('imagem_capa')) {
            $data['imagem_capa'] = $request->file('imagem_capa')
                                        ->store('livros', 'public');
        }

        // Retirar autores do array para criar o livro
        $autores = $data['autores'] ?? [];
        unset($data['autores']);

        $livro = Livro::create($data);

        if (!empty($autores)) {
            $livro->autores()->sync($autores);
        }

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro criado com sucesso!');

        
    }*/
    public function store(LivroStoreRequest $request)
    {
        $data = $request->validated();

        // Upload da capa
        if ($request->hasFile('imagem_capa')) {
            $data['imagem_capa'] = $request->file('imagem_capa')
                                        ->store('livros', 'public');
        }

        // Retirar autores do array para criar o livro
        $autores = $data['autores'] ?? [];
        unset($data['autores']);

        $livro = Livro::create($data);

        if (!empty($autores)) {
            $livro->autores()->sync($autores);
        }

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro criado com sucesso!');
    }

    public function edit(Livro $livro)
    {
        $editoras = Editora::orderBy('nome')->get();
        $autores  = Autor::orderBy('nome')->get();
        $autoresSelecionados = $livro->autores()->pluck('autores.id')->toArray();

        return view('livros.edit', compact('livro', 'editoras', 'autores', 'autoresSelecionados'));
    }

    /*public function update(Request $request, Livro $livro)
    {
        $data = $request->validate([
            'isbn'        => ['required', 'string', 'max:255', 'unique:livros,isbn,'.$livro->id],
            'nome'        => ['required', 'string', 'max:255'],
            'editora_id'  => ['required', 'exists:editoras,id'],
            'bibliografia'=> ['nullable', 'string'],
            'imagem_capa' => ['nullable', 'image', 'max:2048'],
            'preco'       => ['required', 'numeric', 'min:0'],
            'autores'     => ['nullable', 'array'],
            'autores.*'   => ['exists:autores,id'],
        ]);

        // Upload da nova capa (se enviada)
        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }

            $data['imagem_capa'] = $request->file('imagem_capa')
                                        ->store('livros', 'public');
        }

        $autores = $data['autores'] ?? [];
        unset($data['autores']);

        $livro->update($data);

        // Atualizar relação N:N
        $livro->autores()->sync($autores);

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }*/

    public function update(LivroUpdateRequest $request, Livro $livro)
    {
        $data = $request->validated();

        // Upload da nova capa (se enviada)
        if ($request->hasFile('imagem_capa')) {
            if ($livro->imagem_capa) {
                Storage::disk('public')->delete($livro->imagem_capa);
            }

            $data['imagem_capa'] = $request->file('imagem_capa')
                                        ->store('livros', 'public');
        }

        $autores = $data['autores'] ?? [];
        unset($data['autores']);

        $livro->update($data);

        // Atualizar relação N:N
        $livro->autores()->sync($autores);

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro atualizado com sucesso!');
    }

    public function destroy(Livro $livro)
    {
        if ($livro->imagem_capa) {
            Storage::disk('public')->delete($livro->imagem_capa);
        }

        $livro->autores()->detach();
        $livro->delete();

        return redirect()
            ->route('livros.index')
            ->with('success', 'Livro removido com sucesso!');
    }
    

    public function exportCsv(): StreamedResponse
    {
        $fileName = 'livros.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$fileName.'"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // BOM para Excel reconhecer UTF-8
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Cabeçalhos
            fputcsv($handle, ['ISBN', 'Nome', 'Editora', 'Autores', 'Preço'], ';');

            Livro::with(['editora', 'autores'])
                ->orderBy('nome')
                ->chunk(200, function ($livros) use ($handle) {
                    foreach ($livros as $livro) {
                        $autores = $livro->autores->pluck('nome')->join(', ');

                        fputcsv($handle, [
                            $livro->isbn,
                            $livro->nome,
                            optional($livro->editora)->nome,
                            $autores,
                            number_format($livro->preco, 2, ',', ''),
                        ], ';');
                    }
                });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }


}
