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
        'dias_decorridos',
    ];

    protected $casts = [
        'data_requisicao' => 'date',
        'data_prevista_fim' => 'date',
        'data_entrega_real' => 'date',
        'dias_decorridos' => 'integer',
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
    
    public function avaliacao()
    {
        return $this->hasOne(Avaliacao::class);
    }
}
