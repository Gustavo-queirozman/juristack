<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enderecos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->cascadeOnDelete();

            $table->string('logradouro');
            $table->string('numero', 20)->nullable();
            $table->string('complemento')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade');
            $table->string('estado', 2);
            $table->string('cep', 10);

            $table->timestamps();

            $table->index('cliente_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enderecos');
    }
};
