# Tecnologias

- Laravel
- Mysql
- Redis

## DataJud API

Configure a chave de acesso no arquivo `.env` (não comitar a chave no repositório):

```
DATAJUD_TOKEN=cDZHYzlZa0JadVREZDJCendQbXY6SkJlTzNjLV9TRENyQk1RdnFKZGRQdw==
DATAJUD_BASE_URL=https://api-publica.datajud.cnj.jus.br
```

Testando a API (sem CSRF):

```bash
curl -X POST http://localhost:8000/api/datajud/search \
	-H "Content-Type: application/json" \
	-d '{"tribunal":"STF","numero_processo":"0001234-56.2023.8.26.0000"}'
```

Ou usando a página: `GET /datajud/pesquisa` (form envia via AJAX para a rota web protegida por CSRF).

Debug rápido via API (sem CSRF):

```bash
# Busca por advogado em todos os tribunais
curl "http://localhost:8000/api/datajud/debug?tribunal=ALL&tipo=advogado&valor=Nelson%20Mannrich"

# Busca por processo em tribunal específico
curl "http://localhost:8000/api/datajud/debug?tribunal=TRF1&tipo=numero&valor=0001234-56.2023.8.26.0000"
```

Ative logs detalhados definindo no `.env`:

```
DATAJUD_DEBUG=true
```

