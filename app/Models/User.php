<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'last_session_id',
    ];

    /**
     * Comprueba si el usuario es el Usuario Principal (Super Admin).
     */
    public function isPrincipal(): bool
    {
        return $this->role === 'principal';
    }

    /**
     * Comprueba si el usuario tiene privilegios administrativos (Admin o Principal).
     */
    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'principal']);
    }

    /**
     * Comprueba si es un usuario común estándar.
     */
    public function isComun(): bool
    {
        return $this->role === 'usuario' || empty($this->role);
    }

    /**
     * Nombre legible del rol.
     */
    public function getNombreRolAttribute(): string
    {
        return match($this->role) {
            'principal' => '👑 Usuario Principal',
            'admin'     => '⭐ Administrador',
            default     => '👤 Usuario Común',
        };
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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
}
