# 🧳 Diarra Transport – Plateforme Digitale de Gestion des Bagages & Caisses

<p align="center">
  <img src="storage/app/public/logos/logo_societe_1782763691.png" width="130" alt="Logo Diarra Transport Mali" style="border-radius: 50%; box-shadow: 0 4px 15px rgba(0,0,0,0.3);">
</p>

<p align="center">
  <strong>Solution professionnelle de gestion des bagages accompagnés, billetterie thermique 80mm avec QR Code, embarquement et contrôle des caisses journalières.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Base_de_Donn%C3%A9es-MySQL%20%7C%20SQLite-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="Database">
  <img src="https://img.shields.io/badge/Tests-12%20Valid%C3%A9s-success?style=for-the-badge&logo=phpunit&logoColor=white" alt="Tests 100% Passed">
  <img src="https://img.shields.io/badge/D%C3%A9ploiement-o2switch%20%2F%20cPanel-orange?style=for-the-badge&logo=cpanel&logoColor=white" alt="cPanel Ready">
  <img src="https://img.shields.io/badge/Licence-Propri%C3%A9taire-gold?style=for-the-badge" alt="Licence">
</p>

---

## 📌 Présentation Générale

**Diarra Transport Bagages** est un progiciel web métier conçu sur mesure pour digitaliser, sécuriser et accélérer l'ensemble de la chaîne logistique des bagages accompagnés au sein du réseau d'agences de la compagnie **Diarra Transport** (Bamako, Kayes, Kéniéba, Sikasso, Mopti, Dakar, Abidjan, etc.).

L'application répond à trois impératifs majeurs des compagnies de transport de passagers :
1. **Fluidité au guichet :** Enregistrement ultra-rapide des colis en moins de 30 secondes avec autocomplétion intelligente et calcul de rendu de monnaie.
2. **Sécurité et anti-fraude :** Attribution d'un identifiant transactionnel unique (`BG-YYYY-NNNNN`), découpage automatique en sous-colis étiquetés individuellement, et QR Code SVG vectoriel infalsifiable scannable sur ticket thermique standard POS 80mm.
3. **Contrôle financier infaillible :** Clôture journalière de caisse par agent, ventilation par départ de bus, et consolidation en direct pour la direction générale afin d'éliminer toute perte de recettes.

---

## 🏛️ Architecture Modulaire de la Plateforme

Chaque fonctionnalité a été conçue comme un **module métier autonome, robuste et spécialisé**, garantissant une séparation stricte des responsabilités :

```text
┌─────────────────────────────────────────────────────────────────────────────────────────────────┐
│                        DIARRA TRANSPORT – SYSTÈME BAGAGES & CAISSES                            │
├──────────────────────────┬──────────────────────────┬───────────────────────────────────────────┤
│    GUICHET & ENREG.      │   EMBARQUEMENT & BUS     │       CAISSE & COMPTABILITÉ               │
├──────────────────────────┼──────────────────────────┼───────────────────────────────────────────┤
│ • Saisie rapide colis    │ • Filtrage par voyage    │ • Ma Caisse (vue agent guichet)           │
│ • Autocomplétion client  │ • Pointage au bus        │ • Caisse par départ de bus                │
│ • Calcul rendu-monnaie   │ • Affectation matricule  │ • État Global consolidé (Direction)       │
│ • Impression ticket 80mm │ • Horodatage validation  │ • Audit & traçabilité des règlements      │
│ • Étiquettes adhésives   │ • Suivi état (embarqué)  │ • Clôture journalière verrouillée         │
└──────────────────────────┴──────────────────────────┴───────────────────────────────────────────┘
```

---

## 🚀 Modules Fonctionnels Détaillés

