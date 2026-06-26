<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            if (! Schema::hasColumn('document_templates', 'description')) {
                $table->string('description', 500)->nullable()->after('type');
            }

            if (! Schema::hasColumn('document_templates', 'content')) {
                $table->longText('content')->nullable()->after('date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('document_templates', 'content')) {
                $dropColumns[] = 'content';
            }

            if (Schema::hasColumn('document_templates', 'description')) {
                $dropColumns[] = 'description';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
