<?php
/**
 * Configuração de Conexão com o Banco de Dados (PDO Singleton)
 */

if (!defined('DB_HOST')) define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_PORT')) define('DB_PORT', $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'cidade_transparente');
if (!defined('DB_USER')) define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'cidade_user');
if (!defined('DB_PASS')) define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'cidade123');

class Database {
    private static ?PDO $instancia = null;
    private function __construct() {}
    private function __clone() {}

    public static function getInstance(): PDO {
        if (self::$instancia === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $opcoes = [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ];
                self::$instancia = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
            } catch (PDOException $e) {
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
