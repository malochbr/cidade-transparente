<?php
/**
 * CIDADE TRANSPARENTE — FRONT CONTROLLER & ROTAS
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Ocorrencia.php';

// Verificação automática de ocorrências atrasadas
try {
    Ocorrencia::verificarAtrasadas(Database::getInstance());
} catch (Exception $e) {
    // Silencioso se DB não estiver conectado ainda
}

$page = $_GET['page'] ?? (isLoggedIn() ? 'home' : 'auth/splash');
$action = $_GET['action'] ?? null;
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

switch ($page) {
    // AUTHENTICATION
    case 'auth/splash':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->splash();
        break;

    case 'auth/login':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->login();
        break;

    case 'auth/cadastro':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->cadastro();
        break;

    case 'auth/logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'auth/recuperar-senha':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->recuperarSenha();
        break;

    // HOME & PUBLIC PANEL
    case 'home':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        (new OcorrenciaController())->home();
        break;

    case 'painel':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        (new OcorrenciaController())->painel();
        break;

    case 'ocorrencias':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        (new OcorrenciaController())->listarMinhas();
        break;

    case 'ocorrencia/detalhe':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        if ($id) {
            (new OcorrenciaController())->detalhe($id);
        } else {
            redirect('?page=painel');
        }
        break;

    case 'ocorrencia/apoiar':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        if ($id) (new OcorrenciaController())->apoiar($id);
        break;

    case 'ocorrencia/comentar':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        if ($id) (new OcorrenciaController())->comentar($id);
        break;

    case 'ocorrencia/validar':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        if ($id) (new OcorrenciaController())->validarResolucao($id);
        break;

    // WIZARD NOVA OCORRÊNCIA
    case 'nova-ocorrencia':
        require_once __DIR__ . '/controllers/OcorrenciaController.php';
        $controller = new OcorrenciaController();
        if ($action === 'step1') {
            $controller->salvarStep1();
        } elseif ($action === 'step2') {
            $controller->salvarStep2();
        } elseif ($action === 'step3') {
            $controller->salvarStep3();
        } else {
            $controller->novaOcorrencia();
        }
        break;

    // USER PROFILE
    case 'perfil':
        require_once __DIR__ . '/controllers/PerfilController.php';
        (new PerfilController())->index();
        break;

    case 'perfil/meus-dados':
        require_once __DIR__ . '/controllers/PerfilController.php';
        (new PerfilController())->meusDados();
        break;

    case 'perfil/alterar-senha':
        require_once __DIR__ . '/controllers/PerfilController.php';
        (new PerfilController())->alterarSenha();
        break;

    // NOTIFICATIONS
    case 'notificacoes':
        require_once __DIR__ . '/controllers/NotificacaoController.php';
        (new NotificacaoController())->index();
        break;

    case 'notificacoes/marcar-lida':
        require_once __DIR__ . '/controllers/NotificacaoController.php';
        if ($id) (new NotificacaoController())->marcarLida($id);
        break;

    case 'notificacoes/marcar-todas':
        require_once __DIR__ . '/controllers/NotificacaoController.php';
        (new NotificacaoController())->marcarTodasLidas();
        break;

    // ADMIN PANEL
    case 'admin/dashboard':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->dashboard();
        break;

    case 'admin/ocorrencias':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->ocorrencias();
        break;

    case 'admin/usuarios':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->usuarios();
        break;

    case 'admin/usuarios/toggle':
        require_once __DIR__ . '/controllers/AdminController.php';
        if ($id) (new AdminController())->toggleUsuario($id);
        break;

    case 'admin/usuarios/ajustar-reputacao':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->ajustarReputacao();
        break;

    case 'admin/atualizar-status':
        require_once __DIR__ . '/controllers/AdminController.php';
        (new AdminController())->atualizarStatus();
        break;

    default:
        redirect('?page=home');
        break;
}
