<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\ListarEncomendas;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EncomendaAdminController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (($user->role ?? null) !== 'admin') {
            abort(403);
        }

        $estado = $request->query('estado'); // 'pendente' | 'paga' | null

        try {
            $result = app(ListarEncomendas::class)->execute($estado);

            return view('admin.encomendas.index', [
                'encomendas' => $result['encomendas'],
                'estado' => $result['estado'],
            ]);
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?? 'Não foi possível carregar encomendas.';
            return back()->with('error', $msg);
        }
    }
}

