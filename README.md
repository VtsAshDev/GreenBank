# GreenBank

Plataforma de transferências simplificada entre usuários e lojistas.  
Este projeto foi desenvolvido como solução para um desafio técnico de back-end.

## 💡 Objetivo

Implementar uma API RESTful usando Laravel para simular funcionalidades básicas de uma carteira digital:

- Cadastro de usuários (comuns e lojistas)
- Transferência de saldo entre usuários
- Restrições específicas para lojistas
- Validação de saldo
- Autorização via serviço externo
- Notificação via serviço externo

## 🚀 Funcionalidades

- Cadastro de usuários (`POST /api/store`)
- Transferência de saldo entre usuários (`POST /api/transfer`)
- Verificação de saldo suficiente
- Lojistas não podem enviar dinheiro, apenas receber
- Validação externa via mock de autorização
- Notificação via mock de notificação
- Transações seguras (com rollback em falhas)

## 📦 Tecnologias

- Laravel (PHP Framework)
- Composer
- Docker + Docker Compose
- MySQL
- RabbitMQ
- PHPUnit (para testes)

## 🔐 Regras de negócio

- CPF/CNPJ e e-mail devem ser únicos
- Lojistas não podem transferir saldo
- Usuários precisam de saldo suficiente
- Toda transferência deve:
    - Ser validada por um serviço externo (`GET https://util.devi.tools/api/v2/authorize`)
    - Gerar uma notificação via outro serviço (`POST https://util.devi.tools/api/v1/notify`)
- Transferências devem ser transacionais (não pode debitar sem creditar o outro)

## 🛠 Como rodar o projeto com Docker

1. Clone o repositório:
   ```bash
   git clone https://github.com/VtsAshDev/GreenBank
   cd GreenBank
   ```

2. Copie o `.env.example` para `.env`:
   ```bash
   cp .env.example .env
   ```

3. Suba os containers com Docker Compose:
   ```bash
   docker compose up -d
   ```

4. Acesse o container do Laravel e finalize a instalação:
   ```bash
   docker exec -it nome_do_container_laravel bash
   composer install
   php artisan key:generate
   php artisan migrate
   ```

5. A aplicação estará disponível em `http://localhost:8000`

---

## 🔗 Endpoints da API

### 📥 `POST /api/store` – Cadastrar novo usuário

**Descrição:**  
Cria um novo usuário comum ou lojista.

**JSON de exemplo:**
```json
{
  "name": "Vitor Gomes Guimarães",
  "email": "vitorexg@email.com",
  "document": "12345678900",
  "password": "senha123",
  "shopkeeper": false,
}
```

--- 

### 💸 `POST /api/transfer` – Realizar transferência

**Descrição:**  
Realiza a transferência de saldo entre usuários.

**JSON de exemplo:**
```json
{
    "payer_id":	1,
    "payee_id": 2,
    "value": 3
}
```

---

## 🗄 ️ Diagrama do banco de dados

![Diagrama ER](https://raw.githubusercontent.com/VtsAshDev/GreenBank/main/docs/db-diagram.svg)

## ✅ Requisitos atendidos

- [x] Transferência entre usuários
- [x] Lógica para impedir que lojistas enviem saldo
- [x] Validação externa de autorização
- [x] Mock de notificação
- [x] Transação segura
- [x] Ambiente Dockerizado com Laravel, MySQL e RabbitMQ

## 📚 O que foi considerado

- Código limpo e legível
- Separação de camadas (service, controller, repository)
- Aplicação de boas práticas (PSR, SOLID)
- Uso de Git com histórico de commits claro
- Preparado para execução via Docker com Dockerfile e docker-compose.yml

## 📌 Melhorias possíveis

- Autenticação e autorização (não exigido)
- Testes automatizados
- Versionamento de API
- Monitoramento e logging
- Processamento assíncrono de eventos com RabbitMQ

## 🧑 Informações do autor

**Nome**: Vitor  
**LinkedIn**: [Vitor Gomes](https://www.linkedin.com/in/vitorgomesguimaraes/)  
**Contato para avaliação técnica**: [vitorgguimaraes56@gmail.com]
