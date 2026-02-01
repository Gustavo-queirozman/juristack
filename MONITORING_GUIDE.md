# Guia de Uso - Funcionalidade de Monitoramento de Processos

## Visão Geral

O JuriStack agora oferece uma funcionalidade completa de monitoramento de processos jurídicos com notificações automáticas por email quando há atualizações.

## Fluxo de Funcionamento

### 1. Pesquisar e Salvar Processo

1. Acesse **Pesquisa de Processos** (menu DataJud)
2. Selecione o tribunal
3. Procure pelo número do processo ou nome do advogado
4. Nos resultados, clique no botão **"Salvar"** para salvar o processo
5. Uma notificação de sucesso aparecerá confirmando o salvamento

### 2. Monitorar Processo

Ao salvar um processo, ele é automaticamente adicionado à lista de monitoramento.

**Verificações automáticas:**
- O sistema verifica atualizações a cada **6 horas**
- As verificações são executadas automaticamente via scheduler do Laravel
- Quando uma atualização é detectada, um email é enviado ao usuário

### 3. Receber Notificações

Quando um processo monitorado sofre alterações:
1. O sistema detecta a mudança na API do DataJud
2. Uma **notificação por email** é enviada automaticamente
3. O email contém:
   - Número do processo
   - Tribunal
   - Último movimento registrado
   - Link direto para ver detalhes

## Configuração

### Variáveis de Ambiente (.env)

```env
# Banco de dados (já configurado)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=juristack
DB_USERNAME=root
DB_PASSWORD=

# Email (configurar conforme seu provedor)
MAIL_MAILER=log          # usar 'smtp' em produção
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="JuriStack"
```

### Configurar Email em Produção

No arquivo `.env`, altere para:

```env
MAIL_MAILER=smtp
MAIL_HOST=seu-servidor-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-aplicacao
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="JuriStack"
```

## Comandos Disponíveis

### Executar verificação manual

```bash
php artisan datajud:monitor-updates
```

Opções:
- `--limit=50`: Define o número máximo de processos a verificar (padrão: 50)

```bash
php artisan datajud:monitor-updates --limit=100
```

### Verificar agendamento do scheduler

```bash
php artisan schedule:list
```

## Estrutura do Banco de Dados

### Tabela `processo_monitors`

Rastreia quais processos estão sendo monitorados por qual usuário:

| Campo | Tipo | Descrição |
|-------|------|-----------|
| id | INT | ID único |
| user_id | INT | Usuário que está monitorando |
| processo_id | INT | ID do processo (DatajudProcesso) |
| tribunal | VARCHAR | Tribunal do processo |
| numero_processo | VARCHAR | Número do processo |
| ultima_verificacao | DATETIME | Última vez que foi verificado |
| ultima_atualizacao_datajud | DATETIME | Última atualização detectada no DataJud |
| verificacoes_consecutivas_sem_mudanca | INT | Contador de verificações sem mudanças |
| ativo | BOOLEAN | Se o monitoramento está ativo |
| observacoes | TEXT | Observações do usuário |

## Como Funciona em Detalhes

### 1. Salvamento (Backend)

Quando o botão "Salvar" é clicado:
1. Os dados do processo são enviados para `POST /datajud/salvar`
2. O controlador `DataJudController@salvarProcesso()` recebe os dados
3. `DatajudPersistService` salva o processo em `datajud_processos`
4. Um registro em `processo_monitors` é criado para rastrear o monitoramento
5. Uma resposta `{ok: true}` é retornada

### 2. Monitoramento (Scheduler)

A cada 6 horas:
1. Laravel executa `datajud:monitor-updates` via scheduler
2. O comando consulta todos os processos com monitoramento ativo
3. Para cada processo, faz uma requisição à API do DataJud
4. Compara a data da última atualização com a anterior
5. Se houver mudança:
   - Os dados são atualizados no banco
   - Uma notificação é enviada ao usuário via email
6. O campo `ultima_verificacao` é atualizado

### 3. Notificação (Email)

A classe `ProcessoAtualizadoNotification`:
1. Herda de `Notification` com `ShouldQueue` (processada assincronamente)
2. Envia via canal `mail` e `database`
3. Formata a notificação com:
   - Assunto: "Processo #XXXX foi atualizado"
   - Corpo: Detalhes do processo e último movimento
   - Link: Para página de processos salvos

## Troubleshooting

### Email não está sendo enviado

1. Verifique configuração em `.env`:
   ```bash
   php artisan config:show mail
   ```

2. Teste manualmente:
   ```bash
   php artisan tinker
   >>> use App\Models\User;
   >>> $user = User::first();
   >>> $user->notify(new \App\Notifications\ProcessoAtualizadoNotification(...));
   ```

### Scheduler não está executando

1. Verifique se está registrado:
   ```bash
   php artisan schedule:list
   ```

2. Execute manualmente em cron:
   ```bash
   * * * * * cd /path/to/juristack && php artisan schedule:run >> /dev/null 2>&1
   ```

### Processos não estão sendo atualizados

1. Verifique se existem processos monitorados:
   ```bash
   php artisan tinker
   >>> App\Models\ProcessoMonitor::where('ativo', true)->count()
   ```

2. Execute o comando manualmente:
   ```bash
   php artisan datajud:monitor-updates --limit=5
   ```

3. Verifique os logs:
   ```bash
   tail -f storage/logs/laravel.log
   ```

## Próximas Melhorias (Futuro)

- [ ] Interface para gerenciar processos monitorados
- [ ] Histórico de notificações
- [ ] Alertas via SMS ou Push Notification
- [ ] Filtros avançados de monitoramento
- [ ] Relatórios de processos
- [ ] Exportar dados para PDF/Excel

## Suporte

Para dúvidas ou problemas, consulte a documentação do Laravel:
- [Scheduler](https://laravel.com/docs/11.x/scheduling)
- [Notifications](https://laravel.com/docs/11.x/notifications)
- [Queues](https://laravel.com/docs/11.x/queues)
