<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\LoginHistory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $login    = trim($request->email);
        $password = $request->password;

        // Chercher l'utilisateur par login (champ libre)
        $user = \App\Models\User::where('login', $login)->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'Identifiant ou mot de passe incorrect.',
            ])->onlyInput('email');
        }

        // Vérifier le mot de passe manuellement puis connecter
        if (!\Illuminate\Support\Facades\Hash::check($password, $user->password)) {
            return back()->withErrors([
                'email' => 'Identifiant ou mot de passe incorrect.',
            ])->onlyInput('email');
        }

        // Vérifier statut avant connexion
        if ($user->statut === 'inactif') {
            return back()->withErrors([
                'email' => 'Votre compte est désactivé. Contactez l\'administrateur.',
            ]);
        }

        // Connecter l'utilisateur
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        // Historique connexion
        LoginHistory::create([
            'user_id'    => $user->id,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logged_at'  => now(),
        ]);

        // Log activité
        ActivityLog::create([
            'user_id'    => $user->id,
            'action'     => 'Connexion',
            'detail'     => 'Authentification réussie depuis ' . $request->ip(),
            'ip_address' => $request->ip(),
        ]);

        // Redirection selon le rôle
        $slug = $user->role?->slug;

        $destination = match($slug) {
            'administrateur', 'agent_bagages' => route('bagages.create'), // Page Bagage Simple
            'controleur'                      => route('bagages.index'),  // Page Bagages Enregistrés
            default                           => route('bagages.index'),
        };

        return redirect()->intended($destination);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
