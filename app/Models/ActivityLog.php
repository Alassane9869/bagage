<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'detail',
        'ip_address',
    ];

    const ACTIONS = [
        'Connexion'    => 'success',
        'Bagage'       => 'warning',
        'Modification' => 'info',
        'Suppression'  => 'danger',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBadgeColorAttribute(): string
    {
        return self::ACTIONS[$this->action] ?? 'secondary';
    }
}
