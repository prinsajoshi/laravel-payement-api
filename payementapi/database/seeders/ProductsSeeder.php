<?php

namespace Database\Seeders;

use App\Models\Categories;
use App\Models\Products;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics=Categories::where('category_name','Electronics')->first();
        $furniture=Categories::where('category_name','Furniture')->first();
        $books=Categories::where('category_name','Books')->first();

         // Create some products
         Products::create([
            'title' => 'Smartphone',
            'description' => 'Latest 5G smartphone',
            'category_id' => $electronics->id,
            'price' => 50,
            'stock' => 50
        ]);

        Products::create([
            'title' => 'Laptop',
            'description' => 'Gaming laptop',
            'category_id' => $electronics->id,
            'price' => 30,
            'stock' => 30
        ]);

        Products::create([
            'title' => 'Table',
            'description' => 'Comfortable wooden table',
            'category_id' => $furniture->id,
            'price' => 100,
            'stock' => 100
        ]);

        Products::create([
            'title' => 'Chairs',
            'description' => 'High-efficiency strong wooden chair',
            'category_id' => $furniture->id,
            'price' => 20,
            'stock' => 20
        ]);

        Products::create([
            'title' => 'It ends with us',
            'description' => 'love story book of ryle,lily and atlas',
            'category_id' => $books->id,
            'price' => 100,
            'stock' => 100
        ]);

        Products::create([
            'title' => 'Verity',
            'description' => 'Horror book with mystery',
            'category_id' => $books->id,
            'price' => 20,
            'stock' => 20
        ]);


    }
}
