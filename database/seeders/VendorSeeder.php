<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $users = [];
        $vendors = [];

        for ($i = 0; $i < 99; $i++) {

           
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;

            $users[] = [
                'name' => $name,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => bcrypt('password'),
                'user_type' => 'vendor',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }


        DB::table('users')->insert($users);
        $userIds = DB::table('users')
            ->where('user_type', 'vendor')
            ->pluck('id')
            ->toArray();

      
        foreach ($userIds as $userId) {
            $shopName = $faker->company;
            $vendors[] = [
                'user_id' => $userId,
                'shop_name' => $shopName,
                'shop_slug' => Str::slug($shopName) . '-' . Str::random(5), 
                'description' => $faker->sentence(15),
                'logo' => $faker->imageUrl(200, 200, 'business', true), 
                'banner' => $faker->imageUrl(600, 200, 'business', true),
                'address' => $faker->address,
                'status' => $faker->randomElement(['pending', 'approved', 'suspended']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('vendors')->insert($vendors);
    }
}
