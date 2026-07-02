<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('enterprises')) {
            return;
        }

        Schema::table('enterprises', function (Blueprint $table) {
            if (! Schema::hasColumn('enterprises', 'payment_overdue_since')) {
                $table->timestamp('payment_overdue_since')->nullable()->after('subscription_canceled_at');
            }

            if (! Schema::hasColumn('enterprises', 'last_payment_overdue_reminder_at')) {
                $table->timestamp('last_payment_overdue_reminder_at')->nullable()->after('payment_overdue_since');
            }

            if (! Schema::hasColumn('enterprises', 'last_payment_overdue_reminder_stage')) {
                $table->unsignedSmallInteger('last_payment_overdue_reminder_stage')->nullable()->after('last_payment_overdue_reminder_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('enterprises')) {
            return;
        }

        Schema::table('enterprises', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'payment_overdue_since',
                'last_payment_overdue_reminder_at',
                'last_payment_overdue_reminder_stage',
            ] as $column) {
                if (Schema::hasColumn('enterprises', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
