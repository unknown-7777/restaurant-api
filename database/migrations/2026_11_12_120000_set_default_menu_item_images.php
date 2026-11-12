<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private array $images = [
        'spring-rolls' => 'menu-images/spring-rolls.jpg',
        'chicken-wings' => 'menu-images/chicken-wings.jpg',
        'grilled-salmon' => 'menu-images/grilled-salmon.jpg',
        'beef-burger' => 'menu-images/beef-burger.jpg',
        'grilled-chicken' => 'menu-images/grilled-chicken.jpg',
        'fish-chips' => 'menu-images/fish-and-chips.jpg',
        'spaghetti-bolognese' => 'menu-images/spaghetti.jpg',
        'chicken-carbonara' => 'menu-images/spaghetti.jpg',
        'fried-rice' => 'menu-images/fried-rice.jpg',
        'ribeye-steak' => 'menu-images/grilled-steak.jpg',
        'caesar-salad' => 'menu-images/caesar-salad.jpg',
        'greek-salad' => 'menu-images/caesar-salad.jpg',
        'chocolate-lava-cake' => 'menu-images/chocolate-lava-cake.jpg',
        'cheesecake' => 'menu-images/cheesecake.jpg',
        'ice-cream' => 'menu-images/ice-cream.jpg',
        'espresso' => 'menu-images/cappuccino.jpg',
        'cappuccino' => 'menu-images/cappuccino.jpg',
        'latte' => 'menu-images/cappuccino.jpg',
        'fresh-orange-juice' => 'menu-images/orange-juice.jpg',
        'mango-smoothie' => 'menu-images/mango-smoothie.jpg',
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
