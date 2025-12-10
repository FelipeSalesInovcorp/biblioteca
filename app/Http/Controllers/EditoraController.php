<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Http\Request;

class EditoraController extends Controller
{
    // Controller methods will be defined here

    public function index(Request $request)
    {
        $query = Editora::query();

        // Pesquisa por nome
        if ($search = $request->input('search')) {
            $query->where('nome', 'like', "%{$search}%");
        }

        // ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'nome';
        }

        $query->orderBy($sort, $direction);

        $editoras = $query->paginate(10)->withQueryString();

        return view('editoras.index', compact('editoras', 'sort', 'direction'));
    }
}
