<?php

namespace Database\Seeders;

use App\Models\discount;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class DiscountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        discount::create([
            "discount_token"=>Str::random(5),
            "discount_percentage"=>10,
        ]);

        discount::create([
            "discount_token"=>Str::random(5),
            "discount_percentage"=>5,
        ]);
        discount::create([
            "discount_token"=>Str::random(5),
            "discount_percentage"=>15,
        ]);

        // Customer::create([
        //     "username"=>"Jimin",
        //     "password"=>Hash::make('Jimin'),
        //     "email"=>"prinsajoshi1@gmail.com"
        // ]);

        // Customer::create([
        //     "username"=>"Jungkook",
        //     "password"=>Hash::make('Jungkook'),
        //     "email"=>"prinsajoshi2@gmail.com"
        // ]);
    }
}
