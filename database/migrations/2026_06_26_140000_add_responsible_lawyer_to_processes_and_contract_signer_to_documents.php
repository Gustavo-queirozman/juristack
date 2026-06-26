<?php

use App\Models\Document;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasColumn('datajud_processos', 'responsible_lawyer_user_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->foreignId('responsible_lawyer_user_id')
                    ->nullable()
                    ->after('customer_id')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (! Schema::hasColumn('documents', 'service_contract_signer_type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('service_contract_signer_type', 32)
                    ->nullable()
                    ->after('enterprise_id');
            });
        }

        if (! Schema::hasColumn('documents', 'service_contract_signer_user_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->foreignId('service_contract_signer_user_id')
                    ->nullable()
                    ->after('service_contract_signer_type')
                    ->constrained('users')
                    ->nullOnDelete();
            });
        }

        if (Schema::hasColumn('documents', 'service_contract_signer_type')) {
            DB::table('documents')
                ->where('type', Document::TYPE_CONTRACT)
                ->whereNull('service_contract_signer_type')
                ->update([
                    'service_contract_signer_type' => 'enterprise',
                    'updated_at' => now(),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('documents', 'service_contract_signer_user_id')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropConstrainedForeignId('service_contract_signer_user_id');
            });
        }

        if (Schema::hasColumn('documents', 'service_contract_signer_type')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('service_contract_signer_type');
            });
        }

        if (Schema::hasColumn('datajud_processos', 'responsible_lawyer_user_id')) {
            Schema::table('datajud_processos', function (Blueprint $table) {
                $table->dropConstrainedForeignId('responsible_lawyer_user_id');
            });
        }
    }
};
