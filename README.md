```markdown
# 🚀 OnFly - Microsserviço de Viagens Corporativas

![Docker](https://img.shields.io/badge/Docker-OK-green?logo=docker)
![Laravel](https://img.shields.io/badge/Laravel-12.x-orange?logo=laravel)

Solução Dockerizada para gerenciamento de pedidos de viagem corporativa.

## 📥 Download Direto
[⬇️ Baixar README.md](https://gist.githubusercontent.com/seu-usuario/ID-DO-GIST/raw/README.md) _(substitua com seu link real)_

## 🛠️ Instalação Rápida

### 1. Clone e entre na pasta
```bash
git clone https://github.com/seu-usuario/onfly.git && cd onfly
```

### 2. Inicie os containers
```bash
docker-compose up -d --build
```

### 3. Comandos essenciais
```bash
# Instalar dependências
docker-compose exec app composer install

# Configurar ambiente
docker-compose exec app cp .env.example .env
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan jwt:secret

# Migrar banco de dados
docker-compose exec app php artisan migrate
```

## 🌐 Endpoints Principais
| Método | Endpoint                | Descrição               |
|--------|-------------------------|-------------------------|
| POST   | `/api/register`         | Registrar usuário       |
| POST   | `/api/login`            | Login (obter JWT)       |
| POST   | `/api/travel-orders`    | Criar pedido de viagem  |

## 🐛 Solução de Problemas

### Erro de permissão
```bash
docker-compose exec app chmod -R 775 storage bootstrap/cache
```

### Reconstruir containers
```bash
docker-compose down && docker-compose up -d --build
```

## 📦 Estrutura Docker
```
onfly/
├── docker/
│   ├── nginx/
│   │   └── default.conf
│   └── docker-compose.yml
└── src/ (código Laravel)
```

## 📌 Importante!
1. Todas as rotas estão em `routes/web.php`
2. CSRF desativado para rotas API
3. Acesse o Mailhog em: http://localhost:8025

