<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Log extends Model
{
    use HasFactory;

    protected $table = 'logs';

    protected $fillable = [
        'user_id',
        'module',
        'object_id',
        'change',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'user_id'   => 'integer',
        'object_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
