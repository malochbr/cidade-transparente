# 🏙️ Cidade Transparente
**Versão:** 1.0  
**Feito por:** Apaminondas de Moura Lima Neto, Daniele Laís Reis da Silva, Matheus Henrique Gomes de Luna, Paulo Sergio da Silva Sales, Victor Adriel Venancio de Souza  
**Curso:** Análise e Desenvolvimento de Sistemas — UEPB  
**Projeto:** Limite do Visível | Prof. João Igor Barros Rocha  

---

## 🧐 O que é esse projeto?

O **Cidade Transparente** é uma plataforma desenvolvida para conectar moradores de uma cidade às secretarias da prefeitura de forma transparente e em tempo real. Os moradores podem registrar problemas urbanos da sua região — como buracos nas vias, iluminação pública deficiente, terrenos abandonados ou acúmulo de lixo —, acompanhar a tramitação dos chamados na linha do tempo e apoiar ocorrências abertas por outros vizinhos do mesmo bairro.

---

## 🛠️ O que você vai precisar instalar (pré-requisitos)

Antes de começar, você precisa ter instalados no seu computador:

1. **Docker Desktop** ([Baixar aqui](https://www.docker.com/products/docker-desktop))  
   *O que é:* Programa que cria um ambiente virtual isolado para o sistema rodar sem precisar instalar PHP ou banco de dados MySQL diretamente no seu sistema operacional.
2. **Git** ([Baixar aqui](https://git-scm.com/downloads))  
   *O que é:* Ferramenta de controle de versão usada para clonar e baixar o projeto.
3. **Um Navegador de Internet** (Google Chrome, Mozilla Firefox ou Microsoft Edge).

---

## 🚀 Como rodar o projeto (passo a passo)

### Passo 1 — Baixar o projeto
Abra o terminal do seu sistema operacional e execute:
```bash
git clone https://github.com/malochbr/cidade-transparente.git
cd cidade-transparente
```
*Alternativa:* Baixe o arquivo ZIP no GitHub e extraia numa pasta no seu computador.

### Passo 2 — Abrir o Docker Desktop
Inicie o aplicativo **Docker Desktop** no seu computador e aguarde até que o ícone da baleia esteja ativo na barra de tarefas. Sem o Docker rodando, o sistema não iniciará.

### Passo 3 — Abrir o Terminal na pasta do projeto
Navegue até a pasta do projeto pelo terminal ou prompt de comando (no Windows, abra a pasta no Explorador de Arquivos, clique na barra de endereços, digite `cmd` e pressione Enter).

### Passo 4 — Subir o sistema
Execute o comando:
```bash
docker-compose up -d --build
```
*O que faz:* Baixa as dependências e inicia o container com a aplicação PHP e o banco de dados MySQL automaticamente.

### Passo 5 — Aguardar o banco de dados ficar pronto
Após a conclusão do comando, aguarde cerca de 30 segundos antes de acessar o navegador para que o MySQL carregue a estrutura e as seeds de demonstração.

### Passo 6 — Abrir o sistema no navegador
Abra o seu navegador e acesse:
```text
http://localhost:8080
```

### Passo 7 — Fazer login para testar
Utilize uma das contas de teste disponíveis:

| Tipo de usuário | CPF | Senha |
|---|---|---|
| **Administrador (Gestor)** | `000.000.000-00` | `Admin@123` |
| **Cidadão (João Silva)** | `111.111.111-11` | `Teste@123` |
| **Cidadão (Maria Santos)** | `222.222.222-22` | `Teste@123` |
| **Cidadão (Ana Paula)** | `444.444.444-44` | `password` |

---

## ⏹️ Como parar o sistema

Para desligar o sistema sem perder os dados salvos:
```bash
docker-compose stop
```
*Para reiniciar depois:* `docker-compose start`.

Para encerrar e remover os containers:
```bash
docker-compose down
```

---

## 🗄️ Como ver os dados do banco de dados

Para acessar o banco MySQL diretamente com qualquer gerenciador de banco de dados (DBeaver, VS Code, etc):

| Campo | Valor |
|---|---|
| **Host** | `localhost` |
| **Porta** | `3306` |
| **Usuário** | `cidade_user` |
| **Senha** | `cidade123` |
| **Banco de Dados** | `cidade_transparente` |

---

## ❓ Problemas comuns e soluções

- **"O site não abre no navegador (`http://localhost:8080`)"**  
  → Espere 1 minuto e recarregue a página (F5). Verifique no Docker Desktop se os containers `cidade_transparente_php` e `cidade_transparente_db` estão com status "Running".
- **"O login não funciona"**  
  → Certifique-se de incluir a formatação completa do CPF com pontos e traço (ex: `111.111.111-11`).
- **"Deu erro 500 no navegador"**  
  → No terminal, rode `docker-compose logs php` para visualizar os detalhes do log.
- **"Esqueci de parar o Docker e reiniciei o PC"**  
  → Apenas navegue até a pasta do projeto e execute `docker-compose start`.
- **"Quero apagar tudo e recomeçar do zero"**  
  → Execute `docker-compose down -v` e depois `docker-compose up -d --build`.

---

## 💻 Tecnologias usadas

- **PHP 8.2** — Linguagem principal utilizada para construir a inteligência e o comportamento das páginas.
- **MySQL 8.0** — Banco de dados relacional que armazena os registros, usuários e históricos.
- **Docker** — Tecnologia que cria o ambiente virtual isolado para execução padronizada.
- **HTML5, CSS3 e JavaScript** — Responsáveis pela estrutura visual e navegação do aplicativo.
- **Apache** — Servidor web que entrega as páginas para o navegador.

---

## 📁 Estrutura das pastas do projeto

```text
cidade/
├── config/          → Configurações de conexão com o banco de dados e funções gerais
├── controllers/     → Lógica de cada tela (o que acontece quando o usuário clica em algo)
├── models/          → Regras de negócio e comunicação com o banco MySQL
├── views/           → As telas em HTML que o usuário vê no navegador
├── public/          → Arquivos públicos (CSS, JavaScript, mídias enviadas)
├── database/        → Script SQL para criar o banco de dados e dados de teste
├── docker-compose.yml → Configuração do Docker
└── index.php        → Ponto de entrada de todas as páginas do sistema
```
