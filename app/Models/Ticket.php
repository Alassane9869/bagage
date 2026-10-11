<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ticket',
        'bagage_id',
        'client_id',
        'voyage_id',
        'montant',
        'date_emission',
        'qr_code',
    ];

    protected $casts = [
        'date_emission' => 'datetime',
        'montant'       => 'decimal:2',
    ];

    public function bagage(): BelongsTo
    {
        return $this->belongsTo(Bagage::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function voyage(): BelongsTo
    {
        return $this->belongsTo(Voyage::class);
    }
}
