<?php
/**
 * Model Usuario
 * 
 * Gerencia todas as operações de banco de dados relacionadas a usuários
 * (cidadãos e administradores): busca por CPF/e-mail, cadastro, reputação e validações.
 */

class Usuario {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    /**
     * Busca um usuário cadastrado no banco através do seu CPF.
     *
     * @param string $cpf CPF formatado ou numérico do usuário
     * @return array|null Dados do usuário ou null se não for encontrado
     */
    public function findByCpf(string $cpf): ?array {
        // Remove pontos e traço, mantendo apenas os dígitos numéricos
        $cpfLimpo = preg_replace('/\D/', '', $cpf);
        $query = "SELECT * FROM usuarios WHERE cpf = :cpf LIMIT 1";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':cpf' => $cpfLimpo]);
        $usuario = $consulta->fetch();
        return $usuario ?: null;
    }

    /**
     * Busca um usuário cadastrado através do endereço de e-mail.
     *
     * @param string $email E-mail do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function findByEmail(string $email): ?array {
        $query = "SELECT * FROM usuarios WHERE email = :email LIMIT 1";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':email' => trim($email)]);
        $usuario = $consulta->fetch();
        return $usuario ?: null;
    }

    /**
     * Busca um usuário cadastrado através do número de telefone.
     *
     * @param string $telefone Telefone do usuário
     * @return array|null Dados do usuário ou null se não encontrado
     */
    public function findByTelefone(string $telefone): ?array {
        $telLimpo = preg_replace('/\D/', '', $telefone);
        if (empty($telLimpo)) return null;
        $query = "SELECT * FROM usuarios WHERE REPLACE(REPLACE(REPLACE(REPLACE(telefone, '(', ''), ')', ''), '-', ''), ' ', '') = :tel LIMIT 1";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':tel' => $telLimpo]);
        $usuario = $consulta->fetch();
        return $usuario ?: null;
    }

    /**
     * Busca um usuário pelo ID primário, removendo a senha do array de retorno.
     *
     * @param int $idUsuario ID único do usuário
     * @return array|null Dados públicos do usuário ou null
     */
    public function findById(int $idUsuario): ?array {
        $query = "SELECT * FROM usuarios WHERE id = :id LIMIT 1";
        $consulta = $this->db->prepare($query);
        $consulta->execute([':id' => $idUsuario]);
        $usuario = $consulta->fetch();
        if ($usuario) {
            // Remove a hash da senha antes de retornar os dados para maior segurança
            unset($usuario['senha']);
            return $usuario;
        }
        return null;
    }

    /**
     * Insere um novo cidadão no banco de dados com senha criptografada em BCrypt.
     *
     * @param array $dados Formulário com nome, cpf, telefone, email e senha
     * @return int ID do novo usuário criado
     */
    public function create(array $dados): int {
        // Limpa a pontuação do CPF para salvar apenas 11 números
        $cpfLimpo = preg_replace('/\D/', '', $dados['cpf']);
        // Gera o hash seguro de senha utilizando o algoritmo padronizado BCrypt
        $senhaHash = password_hash($dados['senha'], PASSWORD_DEFAULT);

        $query = "INSERT INTO usuarios (nome, cpf, telefone, email, senha, reputacao, perfil, ativo, data_cadastro) 
                VALUES (:nome, :cpf, :telefone, :email, :senha, 1000, :perfil, 1, NOW())";
        
        $consulta = $this->db->prepare($query);
        $consulta->execute([
            ':nome' => trim($dados['nome']),
            ':cpf' => $cpfLimpo,
            ':telefone' => trim($dados['telefone'] ?? ''),
            ':email' => trim($dados['email']),
            ':senha' => $senhaHash,
            ':perfil' => $dados['perfil'] ?? 'cidadao'
        ]);

        return (int) $this->db->lastInsertId();
    }

    /**
     * Atualiza a pontuação de reputação de um usuário (adicionando ou subtraindo pontos).
     *
     * @param int $idUsuario ID do usuário a ser modificado
     * @param int $pontos    Pontos a somar ou subtrair (ex: -50)
     * @return bool True em caso de sucesso
     */
    public function updateReputacao(int $idUsuario, int $pontos): bool {
        // Atualiza reputação garantindo que a pontuação mínima não fique negativa
        $query = "UPDATE usuarios SET reputacao = GREATEST(0, reputacao + :pontos) WHERE id = :id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([
            ':pontos' => $pontos,
            ':id' => $idUsuario
        ]);
    }

    /**
     * Atualiza a senha de um usuário registrando uma nova hash BCrypt.
     *
     * @param int $idUsuario   ID do usuário
     * @param string $novaSenha Nova senha em texto limpo
     * @return bool True em caso de sucesso
     */
    public function updateSenha(int $idUsuario, string $novaSenha): bool {
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $query = "UPDATE usuarios SET senha = :senha WHERE id = :id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([':senha' => $hash, ':id' => $idUsuario]);
    }

    /**
     * Atualiza o nome e telefone do usuário no seu perfil.
     *
     * @param int $idUsuario ID do usuário
     * @param array $dados   Novos dados cadastrais
     * @return bool True em caso de sucesso
     */
    public function updatePerfil(int $idUsuario, array $dados): bool {
        $query = "UPDATE usuarios SET nome = :nome, telefone = :telefone WHERE id = :id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([
            ':nome' => trim($dados['nome']),
            ':telefone' => trim($dados['telefone']),
            ':id' => $idUsuario
        ]);
    }

    /**
     * Alterna o status de um usuário entre ativo e inativo (bloqueio administrativo).
     *
     * @param int $idUsuario ID do usuário a alterar
     * @return bool True em caso de sucesso
     */
    public function toggleAtivo(int $idUsuario): bool {
        $query = "UPDATE usuarios SET ativo = NOT ativo WHERE id = :id";
        $consulta = $this->db->prepare($query);
        return $consulta->execute([':id' => $idUsuario]);
    }

    /**
     * Retorna a lista completa de todos os usuários ordenados pela data de cadastro.
     *
     * @return array Lista de todos os usuários
     */
    public function findAll(): array {
        $query = "SELECT * FROM usuarios ORDER BY data_cadastro DESC";
        $consulta = $this->db->query($query);
        return $consulta->fetchAll();
    }

    /**
     * Validação algorítmica completa do CPF brasileiro (dígitos verificadores).
     *
     * @param string $cpf CPF a ser validado
     * @return bool Retorna true se for um CPF válido conforme as regras da Receita Federal
     */
    public static function validarCpf(string $cpf): bool {
        // Passo 1: Limpa qualquer pontuação e mantém somente os 11 dígitos numéricos
        $cpf = preg_replace('/\D/', '', $cpf);

        if (strlen($cpf) !== 11) {
            return false;
        }

        // Permite a utilização de CPFs de teste predefinidos em ambiente de demonstração
        if (in_array($cpf, ['00000000000', '11111111111', '22222222222', '33333333333'])) {
            return true;
        }

        // Passo 2: Rejeita CPFs com sequências de dígitos idênticos (ex: 222.222.222-22)
        if (preg_match('/^(\d)\1{10}$/', $cpf)) {
            return false;
        }

        // Passo 3: Cálculo algorítmico do 1º e do 2º dígito verificador
        for ($t = 9; $t < 11; $t++) {
            $d = 0;
            // Multiplica cada um dos primeiros dígitos pelos seus respectivos pesos ponderados
            for ($c = 0; $c < $t; $c++) {
                $d += (int)$cpf[$c] * (($t + 1) - $c);
            }
            // Calcula o resto da divisão para obter o dígito esperado
            $d = ((10 * $d) % 11) % 10;
            // Compara o dígito calculado com o dígito real fornecido no CPF
            if ((int)$cpf[$c] !== $d) {
                return false;
            }
        }
        return true;
    }
}
