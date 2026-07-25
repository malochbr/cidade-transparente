<?php
/**
 * Controller OcorrenciaController
 * 
 * Controla a navegação pública e o fluxo de criação em 3 passos (wizard) de ocorrências,
 * validação de mídias enviadas, listagens, apoios e interações dos cidadãos.
 */

require_once __DIR__ . '/../models/Ocorrencia.php';
require_once __DIR__ . '/../models/Usuario.php';

class OcorrenciaController {
    private Ocorrencia $ocorrenciaModel;
    private Usuario $usuarioModel;

    public function __construct() {
        $db = Database::getInstance();
        $this->ocorrenciaModel = new Ocorrencia($db);
        $this->usuarioModel = new Usuario($db);
    }

    /**
     * Exibe a página inicial do cidadão com resumo das suas últimas ocorrências.
     */
    public function home(): void {
        if (!isLoggedIn()) {
            redirect('?page=auth/login');
        }

        $idUsuario = $_SESSION['user_id'];
        $usuario = $this->usuarioModel->findById($idUsuario);
        $minhasOcorrencias = $this->ocorrenciaModel->findByUsuario($idUsuario, 3);

        $tituloPagina = 'Início';
        $activePage = 'home';
        require_once __DIR__ . '/../views/home/index.php';
    }

