<?php
/**
 * Configuração de Conexão com o Banco de Dados (PDO Singleton)
 * 
 * Esta classe garante que exista apenas uma única conexão aberta com o banco
 * de dados MySQL durante toda a execução da requisição (Padrão Singleton).
 */

if (!defined('DB_HOST')) define('DB_HOST', $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?: 'cidade_transparente');
if (!defined('DB_USER')) define('DB_USER', $_ENV['DB_USER'] ?? getenv('DB_USER') ?: 'cidade_user');
if (!defined('DB_PASS')) define('DB_PASS', $_ENV['DB_PASS'] ?? getenv('DB_PASS') ?: 'cidade123');

class Database {
    // Instância única da conexão PDO armazenada estaticamente
    private static ?PDO $instancia = null;

    // Construtor e clone privados impedem a criação direta do objeto fora da classe
    private function __construct() {}
    private function __clone() {}

    /**
     * Retorna a instância única da conexão PDO com o banco de dados MySQL.
     * Se a conexão ainda não existir, cria e configura uma nova.
     *
     * @return PDO Instância ativa da conexão com o banco de dados
     */
    public static function getInstance(): PDO {
        if (self::$instancia === null) {
            try {
                // String de conexão DSN especificando host, nome do banco e charset utf8mb4
                $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
                $opcoes = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                ];
                self::$instancia = new PDO($dsn, DB_USER, DB_PASS, $opcoes);
            } catch (PDOException $e) {
                // Interrompe o script e exibe mensagem amigável caso a conexão falhe
                die("Erro na conexão com o banco de dados: " . $e->getMessage());
            }
        }
        return self::$instancia;
    }
}
