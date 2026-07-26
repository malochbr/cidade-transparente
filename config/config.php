<?php
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: text/html; charset=UTF-8');

define('APP_NAME', 'Cidade Transparente');

// APP_URL dinâmico: usa variável de ambiente (Railway) ou detecta automaticamente
$appUrl = getenv('APP_URL') ?: (
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http')
    . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost')
);
define('APP_URL', rtrim($appUrl, '/'));

define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'mp4']);

require_once __DIR__ . '/db.php';

function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    return $_SESSION['user'] ?? null;
}

function getUserRole(): ?string {
    return $_SESSION['perfil'] ?? null;
}

function flash(string $mensagem, string $tipo = 'info'): void {
    $_SESSION['flash'] = [
        'message' => $mensagem,
        'type' => $tipo
    ];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $mensagemFlash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $mensagemFlash;
    }
    return null;
}

function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

function verify_csrf(): bool {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tokenSeguranca = $_POST['csrf_token'] ?? '';
        if (empty($tokenSeguranca) || !hash_equals($_SESSION['csrf_token'] ?? '', $tokenSeguranca)) {
            flash('Sessão expirada ou requisição inválida (CSRF). Tente novamente.', 'error');
            return false;
        }
    }
    return true;
}

function sanitize(string $dados): string {
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}
