<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('categories')->insert([
            ['category_name'=>'Electronics','created_at'=>now(),'updated_at'=>now()],
            ['category_name'=>'Furniture','created_at'=>now(),'updated_at'=>now()],
            ['category_name'=>'Books','created_at'=>now(),'updated_at'=>now()]
        ]);
    }
}
