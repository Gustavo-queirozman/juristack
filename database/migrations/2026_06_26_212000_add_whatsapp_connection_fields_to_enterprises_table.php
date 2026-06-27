<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            if (! Schema::hasColumn('enterprises', 'evolution_instance')) {
                $table->string('evolution_instance')->nullable()->unique()->after('address');
            }

            if (! Schema::hasColumn('enterprises', 'whatsapp_connection_status')) {
                $table->string('whatsapp_connection_status', 32)->nullable()->after('evolution_instance');
            }

            if (! Schema::hasColumn('enterprises', 'whatsapp_qr_code')) {
                $table->longText('whatsapp_qr_code')->nullable()->after('whatsapp_connection_status');
            }

            if (! Schema::hasColumn('enterprises', 'whatsapp_connected_at')) {
                $table->timestamp('whatsapp_connected_at')->nullable()->after('whatsapp_qr_code');
            }

            if (! Schema::hasColumn('enterprises', 'whatsapp_disconnected_at')) {
                $table->timestamp('whatsapp_disconnected_at')->nullable()->after('whatsapp_connected_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('enterprises', function (Blueprint $table) {
            foreach ([
                'whatsapp_disconnected_at',
                'whatsapp_connected_at',
                'whatsapp_qr_code',
                'whatsapp_connection_status',
                'evolution_instance',
            ] as $column) {
                if (Schema::hasColumn('enterprises', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
