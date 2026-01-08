<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EncomendaItem extends Model
{
    //

    use HasFactory;

    protected $fillable = [
        'encomenda_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
        'subtotal',
    ];
    
    // Definição dos casts
    protected $casts = [
        'preco_unitario' => 'decimal:2',
        'subtotal'       => 'decimal:2',
    ];


    // Relação: Item pertence a uma encomenda
    public function encomenda()
    {
        return $this->belongsTo(Encomenda::class);
    }
    
    // Relação: Item pertence a um livro
    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function subtotalCalculado(): float
    {
        return (float) ($this->preco_unitario * $this->quantidade);
    }

}
