<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Autor extends Model
{
    //
    use HasFactory;

    protected $table = 'autores';

    protected $fillable = [
        'nome',
        'foto',
    ];

    // cifrar foto (não precisamos pesquisar por isso)
    protected $casts = [
        'foto' => 'encrypted',
    ];

    // Relação com livros
    public function livros()
    {
        return $this->belongsToMany(Livro::class, 'autor_livro');
    }
    
}