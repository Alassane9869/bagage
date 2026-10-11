<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'prenom',
        'nom',
        'email',
        'login',
        'telephone',
        'password',
        'role_id',
        'agence',
        'statut',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relations ──────────────────────────────────────────────
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function bagages(): HasMany
    {
        return $this->hasMany(Bagage::class);
    }

    public function suivis(): HasMany
    {
        return $this->hasMany(Suivi::class);
    }

    public function caisses(): HasMany
    {
        return $this->hasMany(Caisse::class);
    }

    public function loginHistories(): HasMany
    {
        return $this->hasMany(LoginHistory::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    /** Retourne le nom complet affiché (PRENOM NOM) */
    public function getNomCompletAttribute(): string
    {
        return trim(strtoupper($this->prenom . ' ' . $this->nom))
            ?: $this->name;
    }

    /** Initiale pour l'avatar */
    public function getInitialeAttribute(): string
    {
        return strtoupper(substr($this->prenom ?: $this->name, 0, 1));
    }

    public function hasRole(string $slug): bool
    {
        return $this->role?->slug === $slug;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole(Role::ADMIN);
    }

    public function canAccessAdmin(): bool
    {
        return $this->role?->slug === Role::ADMIN;
    }
}
