<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (!Schema::hasColumn('events', 'status')) {
                $table->string('status', 30)
                    ->default('pending')
                    ->after('title');
            }

            if (!Schema::hasColumn('events', 'category')) {
                $table->string('category', 100)
                    ->nullable()
                    ->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            if (Schema::hasColumn('events', 'category')) {
                $table->dropColumn('category');
            }

            if (Schema::hasColumn('events', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
