<?php

namespace Database\Seeders;

use App\Models\Table;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            ['number' => 1,  'capacity' => 2],
            ['number' => 2,  'capacity' => 2],
            ['number' => 3,  'capacity' => 4],
            ['number' => 4,  'capacity' => 4],
            ['number' => 5,  'capacity' => 4],
            ['number' => 6,  'capacity' => 4],
            ['number' => 7,  'capacity' => 6],
            ['number' => 8,  'capacity' => 6],
            ['number' => 9,  'capacity' => 8],
            ['number' => 10, 'capacity' => 8],
        ];

        foreach ($tables as $table) {
            Table::create([
                'number'   => $table['number'],
                'capacity' => $table['capacity'],
                'status'   => 'available',
                'qr_code'  => Str::uuid(),
            ]);
        }

        $this->command->info('✓ Tables seeded — 10 tables created.');
    }
}
