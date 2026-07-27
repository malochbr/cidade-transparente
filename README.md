# 🏙️ Cidade Transparente
**Versão:** 1.0  
**Feito por:** Apaminondas de Moura Lima Neto, Daniele Laís Reis da Silva, Matheus Henrique Gomes de Luna, Paulo Sergio da Silva Sales, Victor Adriel Venancio de Souza  
**Curso:** Análise e Desenvolvimento de Sistemas — UEPB  
**Projeto:** Limite do Visível | Prof. João Igor Barros Rocha  

---

## 🧐 O que é esse projeto?

O **Cidade Transparente** é uma plataforma web que conecta moradores de uma cidade às secretarias da prefeitura de forma transparente e em tempo real. Moradores podem registrar problemas urbanos — buracos nas vias, iluminação pública deficiente, terrenos abandonados, alagamentos — e acompanhar a tramitação dos chamados em tempo real. Outros cidadãos podem apoiar e comentar nas ocorrências abertas por vizinhos do mesmo bairro.

O sistema possui dois perfis distintos: **cidadão** (registra e acompanha ocorrências) e **administrador** (gerencia, encaminha para secretarias e atualiza status).

---

## 🏗️ Arquitetura do projeto

O projeto segue o padrão **MVC (Model-View-Controller)**, onde cada camada tem uma responsabilidade bem definida:

- **Model** → cuida exclusivamente do acesso ao banco de dados (queries, inserções, atualizações)
- **Controller** → recebe a requisição HTTP, chama o Model necessário e decide qual View renderizar
- **View** → apenas exibe o HTML com os dados que o Controller passou — sem lógica de negócio

```text
cidade-transparente/
├── index.php           ← Front Controller — ponto de entrada único do sistema
├── config/
│   ├── config.php      ← Sessão, helpers globais (redirect, flash, csrf, sanitize)
│   └── db.php          ← Conexão com o banco via PDO Singleton
├── controllers/        ← Um controller por área (Auth, Ocorrencia, Admin, Perfil, Notificacao)
├── models/             ← Um model por entidade (Ocorrencia, Usuario, Notificacao)
├── views/              ← Telas separadas por módulo (auth/, ocorrencias/, admin/, perfil/)
├── public/             ← CSS, JS e uploads de imagens enviadas pelos cidadãos
├── database/           ← Schema SQL completo + seed de dados para demonstração
└── Dockerfile          ← Configuração do container para deploy em produção
```

### Como o roteamento funciona

Todo o sistema passa pelo `index.php` — o **Front Controller**. A URL usa o parâmetro `?page=` para definir qual controller e método executar:

| URL | Controller chamado |
|---|---|
| `?page=home` | `OcorrenciaController::home()` |
| `?page=auth/login` | `AuthController::login()` |
| `?page=nova-ocorrencia` | `OcorrenciaController::novaOcorrencia()` (wizard 3 etapas) |
| `?page=admin/dashboard` | `AdminController::dashboard()` |

Se o parâmetro não for passado, o sistema verifica se há sessão ativa e redireciona automaticamente — usuário logado vai para `home`, usuário deslogado vai para a splash screen.

---

## 🗄️ Banco de dados

O banco usa **MySQL 8** com InnoDB em todas as tabelas, o que garante suporte a transações e chaves estrangeiras. São 11 tabelas no total:

```
usuarios
   └── ocorrencias ──── secretarias ──── prefeituras
           ├── localizacoes       (endereço exato da ocorrência)
           ├── midias             (fotos e vídeos enviados pelo cidadão)
           ├── historico_status   (trilha completa de mudanças de status)
           ├── comentarios        (comentários de cidadãos na ocorrência)
           ├── apoios             (cidadãos que apoiam a ocorrência)
           ├── notificacoes       (avisos automáticos ao autor)
           └── validacoes_resolucao (cidadão confirma se foi resolvido de fato)
```

