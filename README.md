# 🚀 Juristack

Sistema backend desenvolvido para gerenciamento de processos jurídicos, com foco em organização, escalabilidade e integração com aplicações frontend.

---

## 📌 Visão Geral

O **Juristack** é uma API desenvolvida com foco em centralizar e gerenciar informações jurídicas, permitindo o controle de usuários, processos e operações relacionadas de forma estruturada.

O projeto foi pensado para simular um ambiente real de produção, aplicando boas práticas de desenvolvimento backend, organização de código e escalabilidade.

---

## 🧠 Problema Resolvido

Sistemas jurídicos geralmente possuem:
- grande volume de dados
- necessidade de organização por usuários
- controle de permissões
- operações críticas que exigem consistência

O Juristack busca resolver isso oferecendo uma base sólida para gerenciamento desses dados via API.

---

## ⚙️ Tecnologias Utilizadas

- PHP 8+
- Laravel 10
- MySQL
- Docker
- Composer

---

## 🏗️ Arquitetura

O projeto segue boas práticas de organização utilizando:

- **MVC (Model-View-Controller)**
- Separação de responsabilidades
- Uso de Services (quando aplicável)
- Controllers enxutos
- Validações desacopladas

---

## 🔐 Funcionalidades

- Autenticação de usuários
- CRUD de entidades principais
- Estrutura de API REST
- Validação de dados
- Controle de acesso básico

---

## 🚀 Como Executar o Projeto

### Pré-requisitos

- Docker
- Docker Compose

### Passos

```bash
# Clonar o repositório
git clone https://github.com/Gustavo-queirozman/juristack.git

# Entrar na pasta
cd juristack

# Subir os containers
docker-compose up -d

# Instalar dependências
docker exec -it app composer install

# Copiar .env
cp .env.example .env

# Gerar chave da aplicação
docker exec -it app php artisan key:generate

# Rodar migrations
docker exec -it app php artisan migrate
