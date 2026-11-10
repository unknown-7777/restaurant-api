<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_item_modifiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('modifier_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->decimal('price_delta', 10, 2)->default(0.00);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_modifiers');
    }
};