### 1. 🧳 Module Guichet & Enregistrement Rapide
- **Route :** `/bagages/create`
- **Rôles :** `Agent Bagages`, `Administrateur`
- **Génération automatique de référence :** Format séquentiel annuel `BG-2026-XXXXX` généré avec verrouillage atomique de base de données pour empêcher tout doublon même en cas de saisies simultanées multi-guichets.
- **Autocomplétion client intelligente :** Recherche asynchrone dès 2 caractères par nom, prénom ou numéro de téléphone pour réutiliser instantanément les fiches existantes sans ressaisie.
- **Calculateur dynamique de monnaie :** L'agent saisit le montant remis par le client, le système calcule instantanément la monnaie à rendre pour éviter toute erreur de caisse humaine.
- **Gestion multi-colis :** Prise en charge d'un nombre arbitraire de colis par voyageur avec génération automatique de sous-identifiants uniques (`BG-2026-00001-C1`, `C2`, etc.).

### 2. 🖨️ Module Billetterie Thermique 80mm & QR Code
- **Route :** `/bagages/{id}/ticket`
- **Compatibilité matérielle :** Optimisé pour les imprimantes thermiques professionnelles de caisse (Epson TM-T20/T88, Xprinter, Munbyn, Bixolon) sur rouleaux standards de 80mm.
- **Ticket Voyageur :** En-tête officiel aux couleurs de la compagnie, itinéraire de départ et d'arrivée, désignation des colis, poids, montant net perçu, conditions générales de transport et talon de contrôle détachable.
- **Étiquettes Adhésives de Colis :** Impression individualisée pour chaque bagage avec mention du destinataire, ville d'arrivée et QR Code vectoriel.
- **QR Code SVG Vectoriel :** Généré dynamiquement sans dépendance externe cloud, offrant une netteté de contraste maximale pour les douchettes de scan 2D.

### 3. 🚌 Module Embarquement & Pointage Bus
- **Route :** `/embarquements`
- **Rôles :** `Agent Bagages`, `Contrôleur`, `Administrateur`
- **Gestion par voyage :** Sélection de la ligne de bus, de la date et de l'heure de départ.
- **Validation du chargement :** Pointage colis par colis avec affectation du matricule de bus (ex. `BM-001`, `SK-002`) et horodatage de la prise en charge.
- **Traçabilité des statuts :** Cycle de vie maîtrisé : `enregistré` ➔ `embarqué` ➔ `livré`.

### 4. 💵 Module Caisse & Contrôle des Recettes
- **Routes :** `/caisse/ma-caisse`, `/caisse/par-depart`, `/caisse/global`
- **Ma Caisse (Agent) :** Vue personnelle de l'agent en service affichant le total brut encaissé, le nombre de bagages traités dans la journée et le détail chronologique de chaque opération.
- **Caisse par Départ :** Ventilation financière ligne par ligne pour rapprocher les recettes bagages avec les feuilles de route des chauffeurs.
- **État Global (Direction) :** Console de supervision consolidée réservée à la direction :
  - Chiffre d'affaires global de la journée ou d'une période personnalisée.
  - Répartition des encaissements par agent de guichet.
  - Statistiques des modes de règlement.

### 5. 📈 Module Tableau de Bord & Analytics
- **Route :** `/dashboard`
- **Indicateurs de performance (KPIs) :**
  - Chiffre d'affaires cumulé du jour, de la semaine et du mois.
  - Nombre total de bagages enregistrés vs embarqués.
  - Taux d'embarquement moyen en temps réel.
- **Graphiques d'évolution :** Recettes mensuelles comparatives et Top 10 des destinations les plus fréquentées.

### 6. 🛡️ Module Administration & Traçabilité (RBAC)
- **Routes :** `/admin/users`, `/admin/societe`, `/admin/activity-logs`, `/admin/login-history`
- **Contrôle d'accès par rôle :**
  - `Administrateur` : Gouvernance totale, paramétrage de la société, gestion des utilisateurs, audits.
  - `Agent Bagages` : Saisie des bagages, billetterie, validation d'embarquement et consultation de sa caisse.
  - `Contrôleur` : Contrôle et pointage lors du chargement des soutes.
