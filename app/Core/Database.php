<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Connexion PDO unique (singleton) a la base de donnees MySQL.
 * Equivalent PHP du DatabaseConfig.java du Module A : les deux pointent
 * vers la meme base "restaurant_saveur221".
 */
class Database
{
    private static ?PDO $connection = null;

    private function __construct()
    {
        // Empeche l'instanciation directe : classe utilitaire
    }

    public static function getConnection(): PDO
    {
        if (self::$connection === null) {
            $config = require __DIR__ . '/../../config/config.php';
            $db = $config['db'];

            $dsn = sprintf(
                'mysql:host=%s;port=%d;dbname=%s;charset=%s',
                $db['host'],
                $db['port'],
                $db['name'],
                $db['charset']
            );

            try {
                self::$connection = new PDO($dsn, $db['user'], $db['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                // On ne renvoie jamais le message PDO brut au navigateur
                // (peut contenir des identifiants de connexion).
                throw new PDOException('Connexion a la base de donnees impossible.', (int) $e->getCode());
            }
        }

        return self::$connection;
    }
}
