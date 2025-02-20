<?php

namespace Database\Seeders;
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Gadgets and tech devices'],
            ['name' => 'Clothing', 'slug' => 'clothing', 'description' => 'Apparel and accessories'],
            ['name' => 'Books', 'slug' => 'books', 'description' => 'Fiction, non-fiction, and more'],
            ['name' => 'Home Appliances', 'slug' => 'home-appliances', 'description' => 'Household essentials'],
            ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Gear for active lifestyles'],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}