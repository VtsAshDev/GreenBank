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

- Transferência de saldo entre usuários (`POST /transfer`)
- Verificação de saldo suficiente
- Lojistas não podem enviar dinheiro, apenas receber
- Validação externa via mock de autorização
- Notificação via mock de notificação
- Transações seguras (com rollback em falhas)

## 📦 Tecnologias

- Laravel (PHP Framework)
- Composer
- Docker (se estiver usando container)
- MySQL/PostgreSQL
- PHPUnit (para testes)

## 🔐 Regras de negócio

- CPF/CNPJ e e-mail devem ser únicos
- Lojistas não podem transferir saldo
- Usuários precisam de saldo suficiente
- Toda transferência deve:
  - Ser validada por um serviço externo (`GET https://util.devi.tools/api/v2/authorize`)
  - Gerar uma notificação via outro serviço (`POST https://util.devi.tools/api/v1/notify`)
- Transferências devem ser transacionais (não pode debitar sem creditar o outro)

## 🛠 Como rodar o projeto

1. Clone o repositório:
   ```bash
   git clone https://github.com/seuusuario/GreenBank.git
   cd GreenBank
   ```

2. Instale as dependências:
   ```bash
   composer install
   ```

3. Copie o arquivo `.env.example` para `.env` e configure:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Configure o banco de dados no `.env`

5. Execute as migrations:
   ```bash
   php artisan migrate
   ```

6. Rode a aplicação:
   ```bash
   php artisan serve
   ```

7. Teste o endpoint de transferência:
   ```http
   POST /api/transfer
   Content-Type: application/json

   {
     "value": 100.0,
     "payer": 4,
     "payee": 15
   }
   ```

## ✅ Requisitos atendidos

- [x] Transferência entre usuários
- [x] Lógica para impedir que lojistas enviem saldo
- [x] Validação externa de autorização
- [x] Mock de notificação
- [x] Transação segura

## 📚 O que foi considerado

- Código limpo e legível
- Separação de camadas (service, controller, repository)
- Aplicação de boas práticas (PSR, SOLID)
- Uso de Git com histórico de commits claro
- Preparado para Docker (se aplicável)

## 📌 Melhorias possíveis

- Autenticação e autorização (não exigido)
- Testes automatizados
- Versionamento de API
- Monitoramento e logging
- Mensageria assíncrona (ex: RabbitMQ)

## 🧑 Informações do autor

**Nome**: Vitor  
**LinkedIn**: [Vitor Gomes](https://www.linkedin.com/in/vitorgomesguimaraes/)  
**Contato para avaliação técnica**: [vitorgguimaraes56@gmail.com]
