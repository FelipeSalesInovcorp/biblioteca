<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivroAlerta extends Model
{
    protected $table = 'livro_alertas';

    protected $fillable = [
        'livro_id',
        'user_id',
        'notificado_em',
    ];

    protected $casts = [
        'notificado_em' => 'datetime',
    ];

    public function livro()
    {
        return $this->belongsTo(Livro::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