    /**
     * Exibe o painel público com todas as ocorrências da cidade e filtros de busca.
     */
    public function painel(): void {
        $filtros = [
            'status' => $_GET['status'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'bairro' => $_GET['bairro'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];

        // Busca até 20 ocorrências por página aplicando os filtros da URL
        $ocorrencias = $this->ocorrenciaModel->findAll($filtros, 20);

        $tituloPagina = 'Painel Público';
        $activePage = 'painel';
        require_once __DIR__ . '/../views/painel/index.php';
    }

    /**
     * Exibe os detalhes completos de uma ocorrência específica.
     *
     * @param int $idOcorrencia ID da ocorrência
     */
    public function detalhe(int $idOcorrencia): void {
        $ocorrencia = $this->ocorrenciaModel->findById($idOcorrencia);

        if (!$ocorrencia) {
            flash('Ocorrência não encontrada.', 'error');
            redirect('?page=painel');
        }

        $userApoiou = false;
        if (isLoggedIn()) {
            $userApoiou = $this->ocorrenciaModel->userApoiou($idOcorrencia, $_SESSION['user_id']);
        }

        $tituloPagina = "Ocorrência #" . $idOcorrencia;
        $activePage = 'ocorrencias';
        require_once __DIR__ . '/../views/ocorrencias/detalhe.php';
    }

    /**
     * Lista todas as ocorrências registradas pelo cidadão logado.
     */
    public function listarMinhas(): void {
        if (!isLoggedIn()) {
            redirect('?page=auth/login');
        }

        $idUsuario = $_SESSION['user_id'];
        $ocorrencias = $this->ocorrenciaModel->findByUsuario($idUsuario, 50);

        $tituloPagina = 'Minhas Ocorrências';
        $activePage = 'ocorrencias';
        require_once __DIR__ . '/../views/ocorrencias/minhas.php';
    }

    /**
     * Controla a exibição das telas do Wizard em 3 passos para nova ocorrência.
     */
    public function novaOcorrencia(): void {
        if (!isLoggedIn()) {
            redirect('?page=auth/login');
        }

        // Bloqueia o envio se a reputação do cidadão estiver abaixo de 200 pontos
        $usuario = $this->usuarioModel->findById($_SESSION['user_id']);
        if ($usuario['reputacao'] < 200) {
            flash('Sua conta está bloqueada para criar novas ocorrências devido à reputação baixa (< 200 pts).', 'error');
            redirect('?page=home');
        }

        $step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
        if (!in_array($step, [1, 2, 3])) $step = 1;

        $tituloPagina = 'Nova Ocorrência';
        $activePage = 'nova';

        // Redireciona a visualização da view conforme o passo atual salvo na sessão
        if ($step === 1) {
            require_once __DIR__ . '/../views/ocorrencias/nova-step1.php';
        } elseif ($step === 2) {
            if (empty($_SESSION['nova_ocorrencia']['categoria'])) {
                redirect('?page=nova-ocorrencia&step=1');
            }
            require_once __DIR__ . '/../views/ocorrencias/nova-step2.php';
        } elseif ($step === 3) {
            if (empty($_SESSION['nova_ocorrencia']['categoria']) || empty($_SESSION['nova_ocorrencia']['bairro'])) {
                redirect('?page=nova-ocorrencia&step=1');
            }
            require_once __DIR__ . '/../views/ocorrencias/nova-step3.php';
        }
    }

    /**
     * Salva a categoria selecionada no Passo 1 do wizard na sessão.
     */
    public function salvarStep1(): void {
        if (!isLoggedIn() || !verify_csrf()) redirect('?page=auth/login');

        $categoria = $_POST['categoria'] ?? '';
        $categoriasValidas = ['buraco_na_via', 'iluminacao_publica', 'alagamento', 'terreno_baldio', 'limpeza_urbana', 'outros'];

        if (!in_array($categoria, $categoriasValidas)) {
            flash('Selecione uma categoria válida.', 'error');
            redirect('?page=nova-ocorrencia&step=1');
        }

        $_SESSION['nova_ocorrencia']['categoria'] = $categoria;
        redirect('?page=nova-ocorrencia&step=2');
    }

    /**
     * Salva o endereço e localização no Passo 2 do wizard na sessão.
     */
    public function salvarStep2(): void {
        if (!isLoggedIn() || !verify_csrf()) redirect('?page=auth/login');

        $estado = $_POST['estado'] ?? 'PB';
        $cidade = $_POST['cidade'] ?? 'João Pessoa';
        $bairro = $_POST['bairro'] ?? '';
        $rua = $_POST['rua'] ?? '';
        $numero = $_POST['numero'] ?? '';

        if (empty($bairro) || empty($rua)) {
            flash('Preencha os campos de Bairro e Rua obrigatoriamente.', 'error');
            redirect('?page=nova-ocorrencia&step=2');
        }

        $_SESSION['nova_ocorrencia']['estado'] = $estado;
        $_SESSION['nova_ocorrencia']['cidade'] = $cidade;
        $_SESSION['nova_ocorrencia']['bairro'] = $bairro;
        $_SESSION['nova_ocorrencia']['rua'] = $rua;
        $_SESSION['nova_ocorrencia']['numero'] = $numero;
        $_SESSION['nova_ocorrencia']['latitude'] = $_POST['latitude'] ?? -7.11500000;
        $_SESSION['nova_ocorrencia']['longitude'] = $_POST['longitude'] ?? -34.86300000;

        redirect('?page=nova-ocorrencia&step=3');
    }

    /**
     * Salva os detalhes finais (título, descrição, mídias) e consolida a ocorrência no banco.
     */
    public function salvarStep3(): void {
        if (!isLoggedIn() || !verify_csrf()) redirect('?page=auth/login');

        $titulo = $_POST['titulo'] ?? '';
        $descricao = $_POST['descricao'] ?? '';

        if (empty($titulo) || strlen($descricao) < 20) {
            flash('Informe um título e uma descrição detalhada de no mínimo 20 caracteres.', 'error');
            redirect('?page=nova-ocorrencia&step=3');
        }

        $sessionData = $_SESSION['nova_ocorrencia'] ?? [];
        $categoria = $sessionData['categoria'] ?? 'outros';
        $bairro = $sessionData['bairro'] ?? '';

        // Verifica se a ocorrência é duplicada no mesmo bairro nos últimos 7 dias
        if ($this->ocorrenciaModel->checkDuplicidade($categoria, $bairro)) {
            // Aplica penalidade de -50 pontos na reputação do cidadão por tentativa de duplicidade
            $this->usuarioModel->updateReputacao($_SESSION['user_id'], -50);
            flash('Aviso: Já existe uma ocorrência ativa com esta categoria no mesmo bairro nos últimos 7 dias. Seu registro foi bloqueado por duplicidade (-50 pts de reputação).', 'warning');
            unset($_SESSION['nova_ocorrencia']);
            redirect('?page=home');
        }

        // Validação e upload de arquivos de imagem/vídeo
        $midiasProcessadas = [];
        $uploadDir = UPLOAD_PATH . 'ocorrencias/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (!empty($_FILES['midias']['name'][0])) {
            $totalArquivos = count($_FILES['midias']['name']);
            if ($totalArquivos > 3) {
                flash('Você pode enviar no máximo 3 arquivos de mídia.', 'error');
                redirect('?page=nova-ocorrencia&step=3');
            }

            for ($i = 0; $i < $totalArquivos; $i++) {
                if ($_FILES['midias']['error'][$i] === UPLOAD_ERR_OK) {
                    $tmpName = $_FILES['midias']['tmp_name'][$i];
                    $nomeArquivo = $_FILES['midias']['name'][$i];
                    $fileSize = $_FILES['midias']['size'][$i];
                    $extensao = strtolower(pathinfo($nomeArquivo, PATHINFO_EXTENSION));

                    // Verifica o tamanho máximo permitido (5MB)
                    if ($fileSize > MAX_FILE_SIZE) {
                        flash("O arquivo {$nomeArquivo} excede o tamanho máximo permitido (5MB).", 'error');
                        redirect('?page=nova-ocorrencia&step=3');
                    }

                    // Verifica a extensão do arquivo
                    if (!in_array($extensao, ALLOWED_EXTENSIONS)) {
                        flash("A extensão .{$extensao} não é suportada.", 'error');
                        redirect('?page=nova-ocorrencia&step=3');
                    }

                    // Renomeia o arquivo com hash único para evitar colisões
                    $novoNome = uniqid('midia_') . '.' . $extensao;
                    $destino = $uploadDir . $novoNome;

                    if (move_uploaded_file($tmpName, $destino)) {
                        $tipo = ($extensao === 'mp4') ? 'video' : 'imagem';
                        $midiasProcessadas[] = [
                            'tipo' => $tipo,
                            'arquivo' => 'public/uploads/ocorrencias/' . $novoNome
                        ];
                    }
                }
            }
        }

        // Monta o array consolidado de dados da ocorrência
        $dadosOcorrencia = array_merge($sessionData, [
            'titulo' => $titulo,
            'descricao' => $descricao,
            'usuario_id' => $_SESSION['user_id']
        ]);

        try {
            // Executa a transação completa no model de Ocorrencia
            $idOcorrencia = $this->ocorrenciaModel->create($dadosOcorrencia, $midiasProcessadas);
            unset($_SESSION['nova_ocorrencia']);
            flash('Ocorrência #' . $idOcorrencia . ' cadastrada com sucesso!', 'success');
            redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
        } catch (Exception $e) {
            flash('Erro ao salvar a ocorrência: ' . $e->getMessage(), 'error');
            redirect('?page=nova-ocorrencia&step=3');
        }
    }

    /**
     * Alterna (liga/desliga) o apoio do cidadão logado a uma ocorrência.
     *
     * @param int $idOcorrencia ID da ocorrência
     */
    public function apoiar(int $idOcorrencia): void {
        if (!isLoggedIn()) redirect('?page=auth/login');
        if (!verify_csrf()) redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);

        $this->ocorrenciaModel->apoiar($idOcorrencia, $_SESSION['user_id']);
        flash('Seu apoio foi atualizado!', 'success');
        redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
    }

