<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modifiers', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('menu_item_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->enum('type', ['single', 'multiple'])->default('single');
            $table->decimal('price_delta', 10, 2)->default(0.00);
            $table->boolean('is_available')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('modifiers');
    }
};
