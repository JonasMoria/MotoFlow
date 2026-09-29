# 🏍️ MotoFlow

**MotoFlow** é um sistema de gestão para oficinas de motocicletas, desenvolvido para centralizar e facilitar o controle das principais operações de uma oficina.

O projeto está sendo desenvolvido como parte do meu portfólio, com foco em aplicar boas práticas de desenvolvimento de software, organização de arquitetura, desenvolvimento de APIs e construção de uma interface moderna para gestão.

> 🚧 **Status:** Em desenvolvimento

---

## 📋 Sobre o projeto

O MotoFlow tem como objetivo oferecer uma solução simples e eficiente para que mecânicos e gestores de oficinas possam administrar suas operações em um único sistema.

A ideia é permitir o gerenciamento de:

* 👤 Clientes
* 🏍️ Motocicletas
* 🔧 Serviços
* 📋 Ordens de serviço
* 🧾 Comandas
* 💰 Valores e pagamentos
* 📊 Histórico de manutenção
* 📦 Peças e produtos
* 📈 Indicadores da oficina

O sistema será desenvolvido de forma modular, permitindo que novas funcionalidades sejam adicionadas conforme o projeto evolui.

---

## 🛠️ Tecnologias

### Backend

* PHP
* Laravel
* MySQL
* Nginx

### Frontend

* Nuxt
* Node.js

### Infraestrutura

* Docker
* Docker Compose
* phpMyAdmin
* Mailpit

---

## 📁 Estrutura do projeto

```text
motoflow/
├── api/                    # Backend Laravel
├── app/                    # Frontend Nuxt
├── docker/
│   ├── mysql/
│   │   └── init/
│   ├── nginx/
│   │   └── conf.d/
│   │       └── default.conf
│   ├── node/
│   │   ├── Dockerfile
│   │   └── entrypoint.sh
│   └── php/
│       ├── Dockerfile
│       └── www.conf
├── docker-compose.yml
├── .env.example
├── .gitignore
├── Makefile
└── README.md
```

---

## 🚀 Como executar o projeto

### Pré-requisitos

Antes de iniciar, certifique-se de ter instalado:

* Docker
* Docker Compose

Verifique as instalações:

```bash
docker --version
docker compose version
```

---

### 1. Clone o repositório

```bash
git clone https://github.com/JonasMoria/MotoFlow.git
```

Entre na pasta do projeto:

```bash
cd MotoFlow
```

---

### 2. Configure as variáveis do Docker

Copie o arquivo `.env.example`:

```bash
cp .env.example .env
```

Configure o UID e GID do seu usuário Linux no `.env`.

Para descobrir essas informações:

```bash
id
```

Exemplo:

```text
uid=1000(moriah) gid=1000(moriah)
```

Nesse caso:

```env
DOCKER_USER=motoflow
DOCKER_UID=1000
DOCKER_GID=1000
```

Esses valores são utilizados pelos containers para evitar que arquivos do projeto sejam criados com `root:root`.

---

### 3. Configure o Laravel

Entre na pasta da API:

```bash
cd api
```

Caso o arquivo `.env` do Laravel ainda não exista:

```bash
cp .env.example .env
```

Configure a conexão com o MySQL:

```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=motoflow
DB_USERNAME=motoflow
DB_PASSWORD=motoflow
```

Configure também o Mailpit:

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@motoflow.test"
MAIL_FROM_NAME="MotoFlow"
```

Volte para a raiz do projeto:

```bash
cd ..
```

---

### 4. Suba os containers

Execute:

```bash
docker compose up -d --build
```

Confira o status:

```bash
docker compose ps
```

Os principais serviços serão:

```text
motoflow-api
motoflow-api-cli
motoflow-app
motoflow-database
motoflow-nginx
motoflow-phpmyadmin
motoflow-mailpit
```

---

### 5. Configure o Laravel

Instale as dependências:

```bash
docker compose exec api-cli composer install
```

Gere a chave da aplicação:

```bash
docker compose exec api-cli php artisan key:generate
```

Execute as migrations:

```bash
docker compose exec api-cli php artisan migrate
```

---

## 🌐 Acessando o projeto

Depois que os containers estiverem em execução, os serviços estarão disponíveis nas seguintes portas:

| Serviço                | URL                   |
| ---------------------- | --------------------- |
| 🏍️ MotoFlow / Laravel | http://localhost:8080 |
| 🖥️ Nuxt               | http://localhost:3000 |
| 🗄️ phpMyAdmin         | http://localhost:8081 |
| ✉️ Mailpit             | http://localhost:8025 |

### Backend

O Laravel é servido pelo Nginx:

```text
http://localhost:8080
```

O navegador acessa o Nginx, que encaminha as requisições PHP para o PHP-FPM:

```text
Browser
   ↓
Nginx :8080
   ↓
PHP-FPM :9000
   ↓
Laravel
   ↓
MySQL
```

A porta `9000` é interna do Docker e não precisa ser acessada diretamente pelo navegador.

### Frontend

A aplicação Nuxt fica disponível em:

```text
http://localhost:3000
```

### Banco de dados

O phpMyAdmin pode ser acessado em:

```text
http://localhost:8081
```

Configuração:

```text
Servidor: mysql
Usuário: root
Senha: root
```

Ou utilizando o usuário da aplicação:

```text
Servidor: mysql
Usuário: motoflow
Senha: motoflow
Banco: motoflow
```

### E-mails

O Mailpit captura os e-mails enviados pela aplicação durante o desenvolvimento:

```text
http://localhost:8025
```

---

## 🐳 Principais comandos Docker

Subir o projeto:

```bash
docker compose up -d
```

Subir reconstruindo as imagens:

```bash
docker compose up -d --build
```

Parar os containers:

```bash
docker compose down
```

Verificar os containers:

```bash
docker compose ps
```

Acompanhar todos os logs:

```bash
docker compose logs -f
```

Acompanhar os logs da API:

```bash
docker compose logs -f api
```

Acompanhar os logs do Nuxt:

```bash
docker compose logs -f app
```

---

Os comandos PHP, Artisan e Composer devem ser executados pelo container `api-cli`.

### Composer

```bash
docker compose exec api-cli composer install
```

Atualizar dependências:

```bash
docker compose exec api-cli composer update
```

### Artisan

```bash
docker compose exec api-cli php artisan
```

Executar migrations:

```bash
docker compose exec api-cli php artisan migrate
```

Criar uma migration:

```bash
docker compose exec api-cli php artisan make:migration create_example_table
```

Criar um model:

```bash
docker compose exec api-cli php artisan make:model Example
```

---


Verificar o container:

```bash
docker compose ps app
```

Ver os logs:

```bash
docker compose logs -f app
```

Abrir um shell no container:

```bash
docker compose exec app sh
```

Verificar a versão do Node:

```bash
docker compose exec app node --version
```

Verificar a versão do npm:

```bash
docker compose exec app npm --version
```

---

## 🔐 Permissões

O projeto utiliza `DOCKER_UID` e `DOCKER_GID` para manter os arquivos criados pelos containers compatíveis com o usuário do sistema operacional.

Exemplo:

```env
DOCKER_USER=motoflow
DOCKER_UID=1000
DOCKER_GID=1000
```

Isso evita problemas comuns de desenvolvimento em Linux, como arquivos pertencentes ao usuário `root` no diretório do projeto.

---

⭐ Se você gostou do projeto, considere deixar uma estrela no repositório.
