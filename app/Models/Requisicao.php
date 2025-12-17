<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requisicao extends Model
{
    //

    protected $table = 'requisicoes';

    protected $fillable = [
        'numero_sequencial',
        'user_id',
        'livro_id',
        'data_requisicao',
        'data_prevista_fim',
        'data_entrega_real',
    ];

    protected $casts = [
        'data_requisicao' => 'date',
        'data_prevista_fim' => 'date',
        'data_entrega_real' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function scopeAtivas($query)
    {
        return $query->whereNull('data_entrega_real');
    }
    
}
