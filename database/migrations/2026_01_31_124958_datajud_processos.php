<?php


// database/migrations/xxxx_xx_xx_create_datajud_processos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datajud_processos', function (Blueprint $table) {
            $table->id();

            // Se for por usuário (recomendado)
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Glossário: id (chave Tribunal_Classe_Grau_OrgaoJulgador_NumeroProcesso) :contentReference[oaicite:1]{index=1}
            $table->string('datajud_id')->nullable()->index();

            // Glossário: tribunal, numeroProcesso, dataAjuizamento, grau, nivelSigilo :contentReference[oaicite:2]{index=2}
            $table->string('tribunal', 20);
            $table->string('numero_processo', 40); // numeroProcesso sem formatação
            $table->dateTime('data_ajuizamento')->nullable();
            $table->string('grau', 10)->nullable();
            $table->unsignedBigInteger('nivel_sigilo')->nullable();

            // Glossário: formato (codigo, nome) :contentReference[oaicite:3]{index=3}
            $table->unsignedBigInteger('formato_codigo')->nullable();
            $table->string('formato_nome')->nullable();

            // Glossário: sistema (codigo, nome) :contentReference[oaicite:4]{index=4}
            $table->unsignedBigInteger('sistema_codigo')->nullable();
            $table->string('sistema_nome')->nullable();

            // Glossário: classe (codigo, nome) :contentReference[oaicite:5]{index=5}
            $table->unsignedBigInteger('classe_codigo')->nullable();
            $table->string('classe_nome')->nullable();

            // Glossário: orgaoJulgador (codigo, nome, codigoMunicipioIBGE) :contentReference[oaicite:6]{index=6}
            $table->unsignedBigInteger('orgao_julgador_codigo')->nullable();
            $table->string('orgao_julgador_nome')->nullable();
            $table->unsignedBigInteger('orgao_julgador_codigo_municipio_ibge')->nullable();

            // Campos internos do DataJud (controle do índice) :contentReference[oaicite:7]{index=7}
            $table->dateTime('datahora_ultima_atualizacao')->nullable(); // dataHoraUltimaAtualizacao
            $table->dateTime('indexed_at')->nullable(); // @timestamp

            // Opcional: manter o JSON bruto para debug/reprocessamento
            $table->json('payload')->nullable();

            $table->timestamps();

            // Evita duplicar o mesmo processo para o mesmo usuário
            $table->unique(['user_id', 'tribunal', 'numero_processo', 'grau'], 'uniq_user_trib_num_grau');
            $table->index(['tribunal', 'numero_processo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datajud_processos');
    }
};

