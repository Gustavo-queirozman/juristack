<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('processo_monitors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('processo_id')->constrained('datajud_processos')->cascadeOnDelete();
            $table->string('tribunal')->index();
            $table->string('numero_processo')->index();
            $table->datetime('ultima_verificacao')->nullable();
            $table->datetime('ultima_atualizacao_datajud')->nullable();
            $table->integer('verificacoes_consecutivas_sem_mudanca')->default(0);
            $table->boolean('ativo')->default(true)->index();
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('processo_monitors');
    }
};
