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
│   ├── Controllers/       # logique de traitement des requêtes
│   ├── Models/            # objets valeur (Produit, Categorie) avec fromRow()
│   ├── Repositories/      # accès aux données PDO (hydratation des modèles)
│   ├── Interfaces/        # contrats des repositories
│   ├── Services/          # règles métier
│   ├── Views/             # templates HTML/PHP
│   ├── Middleware/        # auth, guest, role
│   ├── Core/              # Router, Container, Database, Controller
│   └── Exceptions/        # exceptions métier (AppException, NotFound, etc.)
├── config/
│   └── config.php         # configuration BDD
├── database/
│   └── script.sql         # schéma partagé avec le Module Java
├── public/
│   ├── index.php           # front controller (point d'entrée unique)
│   └── assets/             # CSS, JS, images
├── routes/
│   └── web.php             # déclaration des routes
├── vendor/                 # généré par Composer
└── composer.json
```

## Architecture

Pattern **front-controller** : toutes les requêtes passent par `public/index.php`
qui instancie le **Container** (IoC) et le **Router** (`app/Core/Router.php`).

- Les controllers sont résolus par injection de dépendances (Container).
- Les interfaces des repositories sont liées à leurs implémentations PDO.
- Le routeur supporte les middlewares et la vérification CSRF.
- Les modèles sont des objets valeur immuables hydratés via `fromRow()`.
- Les vues d'erreur (404, 403) sont dédiées.

## État d'avancement

- [x] Squelette du projet (autoload Composer, front controller, routeur)
- [x] Connexion à la base de données (`App\Core\Database`)
- [x] Architecture POO (Container IoC, Repositories, Middleware, Exceptions)
- [x] Catalogue produits (partie publique) : liste, recherche, filtre, pagination, détail
- [ ] Authentification (client + gérant/admin)
- [ ] Panier et commandes (client)
- [ ] Espace gérant
- [ ] Espace administrateur