**Decisões de modelagem:**
- `FOREIGN KEY` com `ON DELETE CASCADE` nas tabelas filhas — ao deletar uma ocorrência, todos os registros relacionados (fotos, comentários, histórico) são removidos automaticamente
- `ON DELETE SET NULL` na relação `ocorrencias → secretarias` — se uma secretaria for removida, as ocorrências não são perdidas, apenas ficam sem secretaria vinculada
- `UNIQUE KEY` nas tabelas de apoios e validações — impede que o mesmo usuário apoie ou valide duas vezes a mesma ocorrência
- `ENUM` nos campos de status, categoria e perfil — garante integridade sem precisar de tabela auxiliar para valores fixos
- `TIMESTAMP DEFAULT CURRENT_TIMESTAMP` em todos os registros — auditoria automática de quando cada dado foi criado

---

## 🔌 Conexão com o banco — PDO Singleton

```php
// config/db.php
class Database {
    private static ?PDO $instancia = null;

    public static function getInstance(): PDO {
        if (self::$instancia === null) {
            $dsn = "mysql:host=".DB_HOST.";port=".DB_PORT
                 . ";dbname=".DB_NAME.";charset=utf8mb4";
            self::$instancia = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        }
        return self::$instancia;
    }
}
```

**Por que Singleton?**  
Durante uma requisição PHP, vários controllers e models podem precisar do banco. Sem Singleton, cada `new PDO(...)` abre uma nova conexão — isso desperdiça recursos e pode estourar o limite de conexões do MySQL. Com Singleton, a primeira chamada cria a conexão e todas as seguintes reutilizam a mesma instância.

**Por que PDO em vez de mysqli direto?**  
O PDO tem suporte nativo a Prepared Statements com bind de parâmetros, o que elimina SQL Injection sem esforço extra. Além disso, se o banco mudar de MySQL para PostgreSQL no futuro, só o DSN muda — o restante do código continua igual.

As credenciais chegam por **variáveis de ambiente** (`getenv()`), nunca escritas diretamente no código — em produção isso é fundamental para não expor senha no GitHub.

---

## 🔒 Segurança implementada

**1. SQL Injection — PDO Prepared Statements**  
100% das queries usam `prepare()` + `bindValue()` ou parâmetros nomeados. Nenhuma query concatena dado do usuário diretamente na string SQL.

```php
$consulta = $this->db->prepare("SELECT * FROM usuarios WHERE cpf = :cpf");
$consulta->execute([':cpf' => $cpfLimpo]);
```

**2. Senhas — BCrypt**  
Nenhuma senha é salva em texto limpo. O PHP gera o hash com `password_hash($senha, PASSWORD_DEFAULT)` (bcrypt com custo 10) e verifica com `password_verify()`.

**3. CSRF — Token por sessão**  
Todos os formulários POST incluem um token único gerado por sessão com `bin2hex(random_bytes(32))`. O backend verifica o token antes de processar qualquer ação com `hash_equals()` — resistente a timing attacks.

**4. XSS — Sanitização na saída**  
Todo dado exibido nas views passa por `htmlspecialchars()` via função `sanitize()`. Dados do usuário nunca são impressos crus no HTML.

**5. Validação de CPF — Algoritmo real**  
O sistema valida os dois dígitos verificadores do CPF pelo cálculo de peso ponderado e resto por 11 — impede cadastros com CPFs inválidos ou sequências repetidas (111.111.111-11 etc.).

**6. Controle de acesso por perfil**  
Controllers de área administrativa verificam o perfil da sessão antes de executar qualquer ação. Cidadão que tentar acessar `?page=admin/dashboard` é redirecionado.

**7. Session Hijacking — Regeneração de ID**  
Após login bem-sucedido, o sistema chama `session_regenerate_id(true)` para invalidar o ID de sessão anterior e criar um novo — protege contra fixação de sessão.

---

## ✨ Funcionalidades

