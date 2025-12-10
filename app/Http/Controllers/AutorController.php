<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use Illuminate\Http\Request;

class AutorController extends Controller
{
    // Controller methods will be defined here
    
    public function index(Request $request)
    {
        $query = Autor::query();

        // Pesquisa por nome
        if ($search = $request->input('search')) {
            $query->where('nome', 'like', "%{$search}%");
        }

        // Ordenação
        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'nome';
        }

        $query->orderBy($sort, $direction);

        $autores = $query->paginate(10)->withQueryString();

        return view('autores.index', compact('autores', 'sort', 'direction'));
        
    }
    

}
