<?php

namespace App\Database;

use PDO;
use PDOException;
use Dotenv\Dotenv;

// Load Composer's autoloader
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
$dotenv->safeLoad();

class Database
{
    private string $host;
    private string $username;
    private string $password;
    private string $database;

    public function __construct()
    {
        $this->host = $_ENV['DB_HOST'];
        $this->username = $_ENV['DB_USERNAME'];
        $this->password = $_ENV['DB_PASSWORD'];
        $this->database = $_ENV['DB_DATABASE'];
    }

    // Return the PDO connection
    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->database};charset=utf8mb4";

        try {
            $connection = new PDO(
                $dsn,
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );

            return $connection;

        } catch (PDOException $e) {
            throw new \RuntimeException(
                'Database connection failed.',
                0,
                $e
            );
        }
    }
}

?>
