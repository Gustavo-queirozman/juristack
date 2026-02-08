# Deploy Guide - JuriStack

## Pré-requisitos

- PHP 8.1+ com extensões: pdo_mysql, curl, json, openssl
- MySQL 5.7+
- Composer
- Node.js 18+ (para builds CSS/JS, opcional)
- Git

---

## Desenvolvimento local (com Docker para o banco)

Se você **não tem MySQL instalado**, use o Docker apenas para subir o banco. A aplicação Laravel continua rodando na sua máquina.

**Requisito:** PHP com extensão `pdo_mysql`. Se ao rodar `php artisan migrate` aparecer "could not find driver", instale:

```bash
# Ubuntu/Debian (ajuste 8.3 para sua versão do PHP)
sudo apt install php8.3-mysql
# ou
sudo apt install php-mysql
```

**Alternativa sem MySQL:** use SQLite (não precisa de Docker nem do driver MySQL). No `.env`: `DB_CONNECTION=sqlite`, `DB_DATABASE=database/database.sqlite`, crie o arquivo `touch database/database.sqlite` e rode `php artisan migrate`.

### 1. Subir o MySQL em um container

```bash
docker compose up -d
```

Aguarde o MySQL ficar saudável (cerca de 10–20 segundos). O banco `juristack` e o usuário `juristack`/senha `juristack` são criados automaticamente.

### 2. Configurar o ambiente

```bash
cp .env.example .env
php artisan key:generate
```

O `.env.example` já está preparado para o MySQL do Docker (`DB_HOST=127.0.0.1`, `DB_DATABASE=juristack`, `DB_USERNAME=juristack`, `DB_PASSWORD=juristack`). Se o seu `.env` já existia, confira se essas variáveis estão assim.

Coloque seu token do DataJud no `.env` (se ainda não tiver):

```env
DATAJUD_TOKEN=seu-token
DATAJUD_BASE_URL=https://api-publica.datajud.cnj.jus.br
```

### 3. Migrations e tabela da fila

```bash
php artisan migrate
php artisan queue:table
php artisan migrate
```

### 4. Rodar a aplicação

```bash
php artisan serve
```

Acesse: **http://localhost:8000**

### 5. (Opcional) Build dos assets e fila

```bash
npm install && npm run dev
```

Em outro terminal, se for usar jobs em fila:

```bash
php artisan queue:work --tries=3
```

### Parar o banco

```bash
docker compose down
```

Os dados ficam no volume `juristack_mysql_data`. Para apagar tudo e recomeçar:

```bash
docker compose down -v
```

---

## Passos de Deploy (produção)

### 1. Clonar Repositório

```bash
git clone https://github.com/Gustavo-queirozman/juristack.git
cd juristack
```

### 2. Instalar Dependências

```bash
composer install --optimize-autoloader --no-dev
npm install
npm run build  # Opcional, se tiver assets
```

### 3. Configurar Ambiente

```bash
cp .env.example .env
php artisan key:generate
```

Editar `.env` com as informações do servidor:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://seu-dominio.com

# Banco de Dados
DB_CONNECTION=mysql
DB_HOST=seu-host-mysql
DB_PORT=3306
DB_DATABASE=juristack
DB_USERNAME=seu-usuario
DB_PASSWORD=sua-senha

# Email
MAIL_MAILER=smtp
MAIL_HOST=smtp.seu-provedor.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@example.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@seu-dominio.com
MAIL_FROM_NAME="JuriStack"

# Fila (opcional, recomendado)
QUEUE_CONNECTION=database
```

### 4. Executar Migrations

```bash
php artisan migrate --force
```

### 5. Otimizar Aplicação

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

### 6. Configurar Scheduler

Adicionar à crontab do servidor:

```bash
* * * * * cd /caminho/para/juristack && php artisan schedule:run >> /dev/null 2>&1
```

Isso vai executar `datajud:monitor-updates` a cada 6 horas automaticamente.

### 7. Configurar Queue (Recomendado)

Se usar `QUEUE_CONNECTION=database`, execute a migration de jobs:

```bash
php artisan queue:table
php artisan migrate --force
```

Iniciar o queue worker:

```bash
php artisan queue:work --tries=3 --timeout=90
```

Para systemd, criar arquivo `/etc/systemd/system/juristack-queue.service`:

```ini
[Unit]
Description=JuriStack Queue Worker
After=network.target

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/juristack
ExecStart=/usr/bin/php /var/www/juristack/artisan queue:work --tries=3
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

