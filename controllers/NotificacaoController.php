<?php
/**
 * Controller NotificacaoController
 * 
 * Gerencia a exibição da central de notificações do cidadão e as ações
 * de marcar mensagens individuais ou coletivas como lidas.
 */

require_once __DIR__ . '/../models/Notificacao.php';

class NotificacaoController {
    private Notificacao $notificacaoModel;

    public function __construct() {
        $this->notificacaoModel = new Notificacao(Database::getInstance());
    }

    /**
     * Exibe a central de notificações do usuário logado.
     */
    public function index(): void {
        if (!isLoggedIn()) redirect('?page=auth/login');

        $idUsuario = $_SESSION['user_id'];
        $notificacoes = $this->notificacaoModel->findByUsuario($idUsuario);

        $tituloPagina = 'Notificações';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/notificacoes/index.php';
    }

    /**
     * Marca uma notificação específica como lida e redireciona para a ocorrência vinculada (se houver).
     *
     * @param int $id ID da notificação
     */
    public function marcarLida(int $id): void {
        if (!isLoggedIn()) redirect('?page=auth/login');

        $idUsuario = $_SESSION['user_id'];
        $this->notificacaoModel->marcarLida($id, $idUsuario);

        $ocId = $_GET['ocorrencia_id'] ?? null;
        if ($ocId) {
            redirect('?page=ocorrencia/detalhe&id=' . (int)$ocId);
        } else {
            redirect('?page=notificacoes');
        }
    }

    /**
     * Marca todas as notificações pendentes do usuário como lidas simultaneamente.
     */
    public function marcarTodasLidas(): void {
        if (!isLoggedIn() || !verify_csrf()) redirect('?page=notificacoes');

        $idUsuario = $_SESSION['user_id'];
        $this->notificacaoModel->marcarTodasLidas($idUsuario);

        flash('Todas as notificações foram marcadas como lidas.', 'success');
        redirect('?page=notificacoes');
    }
}
