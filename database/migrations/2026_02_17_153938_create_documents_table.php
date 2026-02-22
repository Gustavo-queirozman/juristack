<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // titulo_documento
            $table->enum('type', ['power_of_attorney', 'contract', 'petition']);
            $table->string('document_link');
            $table->string('form_link')->nullable();
            $table->foreignId('document_template_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

