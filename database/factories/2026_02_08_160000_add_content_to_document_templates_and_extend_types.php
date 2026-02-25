<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->longText('content')->nullable()->after('date');
            $table->string('description', 500)->nullable()->after('title');
        });

        Schema::table('documents', function (Blueprint $table) {
            $table->string('document_link')->nullable()->change();
        });

        $driver = Schema::getConnection()->getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE document_templates MODIFY type VARCHAR(80) NOT NULL DEFAULT 'power_of_attorney'");
            DB::statement("ALTER TABLE documents MODIFY type VARCHAR(80) NOT NULL DEFAULT 'power_of_attorney'");
        }
    }

    public function down(): void
    {
        Schema::table('document_templates', function (Blueprint $table) {
            $table->dropColumn(['content', 'description']);
        });
        Schema::table('documents', function (Blueprint $table) {
            $table->string('document_link')->nullable(false)->change();
        });
    }
};
