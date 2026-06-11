<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')
                ->nullable()
                ->constrained('enterprises')
                ->nullOnDelete();
            $table->string('title');
            $table->decimal('amount', 12, 2);
            $table->date('entry_date');
            $table->string('entry_type', 20);
            $table->string('payment_method', 20);
            $table->timestamps();

            $table->index(['enterprise_id', 'entry_date']);
            $table->index(['entry_type', 'payment_method']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_entries');
    }
};
