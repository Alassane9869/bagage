<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Societe extends Model
{
    use HasFactory;

    protected $table = 'societe';

    protected $fillable = [
        'raison_sociale',
        'telephone',
        'email',
        'adresse',
        'ville',
        'pays',
        'site_web',
        'guichet_principal',
        'numero_rccm',
        'numero_ifu',
        'devise',
        'fuseau_horaire',
        'logo_path',
        'mentions_recu',
    ];
}
