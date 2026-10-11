# 🧳 Diarra Transport – Plateforme de Gestion des Bagages & Caisses

<p align="center">
  <img src="storage/app/public/logos/logo_societe_1782763691.png" width="130" alt="Logo Diarra Transport" style="border-radius: 50%;">
</p>

<p align="center">
  <strong>Solution complète de gestion, traçabilité et billetterie thermique des bagages pour compagnies de transport de voyageurs.</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel 12">
  <img src="https://img.shields.io/badge/PHP-8.2%20%7C%208.3-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Base_de_Donn%C3%A9es-MySQL%20%7C%20SQLite-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="Database">
  <img src="https://img.shields.io/badge/Tests-12%20Valid%C3%A9s-success?style=for-the-badge&logo=phpunit&logoColor=white" alt="Tests 100% Passed">
  <img src="https://img.shields.io/badge/Licence-Propri%C3%A9taire-gold?style=for-the-badge" alt="Licence">
</p>

---

## 📌 Présentation Générale

**Diarra Transport Bagages** est une application web conçue sur mesure pour digitaliser et sécuriser l'ensemble de la chaîne logistique des bagages accompagnés au sein du réseau d'agences de Diarra Transport (Bamako, Kayes, Kéniéba, Sikasso, Mopti, Dakar, Abidjan, etc.).

Le système permet aux agents de guichet d'enregistrer rapidement les colis, d'éditer des reçus thermiques standardisés 80mm dotés de QR Codes d'authentification, de suivre le chargement par bus et de gérer les clôtures de caisse journalières avec un contrôle rigoureux pour la direction.

---

## 🚀 Fonctionnalités Clés

### 1. 🧳 Enregistrement Rapide des Bagages
* **Attribution automatique d'identifiant unique :** Format `BG-YYYY-NNNNN` généré avec verrouillage transactionnel anti-collision.
* **Autocomplétion intelligente des clients :** Recherche instantanée des coordonnées voyageurs par nom, prénom ou téléphone.
* **Calcul automatique des tarifs :** Prise en compte des montants bruts, remises commerciales et montant net à percevoir.
* **Calcul de rendu monnaie en temps réel :** Calculateur dynamique de monnaie au guichet pour éviter les erreurs de caisse.
* **Gestion multi-colis :** Découpage automatique des bagages en sous-colis individuels (`BG-2026-00001-C1`, `C2`, etc.).

### 2. 🖨️ Billetterie & Reçus Thermiques 80mm
* **Reçu client complet :** En-tête officiel, itinéraire, programme, détails colis, montant, avis légal et talon de contrôle détachable.
* **Étiquettes de colis individuelles :** Génération d'étiquettes adhésives dédiées par colis avec QR code scannable.
* **QR Code SVG haute précision :** Intégration d'un QR code vectoriel résistant aux contraintes des imprimantes thermiques POS (Epson, Xprinter, etc.).
* **Gestion du logo haute résolution :** Prise en charge du logo officiel de la compagnie avec affichage dynamique.

### 3. 🚌 Embarquement & Contrôle des Bus
* **Affectation par voyage et par bus :** Filtrage en temps réel des bagages prêts pour le chargement.
* **Pointage au bus :** Validation des colis chargés à bord avec horodatage et affectation du numéro de bus (`BM-001`, `SK-001`, etc.).
* **Traçabilité des statuts :** Suivi chronologique de l'état du bagage (`enregistre` ➔ `embarque`).

### 4. 💵 Gestion des Caisses & Clôtures Journalières
* **Ma Caisse (Vue Agent) :** Récapitulatif instantané des encaissements du jour pour l'agent connecté avec détail transactionnel.
* **Caisse par Départ :** Ventilation des recettes et du nombre de bagages par programme et ligne de bus.
* **État Global (Vue Direction / Admin) :** Tableau de bord consolidé pour l'administrateur avec totaux généraux, performances par agent et historique filtrable par date.

### 5. 📈 Tableau de Bord & Statistiques
* **Indicateurs financiers en temps réel :** Chiffre d'affaires, total des bagages enregistrés, volume embarqué et taux d'embarquement global.
* **Évolution mensuelle des recettes :** Graphiques et tableaux comparatifs multi-mois.
* **Top 10 des destinations :** Répartition du trafic par ville d'arrivée pour optimiser les capacités des bus.

### 6. 🛡️ Sécurité, Audit & Administration
* **Gestion des Rôles & Permissions :**
  * `Administrateur` : Accès total (statistiques, caisse globale, administration des utilisateurs et paramètres société).
  * `Agent Bagages` : Enregistrement, impression reçus, embarquement et sa propre caisse.
  * `Contrôleur` : Consultation des registres et vérification des bagages.
