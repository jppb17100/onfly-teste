# Backend Challenge - Onfly

Microsserviço REST em Laravel para gerenciamento de pedidos de viagem corporativa.

## 🚀 Tecnologias

- PHP 8.3
- Laravel 11
- MySQL
- Docker
- JWT (Autenticação)
- PHPUnit

## 📋 Pré-requisitos

- Docker
- Docker Compose
- Git

## 🔧 Instalação

1. Clone o repositório:
```bash
git clone https://github.com/jppb17100/onfly-teste.git
cd onfly-teste
```

2. Copie o arquivo de ambiente:
```bash
cd src
cp .env.example .env
```

3. Instale as dependências PHP:
```bash
composer install
```

4. Gere a chave da aplicação:
```bash
php artisan key:generate
```

5. Gere uma nova chave JWT:
```bash
php artisan jwt:secret
```

**⚠️ Observação:**  
Se aparecer a mensagem:
```
This will invalidate all existing tokens. Are you sure you want to override the secret key? (yes/no) [no]:
```
Digite `yes` e pressione Enter para continuar.
> Isso vai substituir a linha `JWT_SECRET=` no seu `.env` com uma chave válida.


6. Suba os containers com Docker:
```bash
cd ..
cd docker
docker-compose up -d
```

7. Execute as migrações:
```bash
docker-compose exec app php artisan migrate
```

## ✅ Testes

Para rodar os testes automatizados (PHPUnit):

```bash
docker-compose exec app php artisan test
```

## 🔐 Autenticação

A autenticação é feita via token JWT. Após autenticar, envie o token no cabeçalho `Authorization`:

```
Authorization: Bearer {token}
```
---
## 📫 Visualizar Notificações por E-mail
O projeto utiliza o MailHog para capturar os e-mails enviados pela aplicação durante o desenvolvimento. Para visualizar os e-mails:

👉 Acesse: http://localhost:8025

Lá você poderá ver notificações como confirmações, avisos e mensagens de teste enviadas pela aplicação.

---

## 📬 Collection para Testes

Para facilitar os testes da API, foi incluída uma collection do **Postman** na raiz do projeto, na pasta `collection/`.

### Endpoints Principais

- `POST /api/register` — Cadastrar novo usuário
- `POST /api/login` — Login do usuário
- `POST /api/travel-orders` — Criar pedido de viagem
- `PUT /api/travel-orders/{id}/status` — Atualizar status do pedido (aprovado/cancelado)
- `GET /api/travel-orders/{id}` — Consultar pedido específico
- `GET /api/travel-orders` — Listar pedidos (com filtros por status, destino e datas)
- `DELETE /api/travel-orders/{id}` — Cancelar pedido (com validações)

> Cada pedido pertence ao usuário autenticado. O status só pode ser alterado por outro usuário (ex: um administrador).

### Como usar:

1. Abra o Postman.
2. Vá em **Import**.
3. Selecione o arquivo `collection/Onfly Travel Orders API.postman_collection.json`.
4. A collection estará disponível com todos os endpoints organizados.

> A collection já inclui os headers e exemplos de payloads. Após fazer login, copie o token JWT da resposta e substitua no header `Authorization` das próximas requisições:
```
Authorization: Bearer {token}
```
