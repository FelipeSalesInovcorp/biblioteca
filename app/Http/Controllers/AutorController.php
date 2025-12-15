<?php

namespace App\Http\Controllers;

use App\Models\Autor;
use App\Http\Requests\AutorStoreRequest;
use App\Http\Requests\AutorUpdateRequest;
use App\Actions\Autores\CreateAutor;
use App\Actions\Autores\UpdateAutor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AutorController extends Controller
{
    public function index(Request $request)
    {
        $query = Autor::query();

        if ($search = $request->input('search')) {
            $query->where('nome', 'like', "%{$search}%");
        }

        $sort = $request->input('sort', 'nome');
        $direction = $request->input('direction', 'asc');

        $allowedSorts = ['nome', 'created_at'];
        if (! in_array($sort, $allowedSorts)) {
            $sort = 'nome';
        }

        $query->orderBy($sort, $direction);

        $autores = $query->paginate(6)->withQueryString();

        return view('autores.index', compact('autores', 'sort', 'direction'));
    }

    public function create()
    {
        return view('autores.create');
    }
    
    // Utilizando um método de armazenamento simplificado sem a ação CreateAutor.
    /*public function store(Request $request)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')
                            ->store('autores', 'public');
        }

        Autor::create($data);

        return redirect()
            ->route('autores.index')
            ->with('success', 'Autor criado com sucesso!');
    }*/
    public function store(AutorStoreRequest $request, CreateAutor $action)
    {
        $action->execute($request->validated());

        return redirect()->route('autores.index')
        ->with('success', 'Autor criado com sucesso!');
    }
    
    //
    public function edit(Autor $autore)
    {
        return view('autores.edit', ['autor' => $autore]);
    }

    /*public function update(Request $request, Autor $autore)
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('foto')) {

            // Se tinha uma foto antiga → apaga
            if ($autore->foto) {
                Storage::disk('public')->delete($autore->foto);
            }

            $data['foto'] = $request->file('foto')
                                    ->store('autores', 'public');
        }

        $autore->update($data);

        return redirect()
            ->route('autores.index')
            ->with('success', 'Autor atualizado com sucesso!');
    }*/

    public function update(AutorUpdateRequest $request, Autor $autore, UpdateAutor $action)
    {
        $action->execute($autore, $request->validated());

        return redirect()->route('autores.index')
        ->with('success', 'Autor atualizado com sucesso!');
    }

    public function destroy(Autor $autore)
    {
        if ($autore->foto) {
            Storage::disk('public')->delete($autore->foto);
        }

        $autore->delete();

        return redirect()
            ->route('autores.index')
            ->with('success', 'Autor removido com sucesso!');
    }
}

