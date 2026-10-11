<?php

namespace App\Http\Controllers;

use App\Services\CaisseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CaisseController extends Controller
{
    public function __construct(private CaisseService $caisseService) {}

    public function maCaisse(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());
        $data = $this->caisseService->getMaCaisse(Auth::user(), $date);
        return view('caisse.ma_caisse', $data);
    }

    public function parDepart(Request $request): View
    {
        $date     = $request->get('date', now()->toDateString());
        $lignes   = $this->caisseService->getCaisseParDepart(Auth::user(), $date);
        return view('caisse.par_depart', compact('lignes', 'date'));
    }

    public function global(Request $request): View
    {
        $date = $request->get('date', now()->toDateString());
        $data = $this->caisseService->getEtatGlobal($date);
        return view('caisse.global', $data);
    }
}
