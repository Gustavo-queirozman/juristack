<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('datajud_processos', 'customer_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->foreignId('customer_id')
                    ->nullable()
                    ->after('enterprise_id')
                    ->constrained('customers')
                    ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('datajud_processos', 'customer_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('customer_id');
            });
        }
    }
};
