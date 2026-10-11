<?php
/**
 * Script de Déploiement et d'Installation Automatique pour o2switch / cPanel
 * Projet : Diarra Transport - Gestion des Bagages & Caisses
 * 
 * Ce script prépare et initialise l'application Laravel sans toucher au code :
 * 1. Crée le fichier .env de production depuis .env.production si absent
 * 2. Configure les permissions des répertoires storage et bootstrap/cache
 * 3. Exécute les migrations et les seeders de la base de données MySQL
 * 4. Met en place les caches de production (config, routes, vues)
 * 5. Crée le lien symbolique du stockage public (logos, reçus)
 * 6. Met en place le pont webroot vers diarra-bagage.diawando-tech.com si présent
 */

define('LARAVEL_START', microtime(true));

$isCli = (php_sapi_name() === 'cli');

if (!$isCli) {
    header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Déploiement Automatique • Diarra Transport Bagages</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #0b1f35; color: #f8fafc; padding: 30px 15px; margin: 0; }
        .card { max-width: 820px; margin: 0 auto; background: #06182a; border-radius: 16px; padding: 30px; border: 1px solid #1e3a5f; box-shadow: 0 10px 25px rgba(0,0,0,0.5); }
        .header { display: flex; align-items: center; gap: 15px; border-bottom: 1px solid #1e3a5f; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { width: 55px; height: 55px; border-radius: 50%; border: 2px solid #d4af37; object-fit: cover; }
        h1 { color: #facc15; font-size: 22px; margin: 0; }
        .subtitle { color: #94a3b8; font-size: 14px; margin: 4px 0 0 0; }
        .step { margin: 12px 0; padding: 12px 16px; border-radius: 8px; font-size: 13px; font-family: monospace; line-height: 1.5; }
        .success { background: #064e3b; color: #a7f3d0; border: 1px solid #059669; }
        .warning { background: #78350f; color: #fde68a; border: 1px solid #d97706; }
        .info { background: #1e3a5f; color: #bae6fd; border: 1px solid #0284c7; }
        .btn { display: inline-block; background: #16a34a; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: bold; font-family: sans-serif; margin-top: 15px; font-size: 15px; transition: background 0.2s; }
        .btn:hover { background: #15803d; }
        pre { white-space: pre-wrap; word-break: break-all; }
    </style>
</head>
<body>
<div class="card">
    <div class="header">
        <img src="storage/logos/logo_societe_1782763691.png" onerror="this.src='public/favicon.png'" alt="Logo" class="logo">
        <div>
            <h1>🚀 Déploiement Automatique • Diarra Transport</h1>
            <p class="subtitle">Initialisation et synchronisation de l'application Bagages & Caisses sur o2switch / cPanel.</p>
        </div>
    </div>
<?php
}

$baseDir = __DIR__;

function logMsg($msg, $type = 'info') {
    global $isCli;
    if ($isCli) {
        $clean = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $msg));
        echo "[$type] $clean\n";
    } else {
        echo "<div class='step $type'>$msg</div>";
        flush();
    }
}

// 1. Vérification / Création du fichier .env
$envPath = $baseDir . '/.env';
$envProdPath = $baseDir . '/.env.production';

if (!file_exists($envPath)) {
    if (file_exists($envProdPath)) {
        copy($envProdPath, $envPath);
        logMsg("✅ Fichier .env créé avec succès depuis .env.production.", "success");
    } else {
        logMsg("⚠️ Fichier .env.production introuvable, création d'un .env par défaut.", "warning");
    }
} else {
    logMsg("ℹ️ Fichier .env déjà existant, conservé.", "info");
}

// 2. Vérification et création des dossiers de stockage et permissions
$dirs = [
    $baseDir . '/storage/app/public',
    $baseDir . '/storage/app/public/logos',
    $baseDir . '/storage/framework/cache/data',
    $baseDir . '/storage/framework/sessions',
    $baseDir . '/storage/framework/views',
    $baseDir . '/storage/logs',
    $baseDir . '/bootstrap/cache',
];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    @chmod($dir, 0775);
}
logMsg("✅ Répertoires de stockage (logs, cache, sessions, views) configurés avec permissions 0775.", "success");

// 3. Exécution artisan via shell_exec ou PHP
function runArtisan($command) {
    global $baseDir;
    $artisan = $baseDir . '/artisan';
    $php = PHP_BINARY;
    $cmd = escapeshellcmd("$php $artisan $command");
    $output = @shell_exec($cmd . ' 2>&1');
    return $output;
}

$hasShell = function_exists('shell_exec') && !in_array('shell_exec', array_map('trim', explode(',', ini_get('disable_functions'))));

if ($hasShell) {
    // Vérifier si Composer doit être exécuté
    if (!file_exists($baseDir . '/vendor/autoload.php')) {
        logMsg("⏳ Installation des dépendances Composer...", "info");
        $composerOutput = @shell_exec("cd $baseDir && (composer install --no-dev --optimize-autoloader 2>&1 || /opt/cpanel/composer/bin/composer install --no-dev --optimize-autoloader 2>&1)");
        logMsg("✅ Composer : <pre>" . htmlspecialchars(substr($composerOutput ?? '', 0, 500)) . "</pre>", "info");
    }

    // Exécuter les migrations MySQL
    logMsg("⏳ Exécution des migrations MySQL et seeders...", "info");
    $migOutput = runArtisan('migrate --force --seed');
    logMsg("✅ Migrations : <br><pre style='margin:0;'>" . htmlspecialchars($migOutput ?? 'OK') . "</pre>", "success");

    // Lien symbolique storage
    $linkOutput = runArtisan('storage:link');
    logMsg("✅ Lien symbolique storage:link généré.", "success");

    // Caches de production
    runArtisan('config:cache');
    runArtisan('route:cache');
    runArtisan('view:cache');
    logMsg("✅ Caches optimisés : configuration, routes et vues Blade prêts pour la production.", "success");
} else {
    logMsg("ℹ️ shell_exec est restreint sur cet hébergeur. Veuillez importer database/sc2vuxe8870_bagage.sql directement via phpMyAdmin si ce n'est pas déjà fait.", "info");
}

// 4. Détection et configuration du pont webroot vers diarra-bagage.diawando-tech.com
$domainDir = dirname($baseDir) . '/../diarra-bagage.diawando-tech.com';
$altDomainDir = dirname($baseDir) . '/diarra-bagage.diawando-tech.com';
$targetWebroot = null;

if (is_dir($domainDir)) {
    $targetWebroot = realpath($domainDir);
} elseif (is_dir($altDomainDir)) {
    $targetWebroot = realpath($altDomainDir);
}

if ($targetWebroot && $targetWebroot !== $baseDir) {
    // Créer le index.php bridge dans le dossier du sous-domaine
    $bridgeIndex = "<?php\n" .
        "define('LARAVEL_START', microtime(true));\n\n" .
        "if (file_exists(\$maintenance = __DIR__.'/../repositories/bagage/storage/framework/maintenance.php')) {\n" .
        "    require \$maintenance;\n" .
        "}\n\n" .
        "require __DIR__.'/../repositories/bagage/vendor/autoload.php';\n\n" .
        "(require_once __DIR__.'/../repositories/bagage/bootstrap/app.php')\n" .
        "    ->handleRequest(Illuminate\\Http\\Request::capture());\n";

    file_put_contents($targetWebroot . '/index.php', $bridgeIndex);

    // Créer le .htaccess bridge
    $bridgeHtaccess = "<IfModule mod_rewrite.c>\n" .
        "    Options +FollowSymLinks\n" .
        "    <IfModule mod_negotiation.c>\n" .
        "        Options -MultiViews -Indexes\n" .
        "    </IfModule>\n" .
        "    RewriteEngine On\n" .
        "    RewriteCond %{REQUEST_FILENAME} !-d\n" .
        "    RewriteCond %{REQUEST_FILENAME} !-f\n" .
        "    RewriteRule ^ index.php [L]\n" .
        "</IfModule>\n";

    file_put_contents($targetWebroot . '/.htaccess', $bridgeHtaccess);

    // Copier ou lier les assets build, images et storage
    if (is_dir($baseDir . '/public/build') && !file_exists($targetWebroot . '/build')) {
        @symlink($baseDir . '/public/build', $targetWebroot . '/build');
    }
    if (is_dir($baseDir . '/public/storage') && !file_exists($targetWebroot . '/storage')) {
        @symlink($baseDir . '/public/storage', $targetWebroot . '/storage');
    }

    logMsg("✅ Pont webroot configuré automatiquement vers : " . htmlspecialchars($targetWebroot), "success");
}

logMsg("🎉 <strong>Déploiement terminé avec succès !</strong> La plateforme Diarra Transport est prête.", "success");

if (!$isCli) {
?>
    <div style="margin-top: 25px; text-align: center;">
        <a href="https://diarra-bagage.diawando-tech.com" target="_blank" class="btn">Accéder à Diarra Bagages &rarr;</a>
    </div>
</div>
</body>
</html>
<?php
}
