<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role'         => \App\Http\Middleware\CheckRole::class,
            // 'log.activity' disponible via alias sur route précise si besoin
            // Le logging se fait manuellement dans chaque contrôleur (Bagage,
            // Embarquement, Utilisateur, Login) — ne pas l'ajouter globalement
            // pour éviter les doublons dans activity_logs.
            'log.activity' => \App\Http\Middleware\LogActivity::class,
        ]);
        // Pas de $middleware->web(append: [...]) pour LogActivity ici.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
