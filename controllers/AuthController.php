<?php
/**
 * Controller AuthController
 * 
 * Gerencia a autenticação, controle de sessões, cadastro de novos cidadãos
 * e logout no sistema Cidade Transparente.
 */

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {
    private Usuario $usuarioModel;

    public function __construct() {
        $this->usuarioModel = new Usuario(Database::getInstance());
    }

    /**
     * Exibe a tela de boas-vindas (splash screen) do aplicativo.
     */
    public function splash(): void {
        // Redireciona o usuário para a página inicial se já estiver logado
        if (isLoggedIn()) {
            redirect('?page=home');
        }
        $tituloPagina = 'Bem-vindo';
        require_once __DIR__ . '/../views/auth/splash.php';
    }

    /**
     * Processa a autenticação de login (verificação de CPF, senha e sessão).
     */
    public function login(): void {
        if (isLoggedIn()) {
            redirect('?page=home');
        }

        $erros = [];
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Proteção contra solicitações forjadas entre sites (CSRF)
            if (!verify_csrf()) {
                redirect('?page=auth/login');
            }

            $cpf = $_POST['cpf'] ?? '';
            $senha = $_POST['senha'] ?? '';

            if (empty($cpf)) {
                $erros[] = 'Informe o seu CPF.';
            }
            if (empty($senha)) {
                $erros[] = 'Informe a sua senha.';
            }

            if (empty($erros)) {
                // Busca os dados do usuário no banco pelo CPF informado
                $usuario = $this->usuarioModel->findByCpf($cpf);

                // Valida a senha comparando com o hash seguro do banco
                if (!$usuario || !password_verify($senha, $usuario['senha'])) {
                    $erros[] = 'CPF ou senha incorretos.';
                } elseif ($usuario['ativo'] == 0) {
                    $erros[] = 'Sua conta está suspensa ou desativada devido ao limite de reputação.';
                } else {
                    // Login bem-sucedido: regenera o ID de sessão para prevenir ataque de fixação de sessão
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $usuario['id'];
                    $_SESSION['user'] = $usuario;
                    $_SESSION['perfil'] = $usuario['perfil'];

                    flash('Bem-vindo(a) de volta, ' . htmlspecialchars($usuario['nome']) . '!', 'success');
                    redirect('?page=home');
                }
            }
        }

        $tituloPagina = 'Entrar';
        require_once __DIR__ . '/../views/auth/login.php';
    }

    /**
     * Processa o formulário de cadastro de novos cidadãos no sistema.
     */
    public function cadastro(): void {
        if (isLoggedIn()) {
            redirect('?page=home');
        }

        $erros = [];
        $dados = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                redirect('?page=auth/cadastro');
            }

            $dados = [
                'nome' => $_POST['nome'] ?? '',
                'cpf' => $_POST['cpf'] ?? '',
                'telefone' => $_POST['telefone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'senha' => $_POST['senha'] ?? '',
                'confirmar_senha' => $_POST['confirmar_senha'] ?? '',
                'lgpd' => isset($_POST['lgpd'])
            ];

            // Validações de preenchimento obrigatório e unicidade
            if (empty($dados['nome'])) {
                $erros['nome'] = 'O nome completo é obrigatório.';
            }
            if (empty($dados['cpf']) || !Usuario::validarCpf($dados['cpf'])) {
                $erros['cpf'] = 'Informe um CPF válido.';
            } elseif ($this->usuarioModel->findByCpf($dados['cpf'])) {
                $erros['cpf'] = 'Este CPF já está cadastrado no sistema.';
            }

            if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                $erros['email'] = 'Informe um e-mail válido.';
            } elseif ($this->usuarioModel->findByEmail($dados['email'])) {
                $erros['email'] = 'Este e-mail já está cadastrado.';
            }

            // Exige no mínimo 8 caracteres com 1 letra maiúscula e 1 número
            if (strlen($dados['senha']) < 8 || !preg_match('/[A-Z]/', $dados['senha']) || !preg_match('/[0-9]/', $dados['senha'])) {
                $erros['senha'] = 'A senha deve ter no mínimo 8 caracteres, com pelo menos 1 letra maiúscula e 1 número.';
            }

            if ($dados['senha'] !== $dados['confirmar_senha']) {
                $erros['confirmar_senha'] = 'As senhas não conferem.';
            }

            if (!$dados['lgpd']) {
                $erros['lgpd'] = 'Você precisa aceitar os termos da LGPD.';
            }

            // Se não houver nenhum erro de validação, insere o usuário no banco
            if (empty($erros)) {
                $idUsuario = $this->usuarioModel->create($dados);
                if ($idUsuario) {
                    flash('Conta criada com sucesso! Faça login para continuar.', 'success');
                    redirect('?page=auth/login');
                } else {
                    $erros['geral'] = 'Erro ao realizar cadastro. Tente novamente.';
                }
            }
        }

        $tituloPagina = 'Criar Conta';
        require_once __DIR__ . '/../views/auth/cadastro.php';
    }

    /**
     * Realiza o encerramento seguro da sessão do usuário.
     */
    public function logout(): void {
        session_unset();
        session_destroy();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        flash('Você saiu da sua conta com segurança.', 'info');
        redirect('?page=auth/login');
    }

    /**
     * Exibe o aviso de recuperação de senha enviada para o e-mail.
     */
    public function recuperarSenha(): void {
        flash('Instruções para recuperação de senha foram enviadas para o seu e-mail cadastrado.', 'info');
        redirect('?page=auth/login');
    }
}
