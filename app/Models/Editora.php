<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Editora extends Model
{
    // 
    use HasFactory;

    protected $table = 'editoras';

    protected $fillable = [
        'nome',
        'logotipo',
    ];

    // cifrar logotipo (não precisamos pesquisar por isso)
    protected $casts = [
        'logotipo' => 'encrypted',
    ];

    // Relação com livros
    public function livros()
    {
        return $this->hasMany(Livro::class, 'editora_id');
    }

}
