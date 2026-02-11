<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('type', 2); // PF | PJ
            $table->string('nome'); // nome (PF) ou razão social (PJ)
            $table->string('cpf', 14)->nullable(); // apenas dígitos, único quando preenchido
            $table->string('cnpj', 18)->nullable(); // apenas dígitos, único quando preenchido
            $table->string('email')->nullable();
            $table->string('telefone', 20)->nullable();

            $table->softDeletes();
            $table->timestamps();

            $table->index(['user_id', 'deleted_at']);
            $table->index(['user_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
