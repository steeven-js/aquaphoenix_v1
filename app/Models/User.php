<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

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

    /**
     * Détermine si l'utilisateur peut accéder au panel Filament.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Pour l'instant, tous les utilisateurs connectés peuvent accéder
        // Vous pouvez ajouter des restrictions ici plus tard
        return true;

        // Exemples de restrictions possibles :
        // return $this->email === 'admin@aquaphoenix.fr';
        // return $this->hasRole('admin');
        // return str_ends_with($this->email, '@aquaphoenix.fr');
    }
}
