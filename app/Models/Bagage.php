<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bagage extends Model
{
    use HasFactory;

    protected $fillable = [
        'code_bagage',
        'client_id',
        'voyage_id',
        'user_id',
        'nombre_colis',
        'nature',
        'montant',
        'remise',
        'numero_billet',
        'statut',
        'date_enregistrement',
    ];

    protected $casts = [
        'date_enregistrement' => 'datetime',
        'montant'             => 'decimal:2',
        'remise'              => 'decimal:2',
    ];

    // Statuts disponibles — uniquement enregistre et embarque
    const STATUTS = [
        'enregistre' => ['label' => 'Enregistré', 'color' => 'success'],
        'embarque'   => ['label' => 'Embarqué',   'color' => 'info'],
    ];

    const NATURES = [
        'Valise', 'Sac', 'Carton', 'Colis fragile', 'Marchandise', 'Autre',
    ];

    // ── Relations ──────────────────────────────────────────────
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function voyage(): BelongsTo
    {
        return $this->belongsTo(Voyage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colis(): HasMany
    {
        return $this->hasMany(Colis::class);
    }

    public function suivis(): HasMany
    {
        return $this->hasMany(Suivi::class)->orderBy('created_at', 'desc');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    // ── Helpers ────────────────────────────────────────────────

    public function getNetAttribute(): float
    {
        return (float) $this->montant - (float) $this->remise;
    }

    public function getStatutLabelAttribute(): string
    {
        return self::STATUTS[$this->statut]['label'] ?? $this->statut;
    }

    public function getStatutColorAttribute(): string
    {
        return self::STATUTS[$this->statut]['color'] ?? 'secondary';
    }

    /** Montant formaté FCFA */
    public function getMontantFcfaAttribute(): string
    {
        return number_format((float) $this->montant, 0, ',', ' ') . ' FCFA';
    }

    public function getRemiseFcfaAttribute(): string
    {
        return number_format((float) $this->remise, 0, ',', ' ') . ' FCFA';
    }

    public function getNetFcfaAttribute(): string
    {
        return number_format($this->net, 0, ',', ' ') . ' FCFA';
    }
}
