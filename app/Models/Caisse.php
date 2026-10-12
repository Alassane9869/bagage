<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Caisse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date_caisse',
        'total_bagages',
        'recettes',
        'remises',
        'net',
    ];

    protected $casts = [
        'date_caisse' => 'date',
        'recettes'    => 'decimal:2',
        'remises'     => 'decimal:2',
        'net'         => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
