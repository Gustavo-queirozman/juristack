<?php

// database/migrations/xxxx_xx_xx_create_datajud_movimentos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datajud_movimentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')
                ->constrained('datajud_processos')
                ->cascadeOnDelete();

            // Glossário: movimentos.codigo, movimentos.nome, movimentos.dataHora :contentReference[oaicite:9]{index=9}
            $table->unsignedBigInteger('codigo')->nullable();
            $table->string('nome')->nullable();
            $table->dateTime('data_hora')->nullable();

            // Glossário: movimentos.orgaoJulgador (codigoOrgao, nomeOrgao) :contentReference[oaicite:10]{index=10}
            $table->unsignedBigInteger('orgao_codigo')->nullable();
            $table->string('orgao_nome')->nullable();

            $table->timestamps();

            $table->index(['processo_id', 'data_hora']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datajud_movimentos');
    }
};

