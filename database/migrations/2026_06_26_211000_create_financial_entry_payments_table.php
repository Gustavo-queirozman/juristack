<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_entry_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('financial_entry_id')
                ->constrained('financial_entries')
                ->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('payment_date');
            $table->string('source', 30)->default('manual');
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->json('imported_payload')->nullable();
            $table->timestamps();

            $table->index(['financial_entry_id', 'payment_date']);
            $table->index(['source', 'reference']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_entry_payments');
    }
};
