<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->foreignId('customer_id')
                ->nullable()
                ->after('enterprise_id')
                ->constrained('customers')
                ->nullOnDelete();
            $table->text('notes')->nullable()->after('payment_method');
            $table->boolean('whatsapp_reminder_enabled')->default(true)->after('notes');
            $table->timestamp('last_whatsapp_reminder_at')->nullable()->after('whatsapp_reminder_enabled');

            $table->index(['customer_id', 'entry_type']);
        });
    }

    public function down(): void
    {
        Schema::table('financial_entries', function (Blueprint $table) {
            $table->dropIndex(['customer_id', 'entry_type']);
            $table->dropConstrainedForeignId('customer_id');
            $table->dropColumn([
                'notes',
                'whatsapp_reminder_enabled',
                'last_whatsapp_reminder_at',
            ]);
        });
    }
};
