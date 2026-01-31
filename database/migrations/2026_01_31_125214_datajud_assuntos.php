<?php

// database/migrations/xxxx_xx_xx_create_datajud_assuntos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datajud_assuntos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('processo_id')
                ->constrained('datajud_processos')
                ->cascadeOnDelete();

            // Glossário: assuntos.codigo, assuntos.nome :contentReference[oaicite:8]{index=8}
            $table->unsignedBigInteger('codigo')->nullable();
            $table->string('nome')->nullable();

            $table->timestamps();

            // evita repetição do mesmo assunto no mesmo processo
            $table->unique(['processo_id', 'codigo'], 'uniq_processo_assunto_codigo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datajud_assuntos');
    }
};

