<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_document_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('enterprise_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('datajud_processo_id')->nullable()->constrained('datajud_processos')->nullOnDelete();
            $table->foreignId('requested_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('document_type');
            $table->text('description')->nullable();
            $table->string('status')->default('pending');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['datajud_processo_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_document_requests');
    }
};
