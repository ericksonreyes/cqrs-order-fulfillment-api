<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class Products extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = [
            [
                "id" => "product-1",
                "name" => "Air Jordan 11",
                "price" => 1132,
                "stock" => 1000
            ],
            [
                "id" => "product-2",
                "name" => "Air Qatar 11",
                "price" => 879,
                "stock" => 1000
            ],
            [
                "id" => "product-3",
                "name" => "Air Saudi Arabia 2",
                "price" => 150,
                "stock" => 1000
            ],
            [
                "id" => "product-4",
                "name" => "Air Kuwait 12",
                "price" => 212,
                "stock" => 1000
            ]
        ];

        foreach ($products as $product) {
            DB::table('products')->insert([
                'id' => $product['id'],
                'name' => $product['name'],
                'stock' => $product['stock'],
                'price' => $product['price'],
            ]);
        }
    }
}
