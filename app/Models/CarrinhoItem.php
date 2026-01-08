<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CarrinhoItem extends Model
{
    //
    use HasFactory;

    //Campos que podem ser preenchidos em massa
    protected $fillable = [
        'carrinho_id',
        'livro_id',
        'quantidade',
        'preco_unitario',
    ];

    // Definição dos casts
    protected $casts = [
        'preco_unitario' => 'decimal:2',
    ];

    //Relação: Item pertence a um carrinho
    public function carrinho()
    {
        return $this->belongsTo(Carrinho::class);
    }

    //Relação: Item pertence a um livro
    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }
    
    // Calcula o subtotal do item (preço unitário * quantidade)
    public function subtotal(): float
    {
        return (float) ($this->preco_unitario * $this->quantidade);
    }
}