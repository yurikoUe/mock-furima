<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brand;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $brands = [
            'Apple', 'Samsung', 'Sony', 'Nike', 'Adidas', 'Puma', 'Gucci', 'Louis Vuitton', 'Rolex', 'Ferrari'
        ];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand]);
        }
    }
}