- **Journal d'activité complet (Audit Logs) :** Chaque création, modification ou suppression consigne l'auteur, l'adresse IP et la date exacte.
- **Historique des connexions :** Détection des sessions actives et traçabilité des connexions des agents.

---

## 🛠️ Stack Technique

| Composant | Technologie | Détail & Avantages |
| :--- | :--- | :--- |
| **Framework Backend** | **Laravel 12.x** | Architecture MVC moderne, Eloquent ORM, sécurité renforcée |
| **Langage** | **PHP 8.2 / 8.3** | Typage strict, exécution haute vitesse |
| **Base de Données** | **MySQL 8.0+ / SQLite 3** | Requêtes SQL portables (`whereBetween`), index B-Tree optimisés |
| **Moteur QR Code** | **SimpleSoftwareIO QrCode** | Rendu vectoriel SVG haute résolution sans appel externe |
| **Frontend UI** | **Blade + Bootstrap 5.3 + Vanilla JS** | Léger, autonome, sans compilation Node.js requise en production |
| **Impression POS** | **CSS Print & ESC/P Ready** | Feuilles de style calibrées au millimètre pour rouleaux thermiques 80mm |
| **Thème Visuel** | **CSS3 Dark/Light Mode** | Charte graphique Diarra Transport (Or impérial, Vert forêt et Bleu nuit) |

---

## 💻 Installation Locale en 3 Minutes

### 1. Prérequis
- **PHP** : `^8.2` ou `^8.3` (extensions : `pdo_sqlite`, `pdo_mysql`, `mbstring`, `fileinfo`, `gd`, `openssl`)
- **Composer** : `^2.5`
- **Git**

### 2. Cloner et Installer les Dépendances

```bash
# Cloner le dépôt officiel
git clone https://github.com/Alassane9869/bagage.git
cd bagage

# Installer les dépendances PHP
composer install
```

### 3. Configurer l'Environnement Local

```bash
# Dupliquer le fichier d'environnement
cp .env.example .env

# Générer la clé de sécurité Laravel
php artisan key:generate
```

Pour utiliser **SQLite** en local sans installer de serveur MySQL :
```env
DB_CONNECTION=sqlite
```
*(Le fichier `database/database.sqlite` est créé automatiquement s'il n'existe pas).*

### 4. Initialiser la Base de Données & le Stockage

```bash
# Exécuter les migrations et créer les utilisateurs de démonstration
php artisan migrate:fresh --seed

# Générer le lien symbolique vers les logos et documents
php artisan storage:link
```

### 5. Démarrer le Serveur Local

```bash
php artisan serve
```
L'application est immédiatement accessible sur **`http://localhost:8000`**.

---

## 🌐 Déploiement en Production (Serveur o2switch / cPanel)

L'application a été entièrement pré-configurée pour un hébergement mutualisé ou dédié **o2switch / cPanel** :
- **Sous-domaine officiel :** `https://diarra-bagage.diawando-tech.com`
- **Base MySQL de production :** `sc2vuxe8870_bagage`
- **Utilisateur MySQL :** `sc2vuxe8870_bagageuser`

### Arborescence sur le Serveur cPanel :
```text
/home/sc2vuxe8870/
├── repositories/
│   └── bagage/                          # Clone Git (code source suivi par Git)
└── diarra-bagage.diawando-tech.com/     # Répertoire public du sous-domaine
    ├── public/                          # Racine web Laravel
    ├── storage/                         # Fichiers publics (logos, reçus)
    └── .env                             # Configuration de production active
```

---

### 🚀 Méthode 1 : Déploiement 100% Automatisé via cPanel Git™ (`.cpanel.yml`)

Le projet inclut un fichier de déploiement automatique [`.cpanel.yml`](.cpanel.yml) prêt à l'emploi.

