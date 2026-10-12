<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['nom', 'slug', 'permissions'];

    protected $casts = [
        'permissions' => 'array',
    ];

    // Slugs constants — 3 rôles applicatifs uniquement
    const ADMIN      = 'administrateur';
    const AGENT_BAG  = 'agent_bagages';
    const CONTROLEUR = 'controleur';

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
