<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $images = [
        'bbq-ribs' => 'menu-images/bbq-ribs.jpg',
        'garlic-bread' => 'menu-images/garlic-bread.jpg',
        'green-tea' => 'menu-images/green-tea.jpg',
        'iced-coffee' => 'menu-images/iced-coffee.jpg',
        'soft-drink' => 'menu-images/soft-drink.jpg',
        'sparkling-water' => 'menu-images/sparkling-water.jpg',
    ];

    public function up(): void
    {
        foreach ($this->images as $slug => $path) {
            DB::table('menu_items')
                ->where('slug', $slug)
                ->whereNull('image_path')
                ->update(['image_path' => $path]);
        }
    }

    public function down(): void
    {
        foreach ($this->images as $slug => $path) {
            DB::table('menu_items')
                ->where('slug', $slug)
                ->where('image_path', $path)
                ->update(['image_path' => null]);
        }
    }
};
