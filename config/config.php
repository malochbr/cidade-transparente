<?php
ob_start();
/**
 * Configurações Globais e Funções Auxiliares (Helpers)
 *
 * Este arquivo define constantes globais do sistema e funções utilitárias
 * utilizadas em toda a aplicação (redirecionamento, sessão, mensagens e CSRF).
 */

if (session_status() === PHP_SESSION_NONE) {
    // Inicia a sessão PHP caso ainda não esteja ativa
    session_start();
}
header('Content-Type: text/html; charset=UTF-8');

// Definindo constantes gerais do sistema
define('APP_NAME', 'Cidade Transparente');
define('APP_URL', 'http://localhost:8080');
define('UPLOAD_PATH', __DIR__ . '/../public/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // Limite de 5MB por arquivo
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'mp4']);

require_once __DIR__ . '/db.php';

/**
 * Redireciona o navegador para uma URL específica e encerra o script.
 *
 * @param string $url URL de destino para o redirecionamento
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Verifica se existe um cidadão ou administrador logado na sessão ativa.
 *
 * @return bool Retorna true se houver usuário autenticado, false caso contrário
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Retorna os dados do usuário atualmente logado na sessão.
 *
 * @return array|null Array com os dados do usuário ou null se não estiver logado
 */
function getCurrentUser(): ?array {
    if (!isLoggedIn()) return null;
    return $_SESSION['user'] ?? null;
}

/**
 * Retorna o perfil de acesso do usuário logado (ex: 'cidadao', 'administrador').
 *
 * @return string|null Tipo de perfil ou null
 */
function getUserRole(): ?string {
    return $_SESSION['perfil'] ?? null;
}

/**
 * Armazena uma mensagem temporária na sessão para ser exibida após um redirecionamento.
 *
 * @param string $mensagem Texto da mensagem flash
 * @param string $tipo     Tipo visual da mensagem (info, success, warning, error)
 */
function flash(string $mensagem, string $tipo = 'info'): void {
    $_SESSION['flash'] = [
        'message' => $mensagem,
        'type' => $tipo
    ];
}

/**
 * Recupera e remove a mensagem temporária (flash) da sessão.
 *
 * @return array|null Dados da mensagem flash ou null se não existir
 */
function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $mensagemFlash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $mensagemFlash;
    }
    return null;
}

/**
 * Gera ou recupera o token de segurança CSRF único armazenado na sessão.
 *
 * @return string Hash binário de 32 bytes em formato hexadecimal
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Gera o código HTML de um campo hidden contendo o token CSRF para formulários POST.
 *
 * @return string Tag input hidden com o token CSRF
 */
function csrf_field(): string {
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/**
 * Valida se o token CSRF enviado via formulário POST confere com o token da sessão.
 *
 * @return bool Retorna true se for válido, false se for inválido ou ausente
 */
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

/**
 * Sanitiza textos para prevenção contra ataques de XSS (Cross-Site Scripting).
 *
 * @param string $dados Texto original fornecido pelo usuário
 * @return string Texto convertido em entidades HTML seguras
 */
function sanitize(string $dados): string {
    return htmlspecialchars(trim($dados), ENT_QUOTES, 'UTF-8');
}
