<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('payment_requested_at')->nullable()->after('notes');
            $table->string('payment_requested_method', 20)->nullable()->after('payment_requested_at');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->unique('order_id');
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropUnique(['order_id']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_requested_at', 'payment_requested_method']);
        });
    }
};
