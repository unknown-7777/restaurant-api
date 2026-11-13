<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('tables')
            ->whereIn('id', DB::table('qr_guest_sessions')->select('table_id'))
            ->update(['qr_access_enabled' => false]);
    }

    public function down(): void
    {
    }
};
