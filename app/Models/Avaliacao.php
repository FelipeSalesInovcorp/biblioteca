<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Avaliacao extends Model
{
    //
    use HasFactory;

    protected $table = 'avaliacoes';

    protected $fillable = [
        'requisicao_id',
        'livro_id',
        'user_id',
        'classificacao',
        'comentario',
        'estado',
        'motivo_recusa',
    ];

    protected $casts = [
        'classificacao' => 'integer',
    ];

    public const ESTADO_SUSPENSA = 'suspensa';
    public const ESTADO_ATIVA = 'ativa';
    public const ESTADO_RECUSADA = 'recusada';

    public function requisicao()
    {
        //return $this->belongsTo(Requisicao::class, 'requisicao_id');
        return $this->belongsTo(Requisicao::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
