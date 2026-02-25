<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
             $table->id();

            // 🔗 Relacionamento com users
            $table->foreignUuid('user_id')
                  ->unique()
                  ->constrained('users')
                  ->cascadeOnDelete();

            // Personal Data
            $table->string('full_name');
            $table->string('document_number')->nullable(); // CPF/CNPJ
            $table->string('rg_number')->nullable();
            $table->date('rg_issue_date')->nullable();

            $table->string('driver_license_number')->nullable();
            $table->date('driver_license_issue_date')->nullable();
            $table->date('driver_license_expiration_date')->nullable();

            $table->string('inss_password')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();

            // Contact Information
            $table->string('mobile_phone')->nullable();
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();

            // Address
            $table->string('zip_code', 20)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('city')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('street')->nullable();
            $table->string('street_number', 20)->nullable();

            // Additional Information
            $table->string('profession')->nullable();
            $table->string('marital_status')->nullable();

            // Parents
            $table->string('father_name')->nullable();
            $table->date('father_birth_date')->nullable();
            $table->string('mother_name')->nullable();
            $table->date('mother_birth_date')->nullable();

            // Tags
            $table->json('tags')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
