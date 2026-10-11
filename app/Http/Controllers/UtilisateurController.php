<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Societe;
use App\Models\User;
use App\Models\ActivityLog as Log;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UtilisateurController extends Controller
{
    /** Liste utilisateurs */
    public function index(): View
    {
        $users = User::with('role')->orderBy('nom')->paginate(20);
        $roles = Role::all();
        return view('admin.utilisateurs.index', compact('users', 'roles'));
    }

    /** Créer un utilisateur */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'prenom'   => ['required', 'string', 'max:100'],
            'nom'      => ['nullable', 'string', 'max:100'],
            'login'    => ['required', 'string', 'max:100', 'unique:users,login'],
            'email'    => ['nullable', 'email', 'unique:users,email'],
            'telephone'=> ['nullable', 'string'],
            'role_id'  => ['required', 'exists:roles,id'],
            'agence'   => ['nullable', 'string'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        // Email optionnel — si absent, générer un placeholder unique interne
        $email = $request->filled('email')
            ? $request->email
            : strtolower(trim($request->login)) . '.' . uniqid() . '@diarra.local';

        User::create([
            'name'      => trim($request->prenom . ' ' . ($request->nom ?? '')),
            'prenom'    => $request->prenom,
            'nom'       => $request->nom ?? '',
            'email'     => $email,
            'login'     => strtolower(trim($request->login)),
            'telephone' => $request->telephone,
            'role_id'   => $request->role_id,
            'agence'    => $request->agence,
            'password'  => Hash::make($request->password),
            'statut'    => 'actif',
        ]);

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Création utilisateur ' . $request->prenom . ' / login: ' . $request->login,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Utilisateur créé avec succès.');
    }

    /** Modifier un utilisateur */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'prenom'   => ['required', 'string', 'max:100'],
            'nom'      => ['nullable', 'string', 'max:100'],
            'login'    => ['required', 'string', 'max:100', 'unique:users,login,' . $user->id],
            'email'    => ['nullable', 'email', 'unique:users,email,' . $user->id],
            'telephone'=> ['nullable', 'string'],
            'role_id'  => ['required', 'exists:roles,id'],
            'agence'   => ['nullable', 'string'],
            'statut'   => ['required', 'in:actif,inactif'],
        ]);

        $user->update([
            'name'      => trim($request->prenom . ' ' . ($request->nom ?? '')),
            'prenom'    => $request->prenom,
            'nom'       => $request->nom ?? '',
            'login'     => strtolower(trim($request->login)),
            'email'     => $request->filled('email') ? $request->email : $user->email,
            'telephone' => $request->telephone,
            'role_id'   => $request->role_id,
            'agence'    => $request->agence,
            'statut'    => $request->statut,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Modification utilisateur ' . $user->nom_complet,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Utilisateur mis à jour.');
    }

    /** Supprimer un utilisateur */
    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }

        $nom = $user->nom_complet;
        $user->delete();

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Suppression',
            'detail'     => 'Suppression utilisateur ' . $nom,
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Utilisateur supprimé.');
    }

    /** Page Profils (rôles) */
    public function profils(): View
    {
        $roles = Role::withCount('users')->get();
        return view('admin.profils', compact('roles'));
    }

    /** Logs système — utilise Log (ActivityLog) directement */
    public function logs(): View
    {
        $logs = Log::with('user')->orderByDesc('created_at')->paginate(30);
        return view('admin.logs', compact('logs'));
    }

    /** La Société */
    public function societe(): View
    {
        $societe = Societe::first() ?? new Societe();
        return view('admin.societe', compact('societe'));
    }

    public function uploadLogo(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
        ]);

        $societe = Societe::firstOrCreate(['id' => 1]);

        // Supprimer l'ancien logo si existant
        if ($societe->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($societe->logo_path)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($societe->logo_path);
        }

        // Sauvegarder le nouveau logo
        $path = $request->file('logo')->storeAs(
            'logos',
            'logo_societe_' . time() . '.' . $request->file('logo')->getClientOriginalExtension(),
            'public'
        );

        $societe->update(['logo_path' => $path]);

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Mise à jour du logo de la société',
            'ip_address' => $request->ip(),
        ]);

        return response()->json([
            'success' => true,
            'url'     => asset('storage/' . $path),
            'message' => 'Logo mis à jour avec succès.',
        ]);
    }

    public function deleteLogo(Request $request): \Illuminate\Http\JsonResponse
    {
        $societe = Societe::first();
        if ($societe && $societe->logo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($societe->logo_path);
            $societe->update(['logo_path' => null]);
        }

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Suppression',
            'detail'     => 'Suppression du logo de la société',
            'ip_address' => $request->ip(),
        ]);

        return response()->json(['success' => true, 'message' => 'Logo supprimé.']);
    }

    public function updateSociete(Request $request): RedirectResponse
    {
        $request->validate([
            'raison_sociale'    => ['required', 'string', 'max:200'],
            'telephone'         => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email'],
            'adresse'           => ['nullable', 'string', 'max:255'],
            'ville'             => ['nullable', 'string', 'max:100'],
            'pays'              => ['nullable', 'string', 'max:100'],
            'site_web'          => ['nullable', 'max:255', function($attr, $val, $fail) {
                if ($val && !filter_var($val, FILTER_VALIDATE_URL)) {
                    $fail('Le site web doit être une URL valide (ex: https://www.example.com).');
                }
            }],
            'guichet_principal' => ['nullable', 'string', 'max:50'],
            'numero_rccm'       => ['nullable', 'string', 'max:100'],
            'numero_ifu'        => ['nullable', 'string', 'max:100'],
            'devise'            => ['nullable', 'string', 'max:10'],
            'fuseau_horaire'    => ['nullable', 'string', 'max:50'],
            'mentions_recu'     => ['nullable', 'string', 'max:500'],
        ]);

        $data = $request->only([
            'raison_sociale','telephone','email','adresse','ville','pays',
            'site_web','guichet_principal','numero_rccm','numero_ifu',
            'devise','fuseau_horaire','mentions_recu',
        ]);

        // Convertir les chaînes vides en null pour les champs optionnels
        foreach (['site_web','email','numero_rccm','numero_ifu','ville','pays'] as $field) {
            if (isset($data[$field]) && trim($data[$field]) === '') {
                $data[$field] = null;
            }
        }

        Societe::updateOrCreate(['id' => 1], $data);

        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Mise à jour des paramètres de l\'application',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Paramètres enregistrés avec succès.');
    }

    /** Mon profil */
    public function monProfil(): View
    {
        $user = Auth::user();
        $user->load('role', 'loginHistories');
        $derniereConnexion = $user->loginHistories()->orderByDesc('logged_at')->skip(1)->first();
        return view('admin.profil', compact('user', 'derniereConnexion'));
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required'],
            'password'         => ['required', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Mot de passe actuel incorrect.']);
        }

        Auth::user()->update(['password' => Hash::make($request->password)]);

        // Log activité
        Log::create([
            'user_id'    => Auth::id(),
            'action'     => 'Modification',
            'detail'     => 'Changement de mot de passe personnel',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