**Área do cidadão:**
- Cadastro com validação real de CPF, e-mail e telefone únicos
- Login por CPF + senha
- Registrar ocorrência em wizard de 3 etapas: dados básicos → localização → foto/vídeo
- Acompanhar status das próprias ocorrências com linha do tempo
- Apoiar ocorrências de outros cidadãos (máximo 1 apoio por pessoa)
- Comentar em ocorrências abertas
- Validar ou contra-validar resolução de uma ocorrência
- Receber notificações automáticas a cada mudança de status
- Sistema de reputação — uso indevido penaliza a pontuação e pode suspender a conta

**Área administrativa:**
- Dashboard com estatísticas gerais (total por status, por categoria, atrasadas)
- Listar, filtrar e gerenciar todas as ocorrências
- Atualizar status com justificativa (histórico registrado automaticamente)
- Encaminhar ocorrências para a secretaria responsável pela categoria
- Gerenciar usuários — ativar, suspender e ajustar reputação manualmente

---

## 🐳 Deploy — Docker + Railway

O sistema roda em container Docker para garantir que o ambiente de produção seja idêntico ao de desenvolvimento, independente do sistema operacional.

```dockerfile
FROM debian:bookworm
RUN apt-get install apache2 php8.2 libapache2-mod-php8.2 php8.2-mysql ...
COPY . /var/www/html/
CMD ["apache2ctl", "-D", "FOREGROUND"]
```

O `CMD` mantém o Apache em **foreground** — comportamento obrigatório em containers Docker, pois o container encerra quando o processo principal termina.

Em produção, o Railway faz o build da imagem a cada `git push` no GitHub e sobe o container automaticamente. O MySQL roda como serviço separado no Railway, acessível via rede interna (`mysql.railway.internal`) — sem exposição pública desnecessária. As credenciais chegam como variáveis de ambiente injetadas pelo Railway, nunca hardcoded no código.

---

## 🚀 Como rodar localmente

### Pré-requisitos
- [Docker Desktop](https://www.docker.com/products/docker-desktop)
- [Git](https://git-scm.com/downloads)
- Qualquer navegador moderno

### Passo a passo

```bash
# 1. Clonar o repositório
git clone https://github.com/malochbr/cidade-transparente.git
cd cidade-transparente

# 2. Subir os containers
docker-compose up -d --build

# 3. Aguardar ~30 segundos para o MySQL inicializar

# 4. Acessar no navegador
http://localhost:8080
```

### Contas de teste

| Perfil | CPF | Senha |
|---|---|---|
| Administrador | `000.000.000-00` | `Admin@123` |
| Cidadão | `111.111.111-11` | `Teste@123` |
| Cidadão | `222.222.222-22` | `Teste@123` |

### Parar o sistema

```bash
docker-compose stop     # Para sem apagar os dados
docker-compose down     # Para e remove os containers
docker-compose down -v  # Para, remove containers e apaga o banco
```

---

## 🔍 Acessar o banco localmente

Conecte pelo DBeaver ou qualquer cliente MySQL:

| Campo | Valor |
|---|---|
| Host | `localhost` |
| Porta | `3306` |
| Usuário | `cidade_user` |
| Senha | `cidade123` |
| Banco | `cidade_transparente` |

---

## 💻 Stack

| Tecnologia | Uso |
|---|---|
| PHP 8.2 | Backend, lógica de negócio, geração de HTML |
| MySQL 8 | Banco de dados relacional |
| PDO | Abstração de acesso ao banco com Prepared Statements |
| Apache 2 | Servidor web com mod_rewrite para roteamento |
| Docker | Containerização para ambiente consistente |
| Railway | Hospedagem em nuvem com CI/CD via GitHub |
| HTML5 / CSS3 / JS | Frontend das views |
| Git + GitHub | Controle de versão |

---

## ❓ Problemas comuns

**O site não abre no navegador**  
Aguarde 1 minuto após o `docker-compose up`. Verifique no Docker Desktop se os dois containers estão com status *Running*.

**O login não funciona**  
Use o CPF com pontos e traço: `111.111.111-11`, não `11111111111`.

**Erro 500 no navegador**  
Rode `docker-compose logs php` no terminal para ver o detalhe do erro.

**Quero recomeçar do zero**  
```bash
docker-compose down -v
docker-compose up -d --build
```
