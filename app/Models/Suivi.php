<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suivi extends Model
{
    use HasFactory;

    protected $fillable = [
        'bagage_id',
        'user_id',
        'localisation',
        'statut',
        'commentaire',
    ];

    public function bagage(): BelongsTo
    {
        return $this->belongsTo(Bagage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Couleur timeline selon statut */
    public function getCouleurAttribute(): string
    {
        return match ($this->statut) {
            'enregistre' => '#2b7a44',
            'embarque'   => '#1a6bbf',
            default      => '#7a90a8',
        };
    }
}
