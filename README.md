# Saveur 221 — Module B (PHP Web)

Application web destinée aux **visiteurs, clients, gérants et administrateurs**
du restaurant. Partage la même base de données MySQL que le Module A (Java Console).

## Prérequis

- PHP 8.1+
- Composer
- MySQL (ex: via XAMPP)

## Installation

1. Installe les dépendances (autoload) :
   ```bash
   composer install
   ```
   (Pour l'instant le projet n'a aucune dépendance externe, seul l'autoload PSR-4 est généré.)

2. Si tu n'as pas encore la base de données, exécute `database/script.sql` dans
   phpMyAdmin ou en ligne de commande MySQL. Ça crée la base **`restaurant_saveur221`**
   partagée avec le Module Java.

3. Vérifie les identifiants MySQL dans `config/config.php` si besoin (par défaut : `root` sans mot de passe, comme sur XAMPP).

## Lancer le projet

Avec le serveur PHP intégré, depuis la racine du projet :

```bash
php -S localhost:8000 -t public
```

Puis ouvre `http://localhost:8000` dans le navigateur.

Si tu utilises XAMPP/Apache à la place, place le dossier `php/` dans `htdocs/`
et adapte `config.php` → `app.base_path` avec le chemin complet
(ex: `/saveur221-php/public`).

## Structure du projet

```
php/
├── app/
│   ├── Controllers/    # logique de traitement des requêtes
│   ├── Models/          # accès aux données (PDO)
│   ├── Views/            # templates HTML/PHP
│   ├── Core/             # Router, Database (infrastructure)
│   ├── Services/         # règles métier
│   └── Exceptions/       # exceptions métier
├── config/
│   └── config.php        # configuration BDD, chemin de base
├── database/
│   └── script.sql         # schéma partagé avec le Module Java
├── public/
│   ├── index.php           # front controller (point d'entrée unique)
│   └── assets/              # CSS, JS, images
├── routes/
│   └── web.php              # déclaration des routes
├── vendor/                   # généré par Composer
└── composer.json
```

## Architecture

Pattern **front-controller** : toutes les requêtes passent par `public/index.php`,
qui délègue au `Router` (`app/Core/Router.php`). Le routeur associe une route
(méthode HTTP + chemin) à une méthode d'un Controller, déclarée dans `routes/web.php`.

## État d'avancement

- [x] Squelette du projet (autoload Composer, front controller, routeur)
- [x] Connexion à la base de données (`App\Core\Database`)
- [ ] Authentification (client + gérant/admin)
- [ ] Catalogue produits (partie publique)
- [ ] Panier et commandes (client)
- [ ] Espace gérant
- [ ] Espace administrateur
