# Evolution API em Docker

Este compose sobe a Evolution API separada do Laravel, com Postgres e Redis. Use em VPS com Docker instalado.

## 1. Enviar arquivos para a VPS

Envie estes arquivos para a pasta do JuriStack na hospedagem:

- `docker-compose.evolution.yml`
- `.env.evolution.example`

Depois, na VPS:

```bash
cp .env.evolution.example .env.evolution
nano .env.evolution
```

## 2. Configurar `.env.evolution`

Preencha:

```env
SERVER_URL=https://evolution.seudominio.com
EVOLUTION_PORT=8080
AUTHENTICATION_API_KEY=sua-chave-segura
POSTGRES_DATABASE=evolution
POSTGRES_USERNAME=evolution
POSTGRES_PASSWORD=sua-senha-segura
```

`SERVER_URL` precisa ser o endereco publico que aponta para a Evolution API. Se usar Nginx/Traefik, configure o proxy para encaminhar esse dominio para `127.0.0.1:8080`.

## 3. Subir a Evolution

```bash
docker compose --env-file .env.evolution -f docker-compose.evolution.yml up -d
docker compose --env-file .env.evolution -f docker-compose.evolution.yml logs -f evolution-api
```

## 4. Configurar o `.env` do JuriStack

No `.env` do Laravel/JuriStack hospedado:

```env
EVOLUTION_API_BASE_URL=https://evolution.seudominio.com
EVOLUTION_API_KEY=sua-chave-segura
WHATSAPP_WEBHOOK_TOKEN=token-seguro-do-chatbot
WHATSAPP_WEBHOOK_URL=https://seudominio-do-juristack.com/api/whatsapp/webhook
```

Depois limpe o cache:

```bash
php artisan optimize:clear
php artisan config:clear
```

## 5. Conectar WhatsApp

Entre no JuriStack em `WhatsApp > Conexao` e clique em `Criar conexao`. O sistema cria a instancia na Evolution, cadastra o webhook do chatbot e mostra o QR Code para parear o WhatsApp.
