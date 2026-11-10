<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('number')->unique();
            $table->integer('capacity')->default(4);
            $table->enum('status', ['available', 'occupied', 'reserved'])->default('available');
            $table->string('qr_code')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tables');
    }
};
