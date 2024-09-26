<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Customer::create([
            "username"=>"admin",
            "password"=>Hash::make('admin'),
            "email"=>"admin@gmail.com",
            "token"=>Hash::make(Str::random(16)),
            "usertype"=>"admin",
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
