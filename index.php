<?php
/**
 * Pont automatique pour serveur web pointant à la racine (cPanel / o2switch)
 * Permet d'exécuter l'application que le sous-domaine pointe vers / ou vers /public
 * 
 * Diarra Transport - Gestion des Bagages
 */

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Mode maintenance
if (file_exists($maintenance = __DIR__.'/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Autoloader Composer
if (file_exists(__DIR__.'/vendor/autoload.php')) {
    require __DIR__.'/vendor/autoload.php';
} elseif (file_exists(__DIR__.'/../repositories/bagage/vendor/autoload.php')) {
    require __DIR__.'/../repositories/bagage/vendor/autoload.php';
}

// Démarrage de l'application Laravel
if (file_exists(__DIR__.'/bootstrap/app.php')) {
    $app = require_once __DIR__.'/bootstrap/app.php';
} elseif (file_exists(__DIR__.'/../repositories/bagage/bootstrap/app.php')) {
    $app = require_once __DIR__.'/../repositories/bagage/bootstrap/app.php';
}

if (isset($app)) {
    $app->handleRequest(Request::capture());
} else {
    die("Erreur critique : Impossible de charger l'application Laravel. Veuillez exécuter 'composer install' ou lancer deploy_o2switch.php.");
}
