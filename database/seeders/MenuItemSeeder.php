<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Modifier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        $imagePaths = [
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

        $items = [
            // Appetizers
            'Appetizers' => [
                [
                    'name'        => 'Spring Rolls',
                    'description' => 'Crispy vegetable spring rolls served with sweet chili sauce.',
                    'price'       => 6.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Extra Sauce',    'type' => 'single',   'price_delta' => 0.50],
                        ['name' => 'Make it Spicy',  'type' => 'single',   'price_delta' => 0.00],
                    ],
                ],
                [
                    'name'        => 'Chicken Wings',
                    'description' => 'Crispy fried chicken wings with your choice of sauce.',
                    'price'       => 9.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'BBQ Sauce',      'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Hot Sauce',      'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Honey Mustard',  'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Extra Portion',  'type' => 'single',   'price_delta' => 3.00],
                    ],
                ],
                [
                    'name'        => 'Garlic Bread',
                    'description' => 'Toasted bread with garlic butter and herbs.',
                    'price'       => 4.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Add Cheese',     'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
            ],

            // Main Course
            'Main Course' => [
                [
                    'name'        => 'Grilled Salmon',
                    'description' => 'Fresh Atlantic salmon fillet grilled with lemon butter sauce.',
                    'price'       => 18.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Extra Lemon Butter', 'type' => 'single', 'price_delta' => 1.00],
                        ['name' => 'Add Vegetables',     'type' => 'single', 'price_delta' => 2.00],
                    ],
                ],
                [
                    'name'        => 'Beef Burger',
                    'description' => 'Juicy beef patty with lettuce, tomato, and special sauce.',
                    'price'       => 13.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Add Cheese',     'type' => 'single',   'price_delta' => 1.00],
                        ['name' => 'Add Bacon',      'type' => 'single',   'price_delta' => 1.50],
                        ['name' => 'Add Egg',        'type' => 'single',   'price_delta' => 1.00],
                        ['name' => 'Make it Double', 'type' => 'single',   'price_delta' => 4.00],
                    ],
                ],
                [
                    'name'        => 'Grilled Chicken',
                    'description' => 'Tender chicken breast marinated in herbs and grilled to perfection.',
                    'price'       => 14.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Mushroom Sauce',  'type' => 'single',  'price_delta' => 1.50],
                        ['name' => 'Pepper Sauce',    'type' => 'single',  'price_delta' => 1.50],
                        ['name' => 'Plain',           'type' => 'single',  'price_delta' => 0.00],
                    ],
                ],
                [
                    'name'        => 'Fish & Chips',
                    'description' => 'Beer battered fish fillet with crispy chips and tartar sauce.',
                    'price'       => 15.99,
                    'sort_order'  => 4,
                    'modifiers'   => [
                        ['name' => 'Extra Chips',    'type' => 'single',   'price_delta' => 2.00],
                        ['name' => 'Extra Tartar',   'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
            ],

            // Pasta & Rice
            'Pasta & Rice' => [
                [
                    'name'        => 'Spaghetti Bolognese',
                    'description' => 'Classic Italian pasta with slow-cooked beef ragù.',
                    'price'       => 12.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Add Parmesan',   'type' => 'single',   'price_delta' => 1.00],
                        ['name' => 'Extra Sauce',    'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
                [
                    'name'        => 'Chicken Carbonara',
                    'description' => 'Creamy pasta with grilled chicken, bacon, and parmesan.',
                    'price'       => 14.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Extra Chicken',  'type' => 'single',   'price_delta' => 2.50],
                        ['name' => 'Add Mushrooms',  'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
                [
                    'name'        => 'Fried Rice',
                    'description' => 'Wok-fried rice with eggs, vegetables, and soy sauce.',
                    'price'       => 10.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Add Chicken',    'type' => 'single',   'price_delta' => 2.00],
                        ['name' => 'Add Shrimp',     'type' => 'single',   'price_delta' => 3.00],
                        ['name' => 'Extra Spicy',    'type' => 'single',   'price_delta' => 0.00],
                    ],
                ],
            ],

            // Grills
            'Grills' => [
                [
                    'name'        => 'Ribeye Steak',
                    'description' => '300g premium ribeye steak grilled to your preference.',
                    'price'       => 28.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Rare',           'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Medium Rare',    'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Medium',         'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Well Done',      'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Add Mushrooms',  'type' => 'single',   'price_delta' => 2.00],
                    ],
                ],
                [
                    'name'        => 'BBQ Ribs',
                    'description' => 'Slow-cooked pork ribs glazed with smoky BBQ sauce.',
                    'price'       => 24.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Extra BBQ Sauce', 'type' => 'single',  'price_delta' => 0.50],
                        ['name' => 'Add Coleslaw',    'type' => 'single',  'price_delta' => 1.50],
                    ],
                ],
            ],

            // Salads
            'Salads' => [
                [
                    'name'        => 'Caesar Salad',
                    'description' => 'Romaine lettuce, croutons, parmesan, and Caesar dressing.',
                    'price'       => 9.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Add Chicken',    'type' => 'single',   'price_delta' => 3.00],
                        ['name' => 'Add Shrimp',     'type' => 'single',   'price_delta' => 4.00],
                        ['name' => 'Extra Dressing', 'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
                [
                    'name'        => 'Greek Salad',
                    'description' => 'Tomatoes, cucumber, olives, red onion, and feta cheese.',
                    'price'       => 8.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Extra Feta',     'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
            ],

            // Desserts
            'Desserts' => [
                [
                    'name'        => 'Chocolate Lava Cake',
                    'description' => 'Warm chocolate cake with a gooey molten center.',
                    'price'       => 7.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Add Ice Cream',  'type' => 'single',   'price_delta' => 2.00],
                        ['name' => 'Add Cream',      'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
                [
                    'name'        => 'Cheesecake',
                    'description' => 'New York style cheesecake with berry compote.',
                    'price'       => 6.99,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Strawberry',     'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Blueberry',      'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Mixed Berry',    'type' => 'single',   'price_delta' => 0.00],
                    ],
                ],
                [
                    'name'        => 'Ice Cream',
                    'description' => 'Three scoops of premium ice cream.',
                    'price'       => 5.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Vanilla',        'type' => 'multiple', 'price_delta' => 0.00],
                        ['name' => 'Chocolate',      'type' => 'multiple', 'price_delta' => 0.00],
                        ['name' => 'Strawberry',     'type' => 'multiple', 'price_delta' => 0.00],
                        ['name' => 'Add Waffle',     'type' => 'single',   'price_delta' => 1.50],
                    ],
                ],
            ],

            // Hot Drinks
            'Hot Drinks' => [
                [
                    'name'        => 'Espresso',
                    'description' => 'Strong Italian coffee shot.',
                    'price'       => 2.99,
                    'sort_order'  => 1,
                    'modifiers'   => [
                        ['name' => 'Double Shot',    'type' => 'single',   'price_delta' => 1.00],
                    ],
                ],
                [
                    'name'        => 'Cappuccino',
                    'description' => 'Espresso with steamed milk and foam.',
                    'price'       => 4.49,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Oat Milk',       'type' => 'single',   'price_delta' => 0.60],
                        ['name' => 'Soy Milk',       'type' => 'single',   'price_delta' => 0.60],
                        ['name' => 'Extra Shot',     'type' => 'single',   'price_delta' => 1.00],
                        ['name' => 'Add Vanilla',    'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
                [
                    'name'        => 'Latte',
                    'description' => 'Smooth espresso with steamed milk.',
                    'price'       => 4.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Oat Milk',       'type' => 'single',   'price_delta' => 0.60],
                        ['name' => 'Soy Milk',       'type' => 'single',   'price_delta' => 0.60],
                        ['name' => 'Extra Shot',     'type' => 'single',   'price_delta' => 1.00],
                        ['name' => 'Caramel Syrup',  'type' => 'single',   'price_delta' => 0.50],
                        ['name' => 'Vanilla Syrup',  'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
                [
                    'name'        => 'Green Tea',
                    'description' => 'Premium Japanese green tea.',
                    'price'       => 3.49,
                    'sort_order'  => 4,
                    'modifiers'   => [
                        ['name' => 'Add Honey',      'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
            ],

            // Cold Drinks
            'Cold Drinks' => [
                [
                    'name'        => 'Fresh Orange Juice',
                    'description' => 'Freshly squeezed orange juice.',
                    'price'       => 4.99,
                    'sort_order'  => 1,
                    'modifiers'   => [],
                ],
                [
                    'name'        => 'Iced Coffee',
                    'description' => 'Cold brew coffee over ice with milk.',
                    'price'       => 5.49,
                    'sort_order'  => 2,
                    'modifiers'   => [
                        ['name' => 'Oat Milk',       'type' => 'single',   'price_delta' => 0.60],
                        ['name' => 'Add Vanilla',    'type' => 'single',   'price_delta' => 0.50],
                        ['name' => 'Add Caramel',    'type' => 'single',   'price_delta' => 0.50],
                    ],
                ],
                [
                    'name'        => 'Mango Smoothie',
                    'description' => 'Fresh mango blended with yogurt and honey.',
                    'price'       => 5.99,
                    'sort_order'  => 3,
                    'modifiers'   => [
                        ['name' => 'Add Protein',    'type' => 'single',   'price_delta' => 2.00],
                    ],
                ],
                [
                    'name'        => 'Sparkling Water',
                    'description' => 'Chilled sparkling mineral water.',
                    'price'       => 2.49,
                    'sort_order'  => 4,
                    'modifiers'   => [],
                ],
                [
                    'name'        => 'Soft Drink',
                    'description' => 'Choice of cola, lemonade, or ginger ale.',
                    'price'       => 2.99,
                    'sort_order'  => 5,
                    'modifiers'   => [
                        ['name' => 'Cola',           'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Lemonade',       'type' => 'single',   'price_delta' => 0.00],
                        ['name' => 'Ginger Ale',     'type' => 'single',   'price_delta' => 0.00],
                    ],
                ],
            ],
        ];

        foreach ($items as $categoryName => $categoryItems) {
            $category = Category::where('name', $categoryName)->first();
            if (!$category) continue;

            foreach ($categoryItems as $itemData) {
                $modifiers = $itemData['modifiers'];
                unset($itemData['modifiers']);

                $item = MenuItem::create([
                    'category_id'  => $category->id,
                    'name'         => $itemData['name'],
                    'slug'         => Str::slug($itemData['name']),
                    'description'  => $itemData['description'],
                    'price'        => $itemData['price'],
                    'image_path'   => $imagePaths[Str::slug($itemData['name'])] ?? null,
                    'is_available' => true,
                    'sort_order'   => $itemData['sort_order'],
                ]);

                foreach ($modifiers as $modifier) {
                    Modifier::create([
                        'menu_item_id' => $item->id,
                        'name'         => $modifier['name'],
                        'type'         => $modifier['type'],
                        'price_delta'  => $modifier['price_delta'],
                        'is_available' => true,
                    ]);
                }
            }
        }

        $this->command->info('✓ Menu items seeded — 25 items with modifiers created.');
    }
}
