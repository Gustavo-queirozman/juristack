<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_files', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_files', 'datajud_processo_id')) {
                $table->foreignId('datajud_processo_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('datajud_processos')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('customer_files', 'uploaded_by_user_id')) {
                $table->foreignId('uploaded_by_user_id')
                    ->nullable()
                    ->after('datajud_processo_id')
                    ->constrained('users')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_files', function (Blueprint $table) {
            if (Schema::hasColumn('customer_files', 'uploaded_by_user_id')) {
                $table->dropConstrainedForeignId('uploaded_by_user_id');
            }

            if (Schema::hasColumn('customer_files', 'datajud_processo_id')) {
                $table->dropConstrainedForeignId('datajud_processo_id');
            }
        });
    }
};
