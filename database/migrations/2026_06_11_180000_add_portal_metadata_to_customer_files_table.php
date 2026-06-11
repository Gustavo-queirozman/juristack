<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customer_files', function (Blueprint $table) {
            if (! Schema::hasColumn('customer_files', 'document_type')) {
                $table->string('document_type', 50)
                    ->nullable()
                    ->after('customer_id');
            }

            if (! Schema::hasColumn('customer_files', 'description')) {
                $table->string('description')
                    ->nullable()
                    ->after('document_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('customer_files', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('customer_files', 'description')) {
                $dropColumns[] = 'description';
            }

            if (Schema::hasColumn('customer_files', 'document_type')) {
                $dropColumns[] = 'document_type';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
