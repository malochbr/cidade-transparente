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
                $usuario = $this->usuarioModel->findByCpf($cpf);

                if (!$usuario) {
                    $erros[] = 'CPF não cadastrado no sistema.';
                } elseif (!password_verify($senha, $usuario['senha'])) {
                    $erros[] = 'Senha incorreta. Tente novamente.';
                } elseif ($usuario['ativo'] == 0) {
                    $erros[] = 'Sua conta está suspensa ou desativada devido ao limite de reputação.';
                } else {
                    // Login bem-sucedido
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

            // Validações de preenchimento obrigatório, CPF e unicidade
            if (empty($dados['nome'])) {
                $erros['nome'] = 'O nome completo é obrigatório.';
            }

            if (empty($dados['cpf']) || !Usuario::validarCpf($dados['cpf'])) {
                $erros['cpf'] = 'O CPF informado é inválido. Verifique os números digitados.';
            } elseif ($this->usuarioModel->findByCpf($dados['cpf'])) {
                $erros['cpf'] = 'Este CPF já está cadastrado no sistema.';
            }

            if (empty($dados['email']) || !filter_var($dados['email'], FILTER_VALIDATE_EMAIL)) {
                $erros['email'] = 'Informe um endereço de e-mail válido.';
            } elseif ($this->usuarioModel->findByEmail($dados['email'])) {
                $erros['email'] = 'Este e-mail já está em uso por outro usuário.';
            }

            if (strlen($dados['senha']) < 8 || !preg_match('/[A-Z]/', $dados['senha']) || !preg_match('/[0-9]/', $dados['senha'])) {
                $erros['senha'] = 'A senha deve ter no mínimo 8 caracteres, com pelo menos 1 letra maiúscula e 1 número.';
            }

            if ($dados['senha'] !== $dados['confirmar_senha']) {
                $erros['confirmar_senha'] = 'As senhas não conferem.';
            }

            if (!$dados['lgpd']) {
                $erros['lgpd'] = 'Você precisa aceitar os termos da LGPD.';
            }

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
