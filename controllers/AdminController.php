<?php
/**
 * Controller AdminController
 * 
 * Gerencia o painel administrativo restrito: controle de métricas gerais, gestão de usuários,
 * alteração manual de reputação e atualização de status de ocorrências pelas secretarias.
 */

require_once __DIR__ . '/../models/Ocorrencia.php';
require_once __DIR__ . '/../models/Usuario.php';

class AdminController {
    private Ocorrencia $ocorrenciaModel;
    private Usuario $usuarioModel;

    public function __construct() {
        // Bloqueia o acesso a usuários que não possuem perfil de administrador
        if (!isLoggedIn() || getUserRole() !== 'administrador') {
            flash('Acesso negado. Esta área é restrita a administradores.', 'error');
            redirect('?page=home');
        }

        $db = Database::getInstance();
        $this->ocorrenciaModel = new Ocorrencia($db);
        $this->usuarioModel = new Usuario($db);
    }

    /**
     * Exibe o dashboard principal com contagem de ocorrências por status e usuários.
     */
    public function dashboard(): void {
        $stats = $this->ocorrenciaModel->countByStatus();
        $recentes = $this->ocorrenciaModel->findAll([], 10);
        $usuarios = $this->usuarioModel->findAll();

        $tituloPagina = 'Dashboard Admin';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }

    /**
     * Exibe a lista completa de ocorrências para gerenciamento administrativo.
     */
    public function ocorrencias(): void {
        $filtros = [
            'status' => $_GET['status'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        $ocorrencias = $this->ocorrenciaModel->findAll($filtros, 50);

        $tituloPagina = 'Gerenciar Ocorrências';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/admin/ocorrencias.php';
    }

    /**
     * Exibe a listagem de cidadãos e administradores cadastrados no sistema.
     */
    public function usuarios(): void {
        $usuarios = $this->usuarioModel->findAll();

        $tituloPagina = 'Gerenciar Usuários';
        $activePage = 'perfil';
        require_once __DIR__ . '/../views/admin/usuarios.php';
    }

    /**
     * Ativa ou desativa a conta de um usuário (impedindo seu próprio bloqueio).
     *
     * @param int $idUsuario ID do usuário a ser alternado
     */
    public function toggleUsuario(int $idUsuario): void {
        if (!verify_csrf()) redirect('?page=admin/usuarios');

        // Impede que o próprio administrador desative a sua própria conta
        if ($idUsuario === (int)$_SESSION['user_id']) {
            flash('Você não pode desativar seu próprio perfil de administrador.', 'error');
            redirect('?page=admin/usuarios');
        }

        $this->usuarioModel->toggleAtivo($idUsuario);
        flash('Status do usuário alterado com sucesso!', 'success');
        redirect('?page=admin/usuarios');
    }

    /**
     * Ajusta a reputação de um cidadão manualmente e gera o registro de auditoria.
     */
    public function ajustarReputacao(): void {
        if (!verify_csrf()) redirect('?page=admin/usuarios');

        $idUsuario = (int)($_POST['usuario_id'] ?? 0);
        $pontos = (int)($_POST['pontos'] ?? 0);
        $motivo = $_POST['motivo'] ?? 'Ajuste manual do administrador';

        if ($idUsuario > 0 && $pontos !== 0) {
            $this->usuarioModel->updateReputacao($idUsuario, $pontos);

            // Registra o log histórico da alteração de reputação no banco
            $db = Database::getInstance();
            $consulta = $db->prepare("INSERT INTO logs_reputacao (usuario_id, pontos, motivo, data) VALUES (:u_id, :pts, :motivo, NOW())");
            $consulta->execute([':u_id' => $idUsuario, ':pts' => $pontos, ':motivo' => $motivo]);

            flash("Reputação ajustada em {$pontos} pontos com sucesso!", 'success');
        }

        redirect('?page=admin/usuarios');
    }

    /**
     * Atualiza o status, prazo de resolução ou secretaria responsável de uma ocorrência.
     */
    public function atualizarStatus(): void {
        if (!verify_csrf()) redirect('?page=admin/ocorrencias');

        $idOcorrencia = (int)($_POST['ocorrencia_id'] ?? 0);
        $status = $_POST['status'] ?? '';
        $prazo = $_POST['prazo_resolucao'] ?? null;
        $observacao = $_POST['observacao'] ?? null;

        if ($idOcorrencia > 0 && !empty($status)) {
            // Executa a atualização completa com registro de histórico e notificação ao cidadão
            $this->ocorrenciaModel->updateStatus($idOcorrencia, $status, $_SESSION['user_id'], $observacao, $prazo);
            flash("Ocorrência #{$idOcorrencia} atualizada para {$status}!", 'success');
        }

        redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
    }
}
