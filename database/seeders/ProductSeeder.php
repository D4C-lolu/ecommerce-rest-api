<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $products = [
            [
                'category_id' => 1, // Electronics
                'name' => 'Laptop Pro',
                'slug' => 'laptop-pro',
                'description' => 'High-performance laptop with 16GB RAM',
                'price' => 1299.99,
                'stock_quantity' => 15,
            ],
            [
                'category_id' => 1, // Electronics
                'name' => 'Smartphone X',
                'slug' => 'smartphone-x',
                'description' => 'Latest smartphone with 5G support',
                'price' => 799.50,
                'stock_quantity' => 25,
            ],
            [
                'category_id' => 2, // Clothing
                'name' => 'Winter Jacket',
                'slug' => 'winter-jacket',
                'description' => 'Warm jacket for cold weather',
                'price' => 89.99,
                'stock_quantity' => 30,
            ],
            [
                'category_id' => 2, // Clothing
                'name' => 'Running Shoes',
                'slug' => 'running-shoes',
                'description' => 'Lightweight shoes for runners',
                'price' => 59.95,
                'stock_quantity' => 20,
            ],
            [
                'category_id' => 3, // Books
                'name' => 'Sci-Fi Novel',
                'slug' => 'sci-fi-novel',
                'description' => 'Bestselling space adventure book',
                'price' => 19.99,
                'stock_quantity' => 50,
            ],
            [
                'category_id' => 4, // Home Appliances
                'name' => 'Blender 3000',
                'slug' => 'blender-3000',
                'description' => 'Powerful blender for smoothies',
                'price' => 49.99,
                'stock_quantity' => 10,
            ],
            [
                'category_id' => 5, // Sports
                'name' => 'Yoga Mat',
                'slug' => 'yoga-mat',
                'description' => 'Non-slip mat for yoga practice',
                'price' => 29.99,
                'stock_quantity' => 40,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}