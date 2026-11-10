<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('method', ['cash', 'card', 'qris'])->default('cash');
            $table->decimal('amount', 10, 2);
            $table->decimal('change_amount', 10, 2)->default(0.00);
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
