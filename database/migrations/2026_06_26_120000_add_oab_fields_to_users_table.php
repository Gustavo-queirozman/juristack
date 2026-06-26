<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'oab_state')) {
                $table->string('oab_state', 2)->nullable()->after('is_active');
            }

            if (! Schema::hasColumn('users', 'oab_number')) {
                $table->string('oab_number', 32)->nullable()->after('oab_state');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $dropColumns = [];

            if (Schema::hasColumn('users', 'oab_number')) {
                $dropColumns[] = 'oab_number';
            }

            if (Schema::hasColumn('users', 'oab_state')) {
                $dropColumns[] = 'oab_state';
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};
