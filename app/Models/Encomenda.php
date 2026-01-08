<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Encomenda extends Model
{
    //
    use HasFactory;

    // Estados da encomenda
    public const ESTADO_PENDENTE  = 'pendente';
    public const ESTADO_PAGA      = 'paga';
    public const ESTADO_CANCELADA = 'cancelada';

    protected $fillable = [
        'user_id',
        'estado',

        // Morada de entrega (PT-PT)
        'nome_entrega',
        'morada',
        'codigo_postal',
        'localidade',

        'total',

        // Stripe
        'stripe_session_id',

        // Data de pagamento
        'pago_em',
    ];

    protected $casts = [
        'total'   => 'decimal:2',
        'pago_em' => 'datetime',
    ];

    // Relação: Encomenda pertence a um utilizador
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relação: Encomenda tem vários itens
    public function items()
    {
        return $this->hasMany(EncomendaItem::class);
    }

    // Scope: encomendas pendentes
    public function scopePendente($query)
    {
        return $query->where('estado', self::ESTADO_PENDENTE);
    }

    // Scope: encomendas pagas
    public function scopePaga($query)
    {
        return $query->where('estado', self::ESTADO_PAGA);
    }
}
