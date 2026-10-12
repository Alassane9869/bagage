<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    /**
     * Génère un QR code SVG pour un code bagage.
     * Contient le code bagage en clair (identification à la livraison).
     * Le suivi public ayant été supprimé, le QR code encode
     * directement le code bagage pour identification par l'agent.
     */
    public function generateSvg(string $codeBagage, int $size = 120): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->errorCorrection('M')
            ->generate($codeBagage);
    }

    /**
     * Génère un QR code en base64 PNG pour les PDFs.
     */
    public function generateBase64(string $codeBagage, int $size = 100): string
    {
        $png = QrCode::format('png')
            ->size($size)
            ->errorCorrection('M')
            ->generate($codeBagage);

        return 'data:image/png;base64,' . base64_encode($png);
    }
}
