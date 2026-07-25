<?php
/**
 * Model Ocorrencia
 * 
 * Gerencia a lógica principal de negócios e persistência das ocorrências urbanas:
 * cadastro transacional (ocorrência + localização + mídias), verificação de duplicidade,
 * encaminhamento por categoria, atualização de status, apoios e comentários.
 */

class Ocorrencia {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Busca as ocorrências registradas por um usuário específico.
     *
     * @param int $idUsuario  ID do usuário cidadão
     * @param int $limite     Quantidade máxima de registros a retornar
     * @return array Lista de ocorrências com bairro, rua e secretaria
     */
    public function findByUsuario(int $idUsuario, int $limite = 10): array {
        $query = "SELECT o.*, l.bairro, l.rua, l.cidade, s.nome AS secretaria_nome
                FROM ocorrencias o
                LEFT JOIN localizacoes l ON o.id = l.ocorrencia_id
                LEFT JOIN secretarias s ON o.secretaria_id = s.id
                WHERE o.usuario_id = :user_id
                ORDER BY o.data_registro DESC
                LIMIT :limit";
        $consulta = $this->db->prepare($query);
        $consulta->bindValue(':user_id', $idUsuario, PDO::PARAM_INT);
        $consulta->bindValue(':limit', $limite, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll();
    }

    /**
     * Busca a lista geral de ocorrências aplicando filtros dinâmicos de busca e status.
     *
     * @param array $filtros     Filtros opcionais (status, categoria, bairro, search)
     * @param int $limite        Quantidade de registros por página
     * @param int $deslocamento  Deslocamento da paginação (offset)
     * @return array Lista de ocorrências encontradas
     */
    public function findAll(array $filtros = [], int $limite = 20, int $deslocamento = 0): array {
        // Inicializa a cláusula WHERE com condição base verdeira
        $condicoes = ["1=1"];
        $parametros = [];

        // Filtro opcional por status (ex: em_andamento, resolvida)
        if (!empty($filtros['status'])) {
            $condicoes[] = "o.status = :status";
            $parametros[':status'] = $filtros['status'];
        }

        // Filtro opcional por categoria
        if (!empty($filtros['categoria'])) {
            $condicoes[] = "o.categoria = :categoria";
            $parametros[':categoria'] = $filtros['categoria'];
        }

        // Filtro opcional por bairro
        if (!empty($filtros['bairro'])) {
            $condicoes[] = "l.bairro LIKE :bairro";
            $parametros[':bairro'] = '%' . $filtros['bairro'] . '%';
        }

        // Filtro por termo de busca textual (título, descrição, rua ou bairro)
        if (!empty($filtros['search'])) {
            $condicoes[] = "(o.titulo LIKE :search OR o.descricao LIKE :search OR l.rua LIKE :search OR l.bairro LIKE :search)";
            $parametros[':search'] = '%' . $filtros['search'] . '%';
        }

        $condicoesSql = implode(' AND ', $condicoes);

        $query = "SELECT o.*, l.bairro, l.rua, l.cidade, u.nome AS usuario_nome,
                       (SELECT COUNT(*) FROM apoios a WHERE a.ocorrencia_id = o.id) AS total_apoios,
                       (SELECT COUNT(*) FROM comentarios c WHERE c.ocorrencia_id = o.id) AS total_comentarios
                FROM ocorrencias o
                LEFT JOIN localizacoes l ON o.id = l.ocorrencia_id
                LEFT JOIN usuarios u ON o.usuario_id = u.id
                WHERE {$condicoesSql}
                ORDER BY o.data_registro DESC
                LIMIT :limit OFFSET :offset";

        $consulta = $this->db->prepare($query);
        foreach ($parametros as $chave => $valor) {
            $consulta->bindValue($chave, $valor);
        }
        $consulta->bindValue(':limit', $limite, PDO::PARAM_INT);
        $consulta->bindValue(':offset', $deslocamento, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    /**
     * Retorna os detalhes completos de uma ocorrência específica pelo seu ID.
     *
     * @param int $idOcorrencia ID único da ocorrência
     * @return array|null Dados da ocorrência completa ou null se não existir
     */
    public function findById(int $idOcorrencia): ?array {
        $query = "SELECT o.*, l.bairro, l.rua, l.numero, l.cidade, l.estado, l.latitude, l.longitude,
                       u.nome AS usuario_nome, u.email AS usuario_email,
                       s.nome AS secretaria_nome,
                       (SELECT COUNT(*) FROM apoios a WHERE a.ocorrencia_id = o.id) AS total_apoios,
                       (SELECT COUNT(*) FROM comentarios c WHERE c.ocorrencia_id = o.id) AS total_comentarios,
                       (SELECT COUNT(*) FROM validacoes_resolucao vr WHERE vr.ocorrencia_id = o.id AND vr.tipo = 'valida') AS total_validas,
                       (SELECT COUNT(*) FROM validacoes_resolucao vr WHERE vr.ocorrencia_id = o.id AND vr.tipo = 'contra_valida') AS total_contra_validas
                FROM ocorrencias o
                LEFT JOIN localizacoes l ON o.id = l.ocorrencia_id
                LEFT JOIN usuarios u ON o.usuario_id = u.id
                LEFT JOIN secretarias s ON o.secretaria_id = s.id
                WHERE o.id = :id LIMIT 1";

        $consulta = $this->db->prepare($query);
        $consulta->execute([':id' => $idOcorrencia]);
        $ocorrencia = $consulta->fetch();

        if (!$ocorrencia) return null;

        // Carrega mídias anexadas (fotos/vídeos)
        $consultaMidia = $this->db->prepare("SELECT * FROM midias WHERE ocorrencia_id = :id ORDER BY data_envio ASC");
        $consultaMidia->execute([':id' => $idOcorrencia]);
        $ocorrencia['midias'] = $consultaMidia->fetchAll();

        // Carrega a linha do tempo de mudanças de status
        $consultaHist = $this->db->prepare("SELECT h.*, u.nome AS usuario_nome FROM historico_status h LEFT JOIN usuarios u ON h.usuario_id = u.id WHERE h.ocorrencia_id = :id ORDER BY h.data ASC");
        $consultaHist->execute([':id' => $idOcorrencia]);
        $historico = $consultaHist->fetchAll();
        $ocorrencia['historico'] = $historico;
        $ocorrencia['historico_status'] = $historico;

        // Carrega comentários do público
        $consultaCom = $this->db->prepare("SELECT c.*, u.nome AS usuario_nome FROM comentarios c LEFT JOIN usuarios u ON c.usuario_id = u.id WHERE c.ocorrencia_id = :id ORDER BY c.data ASC");
        $consultaCom->execute([':id' => $idOcorrencia]);
        $ocorrencia['comentarios'] = $consultaCom->fetchAll();

        return $ocorrencia;
    }

    /**
     * Checa se já existe uma ocorrência ativa com a mesma categoria e bairro nos últimos 7 dias.
     *
     * @param string $categoria Categoria da nova ocorrência
     * @param string $bairro    Nome do bairro
     * @return bool True se for duplicada (bloqueia o registro)
     */
    public function checkDuplicidade(string $categoria, string $bairro): bool {
        // Bloqueia registros com mesma categoria e mesmo bairro ativos nos últimos 7 dias
        $query = "SELECT COUNT(*) FROM ocorrencias o
                JOIN localizacoes l ON o.id = l.ocorrencia_id
                WHERE o.categoria = :categoria
                AND LOWER(l.bairro) = LOWER(:bairro)
                AND o.status IN ('em_andamento', 'encaminhada')
                AND o.data_registro >= DATE_SUB(NOW(), INTERVAL 7 DAY)";

        $consulta = $this->db->prepare($query);
        $consulta->execute([
            ':categoria' => $categoria,
            ':bairro' => trim($bairro)
        ]);
        return (int) $consulta->fetchColumn() > 0;
    }

    /**
     * Cadastra uma nova ocorrência com localização e mídias de forma atômica (transação PDO).
     *
     * @param array $dadosOcorrencia Dados do formulário e usuário
     * @param array $midias          Lista de mídias enviadas
     * @return int ID da ocorrência gerada
     */
    public function create(array $dadosOcorrencia, array $midias = []): int {
        // Inicia a transação para garantir que tudo salva junto ou nada salva
        $this->db->beginTransaction();

        try {
            // Passo 1: Inserção do registro principal da ocorrência
            $query = "INSERT INTO ocorrencias (titulo, descricao, categoria, status, prioridade, usuario_id, data_registro)
                    VALUES (:titulo, :descricao, :categoria, 'em_andamento', :prioridade, :usuario_id, NOW())";
            
            $consulta = $this->db->prepare($query);
            $consulta->execute([
                ':titulo' => trim($dadosOcorrencia['titulo']),
                ':descricao' => trim($dadosOcorrencia['descricao']),
                ':categoria' => $dadosOcorrencia['categoria'],
                ':prioridade' => $dadosOcorrencia['prioridade'] ?? 'media',
                ':usuario_id' => $dadosOcorrencia['usuario_id']
            ]);

            $idOcorrencia = (int) $this->db->lastInsertId();

            // Passo 2: Inserção do endereço e coordenadas de localização
            $queryLoc = "INSERT INTO localizacoes (ocorrencia_id, estado, cidade, bairro, rua, numero, latitude, longitude)
                       VALUES (:ocorrencia_id, :estado, :cidade, :bairro, :rua, :numero, :latitude, :longitude)";
            $consultaLoc = $this->db->prepare($queryLoc);
            $consultaLoc->execute([
                ':ocorrencia_id' => $idOcorrencia,
                ':estado' => $dadosOcorrencia['estado'] ?? 'PB',
                ':cidade' => $dadosOcorrencia['cidade'] ?? 'João Pessoa',
                ':bairro' => trim($dadosOcorrencia['bairro']),
                ':rua' => trim($dadosOcorrencia['rua']),
                ':numero' => trim($dadosOcorrencia['numero'] ?? ''),
                ':latitude' => $dadosOcorrencia['latitude'] ?? 0,
                ':longitude' => $dadosOcorrencia['longitude'] ?? 0
            ]);

            // Passo 3: Inserção das mídias enviadas
            if (!empty($midias)) {
                $queryMid = "INSERT INTO midias (ocorrencia_id, tipo, arquivo) VALUES (:ocorrencia_id, :tipo, :arquivo)";
                $consultaMid = $this->db->prepare($queryMid);
                foreach ($midias as $mid) {
                    $consultaMid->execute([
                        ':ocorrencia_id' => $idOcorrencia,
                        ':tipo' => $mid['tipo'],
                        ':arquivo' => $mid['arquivo']
                    ]);
                }
            }

            // Passo 4: Registro do histórico inicial de status
            $queryHist = "INSERT INTO historico_status (ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data)
                        VALUES (:ocorrencia_id, NULL, 'em_andamento', :usuario_id, 'Ocorrência registrada pelo cidadão.', NOW())";
            $consultaHist = $this->db->prepare($queryHist);
            $consultaHist->execute([
                ':ocorrencia_id' => $idOcorrencia,
                ':usuario_id' => $dadosOcorrencia['usuario_id']
            ]);

            // Passo 5: Criação da notificação inicial para o cidadão
            $queryNot = "INSERT INTO notificacoes (usuario_id, ocorrencia_id, mensagem, tipo, data)
                       VALUES (:usuario_id, :ocorrencia_id, :mensagem, 'status', NOW())";
            $consultaNot = $this->db->prepare($queryNot);
            $consultaNot->execute([
                ':usuario_id' => $dadosOcorrencia['usuario_id'],
                ':ocorrencia_id' => $idOcorrencia,
                ':mensagem' => 'Sua ocorrência #' . $idOcorrencia . ' "' . trim($dadosOcorrencia['titulo']) . '" foi registrada com sucesso.'
            ]);

            // Encaminha automaticamente para a secretaria responsável pela categoria
            $this->encaminharPorCategoria($idOcorrencia, $dadosOcorrencia['categoria']);

            // Confirma todas as inserções no banco
            $this->db->commit();
            return $idOcorrencia;
        } catch (Exception $e) {
            // Em caso de erro em qualquer etapa, desfaz todas as alterações
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Localiza a secretaria responsável pela categoria e vincula a ocorrência.
     *
     * @param int $idOcorrencia ID da ocorrência
     * @param string $categoria Categoria do problema
     * @return bool True se encontrou e vinculou a secretaria
     */
    public function encaminharPorCategoria(int $idOcorrencia, string $categoria): bool {
        $consultaSec = $this->db->prepare("SELECT id, nome FROM secretarias WHERE categoria_responsavel = :cat LIMIT 1");
        $consultaSec->execute([':cat' => $categoria]);
        $sec = $consultaSec->fetch();

        if ($sec) {
            $secretariaId = $sec['id'];
            $secretariaNome = $sec['nome'];

            // Atualiza o status para 'encaminhada' e define a secretaria responsável
            $consultaUp = $this->db->prepare("UPDATE ocorrencias SET secretaria_id = :sec_id, status = 'encaminhada' WHERE id = :id");
            $consultaUp->execute([':sec_id' => $secretariaId, ':id' => $idOcorrencia]);

            $consultaOc = $this->db->prepare("SELECT usuario_id FROM ocorrencias WHERE id = :id");
            $consultaOc->execute([':id' => $idOcorrencia]);
            $idUsuario = $consultaOc->fetchColumn();

            // Adiciona a transição ao histórico de status
            $queryHist = "INSERT INTO historico_status (ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data)
                        VALUES (:ocorrencia_id, 'em_andamento', 'encaminhada', :usuario_id, :obs, NOW())";
            $consultaHist = $this->db->prepare($queryHist);
            $consultaHist->execute([
                ':ocorrencia_id' => $idOcorrencia,
                ':usuario_id' => $idUsuario ?: null,
                ':obs' => "Encaminhada para " . $secretariaNome
            ]);

            // Envia notificação informativa ao cidadão
            if ($idUsuario) {
                $queryNot = "INSERT INTO notificacoes (usuario_id, ocorrencia_id, mensagem, tipo, data)
                           VALUES (:usuario_id, :ocorrencia_id, :mensagem, 'status', NOW())";
                $consultaNot = $this->db->prepare($queryNot);
                $consultaNot->execute([
                    ':usuario_id' => $idUsuario,
                    ':ocorrencia_id' => $idOcorrencia,
                    ':mensagem' => "Sua ocorrência #{$idOcorrencia} foi encaminhada para " . $secretariaNome
                ]);
            }
            return true;
        }
        return false;
    }

    /**
     * Atualiza o status da ocorrência e registra o histórico e as notificações correspondentes.
     *
     * @param int $idOcorrencia   ID da ocorrência
     * @param string $novoStatus  Novo status a atribuir
     * @param int $idUsuario      ID do autor da ação (admin)
     * @param string|null $observacao Observação sobre a mudança
     * @param string|null $prazoResolucao Data limite de prazo
     * @param int|null $secretariaId ID de nova secretaria vinculada
     * @return bool True em caso de sucesso
     */
    public function updateStatus(int $idOcorrencia, string $novoStatus, int $idUsuario, ?string $observacao = null, ?string $prazoResolucao = null, ?int $secretariaId = null): bool {
        $ocorrencia = $this->findById($idOcorrencia);
        if (!$ocorrencia) return false;

        $statusAnterior = $ocorrencia['status'];
        $dataConclusao = ($novoStatus === 'resolvida') ? date('Y-m-d H:i:s') : null;

        $query = "UPDATE ocorrencias SET status = :status";
        $parametros = [':status' => $novoStatus, ':id' => $idOcorrencia];

        if ($prazoResolucao) {
            $query .= ", prazo_resolucao = :prazo";
            $parametros[':prazo'] = $prazoResolucao;
        }

        if ($secretariaId) {
            $query .= ", secretaria_id = :sec_id";
            $parametros[':sec_id'] = $secretariaId;
        }

        if ($dataConclusao) {
            $query .= ", data_conclusao = :data_conclusao";
            $parametros[':data_conclusao'] = $dataConclusao;
        }

        $query .= " WHERE id = :id";
        $consulta = $this->db->prepare($query);
        $resultado = $consulta->execute($parametros);

        if ($resultado) {
            // Registra a alteração na linha do tempo
            $consultaHist = $this->db->prepare("INSERT INTO historico_status (ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data) VALUES (:ocorrencia_id, :status_ant, :status_novo, :usuario_id, :observacao, NOW())");
            $consultaHist->execute([
                ':ocorrencia_id' => $idOcorrencia,
                ':status_ant' => $statusAnterior,
                ':status_novo' => $novoStatus,
                ':usuario_id' => $idUsuario,
                ':observacao' => $observacao ?? "Status alterado de '{$statusAnterior}' para '{$novoStatus}'."
            ]);

            // Envia notificação do novo status ao cidadão
            $consultaNot = $this->db->prepare("INSERT INTO notificacoes (usuario_id, ocorrencia_id, mensagem, tipo, data) VALUES (:user_id, :ocorrencia_id, :msg, 'status', NOW())");
            $consultaNot->execute([
                ':user_id' => $ocorrencia['usuario_id'],
                ':ocorrencia_id' => $idOcorrencia,
                ':msg' => "Sua ocorrência #{$idOcorrencia} teve o status atualizado para: " . strtoupper(str_replace('_', ' ', $novoStatus)) . "."
            ]);
        }

        return $resultado;
    }

    /**
     * Lógica de toggle de apoio: adiciona o apoio se o usuário ainda não apoiou, ou remove se já apoiou.
     *
     * @param int $idOcorrencia ID da ocorrência
     * @param int $idUsuario    ID do usuário
     * @return bool True se a operação foi executada
     */
    public function apoiar(int $idOcorrencia, int $idUsuario): bool {
        // Verifica se o usuário já apoiou esta ocorrência previamente
        $consultaCheck = $this->db->prepare("SELECT id FROM apoios WHERE ocorrencia_id = :o_id AND usuario_id = :u_id LIMIT 1");
        $consultaCheck->execute([':o_id' => $idOcorrencia, ':u_id' => $idUsuario]);
        if ($consultaCheck->fetch()) {
            // Caso já tenha apoiado -> remove o apoio (toggle desativar)
            $consultaDel = $this->db->prepare("DELETE FROM apoios WHERE ocorrencia_id = :o_id AND usuario_id = :u_id");
            return $consultaDel->execute([':o_id' => $idOcorrencia, ':u_id' => $idUsuario]);
        } else {
            // Caso ainda não tenha apoiado -> adiciona o apoio
            $consultaIns = $this->db->prepare("INSERT IGNORE INTO apoios (ocorrencia_id, usuario_id, data) VALUES (:o_id, :u_id, NOW())");
            return $consultaIns->execute([':o_id' => $idOcorrencia, ':u_id' => $idUsuario]);
        }
    }

    /**
     * Verifica se um determinado usuário apoia uma ocorrência.
     *
     * @param int $idOcorrencia ID da ocorrência
     * @param int $idUsuario    ID do usuário
     * @return bool True se apoia, false caso contrário
     */
    public function userApoiou(int $idOcorrencia, int $idUsuario): bool {
        $consulta = $this->db->prepare("SELECT COUNT(*) FROM apoios WHERE ocorrencia_id = :o_id AND usuario_id = :u_id");
        $consulta->execute([':o_id' => $idOcorrencia, ':u_id' => $idUsuario]);
        return (int)$consulta->fetchColumn() > 0;
    }

    /**
     * Adiciona um novo comentário público a uma ocorrência.
     *
     * @param int $idOcorrencia ID da ocorrência
     * @param int $idUsuario    ID do cidadão autor do comentário
     * @param string $texto     Texto do comentário
     * @return bool True em caso de sucesso
     */
    public function comentar(int $idOcorrencia, int $idUsuario, string $texto): bool {
        $consulta = $this->db->prepare("INSERT INTO comentarios (ocorrencia_id, usuario_id, texto, data) VALUES (:o_id, :u_id, :texto, NOW())");
        return $consulta->execute([
            ':o_id' => $idOcorrencia,
            ':u_id' => $idUsuario,
            ':texto' => trim($texto)
        ]);
    }

    /**
     * Registra a validação ou contra-validação da resolução prestada pela prefeitura.
     *
     * @param int $idOcorrencia ID da ocorrência
     * @param int $idUsuario    ID do cidadão
     * @param string $tipo      'valida' ou 'contra_valida'
     * @return bool True em caso de sucesso
     */
    public function validarResolucao(int $idOcorrencia, int $idUsuario, string $tipo): bool {
        $consulta = $this->db->prepare("INSERT INTO validacoes_resolucao (ocorrencia_id, usuario_id, tipo, data) VALUES (:o_id, :u_id, :tipo, NOW()) ON DUPLICATE KEY UPDATE tipo = :tipo");
        return $consulta->execute([
            ':o_id' => $idOcorrencia,
            ':u_id' => $idUsuario,
            ':tipo' => $tipo
        ]);
    }

    /**
     * Agrupa e conta o total de ocorrências registradas separadas por cada status.
     *
     * @return array Contagem de ocorrências por status
     */
    public function countByStatus(): array {
        $query = "SELECT status, COUNT(*) AS total FROM ocorrencias GROUP BY status";
        $consulta = $this->db->query($query);
        $resultado = [
            'em_andamento' => 0,
            'encaminhada' => 0,
            'resolvida' => 0,
            'cancelada' => 0,
            'atrasada' => 0,
            'total' => 0
        ];
        while ($row = $consulta->fetch()) {
            $resultado[$row['status']] = (int) $row['total'];
            $resultado['total'] += (int) $row['total'];
        }
        return $resultado;
    }

    /**
     * Método estático que busca e atualiza automaticamente o status para 'atrasada'
     * em ocorrências com prazo limite vencido (prazo_resolucao < CURDATE()).
     *
     * @param PDO $db Conexão PDO
     */
    public static function verificarAtrasadas(PDO $db): void {
        // Busca ocorrências não finalizadas cujo prazo estipulado já expirou
        $query = "SELECT id, usuario_id, titulo FROM ocorrencias
                WHERE status NOT IN ('resolvida', 'cancelada', 'atrasada')
                AND prazo_resolucao IS NOT NULL
                AND prazo_resolucao < CURDATE()";

        $consulta = $db->query($query);
        $atrasadas = $consulta->fetchAll();

        foreach ($atrasadas as $oc) {
            // Atualiza o status para 'atrasada'
            $db->prepare("UPDATE ocorrencias SET status = 'atrasada' WHERE id = :id")->execute([':id' => $oc['id']]);
            // Adiciona justificativa automática ao histórico
            $db->prepare("INSERT INTO historico_status (ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data) VALUES (:id, 'encaminhada', 'atrasada', NULL, 'Prazo limite expirado automaticamente.', NOW())")->execute([':id' => $oc['id']]);
            // Notifica o cidadão dono da ocorrência
            $db->prepare("INSERT INTO notificacoes (usuario_id, ocorrencia_id, mensagem, tipo, data) VALUES (:u_id, :o_id, :msg, 'status', NOW())")->execute([
                ':u_id' => $oc['usuario_id'],
                ':o_id' => $oc['id'],
                ':msg' => "Sua ocorrência #{$oc['id']} teve o prazo expirado e seu status mudou para ATRASADA."
            ]);
        }
    }
}
