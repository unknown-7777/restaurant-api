<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_guest_sessions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('table_id')->constrained()->cascadeOnDelete();
            $table->string('token_hash', 64)->unique();
            $table->timestamp('expires_at');
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->index(['table_id', 'closed_at', 'expires_at']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignUuid('qr_guest_session_id')
                ->nullable()
                ->after('table_id')
                ->constrained('qr_guest_sessions')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('qr_guest_session_id');
        });

        Schema::dropIfExists('qr_guest_sessions');
    }
};
