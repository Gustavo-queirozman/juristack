<?php

// database/migrations/xxxx_xx_xx_create_datajud_movimento_complementos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('datajud_movimento_complementos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('movimento_id')
                ->constrained('datajud_movimentos')
                ->cascadeOnDelete();

            // Glossário: complementosTabelados.codigo, descricao, valor, nome :contentReference[oaicite:11]{index=11}
            $table->unsignedBigInteger('codigo')->nullable();
            $table->string('descricao')->nullable();
            $table->unsignedBigInteger('valor')->nullable();
            $table->string('nome')->nullable();

            $table->timestamps();

            $table->index(['movimento_id', 'codigo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('datajud_movimento_complementos');
    }
};

