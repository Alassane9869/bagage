<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bagage;
use App\Models\Suivi;
use App\Models\Voyage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class EmbarquementController extends Controller
{
    public function index(Request $request): View
    {
        // Bagages en attente d'embarquement (statut = enregistre)
        $query = Bagage::with(['client', 'voyage'])
            ->where('statut', 'enregistre')
            ->orderBy('date_enregistrement', 'desc');

        if ($busFilter = $request->get('bus')) {
            $query->whereHas('voyage', fn($q) => $q->where('numero_bus', $busFilter));
        }

        $bagages = $query->paginate(20)->withQueryString();

        // Liste des bus distincts
        $buses = Voyage::whereHas('bagages', fn($q) => $q->where('statut', 'enregistre'))
            ->select('numero_bus', 'programme')
            ->distinct()
            ->get();

        // Résultat de recherche
        $found = null;
        if ($search = $request->get('search')) {
            $found = Bagage::with(['client', 'voyage'])
                ->where('code_bagage', 'like', "%{$search}%")
                ->orWhere('numero_billet', 'like', "%{$search}%")
                ->first();
        }

        return view('embarquement.index', compact('bagages', 'buses', 'found', 'search'));
    }

    /** Marquer un bagage comme embarqué */
    public function embarquer(Request $request, Bagage $bagage): RedirectResponse|JsonResponse
    {
        $request->validate([
            'numero_bus' => ['nullable', 'string'],
        ]);

        if ($bagage->statut !== 'enregistre') {
            $msg = 'Ce bagage ne peut pas être embarqué (statut : ' . $bagage->statut_label . ')';
            if ($request->expectsJson()) {
                return response()->json(['error' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        $bagage->update(['statut' => 'embarque']);

        if ($bus = $request->get('numero_bus')) {
            $bagage->voyage?->update(['numero_bus' => $bus]);
        }

        Suivi::create([
            'bagage_id'    => $bagage->id,
            'user_id'      => Auth::id(),
            'localisation' => $bagage->voyage?->ville_depart ?? 'Agence',
            'statut'       => 'embarque',
            'commentaire'  => 'Bagage chargé dans le bus' . ($bus ? ' ' . $bus : ''),
        ]);

        ActivityLog::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Embarquement bagage ' . $bagage->code_bagage,
            'ip_address' => $request->ip(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'code' => $bagage->code_bagage]);
        }

        return back()->with('success', 'Bagage ' . $bagage->code_bagage . ' embarqué avec succès.');
    }

    /** Vue bagages embarqués (filtrable par bus) */
    public function embarques(Request $request): View
    {
        $query = Bagage::with(['client', 'voyage'])
            ->where('statut', 'embarque')
            ->orderBy('date_enregistrement', 'desc');

        if ($busFilter = $request->get('bus')) {
            $query->whereHas('voyage', fn($q) => $q->where('numero_bus', $busFilter));
        }

        $bagages = $query->paginate(20)->withQueryString();

        $buses = Voyage::whereHas('bagages', fn($q) =>
            $q->where('statut', 'embarque')
        )->select('numero_bus', 'programme')->distinct()->get();

        return view('embarquement.embarques', compact('bagages', 'buses'));
    }
}