    /**
     * Publica um novo comentário cidadão sobre a ocorrência.
     *
     * @param int $idOcorrencia ID da ocorrência
     */
    public function comentar(int $idOcorrencia): void {
        if (!isLoggedIn()) redirect('?page=auth/login');
        if (!verify_csrf()) redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);

        $texto = $_POST['texto'] ?? '';
        if (strlen(trim($texto)) < 5) {
            flash('O comentário deve conter no mínimo 5 caracteres.', 'error');
            redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
        }

        $this->ocorrenciaModel->comentar($idOcorrencia, $_SESSION['user_id'], $texto);
        flash('Comentário publicado!', 'success');
        redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
    }

    /**
     * Registra a resposta do cidadão (valida ou contra-valida) referente à solução da prefeitura.
     *
     * @param int $idOcorrencia ID da ocorrência
     */
    public function validarResolucao(int $idOcorrencia): void {
        if (!isLoggedIn()) redirect('?page=auth/login');
        if (!verify_csrf()) redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);

        $tipo = $_POST['tipo'] ?? 'valida'; // 'valida' ou 'contra_valida'
        $this->ocorrenciaModel->validarResolucao($idOcorrencia, $_SESSION['user_id'], $tipo);

        flash('Sua validação de resolução foi registrada!', 'success');
        redirect('?page=ocorrencia/detalhe&id=' . $idOcorrencia);
    }
}
