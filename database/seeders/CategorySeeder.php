<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $categories = [];


        for ($i = 0; $i < 50; $i++) {
            $name = $faker->words(rand(1,3), true) . " $i";
            $categories[] = [
                'parent_id' => null,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $faker->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories')->insert($categories);

        $parentIds = DB::table('categories')->pluck('id')->toArray();
        $allCategories = [];

        for ($i = 0; $i < 950; $i++) {
            $name = $faker->words(rand(1,4), true) . " $i";
            $parent_id = $parentIds[array_rand($parentIds)];
            $allCategories[] = [
                'parent_id' => $parent_id,
                'name' => $name,
                'slug' => Str::slug($name),
                'description' => $faker->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('categories')->insert($allCategories);
    }
}
