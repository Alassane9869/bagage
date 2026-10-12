<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Colis extends Model
{
    use HasFactory;

    protected $fillable = ['bagage_id', 'numero_colis', 'nature', 'valeur', 'prix'];

    protected $casts = [
        'prix' => 'decimal:2',
    ];

    public function bagage(): BelongsTo
    {
        return $this->belongsTo(Bagage::class);
    }
}