* **Journalisation complète des audits (Activity Logs) :** Enregistrement de chaque action (création, modification, suppression, montant, IP de l'agent).
* **Historique des connexions (Login History) :** Traçabilité des accès utilisateurs (IP, navigateur, date et heure).

---

## 🛠️ Stack Technique

| Composant | Technologie | Description |
| :--- | :--- | :--- |
| **Framework Backend** | [Laravel 12.x](https://laravel.com) | Dernière version stable du framework PHP moderne |
| **Langage** | PHP 8.2 / 8.3 | Typage strict, performances maximales |
| **Base de Données** | MySQL 8.0+ / SQLite 3 | Support double moteur avec requêtes portables et index optimisés |
| **Génération QR Code** | SimpleSoftwareIO QrCode | Rendu vectoriel SVG natif et haute performance |
| **Frontend** | Blade + Vanilla JS + Bootstrap 5.3 | Interface réactive, légère et adaptée aux connexions internet lentes |
| **Styles & Thème** | CSS3 Dark/Day Mode | Thème sombre haut de gamme aux couleurs Diarra Transport (Or, Vert & Bleu) |

---

## 💻 Installation & Configuration Locale

### 1. Prérequis Système
* PHP >= 8.2 avec les extensions : `pdo_mysql`, `pdo_sqlite`, `mbstring`, `openssl`, `xml`, `curl`, `fileinfo`, `gd`.
* [Composer](https://getcomposer.org)
* Git

### 2. Cloner le Projet
```bash
git clone https://github.com/Alassane9869/bagage.git
cd bagage
```

### 3. Installer les Dépendances PHP
```bash
composer install
```

### 4. Configurer l'Environnement (`.env`)
Copiez le fichier d'exemple :
```bash
cp .env.example .env
```
Générez la clé de sécurité de l'application :
```bash
php artisan key:generate
```

Pour utiliser **SQLite** en local, assurez-vous d'avoir :
```env
DB_CONNECTION=sqlite
```

### 5. Lancer les Migrations et les Données de Test
```bash
php artisan migrate:fresh --seed
```

### 6. Créer le Lien de Stockage des Fichiers (Logos & Reçus)
```bash
php artisan storage:link
```

### 7. Démarrer le Serveur Local
```bash
php artisan serve
```
L'application est accessible sur : `http://localhost:8000`

---

## 🌐 Déploiement en Production (cPanel / VPS)

### Configuration pour Hébergement Web (Ex: cPanel)
1. **Racine du sous-domaine (Document Root) :**
   * Configurer le sous-domaine pour pointer vers le sous-dossier **/public** :
     `votre-sous-domaine/public`
2. **Variables d'environnement de production (`.env`) :**
   ```env
   APP_NAME="Diarra Transport"
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://diarra-bagage.diawando-tech.com

   DB_CONNECTION=mysql
   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=sc2vuxe8870_bagage
   DB_USERNAME=sc2vuxe8870_bagageuser
   DB_PASSWORD="votre_mot_de_passe"
   ```
3. **Import de la Base de Données :**
   * Importez directement le fichier prêt à l'emploi [`database/sc2vuxe8870_bagage.sql`](database/sc2vuxe8870_bagage.sql) dans **phpMyAdmin**.
4. **Optimisation des performances :**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   php artisan storage:link
   ```

---

## 🔑 Identifiants de Démonstration (Seeders)

| Rôle | Identifiant / Login | Adresse Email | Mot de passe |
| :--- | :--- | :--- | :--- |
| **Administrateur** | `admin` | `admin@diarratransport.ml` | `admin123` |
| **Agent Bagages** | `yacou.diakite` | `yacou.diakite@diarratransport.ml` | `agent123` |

> ⚠️ **Sécurité :** Lors du premier déploiement en production, changez impérativement ces mots de passe dans la section *Mon Profil*.

---

## 🧪 Tests Automatisés

Le projet inclut une suite de tests automatisés couvrant l'authentification double (login ou email), l'enregistrement des bagages, le moteur de caisse et le tableau de bord :

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
Duration: 2.2s
```

---

## 📁 Arborescence Principale du Projet

```text
├── app/
│   ├── Http/
│   │   ├── Controllers/         # Contrôleurs métier (Bagage, Caisse, Embarquement, etc.)
│   │   └── Middleware/          # Contrôle des rôles et traçabilité des activités
│   ├── Models/                  # Modèles Eloquent (Bagage, Caisse, Client, Voyage, User...)
│   └── Services/                # Logique métier pure (BagageService, CaisseService, QrCodeService)
├── database/
│   ├── migrations/              # 20 migrations avec compatibilité MySQL & SQLite
│   ├── seeders/                 # Données de démonstration et rôles applicatifs
│   └── sc2vuxe8870_bagage.sql   # Dump SQL complet pour import direct phpMyAdmin
├── public/
│   ├── favicon.ico              # Favicon multi-tailles officiel Diarra Transport
│   ├── favicon.png              # Favicon HD 32x32
│   └── storage/                 # Lien symbolique vers le stockage public
├── resources/
│   └── views/
│       ├── admin/               # Gestion des utilisateurs, profils et paramètres société
│       ├── auth/                # Interface d'authentification sécurisée
│       ├── bagages/             # Formulaire d'enregistrement et partials d'impression 80mm
│       ├── caisse/              # Vues Ma Caisse, Caisse par départ et État global
│       ├── dashboard/           # Tableau de bord analytique et statistiques
│       └── layouts/             # Layout principal avec support Day/Night Mode
└── tests/                       # Suite de tests fonctionnels et unitaires PHPUnit
```

---

## 📄 Licence & Droits d'Auteur

© 2026 **Diarra Transport**. Tous droits réservés.  
Système développé pour l'exploitation interne et la gestion des flux de bagages de la compagnie Diarra Transport.
