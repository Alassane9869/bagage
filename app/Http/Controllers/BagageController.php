<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Bagage;
use App\Models\Client;
use App\Models\Suivi;
use App\Models\Voyage;
use App\Services\BagageService;
use App\Services\CaisseService;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BagageController extends Controller
{
    public function __construct(
        private BagageService  $bagageService,
        private CaisseService  $caisseService,
        private QrCodeService  $qrCodeService,
    ) {}

    /** Formulaire d'enregistrement */
    public function create(): View
    {
        $villes    = Voyage::VILLES;
        $natures   = Bagage::NATURES;
        $programmes = Voyage::getAllProgrammes();
        $programmesByDest = Voyage::PROGRAMMES_PAR_DESTINATION;
        return view('bagages.create', compact('villes', 'natures', 'programmes', 'programmesByDest'));
    }

    /**
     * Enregistrement d'un nouveau bagage.
     * Toujours retourne JSON (soumission AJAX depuis le formulaire).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date_depart'    => ['required', 'date'],
            'ville_depart'   => ['required', 'string'],
            'ville_arrivee'  => ['required', 'string', 'different:ville_depart'],
            'programme'      => ['nullable', 'string'],
            'numero_billet'  => ['nullable', 'string'],
            'numero_bus'      => ['nullable', 'string'],
            'prenom'         => ['required', 'string', 'max:100'],
            'nom'            => ['nullable', 'string', 'max:100'],
            'telephone'      => ['nullable', 'string', 'max:30'],
            'client_id'      => ['nullable', 'exists:clients,id'],
            'nombre_colis'   => ['required', 'integer', 'min:1', 'max:50'],
            'colis'          => ['required', 'array', 'min:1'],
            'colis.*.nature' => ['required', 'string'],
            'colis.*.valeur' => ['nullable', 'string'],
            'colis.*.prix'   => ['required', 'numeric', 'min:0'],
            'montant'        => ['required', 'numeric', 'min:0'],
            'remise'         => ['nullable', 'numeric', 'min:0'],
        ]);

        DB::beginTransaction();
        try {
            // Voyage : trouver ou créer
            $voyage = Voyage::firstOrCreate(
                [
                    'ville_depart'  => $validated['ville_depart'],
                    'ville_arrivee' => $validated['ville_arrivee'],
                    'date_depart'   => $validated['date_depart'],
                    'programme'     => $validated['programme'] ?? null,
                ],
                [
                    'numero_bus'    => $validated['numero_bus'] ?? null,
                    'statut_voyage' => 'planifie',
                ]
            );

            // Client
            $clientId = $validated['client_id'] ?? null;
            if (!$clientId) {
                $client = Client::create([
                    'prenom'    => $validated['prenom'],
                    'nom'       => $validated['nom'] ?? '',
                    'telephone' => $validated['telephone'] ?? null,
                ]);
                $clientId = $client->id;
            } else {
                // client existant — on récupère son objet pour le sérialiser
                $client = Client::find($clientId);
            }

            // Code bagage
            $codeBagage = $this->bagageService->generateCode();

            // Création bagage (nature = première nature de colis pour compat)
            $colisData = $validated['colis'];
            $premiereNature = $colisData[0]['nature'] ?? '';

            $bagage = Bagage::create([
                'code_bagage'         => $codeBagage,
                'client_id'           => $clientId,
                'voyage_id'           => $voyage->id,
                'user_id'             => Auth::id(),
                'nombre_colis'        => $validated['nombre_colis'],
                'nature'              => $premiereNature,
                'montant'             => $validated['montant'],
                'remise'              => $validated['remise'] ?? 0,
                'numero_billet'       => $validated['numero_billet'] ?? null,
                'statut'              => 'enregistre',
                'date_enregistrement' => now(),
            ]);

            // Colis individuels avec données par colis (nature, valeur, prix)
            $this->bagageService->createColis($bagage, $colisData);

            // Premier suivi
            Suivi::create([
                'bagage_id'    => $bagage->id,
                'user_id'      => Auth::id(),
                'localisation' => $validated['ville_depart'],
                'statut'       => 'enregistre',
                'commentaire'  => 'Bagage enregistré à l\'agence',
            ]);

            // Mise à jour caisse
            $this->caisseService->updateCaisseJournaliere(
                Auth::user(),
                (float) $validated['montant'],
                (float) ($validated['remise'] ?? 0)
            );

            // Log activité
            ActivityLog::create([
                'user_id'    => Auth::id(),
                'action'     => 'Bagage',
                'detail'     => 'Enregistrement ' . $codeBagage,
                'ip_address' => $request->ip(),
            ]);

            DB::commit();

            // QR code SVG
            $qrCode = $this->qrCodeService->generateSvg($bagage->code_bagage);

            // Construire le payload JSON propre avec toutes les données nécessaires
            $agent = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Bagage ' . $codeBagage . ' enregistré avec succès !',
                'qr_code' => $qrCode,
                'bagage'  => [
                    'id'             => $bagage->id,
                    'code_bagage'    => $bagage->code_bagage,
                    'nombre_colis'   => $bagage->nombre_colis,
                    'nature'         => $bagage->nature,
                    'montant'        => $bagage->montant,
                    'remise'         => $bagage->remise,
                    'numero_billet'  => $bagage->numero_billet,
                    'statut'         => $bagage->statut,
                    'client' => [
                        'prenom'    => $client->prenom,
                        'nom'       => $client->nom,
                        'telephone' => $client->telephone ?? '',
                    ],
                    'voyage' => [
                        'ville_depart'  => $voyage->ville_depart,
                        'ville_arrivee' => $voyage->ville_arrivee,
                        'date_depart'   => $voyage->date_depart instanceof \Carbon\Carbon
                            ? $voyage->date_depart->format('Y-m-d')
                            : $voyage->date_depart,
                        'programme'     => $voyage->programme,
                        'numero_bus'    => $voyage->numero_bus,
                    ],
                    'user' => [
                        'prenom' => $agent->prenom ?? '',
                        'nom'    => $agent->nom    ?? '',
                    ],
                    'colis' => $bagage->colis()->orderBy('id')->get()->map(fn($c) => [
                        'numero_colis' => $c->numero_colis,
                        'nature'       => $c->nature,
                        'valeur'       => $c->valeur,
                        'prix'         => $c->prix,
                    ]),
                ],
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
                'errors'  => [],
            ], 500);
        }
    }

    /** Liste des bagages */
    public function index(Request $request): View
    {
        $query = Bagage::with(['client', 'voyage', 'user'])
            ->orderBy('date_enregistrement', 'desc');

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code_bagage', 'like', "%{$search}%")
                  ->orWhere('numero_billet', 'like', "%{$search}%")
                  ->orWhereHas('client', fn($c) =>
                      $c->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('telephone', 'like', "%{$search}%")
                  );
            });
        }

        if ($statut = $request->get('statut')) {
            $query->where('statut', $statut);
        }

        $bagages = $query->paginate(15)->withQueryString();
        $statuts = Bagage::STATUTS;

        return view('bagages.index', compact('bagages', 'statuts'));
    }

    /** Détail d'un bagage (AJAX ou vue) */
    public function show(Request $request, Bagage $bagage): View|JsonResponse
    {
        $bagage->load(['client', 'voyage', 'colis', 'user', 'suivis.user']);
        $qrCode = $this->qrCodeService->generateSvg($bagage->code_bagage);

        if ($request->expectsJson()) {
            return response()->json([
                'bagage' => $bagage,
                'qr_code'=> $qrCode,
            ]);
        }

        return view('bagages.show', compact('bagage', 'qrCode'));
    }

    /** Recherche AJAX clients connus */
    public function searchClients(Request $request): JsonResponse
    {
        $q = $request->get('q', '');
        $clients = Client::where('nom', 'like', "%{$q}%")
            ->orWhere('prenom', 'like', "%{$q}%")
            ->orWhere('telephone', 'like', "%{$q}%")
            ->limit(10)
            ->get(['id', 'nom', 'prenom', 'telephone']);

        return response()->json($clients);
    }

    /** Données bagage pour ré-impression reçu */
    public function receipt(Bagage $bagage): JsonResponse
    {
        $bagage->load(['client', 'voyage', 'colis', 'user']);
        $qrCode = $this->qrCodeService->generateSvg($bagage->code_bagage);

        return response()->json([
            'bagage'  => $bagage,
            'qr_code' => $qrCode,
        ]);
    }
}
