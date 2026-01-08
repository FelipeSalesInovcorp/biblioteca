<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\User;
use App\Models\CarrinhoItem;

class Carrinho extends Model
{
    //
    use HasFactory;

    //Campos que podem ser preenchidos em massa
    protected $fillable = [
        'user_id',
        'estado',
    ];

    // Definição dos casts
    protected $casts = [
        'abandoned_notified_at' => 'datetime',
    ];
    
     // Relação: Carrinho pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

     //Relação: Carrinho tem vários itens
    public function items()
    {
        return $this->hasMany(CarrinhoItem::class)->orderBy('created_at');
    }

     // Scope: carrinhos ativos
    public function scopeAtivo($query)
    {
        return $query->where('estado', 'ativo');
    }

}
