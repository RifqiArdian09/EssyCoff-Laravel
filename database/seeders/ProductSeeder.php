<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            ['category_id' => 1, 'name' => 'Nasi Goreng', 'price' => 20000, 'stock' => 50],
            ['category_id' => 1, 'name' => 'Mie Goreng', 'price' => 18000, 'stock' => 40],
            ['category_id' => 2, 'name' => 'Es Teh Manis', 'price' => 5000, 'stock' => 100],
            ['category_id' => 2, 'name' => 'Cappuccino', 'price' => 15000, 'stock' => 30],
            ['category_id' => 3, 'name' => 'Roti Bakar', 'price' => 12000, 'stock' => 20],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
