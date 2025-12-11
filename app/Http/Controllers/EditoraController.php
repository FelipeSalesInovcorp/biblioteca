<?php

namespace App\Http\Controllers;

use App\Models\Editora;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EditoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Editora::query();

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

        $editoras = $query->paginate(6)->withQueryString();

        return view('editoras.index', compact('editoras', 'sort', 'direction'));
    }

    public function create()
    {
        return view('editoras.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'logotipo' => ['nullable', 'image', 'max:2048'], // 2MB
        ]);

        if ($request->hasFile('logotipo')) {
            $data['logotipo'] = $request->file('logotipo')
                ->store('editoras', 'public');
        }

        Editora::create($data);

        return redirect()
            ->route('editoras.index')
            ->with('success', 'Editora criada com sucesso!');
    }

    public function edit(Editora $editora)
    {
        return view('editoras.edit', compact('editora'));
    }

    public function update(Request $request, Editora $editora)
    {
        $data = $request->validate([
            'nome'     => ['required', 'string', 'max:255'],
            'logotipo' => ['nullable', 'image', 'max:2048'],
        ]);

        if ($request->hasFile('logotipo')) {
            // apaga antigo se existir
            if ($editora->logotipo) {
                Storage::disk('public')->delete($editora->logotipo);
            }

            $data['logotipo'] = $request->file('logotipo')
                ->store('editoras', 'public');
        }

        $editora->update($data);

        return redirect()
            ->route('editoras.index')
            ->with('success', 'Editora atualizada com sucesso!');
    }

    public function destroy(Editora $editora)
    {
        if ($editora->logotipo) {
            Storage::disk('public')->delete($editora->logotipo);
        }

        $editora->delete();

        return redirect()
            ->route('editoras.index')
            ->with('success', 'Editora removida com sucesso!');
    }
}

