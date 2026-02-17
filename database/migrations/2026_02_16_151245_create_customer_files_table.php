<?php

// database/migrations/xxxx_xx_xx_create_customer_files_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('path');              // caminho no storage
            $table->string('original_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_files');
    }
};

