<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Products\Models\ProductCategory;

class ProductCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // إضافة فئات المنتجات الرئيسية
        $categories = [
            [
                'name' => '1L',
                'display_name' => '1 لتر',
                'description' => 'منتجات سن توب بحجم 1 لتر',
                'icon' => 'fa-bottle-water',
                'sort_order' => 1,
                'is_active' => true
            ],
            [
                'name' => '250ml',
                'display_name' => '250 مل',
                'description' => 'منتجات سن توب بحجم 250 مل',
                'icon' => 'fa-glass',
                'sort_order' => 2,
                'is_active' => true
            ]
        ];

        foreach ($categories as $category) {
            ProductCategory::updateOrCreate(
                ['name' => $category['name']],
                $category
            );
        }
    }
}
