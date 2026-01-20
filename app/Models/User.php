<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isCidadao(): bool
    {
        return $this->role === 'cidadao';
    }
    
    public function requisicoes()
    {
        return $this->hasMany(\App\Models\Requisicao::class);
    }

    public function avaliacoes()
    {
        return $this->hasMany(\App\Models\Avaliacao::class);
    }

    // Relationships with Conversation

    //  todas as conversas das quais o usuário participa.
    public function isActive(): bool
    {
        return ($this->status ?? 'active') === 'active';
    }
    
    // todas as conversas das quais o usuário participa.
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class)
            ->withPivot(['role', 'joined_at', 'last_read_at']);
    }

    // todas as mensagens enviadas pelo usuário.
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // todas as salas criadas pelo usuário.
    public function roomsCreated(): HasMany
    {
        return $this->hasMany(Room::class, 'created_by');
    }

}