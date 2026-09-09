<?php

declare(strict_types=1);

/**
 * Front controller : point d'entree unique de l'application.
 * Toutes les requetes HTTP passent par ce fichier, qui delegue au Router.
 */

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Container;
use App\Core\Database;
use App\Core\Router;
use App\Interfaces\CategorieRepositoryInterface;
use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ProduitRepositoryInterface;
use App\Interfaces\UtilisateurRepositoryInterface;
use App\Repositories\CategorieRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ProduitRepository;
use App\Repositories\UtilisateurRepository;

session_start();

// --- Conteneur d'injection de dependances --------------------------------
$container = new Container();

// Liaison des interfaces vers leurs implementations (PDO partage).
$container->bind(ProduitRepositoryInterface::class, function (Container $c) {
    return new ProduitRepository(Database::getInstance()->getConnection());
});
$container->bind(CategorieRepositoryInterface::class, function (Container $c) {
    return new CategorieRepository(Database::getInstance()->getConnection());
});
$container->bind(ClientRepositoryInterface::class, function (Container $c) {
    return new ClientRepository(Database::getInstance()->getConnection());
});
$container->bind(UtilisateurRepositoryInterface::class, function (Container $c) {
    return new UtilisateurRepository(Database::getInstance()->getConnection());
});

$router = new Router($container);

require __DIR__ . '/../routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
