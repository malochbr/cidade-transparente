<?php
/**
 * Controller PerfilController
 * 
 * Gerencia as configurações de perfil do cidadão: atualização de dados cadastrais
 * (nome, telefone) e alteração de senha.
 */

require_once __DIR__ . '/../models/Usuario.php';

class PerfilController {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario(Database::getInstance());
    }

    /**
     * Exibe o menu principal do perfil do cidadão.
     */
    public function index(): void {
        if (!isLoggedIn()) redirect('?page=auth/login');
        $tituloPagina = 'Meu perfil';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/perfil/index.php';
    }

    /**
     * Exibe e processa o formulário de atualização de dados pessoais (nome e telefone).
     */
    public function meusDados(): void {
        if (!isLoggedIn()) redirect('?page=auth/login');

        $usuario = $this->usuarioModel->findById($_SESSION['user_id']);
        $erros = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) redirect('?page=perfil/meus-dados');

            $nome = $_POST['nome'] ?? '';
            $telefone = $_POST['telefone'] ?? '';

            if (empty($nome)) {
                $erros[] = 'O nome não pode estar em branco.';
            }

            if (empty($erros)) {
                $this->usuarioModel->updatePerfil($_SESSION['user_id'], [
                    'nome' => $nome,
                    'telefone' => $telefone
                ]);

                // Atualiza as informações do usuário armazenadas na sessão ativa
                $_SESSION['user'] = $this->usuarioModel->findById($_SESSION['user_id']);

                flash('Seus dados foram atualizados com sucesso!', 'success');
                redirect('?page=perfil');
            }
        }

        $tituloPagina = 'Meus Dados';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/perfil/meus-dados.php';
    }

    /**
     * Exibe e processa o formulário de alteração de senha de acesso.
     */
    public function alterarSenha(): void {
        if (!isLoggedIn()) redirect('?page=auth/login');

        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) redirect('?page=perfil/alterar-senha');

            $senhaAtual = $_POST['senha_atual'] ?? '';
            $novaSenha = $_POST['nova_senha'] ?? '';
            $confirmarSenha = $_POST['confirmar_senha'] ?? '';

            $usuario = $this->usuarioModel->findById($_SESSION['user_id']);

            // Valida se a senha atual digitada confere com o hash salvo no banco
            if (!password_verify($senhaAtual, $usuario['senha'])) {
                $erros[] = 'A senha atual está incorreta.';
            }

            // Exige no mínimo 8 caracteres com 1 letra maiúscula e 1 número
            if (strlen($novaSenha) < 8 || !preg_match('/[A-Z]/', $novaSenha) || !preg_match('/[0-9]/', $novaSenha)) {
                $erros[] = 'A nova senha deve ter no mínimo 8 caracteres, com pelo menos 1 maiúscula e 1 número.';
            }

            if ($novaSenha !== $confirmarSenha) {
                $erros[] = 'A confirmação de senha não confere.';
            }

            if (empty($erros)) {
                $this->usuarioModel->updateSenha($_SESSION['user_id'], $novaSenha);
                flash('Sua senha foi alterada com sucesso!', 'success');
                redirect('?page=perfil');
            }
        }

        $tituloPagina = 'Alterar Senha';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/perfil/alterar-senha.php';
    }
}