Ativar:
```bash
systemctl daemon-reload
systemctl enable juristack-queue
systemctl start juristack-queue
```

### 8. Configurar Web Server (Nginx)

```nginx
server {
    listen 443 ssl http2;
    server_name seu-dominio.com;

    ssl_certificate /etc/letsencrypt/live/seu-dominio.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/seu-dominio.com/privkey.pem;

    root /var/www/juristack/public;
    index index.php;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Redirecionar HTTP para HTTPS
server {
    listen 80;
    server_name seu-dominio.com;
    return 301 https://$server_name$request_uri;
}
```

### 9. Permissões de Diretórios

```bash
sudo chown -R www-data:www-data /var/www/juristack
sudo chmod -R 755 /var/www/juristack
sudo chmod -R 775 /var/www/juristack/storage
sudo chmod -R 775 /var/www/juristack/bootstrap/cache
```

### 10. SSL Certificate (Let's Encrypt)

```bash
sudo certbot certonly --nginx -d seu-dominio.com
```

### 11. Verificar Deploy

```bash
# Testar aplicação
curl https://seu-dominio.com/login

# Verificar logs
tail -f /var/www/juristack/storage/logs/laravel.log

# Testar scheduler
php artisan schedule:list

# Testar fila
php artisan queue:failed
```

## Monitoramento

### Health Check

```bash
# Criar rota de health check
php artisan make:controller HealthController
```

### Logs

- Acesso: `/var/log/nginx/access.log`
- Erros Nginx: `/var/log/nginx/error.log`
- Aplicação: `/var/www/juristack/storage/logs/laravel.log`

### Uptime Monitoring

Usar ferramentas como:
- UptimeRobot
- Grafana
- New Relic

## Backups

### Banco de Dados

```bash
# Backup diário
0 2 * * * mysqldump -u juristack -p'senha' juristack > /backups/juristack-$(date +\%Y-\%m-\%d).sql
```

### Armazenamento

```bash
# Se usar S3 ou similar
aws s3 sync /var/www/juristack/storage/app s3://seu-bucket/juristack/
```

## Rollback

```bash
# Reverter migrations
php artisan migrate:rollback

# Ou até um ponto específico
php artisan migrate:rollback --step=1
```

## Troubleshooting

### Erros de Permissão

```bash
sudo chmod 777 storage bootstrap/cache
```

### Fila não processa

```bash
php artisan queue:failed
php artisan queue:retry all
```

### Email não envia

```bash
# Testar configuração
php artisan tinker
>>> Mail::raw('Teste', fn($msg) => $msg->to('email@test.com'));
```

### Scheduler não executa

```bash
# Verificar cron
crontab -l

# Verificar se há erros
grep CRON /var/log/syslog | tail -20
```

## Performance

### Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Database Query Optimization

```bash
# Ativar query logging
php artisan tinker
>>> DB::listen(function($query) { dump($query->sql); });
```

## Atualizações

```bash
git pull origin main
composer install
php artisan migrate --force
php artisan cache:clear
php artisan config:clear
```

## Segurança

- [x] HTTPS habilitado
- [x] CSRF protection ativa
- [x] Rate limiting configurado
- [x] SQL Injection protegido (Eloquent)
- [x] XSS protection headers

### Adicionar Rate Limiting

```php
// routes/api.php
Route::middleware('throttle:60,1')->group(function () {
    Route::post('/datajud/salvar', [DataJudController::class, 'salvarProcesso']);
});
```

## Contato & Suporte

Para dúvidas ou problemas com o deploy, consulte:
- [Laravel Deployment](https://laravel.com/docs/11.x/deployment)
- [Nginx Configuration](https://laravel.com/docs/11.x/deployment#nginx)
- [SSL Setup](https://letsencrypt.org/getting-started/)
