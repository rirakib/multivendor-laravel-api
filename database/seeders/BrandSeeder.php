<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $brands = [];

        for ($i = 0; $i < 1000; $i++) {
            $name = $faker->unique()->company; // random company name
            $brands[] = [
                'name' => $name,
                'slug' => Str::slug($name) . '-' . Str::random(5), // ensure unique
                'description' => $faker->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('brands')->insert($brands);
    }
}
