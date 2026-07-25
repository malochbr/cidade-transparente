-- ============================================================
-- SEED DE DEMONSTRAÇÃO — CIDADE TRANSPARENTE
-- Executar APÓS o schema.sql original
-- IDs começam em 10 para não conflitar com seed original
-- ============================================================

USE cidade_transparente;

-- ============================================================
-- NOVOS USUÁRIOS (IDs 10–19)
-- Senha de todos: password
-- ============================================================
INSERT IGNORE INTO usuarios (id, nome, cpf, telefone, email, senha, reputacao, perfil, ativo, data_cadastro) VALUES

-- Cidadãos ativos com boa reputação
(10, 'Ana Paula Ferreira',    '44444444444', '(83) 99123-4567', 'ana.ferreira@email.com',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1150, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 45 DAY)),
(11, 'Roberto Lima',          '55555555555', '(83) 98234-5678', 'roberto.lima@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6',  980, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 30 DAY)),
(12, 'Fernanda Costa',        '66666666666', '(83) 97345-6789', 'fernanda.costa@email.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1200, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 60 DAY)),
(13, 'Lucas Oliveira',        '77777777788', '(83) 96456-7890', 'lucas.oliveira@email.com',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6',  760, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(14, 'Juliana Nascimento',    '88888888888', '(83) 95567-8901', 'juliana.nasc@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1050, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(15, 'Pedro Henrique Melo',   '99999999900', '(83) 94678-9012', 'pedro.melo@email.com',       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6',  420, 'cidadao',       1, DATE_SUB(NOW(), INTERVAL 10 DAY)),
-- Usuário com reputação baixa (bloqueado)
(16, 'Marcos Abreu',          '10101010101', '(83) 93789-0123', 'marcos.abreu@email.com',     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6',  150, 'cidadao',       0, DATE_SUB(NOW(), INTERVAL 50 DAY)),
-- Secretaria como usuário
(17, 'Secretaria de Obras',   '11100000001', '(83) 3222-1111', 'obras@joaopessoa.pb.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1000, 'secretaria',    1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
(18, 'Secretaria Infraestrutura', '11100000002', '(83) 3222-2222', 'infra@joaopessoa.pb.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1000, 'secretaria', 1, DATE_SUB(NOW(), INTERVAL 90 DAY)),
(19, 'Secretaria Meio Ambiente', '11100000003', '(83) 3222-3333', 'meioambiente@joaopessoa.pb.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.ucrKiyWi6', 1000, 'secretaria', 1, DATE_SUB(NOW(), INTERVAL 90 DAY));

-- ============================================================
-- NOVAS OCORRÊNCIAS (IDs 10–29)
-- Cobrindo todos os status, categorias e bairros variados
-- ============================================================
INSERT IGNORE INTO ocorrencias (id, titulo, descricao, categoria, status, prioridade, usuario_id, secretaria_id, data_registro, prazo_resolucao, data_conclusao, justificativa_cancelamento) VALUES

-- EM ANDAMENTO (recentes, dentro do prazo)
(10, 'Buraco profundo na Epitácio',
 'Buraco com aproximadamente 40cm de profundidade na faixa da direita da Av. Epitácio Pessoa, próximo ao hotel Tambaú. Causa desvio perigoso de veículos.',
 'buraco_na_via', 'em_andamento', 'alta', 10, 1,
 DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_ADD(CURDATE(), INTERVAL 7 DAY), NULL, NULL),

(11, 'Poste sem iluminação no Bessa',
 'Três postes consecutivos sem funcionar na Rua Deputado Odon Bezerra, deixando o trecho completamente escuro à noite, facilitando assaltos.',
 'iluminacao_publica', 'em_andamento', 'alta', 11, 2,
 DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL, NULL),

(12, 'Lixo acumulado no Altiplano',
 'Entulho e lixo doméstico despejados irregularmente na calçada da Rua Waldemar Bispo Duarte há mais de duas semanas.',
 'limpeza_urbana', 'em_andamento', 'media', 12, 3,
 DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_ADD(CURDATE(), INTERVAL 4 DAY), NULL, NULL),

(13, 'Terreno baldio com mato alto em Mangabeira',
 'Terreno baldio na esquina da Rua Josefa Taveira com vegetação acima de 1m, atraindo cobras e mosquitos. Denúncias de foco de dengue no local.',
 'terreno_baldio', 'em_andamento', 'alta', 14, 3,
 DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_ADD(CURDATE(), INTERVAL 10 DAY), NULL, NULL),

(14, 'Semáforo apagado na Cruz das Armas',
 'Semáforo completamente apagado no cruzamento da Av. Cruz das Armas com Rua Padre Zé. Cruzamento sem sinalização em horário de pico.',
 'outros', 'em_andamento', 'alta', 10, NULL,
 DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL, NULL),

-- ENCAMINHADAS (aguardando resolução)
(15, 'Calçada destruída na Torre',
 'Calçada completamente esburacada na Rua Afonso Campos no bairro Torre. Idosos e cadeirantes não conseguem passar.',
 'buraco_na_via', 'encaminhada', 'media', 11, 1,
 DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_ADD(CURDATE(), INTERVAL 2 DAY), NULL, NULL),

(16, 'Poste tombado na Valentina',
 'Poste de energia elétrica caído sobre a calçada da Rua das Palmeiras, no Valentina Figueiredo. Risco elétrico iminente.',
 'iluminacao_publica', 'encaminhada', 'alta', 12, 2,
 DATE_SUB(NOW(), INTERVAL 7 DAY), DATE_ADD(CURDATE(), INTERVAL 1 DAY), NULL, NULL),

(17, 'Alagamento crônico nos Bancários',
 'Cruzamento da Rua Professora Maria Sales com Rua José Borges alaga toda vez que chove, chegando a 50cm de lama.',
 'alagamento', 'encaminhada', 'alta', 13, 1,
 DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_ADD(CURDATE(), INTERVAL 3 DAY), NULL, NULL),

(18, 'Terreno baldio com esgoto a céu aberto',
 'Terreno abandonado na Av. Josefa Taveira em Mangabeira com esgoto correndo para a calçada. Cheiro insuportável e risco à saúde.',
 'terreno_baldio', 'encaminhada', 'alta', 14, 3,
 DATE_SUB(NOW(), INTERVAL 9 DAY), DATE_ADD(CURDATE(), INTERVAL 5 DAY), NULL, NULL),

-- RESOLVIDAS (concluídas com sucesso)
(19, 'Buraco tapado no Jardim Oceania',
 'Buraco de grande dimensão na Rua Projetada N, no Jardim Oceania. Estava causando acidentes com motociclistas.',
 'buraco_na_via', 'resolvida', 'alta', 12, 1,
 DATE_SUB(NOW(), INTERVAL 30 DAY), DATE_SUB(CURDATE(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), NULL),

(20, 'Iluminação restaurada na Manaíra',
 'Trecho de 200m da Rua Irineu Joffily em Manaíra com 5 postes apagados. Rua próxima a escola.',
 'iluminacao_publica', 'resolvida', 'media', 10, 2,
 DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), NULL),

(21, 'Limpeza de terreno no Bessa',
 'Terreno na Rua Deputado Odon Bezerra com acúmulo de resíduos e entulho. Situação persistia há 3 meses.',
 'terreno_baldio', 'resolvida', 'baixa', 11, 3,
 DATE_SUB(NOW(), INTERVAL 40 DAY), DATE_SUB(CURDATE(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY), NULL),

(22, 'Bueiro desobstruído no Altiplano',
 'Bueiro entupido na Rua Waldemar Bispo causando alagamento a cada chuva.',
 'alagamento', 'resolvida', 'media', 14, 1,
 DATE_SUB(NOW(), INTERVAL 35 DAY), DATE_SUB(CURDATE(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 14 DAY), NULL),

(23, 'Coleta de lixo irregular na Valentina',
 'Calçada da Rua das Palmeiras acumulando lixo sem coleta há 10 dias.',
 'limpeza_urbana', 'resolvida', 'baixa', 15, 3,
 DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(CURDATE(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), NULL),

-- ATRASADAS (prazo vencido, não resolvidas)
(24, 'Buraco antigo na Cruz das Armas',
 'Buraco existente há mais de 3 meses na Rua Padre Zé. Já foi reportado anteriormente sem resolução.',
 'buraco_na_via', 'atrasada', 'alta', 13, 1,
 DATE_SUB(NOW(), INTERVAL 45 DAY), DATE_SUB(CURDATE(), INTERVAL 10 DAY), NULL, NULL),

(25, 'Postes apagados em Mangabeira há semanas',
 'Cinco postes sem funcionar na Rua Josefa Taveira em Mangabeira. Comerciantes reclamam de aumento de furtos.',
 'iluminacao_publica', 'atrasada', 'alta', 12, 2,
 DATE_SUB(NOW(), INTERVAL 38 DAY), DATE_SUB(CURDATE(), INTERVAL 8 DAY), NULL, NULL),

(26, 'Lixo acumulado nos Bancários sem coleta',
 'Área de descarte irregular na Rua José Borges com lixo acumulado há 30 dias. Secretaria não respondeu.',
 'limpeza_urbana', 'atrasada', 'media', 10, 3,
 DATE_SUB(NOW(), INTERVAL 50 DAY), DATE_SUB(CURDATE(), INTERVAL 20 DAY), NULL, NULL),

-- CANCELADAS
(27, 'Buraco duplicado no Tambaú',
 'Buraco na Av. Epitácio Pessoa — já reportado por outro cidadão no mesmo endereço.',
 'buraco_na_via', 'cancelada', 'media', 15, NULL,
 DATE_SUB(NOW(), INTERVAL 4 DAY), NULL, NULL,
 'Ocorrência duplicada — problema já registrado sob o número #10.'),

(28, 'Lâmpada queimada na Manaíra',
 'Poste com lâmpada queimada na Rua Irineu Joffily em Manaíra.',
 'iluminacao_publica', 'cancelada', 'baixa', 16, NULL,
 DATE_SUB(NOW(), INTERVAL 55 DAY), NULL, NULL,
 'Ocorrência cancelada por usuário com reputação insuficiente após verificação.'),

-- Ocorrência preventiva ainda em andamento
(29, 'Risco de alagamento no cruzamento do Bessa',
 'Bueiro parcialmente obstruído na esquina da Rua Dep. Odon Bezerra com Av. Flávio Ribeiro Coutinho. Não alaga ainda mas tem potencial após chuva forte.',
 'alagamento', 'em_andamento', 'baixa', 11, NULL,
 DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_ADD(CURDATE(), INTERVAL 14 DAY), NULL, NULL);

-- ============================================================
-- LOCALIZAÇÕES (uma por ocorrência, IDs 10–29)
-- ============================================================
INSERT IGNORE INTO localizacoes (id, ocorrencia_id, estado, cidade, bairro, rua, numero, latitude, longitude) VALUES
(10, 10, 'PB', 'João Pessoa', 'Tambaú',          'Av. Epitácio Pessoa',           '2001', -7.1115, -34.8384),
(11, 11, 'PB', 'João Pessoa', 'Bessa',            'Rua Deputado Odon Bezerra',     '340',  -7.0870, -34.8340),
(12, 12, 'PB', 'João Pessoa', 'Altiplano',        'Rua Waldemar Bispo Duarte',     '88',   -7.0780, -34.8620),
(13, 13, 'PB', 'João Pessoa', 'Mangabeira',       'Rua Josefa Taveira',            '1502', -7.1620, -34.8480),
(14, 14, 'PB', 'João Pessoa', 'Cruz das Armas',   'Av. Cruz das Armas',            's/n',  -7.1430, -34.8870),
(15, 15, 'PB', 'João Pessoa', 'Torre',            'Rua Afonso Campos',             '210',  -7.1220, -34.8720),
(16, 16, 'PB', 'João Pessoa', 'Valentina',        'Rua das Palmeiras',             '55',   -7.1750, -34.8610),
(17, 17, 'PB', 'João Pessoa', 'Bancários',        'Rua Professora Maria Sales',    '401',  -7.1340, -34.8540),
(18, 18, 'PB', 'João Pessoa', 'Mangabeira',       'Av. Josefa Taveira',            '2200', -7.1650, -34.8460),
(19, 19, 'PB', 'João Pessoa', 'Jardim Oceania',   'Rua Projetada N',               '12',   -7.0920, -34.8290),
(20, 20, 'PB', 'João Pessoa', 'Manaíra',          'Rua Irineu Joffily',            '750',  -7.1050, -34.8360),
(21, 21, 'PB', 'João Pessoa', 'Bessa',            'Rua Deputado Odon Bezerra',     '610',  -7.0880, -34.8320),
(22, 22, 'PB', 'João Pessoa', 'Altiplano',        'Rua Waldemar Bispo Duarte',     '91',   -7.0785, -34.8625),
(23, 23, 'PB', 'João Pessoa', 'Valentina',        'Rua das Palmeiras',             '57',   -7.1755, -34.8615),
(24, 24, 'PB', 'João Pessoa', 'Cruz das Armas',   'Rua Padre Zé',                  '380',  -7.1410, -34.8880),
(25, 25, 'PB', 'João Pessoa', 'Mangabeira',       'Rua Josefa Taveira',            '1800', -7.1630, -34.8490),
(26, 26, 'PB', 'João Pessoa', 'Bancários',        'Rua José Borges',               '144',  -7.1330, -34.8530),
(27, 27, 'PB', 'João Pessoa', 'Tambaú',           'Av. Epitácio Pessoa',           '2003', -7.1116, -34.8385),
(28, 28, 'PB', 'João Pessoa', 'Manaíra',          'Rua Irineu Joffily',            '760',  -7.1052, -34.8362),
(29, 29, 'PB', 'João Pessoa', 'Bessa',            'Rua Dep. Odon Bezerra',         '500',  -7.0875, -34.8330);

-- ============================================================
-- HISTÓRICO DE STATUS (IDs 20–65)
-- ============================================================
INSERT IGNORE INTO historico_status (id, ocorrencia_id, status_anterior, status_novo, usuario_id, observacao, data) VALUES

-- Ocorrência 10 (em_andamento)
(20, 10, NULL,           'em_andamento', 10, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 3 DAY)),
(21, 10, 'em_andamento', 'em_andamento',  1, 'Ocorrência recebida e em análise pela equipe técnica.',                       DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Ocorrência 11 (em_andamento)
(22, 11, NULL,           'em_andamento', 11, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Ocorrência 12 (em_andamento)
(23, 12, NULL,           'em_andamento', 12, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Ocorrência 13 (em_andamento, alta prioridade)
(24, 13, NULL,           'em_andamento', 14, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 2 DAY)),

-- Ocorrência 14 (em_andamento)
(25, 14, NULL,           'em_andamento', 10, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 1 DAY)),

-- Ocorrência 15 (encaminhada)
(26, 15, NULL,           'em_andamento', 11, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 12 DAY)),
(27, 15, 'em_andamento', 'encaminhada',   1, 'Encaminhada para Secretaria de Obras e Infraestrutura.',                     DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Ocorrência 16 (encaminhada, urgente)
(28, 16, NULL,           'em_andamento', 12, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 7 DAY)),
(29, 16, 'em_andamento', 'encaminhada',   1, 'Encaminhada com prioridade para Secretaria de Infraestrutura. Risco elétrico.',DATE_SUB(NOW(), INTERVAL 6 DAY)),

-- Ocorrência 17 (encaminhada)
(30, 17, NULL,           'em_andamento', 13, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 18 DAY)),
(31, 17, 'em_andamento', 'encaminhada',   1, 'Equipe de drenagem urbana notificada.',                                       DATE_SUB(NOW(), INTERVAL 15 DAY)),

-- Ocorrência 18 (encaminhada)
(32, 18, NULL,           'em_andamento', 14, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 9 DAY)),
(33, 18, 'em_andamento', 'encaminhada',   1, 'Encaminhada para Secretaria de Meio Ambiente e Urbanismo.',                  DATE_SUB(NOW(), INTERVAL 7 DAY)),

-- Ocorrência 19 (resolvida, ciclo completo)
(34, 19, NULL,           'em_andamento', 12, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 30 DAY)),
(35, 19, 'em_andamento', 'encaminhada',   1, 'Encaminhada para equipe de tapa-buracos.',                                   DATE_SUB(NOW(), INTERVAL 26 DAY)),
(36, 19, 'encaminhada',  'em_andamento',  1, 'Equipe vistoriou o local. Material sendo preparado.',                        DATE_SUB(NOW(), INTERVAL 15 DAY)),
(37, 19, 'em_andamento', 'resolvida',     1, 'Buraco tapado com asfalto. Serviço concluído e área sinalizada.',            DATE_SUB(NOW(), INTERVAL 6 DAY)),

-- Ocorrência 20 (resolvida)
(38, 20, NULL,           'em_andamento', 10, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 25 DAY)),
(39, 20, 'em_andamento', 'encaminhada',   1, 'Encaminhada para equipe de manutenção de postes.',                           DATE_SUB(NOW(), INTERVAL 22 DAY)),
(40, 20, 'encaminhada',  'resolvida',     1, 'Lâmpadas substituídas em todos os 5 postes do trecho.',                     DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Ocorrência 21 (resolvida)
(41, 21, NULL,           'em_andamento', 11, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 40 DAY)),
(42, 21, 'em_andamento', 'encaminhada',   1, 'Encaminhada para Secretaria de Meio Ambiente.',                              DATE_SUB(NOW(), INTERVAL 35 DAY)),
(43, 21, 'encaminhada',  'resolvida',     1, 'Terreno limpo e proprietário notificado para manutenção periódica.',         DATE_SUB(NOW(), INTERVAL 18 DAY)),

-- Ocorrência 22 (resolvida)
(44, 22, NULL,           'em_andamento', 14, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 35 DAY)),
(45, 22, 'em_andamento', 'encaminhada',   1, 'Equipe de drenagem notificada.',                                              DATE_SUB(NOW(), INTERVAL 30 DAY)),
(46, 22, 'encaminhada',  'resolvida',     1, 'Bueiro limpo e desobstruído. Capacidade de escoamento restaurada.',          DATE_SUB(NOW(), INTERVAL 14 DAY)),

-- Ocorrência 23 (resolvida)
(47, 23, NULL,           'em_andamento', 15, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 20 DAY)),
(48, 23, 'em_andamento', 'encaminhada',   1, 'Encaminhada para Secretaria de Limpeza Urbana.',                             DATE_SUB(NOW(), INTERVAL 18 DAY)),
(49, 23, 'encaminhada',  'resolvida',     1, 'Coleta realizada e calçada higienizada.',                                    DATE_SUB(NOW(), INTERVAL 4 DAY)),

-- Ocorrência 24 (atrasada — ciclo de atraso)
(50, 24, NULL,           'em_andamento', 13, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 45 DAY)),
(51, 24, 'em_andamento', 'encaminhada',   1, 'Encaminhada para equipe de pavimentação.',                                   DATE_SUB(NOW(), INTERVAL 40 DAY)),
(52, 24, 'encaminhada',  'atrasada',      1, 'Prazo expirado sem resolução. Equipe sem material disponível.',              DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Ocorrência 25 (atrasada)
(53, 25, NULL,           'em_andamento', 12, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 38 DAY)),
(54, 25, 'em_andamento', 'encaminhada',   1, 'Encaminhada para manutenção elétrica.',                                      DATE_SUB(NOW(), INTERVAL 34 DAY)),
(55, 25, 'encaminhada',  'atrasada',      1, 'Prazo expirado. Aguardando peças de reposição.',                             DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Ocorrência 26 (atrasada)
(56, 26, NULL,           'em_andamento', 10, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 50 DAY)),
(57, 26, 'em_andamento', 'encaminhada',   1, 'Encaminhada para Limpeza Urbana.',                                           DATE_SUB(NOW(), INTERVAL 46 DAY)),
(58, 26, 'encaminhada',  'atrasada',      1, 'Prazo expirado. Setor sobrecarregado com demandas da região.',               DATE_SUB(NOW(), INTERVAL 20 DAY)),

-- Ocorrência 27 (cancelada por duplicidade)
(59, 27, NULL,           'em_andamento', 15, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 4 DAY)),
(60, 27, 'em_andamento', 'cancelada',     1, 'Cancelada — problema já registrado sob ocorrência #10 no mesmo endereço.',  DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Ocorrência 28 (cancelada)
(61, 28, NULL,           'em_andamento', 16, 'Ocorrência registrada pelo cidadão.',                                         DATE_SUB(NOW(), INTERVAL 55 DAY)),
(62, 28, 'em_andamento', 'cancelada',     1, 'Cancelada. Usuário com reputação insuficiente para registro de ocorrências.',DATE_SUB(NOW(), INTERVAL 54 DAY)),

-- Ocorrência 29 (em_andamento, preventiva)
(63, 29, NULL,           'em_andamento', 11, 'Ocorrência preventiva registrada pelo cidadão.',                              DATE_SUB(NOW(), INTERVAL 1 DAY));

-- ============================================================
-- APOIOS (IDs 10–40, usuários apoiando ocorrências variadas)
-- ============================================================
INSERT IGNORE INTO apoios (id, ocorrencia_id, usuario_id, data) VALUES
-- Ocorrência 10 (buraco Tambaú) — muito apoio
(10, 10, 11, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(11, 10, 12, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(12, 10, 13, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(13, 10, 14, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(14, 10,  2, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(15, 10,  3, NOW()),
-- Ocorrência 11 (postes Bessa)
(16, 11, 10, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(17, 11, 13, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(18, 11,  4, DATE_SUB(NOW(), INTERVAL 2 DAY)),
-- Ocorrência 13 (terreno Mangabeira — urgente)
(19, 13, 10, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(20, 13, 11, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(21, 13, 12, NOW()),
(22, 13,  2, NOW()),
-- Ocorrência 15 (calçada Torre)
(23, 15, 10, DATE_SUB(NOW(), INTERVAL 11 DAY)),
(24, 15, 12, DATE_SUB(NOW(), INTERVAL 10 DAY)),
-- Ocorrência 17 (alagamento Bancários)
(25, 17, 14, DATE_SUB(NOW(), INTERVAL 17 DAY)),
(26, 17, 15, DATE_SUB(NOW(), INTERVAL 16 DAY)),
(27, 17, 10, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(28, 17,  2, DATE_SUB(NOW(), INTERVAL 14 DAY)),
-- Ocorrência 19 (resolvida — buraco Jardim Oceania)
(29, 19, 10, DATE_SUB(NOW(), INTERVAL 28 DAY)),
(30, 19, 13, DATE_SUB(NOW(), INTERVAL 25 DAY)),
(31, 19, 14, DATE_SUB(NOW(), INTERVAL 22 DAY)),
(32, 19, 15, DATE_SUB(NOW(), INTERVAL 20 DAY)),
-- Ocorrência 24 (atrasada — apoio para pressionar)
(33, 24, 10, DATE_SUB(NOW(), INTERVAL 30 DAY)),
(34, 24, 11, DATE_SUB(NOW(), INTERVAL 28 DAY)),
(35, 24, 12, DATE_SUB(NOW(), INTERVAL 25 DAY)),
(36, 24,  2, DATE_SUB(NOW(), INTERVAL 20 DAY)),
(37, 24,  3, DATE_SUB(NOW(), INTERVAL 15 DAY)),
-- Ocorrência 29 (preventiva, recente)
(38, 29, 12, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(39, 29, 13, NOW()),
(40, 29, 14, NOW());

-- ============================================================
-- COMENTÁRIOS (IDs 10–35, conversas realistas)
-- ============================================================
INSERT IGNORE INTO comentarios (id, ocorrencia_id, usuario_id, texto, data) VALUES

-- Ocorrência 10 (buraco Epitácio)
(10, 10, 11, 'Passei por lá ontem de carro. O buraco está cada vez maior. Alguém já ligou para a prefeitura?',                    DATE_SUB(NOW(), INTERVAL 2 DAY)),
(11, 10, 12, 'Já liguei para o 156 duas vezes. Disseram que vão mandar equipe até sexta.',                                         DATE_SUB(NOW(), INTERVAL 2 DAY)),
(12, 10,  2, 'Moro na rua ao lado. Um carro quebrou o eixo aqui semana passada por causa desse buraco.',                          DATE_SUB(NOW(), INTERVAL 1 DAY)),
(13, 10, 14, 'Apoiei a ocorrência. Quanto mais gente apoiar, mais rápido eles resolvem.',                                          NOW()),

-- Ocorrência 11 (postes Bessa)
(14, 11, 10, 'Essa rua fica perigosíssima à noite. Já teve dois assaltos no mês passado nesse trecho.',                           DATE_SUB(NOW(), INTERVAL 4 DAY)),
(15, 11,  4, 'A escola municipal fica aqui perto. Os alunos da tarde saem às 17h e está escuro. Urgente!',                        DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Ocorrência 13 (terreno Mangabeira)
(16, 13, 10, 'Visinhança já limpou esse terreno duas vezes. O proprietário precisa ser multado.',                                   DATE_SUB(NOW(), INTERVAL 1 DAY)),
(17, 13, 12, 'Vi uma cobra no terreno na semana passada. Isso é urgente com crianças morando ao lado.',                            DATE_SUB(NOW(), INTERVAL 1 DAY)),
(18, 13,  2, 'Confirmado foco de dengue pelo agente de saúde. Situação crítica.',                                                  NOW()),

-- Ocorrência 15 (calçada Torre)
(19, 15, 13, 'Minha mãe usa cadeira de rodas e não consegue passar por essa calçada. Há meses assim.',                            DATE_SUB(NOW(), INTERVAL 11 DAY)),
(20, 15, 14, 'A prefeitura encaminhou mas ainda não veio ninguém. Alguém tem contato direto com a secretaria?',                    DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Ocorrência 17 (alagamento Bancários)
(21, 17, 15, 'Toda vez que chove mais de 20 minutos essa rua fica com água na altura do joelho.',                                  DATE_SUB(NOW(), INTERVAL 17 DAY)),
(22, 17, 10, 'Já perdi dois pares de sapato andando por aqui. É inaceitável num bairro central como esse.',                       DATE_SUB(NOW(), INTERVAL 15 DAY)),
(23, 17,  3, 'O bueiro que vai para o canal maior está entupido. É a causa raiz do problema.',                                     DATE_SUB(NOW(), INTERVAL 13 DAY)),

-- Ocorrência 19 (resolvida — buraco Jardim Oceania)
(24, 19, 13, 'Passaram pelo local ontem e fizeram um bom serviço. Buraco completamente fechado.',                                  DATE_SUB(NOW(), INTERVAL 6 DAY)),
(25, 19, 14, 'Confirmo a resolução. A rua ficou muito melhor. Obrigado a quem apoiou e reportou!',                                DATE_SUB(NOW(), INTERVAL 5 DAY)),

-- Ocorrência 20 (resolvida — postes Manaíra)
(26, 20, 11, 'Os postes voltaram. A rua agora está bem iluminada. Rápida resolução!',                                             DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Ocorrência 24 (atrasada — Cruz das Armas)
(27, 24, 11, 'Esse buraco existe há mais de 3 meses. A secretaria já prometeu resolver duas vezes e não cumpriu.',                 DATE_SUB(NOW(), INTERVAL 35 DAY)),
(28, 24, 13, 'Uma criança caiu de bicicleta aqui na semana passada. Quando vão resolver?',                                         DATE_SUB(NOW(), INTERVAL 20 DAY)),
(29, 24, 15, 'Prazo expirado e nenhuma ação. Vou levar isso para o vereador do bairro.',                                          DATE_SUB(NOW(), INTERVAL 8 DAY)),

-- Ocorrência 25 (atrasada — postes Mangabeira)
(30, 25, 10, 'Cinco postes apagados e a rua está completamente escura. Comerciantes estão sofrendo com furtos.',                   DATE_SUB(NOW(), INTERVAL 30 DAY)),
(31, 25, 12, 'Já entrei em contato com o vereador. Disseram que a peça de reposição está sendo importada. Absurdo.',               DATE_SUB(NOW(), INTERVAL 15 DAY)),

-- Ocorrência 29 (preventiva — bueiro Bessa)
(32, 29, 12, 'Bom que alguém reportou antes de alagар. Esse cruzamento sempre alagou em anos anteriores.',                        DATE_SUB(NOW(), INTERVAL 1 DAY)),
(33, 29, 13, 'Apoiei. Prevenção é melhor do que remediar depois que já alagou.',                                                   NOW());

-- ============================================================
-- NOTIFICAÇÕES (IDs 10–45)
-- ============================================================
INSERT IGNORE INTO notificacoes (id, usuario_id, ocorrencia_id, mensagem, tipo, visualizada, data) VALUES

-- Notificações para Ana Paula (10)
(10, 10, 10, 'Sua ocorrência #10 "Buraco profundo na Epitácio" foi registrada com sucesso.',                'status', 1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(11, 10, 10, 'Sua ocorrência #10 recebeu 6 apoios da comunidade!',                                          'apoio',  0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(12, 10, 14, 'Sua ocorrência #14 "Semáforo apagado" foi registrada com sucesso.',                           'status', 0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(13, 10, 26, 'Atenção: prazo da sua ocorrência #26 expirou sem resolução.',                                  'status', 0, DATE_SUB(NOW(), INTERVAL 20 DAY)),

-- Notificações para Roberto Lima (11)
(14, 11, 11, 'Sua ocorrência #11 "Poste sem iluminação no Bessa" foi registrada com sucesso.',              'status', 1, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(15, 11, 11, 'Sua ocorrência #11 recebeu 3 apoios!',                                                        'apoio',  1, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(16, 11, 15, 'Sua ocorrência #15 foi encaminhada para a Secretaria de Obras e Infraestrutura.',             'status', 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(17, 11, 15, 'Prazo da ocorrência #15 vence em 2 dias. Acompanhe no painel.',                                'status', 0, NOW()),

-- Notificações para Fernanda Costa (12)
(18, 12, 12, 'Sua ocorrência #12 "Lixo acumulado no Altiplano" foi registrada.',                            'status', 1, DATE_SUB(NOW(), INTERVAL 8 DAY)),
(19, 12, 16, 'Sua ocorrência #16 foi encaminhada com prioridade para Infraestrutura.',                      'status', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(20, 12, 19, 'Sua ocorrência #19 foi marcada como RESOLVIDA! Obrigado pela denúncia.',                      'status', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(21, 12, 20, 'Sua ocorrência #20 foi marcada como RESOLVIDA!',                                              'status', 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Notificações para Lucas Oliveira (13)
(22, 13, 17, 'Sua ocorrência #17 "Alagamento crônico nos Bancários" foi registrada.',                       'status', 1, DATE_SUB(NOW(), INTERVAL 18 DAY)),
(23, 13, 17, 'Sua ocorrência #17 foi encaminhada para equipe de drenagem urbana.',                          'status', 1, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(24, 13, 17, 'Sua ocorrência #17 recebeu 4 apoios da comunidade!',                                          'apoio',  0, DATE_SUB(NOW(), INTERVAL 14 DAY)),
(25, 13, 24, 'Atenção: o prazo da sua ocorrência #24 está expirado há 10 dias.',                            'status', 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),

-- Notificações para Juliana Nascimento (14)
(26, 14, 13, 'Sua ocorrência #13 "Terreno baldio com mato alto" foi registrada com alta prioridade.',       'status', 1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(27, 14, 13, 'Sua ocorrência #13 recebeu 4 apoios em menos de 24h!',                                        'apoio',  0, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(28, 14, 22, 'Sua ocorrência #22 "Bueiro desobstruído" foi RESOLVIDA.',                                     'status', 1, DATE_SUB(NOW(), INTERVAL 14 DAY)),

-- Notificações para Pedro Henrique (15)
(29, 15, 23, 'Sua ocorrência #23 foi encaminhada para Secretaria de Limpeza Urbana.',                       'status', 1, DATE_SUB(NOW(), INTERVAL 18 DAY)),
(30, 15, 23, 'Sua ocorrência #23 foi RESOLVIDA! Coleta realizada.',                                         'status', 1, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(31, 15, 27, 'Sua ocorrência #27 foi cancelada por duplicidade.',                                            'status', 0, DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Notificações cruzadas (outros usuários sendo notificados de atualizações)
(32,  2, 17, 'A ocorrência #17 que você apoiou foi encaminhada para a equipe de drenagem.',                 'apoio',  0, DATE_SUB(NOW(), INTERVAL 15 DAY)),
(33,  3, 10, 'A ocorrência #10 que você apoiou recebeu nova atualização.',                                   'apoio',  1, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(34,  4, 11, 'A ocorrência #11 que você apoiou ainda está em andamento.',                                    'apoio',  1, DATE_SUB(NOW(), INTERVAL 3 DAY)),

-- Notificações de sistema para admin
(35,  1, 16, 'Alerta: Ocorrência #16 com risco elétrico encaminhada. Ação prioritária necessária.',         'status', 1, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(36,  1, 24, 'Ocorrência #24 está atrasada há 10 dias. Requer atenção imediata.',                           'status', 0, DATE_SUB(NOW(), INTERVAL 10 DAY)),
(37,  1, 25, 'Ocorrência #25 com prazo expirado em Mangabeira. Comunidade insatisfeita.',                    'status', 0, DATE_SUB(NOW(), INTERVAL 8 DAY));

-- ============================================================
-- VERIFICAÇÃO FINAL
-- ============================================================
SELECT 'usuarios'       AS tabela, COUNT(*) AS total FROM usuarios
UNION ALL
SELECT 'ocorrencias',     COUNT(*) FROM ocorrencias
UNION ALL
SELECT 'localizacoes',    COUNT(*) FROM localizacoes
UNION ALL
SELECT 'historico_status',COUNT(*) FROM historico_status
UNION ALL
SELECT 'apoios',          COUNT(*) FROM apoios
UNION ALL
SELECT 'comentarios',     COUNT(*) FROM comentarios
UNION ALL
SELECT 'notificacoes',    COUNT(*) FROM notificacoes;
