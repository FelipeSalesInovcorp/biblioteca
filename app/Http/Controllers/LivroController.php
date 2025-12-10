<?php

namespace App\Http\Controllers;

use App\Models\Livro;
use App\Models\Autor;
use App\Models\Editora;
use Illuminate\Http\Request;

class LivroController extends Controller
{
    // Controller methods will be defined here

    public function index(Request $request)
    {
        $query = Livro::with(['editora', 'autores']);

        // pesquisa por nome ou ISBN
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                ->orWhere('isbn', 'like', "%{$search}%");
            });
        }
        
        // filtro por editora
        if ($editoraId = $request->input('editora_id')) {
            $query->where('editora_id', $editoraId);
        }

        // ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'isbn', 'preco', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'nome';
        }

        $query->orderBy($sort, $direction);

        $livros = $query->paginate(10)->withQueryString();
        $editoras = Editora::orderBy('nome')->get();

        return view('livros.index', compact('livros', 'editoras', 'sort', 'direction'));


        //$livros = $query->paginate(10);

    }
    
}
