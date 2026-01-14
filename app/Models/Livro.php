<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Livro extends Model
{
    //
    use HasFactory;

    protected $table = 'livros';

    protected $fillable = [
        'isbn',
        'nome',
        'editora_id',
        'bibliografia',
        'imagem_capa',
        'preco',
        'stock',
    ];

    // cifrar imagem_capa (não precisamos pesquisar por isso)
    protected $casts = [
        'bibliografia' => 'encrypted',
        'imagem_capa' => 'encrypted',
        'preco'        => 'decimal:2',
        'stock'        => 'integer',
    ];
    
    // Relação com editora
    public function editora()
    {
        return $this->belongsTo(Editora::class);
    }

    // Relação com autores
    public function autores()
    {
        return $this->belongsToMany(Autor::class, 'autor_livro');
    }

    // Preço formatado (para usar na view)
    public function getPrecoFormatadoAttribute(): string
    {
        return number_format((float) $this->preco, 2, ',', '.') . ' €';
    }

    // Preço formatado sem símbolo (útil para CSV ou inputs)
    public function getPrecoFormatadoSemSimboloAttribute(): string
    {
        return number_format((float) $this->preco, 2, ',', '.');
    }

    public function requisicoes()
    {
        return $this->hasMany(\App\Models\Requisicao::class);
    }

    public function estaDisponivel(): bool
    {
        //return ! $this->requisicoes()->whereNull('data_entrega_real')->exists();

        // Se stock for 0 (ou null por algum motivo), não está disponível
        if (($this->stock ?? 0) <= 0) {
            return false;
        }

        $ativas = $this->requisicoes()
            ->whereNull('data_entrega_real')
            ->count();

        return $ativas < $this->stock;

    }

    public function avaliacoes()
    {
        return $this->hasMany(Avaliacao::class);
    }
}