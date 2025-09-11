<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('categories')->insert([
            ['name' => "Men’s Clothing & Accessories", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Women’s Clothing & Accessories", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Furniture", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Home Décor & Appliances", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Vehicles (Cars, Bikes, Scooters, Accessories)", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Mobiles & Electronics (Phones, Laptops, Gadgets)", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Books, Sports & Hobbies", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
            ['name' => "Kids & Baby Items (Clothing, Toys, Furniture)", 'description' => null, 'parent_id' => null, 'status' => 'approved'],
        ]);
    }
}
