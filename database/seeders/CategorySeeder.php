<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Appetizers',    'sort_order' => 1],
            ['name' => 'Main Course',   'sort_order' => 2],
            ['name' => 'Pasta & Rice',  'sort_order' => 3],
            ['name' => 'Grills',        'sort_order' => 4],
            ['name' => 'Salads',        'sort_order' => 5],
            ['name' => 'Desserts',      'sort_order' => 6],
            ['name' => 'Hot Drinks',    'sort_order' => 7],
            ['name' => 'Cold Drinks',   'sort_order' => 8],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name'       => $category['name'],
                'slug'       => Str::slug($category['name']),
                'sort_order' => $category['sort_order'],
                'is_active'  => true,
            ]);
        }

        $this->command->info('✓ Categories seeded — 8 categories created.');
    }
}
