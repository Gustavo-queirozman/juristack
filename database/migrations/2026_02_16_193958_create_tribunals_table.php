<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tribunals', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('cnj_code')->unique();
            $table->string('acronym', 10)->unique();
            $table->string('name_en');
            $table->char('state_code', 2);
            $table->char('country_code', 2)->default('BR');
            $table->string('homepage_url')->nullable();
            $table->string('public_search_url')->nullable();
            $table->string('system')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tribunals');
    }
};

