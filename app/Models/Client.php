<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'adresse',
        'numero_piece',
    ];

    public function bagages(): HasMany
    {
        return $this->hasMany(Bagage::class);
    }

    public function getNomCompletAttribute(): string
    {
        return trim(strtoupper(($this->prenom ?? '') . ' ' . ($this->nom ?? ''))) ?: '—';
    }
}
