<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (! Schema::hasColumn('tasks', 'datajud_processo_id')) {
                $table->foreignId('datajud_processo_id')
                    ->nullable()
                    ->after('enterprise_id')
                    ->constrained('datajud_processos')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('tasks', 'due_date')) {
                $table->date('due_date')
                    ->nullable()
                    ->after('status');
            }

            if (! Schema::hasColumn('tasks', 'priority')) {
                $table->enum('priority', ['low', 'medium', 'high', 'urgent'])
                    ->default('medium')
                    ->after('due_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            if (Schema::hasColumn('tasks', 'datajud_processo_id')) {
                $table->dropConstrainedForeignId('datajud_processo_id');
            }

            if (Schema::hasColumn('tasks', 'priority')) {
                $table->dropColumn('priority');
            }

            if (Schema::hasColumn('tasks', 'due_date')) {
                $table->dropColumn('due_date');
            }
        });
    }
};