1. Rendez-vous dans **cPanel > Git™ Version Control**.
2. Cliquez sur **Gérer** (Manage) en face du dépôt `bagage`.
3. Allez dans l'onglet **Déploiement** (Deployment).
4. Cliquez sur le bouton bleu **Déployer la validation HEAD** (Deploy HEAD Commit).

Le fichier `.cpanel.yml` réalise automatiquement en tâche de fond :
- Synchronisation `rsync` propre des fichiers vers `/home/sc2vuxe8870/diarra-bagage.diawando-tech.com/` (en excluant `.git`).
- Application des permissions strictes `0755` sur les dossiers et `0777` sur `storage` et `bootstrap/cache`.
- Copie automatique de `.env.production` vers `.env`.
- Exécution de `composer install --no-dev --optimize-autoloader`.
- Exécution des migrations MySQL et seeders (`php artisan migrate --force --seed`).
- Création du lien symbolique `php artisan storage:link`.
- Nettoyage et mise en cache des routes, de la configuration et des vues Blade.

---

### ⚡ Méthode 2 : Déploiement en 1 Clic via le Script Web (`deploy_o2switch.php`)

Si vous préférez initialiser le serveur directement depuis votre navigateur sans ouvrir de terminal :

1. Ouvrez dans votre navigateur l'adresse :
   ```text
   https://diarra-bagage.diawando-tech.com/deploy_o2switch.php
   ```
2. Le script exécute en direct toutes les étapes d'installation et affiche un tableau de bord vert avec les statuts.
3. Cliquez sur le bouton vert **Accéder à Diarra Bagages** pour vous connecter.

---

### 💻 Méthode 3 : Ligne de Commande Terminal cPanel / SSH

Pour déployer ou mettre à jour manuellement via le terminal cPanel ou une connexion SSH :

```bash
cd /home/sc2vuxe8870/repositories/bagage && \
git pull origin main && \
/bin/rsync -a --chmod=D755,F644 --exclude='.git' --exclude='node_modules' --exclude='.env' /home/sc2vuxe8870/repositories/bagage/ /home/sc2vuxe8870/diarra-bagage.diawando-tech.com/ && \
cd /home/sc2vuxe8870/diarra-bagage.diawando-tech.com && \
php artisan optimize:clear && \
php artisan optimize
```

---

### 🗄️ Import Direct de la Base de Données (Optionnel)

Un dump MySQL complet et testé est fourni dans le dépôt :
- Fichier : [`database/sc2vuxe8870_bagage.sql`](database/sc2vuxe8870_bagage.sql)
- Dans **phpMyAdmin**, sélectionnez simplement la base `sc2vuxe8870_bagage` et cliquez sur **Importer**.

---

## 🔑 Identifiants d'Accès par Défaut (Seeders)

| Rôle Métier | Identifiant (Login) | Adresse Email | Mot de passe | Périmètre d'Accès |
| :--- | :--- | :--- | :--- | :--- |
| **Administrateur** | `admin` | `admin@diarratransport.ml` | `admin123` | Accès global : Dashboard, Caisses globales, Utilisateurs, Paramètres |
| **Agent Guichet** | `yacou.diakite` | `yacou.diakite@diarratransport.ml` | `agent123` | Enregistrement bagages, Impression tickets, Ma Caisse, Embarquements |

> 🔒 **Recommandation de sécurité :** Dès le premier déploiement en production, modifiez impérativement ces mots de passe dans la section *Mon Profil*.

---

## 🧪 Tests Automatisés & Assurance Qualité

Le projet dispose d'une suite de tests automatisés (Unitaires et Fonctionnels) validant l'ensemble des parcours critiques (authentification double login/email, calcul des tarifs, autocomplétion, soutes et caisses) :

```bash
php artisan test
```

