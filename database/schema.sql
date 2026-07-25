-- ============================================================
-- CIDADE TRANSPARENTE — BANCO DE DADOS
-- Schema completo e dados iniciais (Seed)
-- ============================================================

SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

CREATE DATABASE IF NOT EXISTS cidade_transparente CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cidade_transparente;

-- ------------------------------------------------------------
-- 1. TABELA DE USUÁRIOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cpf VARCHAR(11) NOT NULL UNIQUE,
    telefone VARCHAR(20) NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    reputacao INT DEFAULT 1000,
    perfil ENUM('cidadao', 'administrador', 'secretaria') NOT NULL DEFAULT 'cidadao',
    ativo TINYINT(1) DEFAULT 1,
    data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 2. TABELA DE PREFEITURAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS prefeituras (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    cidade VARCHAR(255) NOT NULL,
    estado VARCHAR(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 3. TABELA DE SECRETARIAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS secretarias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    categoria_responsavel ENUM('buraco_na_via', 'iluminacao_publica', 'alagamento', 'terreno_baldio', 'limpeza_urbana', 'outros') NOT NULL,
    prefeitura_id INT NOT NULL,
    FOREIGN KEY (prefeitura_id) REFERENCES prefeituras(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 4. TABELA DE OCORRÊNCIAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ocorrencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    descricao TEXT NOT NULL,
    categoria ENUM('buraco_na_via', 'iluminacao_publica', 'alagamento', 'terreno_baldio', 'limpeza_urbana', 'outros') NOT NULL,
    status ENUM('em_andamento', 'encaminhada', 'resolvida', 'cancelada', 'atrasada') NOT NULL DEFAULT 'em_andamento',
    prioridade ENUM('baixa', 'media', 'alta') NOT NULL DEFAULT 'media',
    usuario_id INT NOT NULL,
    secretaria_id INT NULL,
    data_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    prazo_resolucao DATE NULL,
    data_conclusao TIMESTAMP NULL,
    justificativa_cancelamento TEXT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (secretaria_id) REFERENCES secretarias(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 5. TABELA DE LOCALIZAÇÕES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS localizacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    estado VARCHAR(2) NOT NULL DEFAULT 'PB',
    cidade VARCHAR(255) NOT NULL DEFAULT 'João Pessoa',
    bairro VARCHAR(255) NOT NULL,
    rua VARCHAR(255) NOT NULL,
    numero VARCHAR(50) NULL,
    latitude DECIMAL(10,8) DEFAULT 0,
    longitude DECIMAL(11,8) DEFAULT 0,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 6. TABELA DE MÍDIAS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS midias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    tipo ENUM('imagem', 'video') NOT NULL DEFAULT 'imagem',
    arquivo VARCHAR(255) NOT NULL,
    data_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 7. TABELA DE HISTÓRICO DE STATUS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS historico_status (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    status_anterior VARCHAR(50) NULL,
    status_novo VARCHAR(50) NOT NULL,
    usuario_id INT NULL,
    observacao TEXT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 8. TABELA DE NOTIFICAÇÕES
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    ocorrencia_id INT NULL,
    mensagem TEXT NOT NULL,
    tipo VARCHAR(50) NOT NULL DEFAULT 'status',
    visualizada TINYINT(1) DEFAULT 0,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 9. TABELA DE APOIOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS apoios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    usuario_id INT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_apoio (ocorrencia_id, usuario_id),
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 10. TABELA DE COMENTÁRIOS
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS comentarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    usuario_id INT NOT NULL,
    texto TEXT NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- 11. TABELA DE VALIDAÇÕES DE RESOLUÇÃO
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS validacoes_resolucao (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ocorrencia_id INT NOT NULL,
    usuario_id INT NOT NULL,
    tipo ENUM('valida', 'contra_valida') NOT NULL,
    data TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_validacao (ocorrencia_id, usuario_id),
    FOREIGN KEY (ocorrencia_id) REFERENCES ocorrencias(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ============================================================
-- DADOS INICIAIS (SEED)
-- ============================================================

-- 1. PREFEITURA
INSERT INTO prefeituras (id, nome, cidade, estado) VALUES 
(1, 'Prefeitura Municipal de João Pessoa', 'João Pessoa', 'PB');

-- 2. SECRETARIAS
INSERT INTO secretarias (id, nome, categoria_responsavel, prefeitura_id) VALUES 
(1, 'Secretaria de Infraestrutura e Obras', 'buraco_na_via', 1),
(2, 'Secretaria de Iluminação Pública', 'iluminacao_publica', 1),
(3, 'Secretaria de Meio Ambiente e Serviços Urbanos', 'terreno_baldio', 1),
(4, 'Secretaria Geral de Gestão Cívica', 'outros', 1);

-- 3. USUÁRIOS
-- Senhas: Admin@123 para admin, Teste@123 para cidadãos (Hashes gerados com bcrypt cost 10)
INSERT INTO usuarios (id, nome, cpf, telefone, email, senha, reputacao, perfil, ativo, data_cadastro) VALUES 
(1, 'Administrador Geral', '00000000000', '(83) 99999-0000', 'admin@cidadetransparente.com', '$2b$10$owIHmC4CbMGIwTTbjFy/YObE1jK1XIQtZNXX40/sRPm6LPjWa1d9O', 1000, 'administrador', 1, NOW()),
(2, 'João Silva', '11111111111', '(83) 98888-1111', 'joao.silva@email.com', '$2b$10$2gDWzLafN3SwDOkq.sSosuPp9Ua55UMGtOq8YQ4bj/AuZbXYgX2ty', 1000, 'cidadao', 1, NOW()),
(3, 'Maria Santos', '22222222222', '(83) 98777-2222', 'maria.santos@email.com', '$2b$10$2gDWzLafN3SwDOkq.sSosuPp9Ua55UMGtOq8YQ4bj/AuZbXYgX2ty', 950, 'cidadao', 1, NOW()),
(4, 'Carlos Souza', '33333333333', '(83) 98666-3333', 'carlos.souza@email.com', '$2b$10$2gDWzLafN3SwDOkq.sSosuPp9Ua55UMGtOq8YQ4bj/AuZbXYgX2ty', 1000, 'cidadao', 1, NOW());

-- 4. OCORRÊNCIAS
INSERT INTO ocorrencias (id, titulo, descricao, categoria, status, prioridade, usuario_id, secretaria_id, data_registro, prazo_resolucao, data_conclusao, justificativa_cancelamento) VALUES 
(1, 'Buraco grande na via principal', 'Existe um buraco de grandes proporções no meio da rua atrapalhando o trânsito e gerando risco de acidentes.', 'buraco_na_via', 'em_andamento', 'alta', 2, 1, DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL, NULL),
(2, 'Lâmpada queimada no poste', 'Poste de iluminação pública com lâmpada queimada deixando a rua escura durante a noite.', 'iluminacao_publica', 'encaminhada', 'media', 2, 2, DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL, NULL),
(3, 'Alagamento recorrente após chuva', 'Bueiro entupido fazendo com que a água da chuva acumule e invada a calçada.', 'alagamento', 'resolvida', 'alta', 3, 1, DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(CURDATE(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), NULL),
(4, 'Acúmulo de lixo em terreno baldio', 'Terreno baldio sem muro acumulando entulho e lixo urbano, atraindo insetos e roedores.', 'terreno_baldio', 'cancelada', 'baixa', 4, 3, DATE_SUB(NOW(), INTERVAL 15 DAY), NULL, NULL, 'Ocorrência duplicada registrada no mesmo endereço.'),
(5, 'Semáforo com defeito no cruzamento', 'Semáforo piscando em amarelo no cruzamento de grande movimento.', 'outros', 'atrasada', 'alta', 3, 4, DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), NULL, NULL);

-- 5. LOCALIZAÇÕES
INSERT INTO localizacoes (id, ocorrencia_id, estado, cidade, bairro, rua, numero, latitude, longitude) VALUES 
(1, 1, 'PB', 'João Pessoa', 'Centro', 'Rua das Flores', '123', -7.11500000, -34.86300000),
(2, 2, 'PB', 'João Pessoa', 'Centro', 'Av. Brasil', '456', -7.11800000, -34.86500000),
(3, 3, 'PB', 'João Pessoa', 'Centro', 'Rua São Paulo', '789', -7.12000000, -34.86000000),
(4, 4, 'PB', 'João Pessoa', 'Manaíra', 'Rua Projetada', '10', -7.09500000, -34.83500000),
(5, 5, 'PB', 'João Pessoa', 'Tambaú', 'Av. Epitácio Pessoa', '2000', -7.11000000, -34.84000000);

-- 6. HISTÓRICO DE STATUS
INSERT INTO historico_status (id, ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data) VALUES 
(1, 1, NULL, 'em_andamento', 2, 'Ocorrência registrada pelo cidadão.', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 2, NULL, 'em_andamento', 2, 'Ocorrência registrada pelo cidadão.', DATE_SUB(NOW(), INTERVAL 4 DAY)),
(3, 2, 'em_andamento', 'encaminhada', 1, 'Ocorrência encaminhada para a Secretaria de Iluminação Pública.', DATE_SUB(NOW(), INTERVAL 3 DAY)),
(4, 3, NULL, 'em_andamento', 3, 'Ocorrência registrada pelo cidadão.', DATE_SUB(NOW(), INTERVAL 10 DAY)),
(5, 3, 'em_andamento', 'encaminhada', 1, 'Encaminhado para equipe de desobstrução.', DATE_SUB(NOW(), INTERVAL 8 DAY)),
(6, 3, 'encaminhada', 'resolvida', 1, 'Serviço de limpeza e desobstrução do bueiro concluído.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 4, NULL, 'em_andamento', 4, 'Ocorrência registrada pelo cidadão.', DATE_SUB(NOW(), INTERVAL 15 DAY)),
(8, 4, 'em_andamento', 'cancelada', 1, 'Cancelada por duplicidade.', DATE_SUB(NOW(), INTERVAL 14 DAY)),
(9, 5, NULL, 'em_andamento', 3, 'Ocorrência registrada.', DATE_SUB(NOW(), INTERVAL 20 DAY)),
(10, 5, 'em_andamento', 'atrasada', 1, 'Prazo de resolução expirado.', DATE_SUB(NOW(), INTERVAL 5 DAY));

-- 7. NOTIFICAÇÕES
INSERT INTO notificacoes (id, usuario_id, ocorrencia_id, mensagem, tipo, visualizada, data) VALUES 
(1, 2, 1, 'Sua ocorrência #1 "Buraco grande na via principal" foi registrada com sucesso.', 'status', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(2, 2, 2, 'Sua ocorrência #2 foi encaminhada para a Secretaria de Iluminação Pública.', 'status', 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(3, 3, 3, 'Sua ocorrência #3 foi marcada como resolvida pela prefeitura.', 'status', 1, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- 8. APOIOS
INSERT INTO apoios (id, ocorrencia_id, usuario_id, data) VALUES 
(1, 1, 3, NOW()),
(2, 1, 4, NOW()),
(3, 3, 2, NOW());

-- 9. COMENTÁRIOS
INSERT INTO comentarios (id, ocorrencia_id, usuario_id, texto, data) VALUES 
(1, 1, 3, 'Realmente o buraco está perigoso, quase furei o pneu do carro ontem.', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 3, 2, 'Confirmo que a equipe esteve no local e desentupiu o bueiro.', NOW());
