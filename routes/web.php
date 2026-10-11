<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BagageController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmbarquementController;
use App\Http\Controllers\SuiviController;
use App\Http\Controllers\UtilisateurController;
use Illuminate\Support\Facades\Route;

// ── Publiques ─────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'))->name('home');

// ── Authentification ──────────────────────────────────────────
Route::get('/login',  [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware(['guest', 'throttle:5,1']);
Route::post('/logout',[LoginController::class, 'logout'])->name('logout');

// ── Zone authentifiée ─────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Création de bagages : administrateur + agent_bagages
    Route::middleware('role:administrateur,agent_bagages')->group(function () {
        Route::get( '/bagages/create', [BagageController::class, 'create'])->name('bagages.create');
        Route::post('/bagages',        [BagageController::class, 'store'])->name('bagages.store');
    });

    // Consultation bagages : tous les rôles authentifiés (y compris controleur)
    Route::get( '/bagages',                  [BagageController::class, 'index'])->name('bagages.index');
    Route::get( '/bagages/{bagage}',         [BagageController::class, 'show'])->name('bagages.show');
    Route::get( '/bagages/{bagage}/receipt', [BagageController::class, 'receipt'])->name('bagages.receipt');
    Route::get( '/bagages/{code}/suivi',     [SuiviController::class,  'show'])->name('suivi.show');

    Route::get('/clients/search', [BagageController::class, 'searchClients'])->name('clients.search');

    // Embarquement : administrateur + agent_bagages
    Route::middleware('role:administrateur,agent_bagages')->group(function () {
        Route::get(  '/embarquement',          [EmbarquementController::class, 'index'])->name('embarquement.index');
        Route::patch('/embarquement/{bagage}', [EmbarquementController::class, 'embarquer'])->name('embarquement.embarquer');
        Route::get(  '/gestion/embarques',     [EmbarquementController::class, 'embarques'])->name('gestion.embarques');
    });

    // Caisse journalière : administrateur + agent_bagages
    Route::middleware('role:administrateur,agent_bagages')->group(function () {
        Route::get('/caisse/ma-caisse',  [CaisseController::class, 'maCaisse'])->name('caisse.ma_caisse');
        Route::get('/caisse/par-depart', [CaisseController::class, 'parDepart'])->name('caisse.par_depart');
    });

    // État global : administrateur uniquement
    Route::get('/caisse/global', [CaisseController::class, 'global'])->name('caisse.global')
         ->middleware('role:administrateur');

    // Mon profil : tous les rôles authentifiés
    Route::get('/profil',          [UtilisateurController::class, 'monProfil'])->name('profil.index');
    Route::put('/profil/password', [UtilisateurController::class, 'updatePassword'])->name('profil.update_password');

    // Administration : administrateur uniquement
    Route::middleware('role:administrateur')->prefix('admin')->name('admin.')->group(function () {
        Route::get   ('/dashboard',            [DashboardController::class,  'index'])->name('dashboard');
        Route::get   ('/societe',              [UtilisateurController::class, 'societe'])->name('societe');
        Route::put   ('/societe',              [UtilisateurController::class, 'updateSociete'])->name('societe.update');
        Route::post  ('/societe/logo',         [UtilisateurController::class, 'uploadLogo'])->name('societe.logo');
        Route::delete('/societe/logo',         [UtilisateurController::class, 'deleteLogo'])->name('societe.logo.delete');
        Route::get   ('/utilisateurs',         [UtilisateurController::class, 'index'])->name('utilisateurs');
        Route::post  ('/utilisateurs',         [UtilisateurController::class, 'store'])->name('utilisateurs.store');
        Route::put   ('/utilisateurs/{user}',  [UtilisateurController::class, 'update'])->name('utilisateurs.update');
        Route::delete('/utilisateurs/{user}',  [UtilisateurController::class, 'destroy'])->name('utilisateurs.destroy');
        Route::get   ('/profils',              [UtilisateurController::class, 'profils'])->name('profils');
        Route::get   ('/logs',                 [UtilisateurController::class, 'logs'])->name('logs');
    });
});
