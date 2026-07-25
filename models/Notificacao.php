<?php
/**
 * Model Notificacao
 * 
 * Responsável por buscar, criar e atualizar o status de leitura das notificações
 * enviadas aos cidadãos e administradores sobre o progresso das ocorrências.
 */

class Notificacao {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Retorna a lista de notificações direcionadas a um usuário específico.
     *
     * @param int $idUsuario ID do usuário
     * @return array Lista de notificações ordenada por leitura e data
     */
    public function findByUsuario(int $idUsuario): array {
        // Ordena para exibir primeiro as não lidas e depois as mais recentes
        $query = "SELECT * FROM notificacoes WHERE usuario_id = :u_id ORDER BY visualizada ASC, data DESC";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':u_id' => $idUsuario]);
        return $consulta->fetchAll();
    }

    /**
     * Conta a quantidade de notificações pendentes de leitura de um usuário.
     *
     * @param int $idUsuario ID do usuário
     * @return int Total de notificações não lidas
     */
    public function countNaoLidas(int $idUsuario): int {
        $query = "SELECT COUNT(*) FROM notificacoes WHERE usuario_id = :u_id AND visualizada = 0";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':u_id' => $idUsuario]);
        return (int) $consulta->fetchColumn();
    }

    /**
     * Marca uma notificação individual como visualizada pelo cidadão.
     *
     * @param int $id          ID da notificação
     * @param int $idUsuario ID do usuário dono da notificação
     * @return bool True em caso de sucesso
     */
    public function marcarLida(int $id, int $idUsuario): bool {
        $query = "UPDATE notificacoes SET visualizada = 1 WHERE id = :id AND usuario_id = :u_id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([':id' => $id, ':u_id' => $idUsuario]);
    }

    /**
     * Marca todas as notificações de um usuário como lidas simultaneamente.
     *
     * @param int $idUsuario ID do usuário
     * @return bool True em caso de sucesso
     */
    public function marcarTodasLidas(int $idUsuario): bool {
        $query = "UPDATE notificacoes SET visualizada = 1 WHERE usuario_id = :u_id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([':u_id' => $idUsuario]);
    }

    /**
     * Cria e insere uma nova notificação de sistema para um cidadão.
     *
     * @param PDO $db               Conexão ativa PDO
     * @param int $idUsuario        ID do usuário destinatário
     * @param int|null $idOcorrencia ID da ocorrência vinculada (opcional)
     * @param string $mensagem      Texto explicativo da notificação
     * @param string $tipo          Categoria da notificação (ex: status, apoio)
     * @return bool True em caso de sucesso
     */
    public static function criar(PDO $db, int $idUsuario, ?int $idOcorrencia, string $mensagem, string $tipo = 'status'): bool {
        $query = "INSERT INTO notificacoes (usuario_id, ocorrencia_id, mensagem, tipo, data) VALUES (:u_id, :o_id, :msg, :tipo, NOW())";
        $consulta = $db->prepare($query);
        return $consulta->execute([
            ':u_id' => $idUsuario,
            ':o_id' => $idOcorrencia,
            ':msg' => trim($mensagem),
            ':tipo' => $tipo
        ]);
    }
}
