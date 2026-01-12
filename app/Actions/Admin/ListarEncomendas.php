<?php

namespace App\Actions\Admin;

use App\Models\Encomenda;
use Illuminate\Validation\ValidationException;

class ListarEncomendas
{
    /**
     * @param string|null $estado 'pendente' | 'paga' | null
     */
    public function execute(?string $estado): array
    {
        if ($estado !== null && !in_array($estado, ['pendente', 'paga'], true)) {
            throw ValidationException::withMessages([
                'estado' => 'Filtro inválido.',
            ]);
        }

        $q = Encomenda::query()
            ->with(['user'])
            ->orderByDesc('created_at');

        if ($estado) {
            $q->where('estado', $estado);
        }

        $perPage = (int) request()->query('per_page', 15);
        $perPage = in_array($perPage, [6, 10, 15, 18], true) ? $perPage : 15;

        return [
            'estado' => $estado,
            'encomendas' => $q->paginate($perPage)->withQueryString(),
        ];
    }
}
