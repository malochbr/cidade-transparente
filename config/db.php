<?php
/**
 * Configuração de Conexão com o Banco de Dados (PDO Singleton)
 * 
 * Esta classe garante que exista apenas uma única conexão aberta com o banco
 * de dados MySQL durante toda a execução da requisição (Padrão Singleton).
 * Suporta variáveis do Railway (MYSQL_URL, MYSQLHOST, etc), de ambiente genéricas e locais.
 */

// Tenta obter string de conexão completa (ex: mysql://user:pass@host:port/dbname)
$mysqlUrl = $_ENV['MYSQL_URL'] ?? getenv('MYSQL_URL')
    ?: ($_ENV['MYSQLURL'] ?? getenv('MYSQLURL')
    ?: ($_ENV['DATABASE_URL'] ?? getenv('DATABASE_URL') ?: null));

$parsedUrl = [];
if (!empty($mysqlUrl)) {
    $parsedUrl = parse_url($mysqlUrl) ?: [];
}

// Extrai host, porta, banco, usuário e senha com múltiplos níveis de fallback
$dbHost = $parsedUrl['host'] 
    ?? $_ENV['MYSQLHOST'] ?? getenv('MYSQLHOST') 
    ?? $_ENV['DB_HOST'] ?? getenv('DB_HOST') 
    ?: 'localhost';

$dbPort = $parsedUrl['port'] 
    ?? $_ENV['MYSQLPORT'] ?? getenv('MYSQLPORT') 
    ?? $_ENV['DB_PORT'] ?? getenv('DB_PORT') 
    ?: 3306;

$dbName = isset($parsedUrl['path']) ? ltrim($parsedUrl['path'], '/') 
    : ($_ENV['MYSQLDATABASE'] ?? getenv('MYSQLDATABASE') 
    ?? $_ENV['DB_NAME'] ?? getenv('DB_NAME') 
    ?: 'cidade_transparente');

$dbUser = $parsedUrl['user'] 
    ?? $_ENV['MYSQLUSER'] ?? getenv('MYSQLUSER') 
    ?? $_ENV['DB_USER'] ?? getenv('DB_USER') 
    ?: 'cidade_user';

$dbPass = $parsedUrl['pass'] 
    ?? $_ENV['MYSQLPASSWORD'] ?? getenv('MYSQLPASSWORD') 
    ?? $_ENV['DB_PASS'] ?? getenv('DB_PASS') 
    ?: 'cidade123';

if (!defined('DB_HOST')) define('DB_HOST', $dbHost);
if (!defined('DB_PORT')) define('DB_PORT', $dbPort);
if (!defined('DB_NAME')) define('DB_NAME', $dbName);
if (!defined('DB_USER')) define('DB_USER', $dbUser);
if (!defined('DB_PASS')) define('DB_PASS', $dbPass);

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
                // String de conexão DSN especificando host, porta, nome do banco e charset utf8mb4
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
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
