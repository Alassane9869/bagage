<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Voyage extends Model
{
    use HasFactory;

    protected $fillable = [
        'ville_depart',
        'ville_arrivee',
        'date_depart',
        'heure_depart',
        'programme',
        'numero_bus',
        'statut_voyage',
    ];

    protected $casts = [
        'date_depart' => 'date',
    ];

    const VILLES = [
        'Bamako', 'Kayes', 'Kenieba', 'Dakar', 'Sikasso', 'Mopti',
        'Koutiala', 'Bobo-Dioulasso', 'Abidjan', 'Sadiola', 'San',
        'Ségou', 'Gao',
    ];

    /**
     * Programmes par ville d'arrivée (destination).
     * Clé = ville_arrivee, valeur = tableau de [heure, programme]
     */
    const PROGRAMMES_PAR_DESTINATION = [
        'Kayes' => [
            ['heure' => '04H', 'programme' => 'BAMAKO-KAYES 04H'],
            ['heure' => '06H', 'programme' => 'BAMAKO-KAYES 06H00'],
            ['heure' => '08H', 'programme' => 'BAMAKO-KAYES 08H'],
            ['heure' => '11H', 'programme' => 'BAMAKO-KAYES 11H00'],
            ['heure' => '12H', 'programme' => 'BAMAKO-KAYES 12H00'],
            ['heure' => '14H', 'programme' => 'BAMAKO-KAYES 14H'],
            ['heure' => '18H', 'programme' => 'BAMAKO-KAYES 18H'],
        ],
        'Kenieba' => [
            ['heure' => '01H', 'programme' => 'BAMAKO-KENIEBA 01H'],
            ['heure' => '03H', 'programme' => 'BAMAKO-KENIEBA 03H'],
            ['heure' => '06H', 'programme' => 'BAMAKO-KENIEBA 06H'],
            ['heure' => '08H', 'programme' => 'BAMAKO-KENIEBA 08H'],
            ['heure' => '09H', 'programme' => 'BAMAKO-KENIEBA 09H'],
            ['heure' => '10H', 'programme' => 'BAMAKO-KENIEBA 10H'],
            ['heure' => '11H', 'programme' => 'BAMAKO-KENIEBA 11H'],
            ['heure' => '13H', 'programme' => 'BAMAKO-KENIEBA 13H'],
            ['heure' => '15H', 'programme' => 'BAMAKO-KENIEBA 15H'],
            ['heure' => '18H', 'programme' => 'BAMAKO-KENIEBA 18H'],
            ['heure' => '20H', 'programme' => 'BAMAKO-KENIEBA 20H'],
            ['heure' => '21H', 'programme' => 'BAMAKO-KENIEBA 21H'],
        ],
        'Dakar' => [
            ['heure' => '06H', 'programme' => 'BAMAKO-DAKAR 06H00'],
        ],
        'Abidjan' => [
            ['heure' => '10H', 'programme' => 'BAMAKO-TENGRELA-ABIDJAN 10H00'],
            ['heure' => '15H', 'programme' => 'BAMAKO-ABIDJAN 15H'],
            ['heure' => '17H', 'programme' => 'BAMAKO-ABIDJAN 17H'],
        ],
        'Sikasso' => [
            ['heure' => '04H', 'programme' => 'BAMAKO-SIKASSO 04H00'],
            ['heure' => '06H', 'programme' => 'BAMAKO-SIKASSO 06H00'],
            ['heure' => '08H', 'programme' => 'BAMAKO-SIKASSO 08H00'],
            ['heure' => '09H', 'programme' => 'BAMAKO-SIKASSO 09H00'],
            ['heure' => '10H', 'programme' => 'BAMAKO-SIKASSO 10H'],
            ['heure' => '12H', 'programme' => 'BAMAKO-SIKASSO 12H00'],
        ],
        'Mopti' => [
            ['heure' => '00H', 'programme' => 'BAMAKO-MOPTI 00H00'],
            ['heure' => '03H', 'programme' => 'BAMAKO-MOPTI 03H00'],
            ['heure' => '04H', 'programme' => 'BAMAKO-MOPTI 04H'],
            ['heure' => '05H', 'programme' => 'BAMAKO-MOPTI 05H00'],
            ['heure' => '06H', 'programme' => 'BAMAKO-MOPTI 06H'],
            ['heure' => '07H', 'programme' => 'BAMAKO-MOPTI 07H'],
            ['heure' => '08H', 'programme' => 'BAMAKO-MOPTI 08H00'],
            ['heure' => '15H', 'programme' => 'BAMAKO-MOPTI 15H'],
            ['heure' => '18H', 'programme' => 'BAMAKO-MOPTI 18H'],
        ],
        'Koutiala' => [
            ['heure' => '05H', 'programme' => 'BAMAKO-KOUTIALA 05H'],
            ['heure' => '06H', 'programme' => 'BAMAKO-KOUTIALA 06H'],
            ['heure' => '08H', 'programme' => 'BAMAKO-KOUTIALA 08H'],
            ['heure' => '09H', 'programme' => 'BAMAKO-KOUTIALA 09H'],
            ['heure' => '10H', 'programme' => 'BAMAKO-KOUTIALA 10H'],
            ['heure' => '11H', 'programme' => 'BAMAKO-KOUTIALA 11H'],
            ['heure' => '13H', 'programme' => 'BAMAKO-KOUTIALA 13H'],
            ['heure' => '14H', 'programme' => 'BAMAKO-KOUTIALA 14H'],
            ['heure' => '16H', 'programme' => 'BAMAKO-KOUTIALA 16H'],
        ],
        'Bobo-Dioulasso' => [
            ['heure' => '06H', 'programme' => 'BAMAKO-BOBO 06H00'],
            ['heure' => '07H', 'programme' => 'BAMAKO-BOBO 07H00'],
            ['heure' => '18H', 'programme' => 'BAMAKO-BOBO VIA KOUTIALA 18H00'],
            ['heure' => '20H', 'programme' => 'BAMAKO-BOBO 20H00'],
        ],
        'Sadiola' => [
            ['heure' => '05H', 'programme' => 'BAMAKO-SADIOLA 05H00'],
            ['heure' => '16H', 'programme' => 'BAMAKO-SADIOLA 16H00'],
        ],
        'San' => [
            ['heure' => '08H', 'programme' => 'BAMAKO-SAN 08H'],
            ['heure' => '15H', 'programme' => 'BAMAKO-SAN 15H'],
        ],
    ];

    /** Retourne tous les programmes à plat (pour compatibilité) */
    public static function getAllProgrammes(): array
    {
        $all = [];
        foreach (self::PROGRAMMES_PAR_DESTINATION as $progs) {
            foreach ($progs as $p) {
                $all[] = $p['programme'];
            }
        }
        return array_unique($all);
    }

    // Garde la constante PROGRAMMES pour compatibilité ascendante
    const PROGRAMMES = [
        'BAMAKO-KAYES 06H00',
        'BAMAKO-KAYES 08H',
        'BAMAKO-DAKAR 06H00',
        'BAMAKO-SIKASSO 06H00',
        'BAMAKO-ABIDJAN 15H',
        'BAMAKO-MOPTI 05H00',
        'BAMAKO-KOUTIALA 08H',
        'BAMAKO-BOBO 06H00',
        'BAMAKO-KENIEBA 08H',
        'BAMAKO-SADIOLA 05H00',
        'BAMAKO-SAN 08H',
    ];

    public function bagages(): HasMany
    {
        return $this->hasMany(Bagage::class);
    }

    public function getTrajetAttribute(): string
    {
        return strtoupper($this->ville_depart) . ' → ' . strtoupper($this->ville_arrivee);
    }

    public function getLabelAttribute(): string
    {
        return $this->programme ?: ($this->trajet . ' ' . $this->heure_depart);
    }
}
