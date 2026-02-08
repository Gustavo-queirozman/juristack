#!/usr/bin/env bash
set -e

# Diretório do projeto (onde está o script)
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
cd "$DIR"

# Cores para mensagens
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${GREEN}[JuriStack] Iniciando ambiente de desenvolvimento...${NC}"

# 1. .env
if [ ! -f .env ]; then
  echo -e "${YELLOW}[JuriStack] Criando .env a partir de .env.example${NC}"
  cp .env.example .env
  php artisan key:generate
fi

# 2. Banco MySQL (Docker) – opcional
if [ -f docker-compose.yml ]; then
  echo -e "${YELLOW}[JuriStack] Subindo MySQL (Docker)...${NC}"
  docker compose up -d 2>/dev/null || true
  sleep 5
  echo -e "${YELLOW}[JuriStack] Rodando migrations...${NC}"
  php artisan migrate --force 2>/dev/null || true
fi

# 3. Dependências (só se faltar algo)
if [ ! -d node_modules ] || [ ! -f node_modules/.package-lock.json ]; then
  echo -e "${YELLOW}[JuriStack] Instalando dependências npm...${NC}"
  npm install
fi

# 4. Build dos assets (CSS/JS) para funcionar com php artisan serve
echo -e "${YELLOW}[JuriStack] Compilando assets (Vite)...${NC}"
npm run build

# 5. Limpar cache do Laravel
php artisan config:clear
php artisan view:clear

# 6. Encerrar processos antigos na porta 8000 (e Vite na 5173), se existir lsof
if command -v lsof >/dev/null 2>&1; then
  echo -e "${YELLOW}[JuriStack] Liberando portas 8000 e 5173...${NC}"
  lsof -ti :8000 | xargs -r kill 2>/dev/null || true
  lsof -ti :5173 | xargs -r kill 2>/dev/null || true
  sleep 2
fi

# Ao sair (Ctrl+C ou fim do script), mata os processos iniciados por este script
cleanup() {
  echo -e "\n${YELLOW}[JuriStack] Encerrando...${NC}"
  kill $(jobs -p) 2>/dev/null || true
  exit 0
}
trap cleanup EXIT INT TERM

# 7. Vite em modo dev (assets com hot reload) em background
echo -e "${YELLOW}[JuriStack] Iniciando Vite (assets)...${NC}"
npm run dev &
VITE_PID=$!

# 8. Dar tempo do Vite subir
sleep 4

# 9. Servidor PHP em background
echo -e "${YELLOW}[JuriStack] Iniciando servidor PHP...${NC}"
php artisan serve --host=127.0.0.1 --port=8000 &
PHP_PID=$!

sleep 2

echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  JuriStack está rodando!${NC}"
echo -e "${GREEN}  Acesse: http://localhost:8000${NC}"
echo -e "${GREEN}========================================${NC}"
echo -e "Pressione ${YELLOW}Ctrl+C${NC} para encerrar."
echo ""

# Mantém o script rodando até Ctrl+C (o trap encerra os dois processos)
wait
