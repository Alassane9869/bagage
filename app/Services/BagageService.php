<?php

namespace App\Services;

use App\Models\Bagage;
use App\Models\Colis;

class BagageService
{
    /**
     * Génère un code unique BG-YYYY-NNNNN avec verrou pour éviter
     * les race conditions en cas d'enregistrements simultanés.
     * Doit être appelé à l'intérieur d'une transaction DB::beginTransaction().
     */
    public function generateCode(): string
    {
        $year = date('Y');

        $last = Bagage::where('code_bagage', 'like', "BG-{$year}-%")
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $lastNum = $last ? (int) substr($last->code_bagage, -5) : 0;
        $next    = $lastNum + 1;
        $code    = 'BG-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);

        // Sécurité supplémentaire : on incrémente si le code existe déjà
        while (Bagage::where('code_bagage', $code)->exists()) {
            $next++;
            $code = 'BG-' . $year . '-' . str_pad($next, 5, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    /**
     * Génère les numéros de colis individuels pour un bagage
     * Ex: BG-2026-00001-C1, BG-2026-00001-C2
     */
    public function generateColisNumbers(string $codeBagage, int $nombreColis): array
    {
        $numbers = [];
        for ($i = 1; $i <= $nombreColis; $i++) {
            $numbers[] = $codeBagage . '-C' . $i;
        }
        return $numbers;
    }

    /**
     * Crée les enregistrements Colis associés à un Bagage
     */
    public function createColis(Bagage $bagage): void
    {
        $numbers = $this->generateColisNumbers($bagage->code_bagage, $bagage->nombre_colis);

        foreach ($numbers as $numero) {
            Colis::create([
                'bagage_id'    => $bagage->id,
                'numero_colis' => $numero,
            ]);
        }
    }
}
