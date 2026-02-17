<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // Dados pessoais
            $table->string('name');
            $table->string('cnp')->nullable(); // supondo CPF/CNPJ
            $table->string('rg')->nullable();
            $table->date('rg_issue_date')->nullable();

            $table->string('cnh')->nullable();
            $table->date('cnh_issue_date')->nullable();
            $table->date('cnh_expiration_date')->nullable();

            $table->string('my_inss_password')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();

            // Contatos
            $table->string('mobile_phone')->nullable();
            $table->string('phone')->nullable();
            $table->string('phone_2')->nullable();
            $table->string('email')->unique()->nullable();

            // Endereço
            $table->string('zip_code', 20)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('street')->nullable();
            $table->string('number', 20)->nullable();

            // Informações adicionais
            $table->string('profession')->nullable();
            $table->string('marital_status')->nullable();

            // Filiação
            $table->string('father_name')->nullable();
            $table->date('father_birth_date')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('mother_birth_date')->nullable();

            // Tags (string simples ou JSON)
            $table->text('tags')->nullable();

            // Autenticação (se customer fizer login)
            $table->string('password')->nullable();
            $table->rememberToken();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

