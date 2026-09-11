<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

/**
 * Connexion PDO en singleton. Meme base que le Module A (Java) :
 * ne change pas son nom sans le repercuter dans DatabaseConfig.java.
 *
 * FETCH_OBJ (et non FETCH_ASSOC) : chaque ligne de resultat devient un
 * objet stdClass, accessible avec la fleche (ex: $produit->libelle) et
 * non des crochets, plus coherent avec une approche orientee objet.
 */
class Database
{
    private static ?Database $instance = null;
    private PDO $conn;

    private function __construct()
    {
        $config = require __DIR__ . '/../../config/config.php';
        // $db = $config['db'];

        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $_ENV['DB_HOST'] ?? 'localhost',
                $_ENV['DB_PORT'] ?? '3306',
                $_ENV['DB_NAME'] ?? 'restaurant_saveur221',
                $_ENV['DB_CHARSET'] ?? 'utf8mb4'
                // $db['host'],
                // $db['port'],
                // $db['name'],
                // $db['charset']
            );

            $this->conn = new PDO($dsn, $_ENV['DB_USER'] ?? 'root', $_ENV['DB_PASSWORD'] ?? '', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            die('Connexion a la base de donnees impossible : ' . $e->getMessage());
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->conn;
    }
}
