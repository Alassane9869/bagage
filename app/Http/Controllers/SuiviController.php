<?php

namespace App\Http\Controllers;

use App\Models\Bagage;
use App\Services\QrCodeService;
use Illuminate\View\View;

class SuiviController extends Controller
{
    public function __construct(private QrCodeService $qrCodeService) {}

    /** Page suivi authentifiée */
    public function show(string $code): View
    {
        $bagage = Bagage::where('code_bagage', $code)
            ->with(['client', 'voyage', 'colis', 'suivis.user'])
            ->firstOrFail();

        $qrCode = $this->qrCodeService->generateSvg($bagage->code_bagage, 100);

        return view('suivi.show', compact('bagage', 'qrCode'));
    }
}