Résultats d'exécution :
```text
   PASS  Tests\Unit\ExampleTest
  ✓ that true is true

   PASS  Tests\Feature\AuthTest
  ✓ user can login with login username
  ✓ user can login with email address
  ✓ user cannot login with invalid password

   PASS  Tests\Feature\BagageTest
  ✓ authenticated user can view bagages index
  ✓ authenticated agent can create bagage
  ✓ client search autocompletion

   PASS  Tests\Feature\CaisseAndDashboardTest
  ✓ authenticated agent can view ma caisse
  ✓ authenticated agent can view caisse par depart
  ✓ administrator can view caisse global
  ✓ administrator can view dashboard

   PASS  Tests\Feature\ExampleTest
  ✓ the application returns a successful response

  Tests:    12 passed (28 assertions)
  Duration: 2.15s
```

---

## 📁 Arborescence Complète du Projet

```text
diarra_bagages/
├── .cpanel.yml                  # Automatisation du déploiement cPanel Git Version Control
├── .env.production              # Gabarit pré-configuré pour la production o2switch
├── .htaccess                    # Pont de redirection racine vers /public et protection des secrets
├── deploy_o2switch.php          # Script web d'initialisation et de maintenance en 1 clic
├── index.php                    # Pont d'amorçage automatique de l'application Laravel
├── app/
│   ├── Http/
│   │   ├── Controllers/         # BagageController, CaisseController, EmbarquementController...
│   │   └── Middleware/          # Contrôle RBAC, journalisation des audits et connexions
│   ├── Models/                  # Eloquent : Bagage, Caisse, Client, Voyage, User, Societe...
│   └── Services/                # Logique métier pure : BagageService, CaisseService, QrCodeService
├── database/
│   ├── migrations/              # 20 migrations avec compatibilité native MySQL et SQLite
│   ├── seeders/                 # DatabaseSeeder (utilisateurs, agences, tarifs de référence)
│   └── sc2vuxe8870_bagage.sql   # Dump MySQL prêt à l'emploi pour import phpMyAdmin
├── public/
│   ├── favicon.ico              # Favicon multi-tailles officiel Diarra Transport
│   ├── favicon.png              # Favicon HD 32x32
│   └── storage/                 # Lien symbolique vers le stockage public (logos, pièces)
├── resources/
│   └── views/
│       ├── admin/               # Gestion utilisateurs, profils, journaux d'audit
│       ├── auth/                # Authentification sécurisée (login ou email)
│       ├── bagages/             # Formulaires guichet et gabarits de tickets thermiques 80mm
│       ├── caisse/              # Ma Caisse, Caisse par départ et État global
│       ├── dashboard/           # Tableaux de bord analytiques et graphiques
│       └── layouts/             # Gabarit maître avec support thèmes Jour / Nuit
└── tests/                       # Suite de tests d'intégration et unitaires PHPUnit
```

---

## 🛡️ Sécurité & Conformité Réglementaire

- **Protection CSRF active :** Toutes les requêtes `POST`, `PUT` et `DELETE` sont protégées par des jetons de session cryptographiques.
- **Verrouillage transactionnel anti-collision :** L'attribution des numéros de bagage s'effectue via un verrou `DB::transaction()` pour garantir l'unicité stricte sous forte charge.
- **Double authentification supportée :** Les agents peuvent se connecter indifféremment via leur identifiant court (ex: `yacou.diakite`) ou leur email officiel.
- **Stockage chiffré des mots de passe :** Hashage industriel via l'algorithme `bcrypt` (facteur de coût 12).
- **Protection des fichiers sensibles :** Masquage automatique des fichiers `.env`, `.git`, `.yml` et `.sqlite` par le fichier `.htaccess`.

---

## 🏛️ Mentions Officielles & Droits d'Auteur

&copy; **2026 Diarra Transport**. Tous droits réservés.  
*Plateforme interne de gestion logistique et financière des bagages accompagnés.*  
Direction Générale : Bamako, République du Mali.
