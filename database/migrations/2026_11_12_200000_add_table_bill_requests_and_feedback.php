<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tables', function (Blueprint $table) {
            $table->timestamp('bill_requested_at')->nullable();
            $table->string('bill_requested_method', 20)->nullable();
        });

        Schema::create('restaurant_feedback', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('table_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('order_id')->constrained()->cascadeOnDelete()->unique();
            $table->unsignedTinyInteger('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
            $table->index(['rating', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_feedback');

        Schema::table('tables', function (Blueprint $table) {
            $table->dropColumn(['bill_requested_at', 'bill_requested_method']);
        });
    }
};
