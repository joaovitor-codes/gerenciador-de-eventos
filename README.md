# Gerenciador de Eventos

API REST para gerenciamento de eventos, desenvolvida em Laravel 13 com autenticação via Laravel Sanctum.

## Requisitos

- PHP 8.3 ou superior, com as extensões `pdo_sqlite`, `mbstring`, `openssl`, `tokenizer`, `xml` e `ctype`
- [Composer](https://getcomposer.org/)

## Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/joaovitor-codes/gerenciador-de-eventos.git 
cd gerenciador-de-eventos

# 2. Instalar as dependências PHP
composer install

# 3. Criar o arquivo de ambiente e gerar a chave da aplicação
cp .env.example .env
php artisan key:generate

# 4. Criar o banco SQLite e rodar as migrations
touch database/database.sqlite
php artisan migrate
```

> No Windows (PowerShell), troque `cp` por `copy` e `touch database/database.sqlite` por `New-Item database/database.sqlite`.

## Executando

```bash
php artisan serve
```

A API fica disponível em `http://localhost:8000/api`.

## Configurações do projeto

- **Banco de dados:** SQLite (`database/database.sqlite`). Para usar MySQL/PostgreSQL, ajuste as variáveis `DB_*` no `.env`.
- **Fuso horário:** `America/Recife` (`APP_TIMEZONE`).
- **Idioma:** `pt_BR` (`APP_LOCALE`), com mensagens de validação traduzidas em `lang/pt_BR`.
- **Respostas da API:** todas as rotas em `api/*` respondem em JSON, inclusive erros (401, 404, 422), sem redirecionar para páginas de login.

## Autenticação

As rotas protegidas usam o middleware `auth:sanctum`. Envie o token obtido no login no cabeçalho:

```
Authorization: Bearer <seu-token>
```

## Testes

```bash
php artisan test
```
