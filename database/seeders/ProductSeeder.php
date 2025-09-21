<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        $vendorIds = DB::table('vendors')->pluck('id')->toArray();
        $categoryIds = DB::table('categories')->pluck('id')->toArray();
        $brandIds = DB::table('brands')->pluck('id')->toArray();

        $totalProducts = 50000;
        $chunkSize = 500;

        $productCounter = 0;

        while($productCounter < $totalProducts) {

            $products = [];
            $productImages = [];
            $productAttributes = [];

            $batch = min($chunkSize, $totalProducts - $productCounter);

            for ($i = 0; $i < $batch; $i++) {

                $vendorId = $vendorIds[array_rand($vendorIds)];
                $categoryId = $categoryIds[array_rand($categoryIds)];
                $brandId = $faker->optional()->randomElement($brandIds);

                $name = $faker->words(rand(2,5), true) . " " . ($productCounter + $i);
                $slug = Str::slug($name) . '-' . Str::random(5);
                $sku = strtoupper(Str::random(8)) . ($productCounter + $i);

                $products[] = [
                    'vendor_id' => $vendorId,
                    'category_id' => $categoryId,
                    'brand_id' => $brandId,
                    'name' => $name,
                    'slug' => $slug,
                    'description' => $faker->sentence(15),
                    'price' => $faker->randomFloat(2, 10, 1000),
                    'discount_price' => $faker->optional(0.5)->randomFloat(2, 5, 900),
                    'sku' => $sku,
                    'stock_quantity' => rand(0, 100),
                    'status' => $faker->randomElement(['active','inactive','draft']),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Insert products batch
            DB::table('products')->insert($products);

            // Fetch inserted product IDs for this batch
            $productIds = DB::table('products')
                ->orderByDesc('id')
                ->limit($batch)
                ->pluck('id')
                ->toArray();

            // Product Images (1 thumbnail + 1–2 extra)
            foreach ($productIds as $productId) {
                $numImages = rand(1,3);
                for ($j = 0; $j < $numImages; $j++) {
                    $productImages[] = [
                        'product_id' => $productId,
                        'image' => 'default.png', 
                        'is_thumbnail' => $j == 0 ? true : false,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            DB::table('product_images')->insert($productImages);

            // Product Attributes (~50% products get attributes)
            $attributeOptions = ['Size','Color','Material'];
            $materialOptions = ['Cotton','Polyester','Leather','Wool'];
            $sizeOptions = ['S','M','L','XL'];
            $colorOptions = ['Red','Blue','Green','Black','White'];

            foreach ($productIds as $productId) {
                if(rand(0,1)) { // 50% products
                    $numAttributes = rand(1,3);
                    for ($k = 0; $k < $numAttributes; $k++) {
                        $attrName = $attributeOptions[array_rand($attributeOptions)];
                        switch($attrName){
                            case 'Size': $value = $sizeOptions[array_rand($sizeOptions)]; break;
                            case 'Color': $value = $colorOptions[array_rand($colorOptions)]; break;
                            case 'Material': $value = $materialOptions[array_rand($materialOptions)]; break;
                        }
                        $productAttributes[] = [
                            'product_id' => $productId,
                            'attribute_name' => $attrName,
                            'attribute_value' => $value,
                            'price_modifier' => $faker->optional()->randomFloat(2, 0, 50),
                            'stock_quantity' => rand(0,50),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }
            }

            if(count($productAttributes) > 0){
                DB::table('product_attributes')->insert($productAttributes);
            }

            $productCounter += $batch;
            echo "Inserted $productCounter / $totalProducts products\n"; // optional progress
        }
    }
}
