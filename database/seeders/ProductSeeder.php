<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Products\Models\Product;
use App\Modules\Products\Models\ProductCategory;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        // Check if products already exist
        if (Product::where('name', 'سن توب برتقال طازج')->exists()) {
            $this->command->info('⚠️ المنتجات موجودة بالفعل، تحديث البيانات...');
            $this->updateExistingProducts();
            return;
        }

        $this->command->info('🚀 إنشاء منتجات SunTop التجريبية...');

        // Get categories
        $category1L = ProductCategory::where('name', '1 لتر')->first();
        $category250ml = ProductCategory::where('name', '250ml')->first();

        $products = [
            [
                'name' => 'سن توب برتقال طازج',
                'description' => 'عصير برتقال طازج فاخر - زجاجة 500 مل مع فيتامين سي طبيعي',
                'price' => 2.50,
                'is_available' => true,
                'back_color' => '#FF8C00', // برتقالي
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب مانجو استوائي',
                'description' => 'عصير مانجو استوائي لذيذ - زجاجة 500 مل بطعم المانجو الطبيعي',
                'price' => 2.75,
                'is_available' => true,
                'back_color' => '#FFD700', // ذهبي
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب تفاح أخضر',
                'description' => 'عصير تفاح أخضر منعش - زجاجة 1 لتر بطعم التفاح الأخضر الطبيعي',
                'price' => 4.25,
                'is_available' => true,
                'back_color' => '#32CD32', // أخضر
                'category_id' => $category1L ? $category1L->id : null,
            ],
            [
                'name' => 'سن توب عنب أحمر',
                'description' => 'عصير عنب أحمر فاخر - زجاجة 500 مل بطعم العنب الأحمر الطبيعي',
                'price' => 2.60,
                'is_available' => false, // غير متوفر للتيست
                'back_color' => '#8B0000', // أحمر داكن
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب ليمون نعناع',
                'description' => 'عصير ليمون بالنعناع منعش - زجاجة 500 مل بطعم الليمون والنعناع',
                'price' => 2.40,
                'is_available' => true,
                'back_color' => '#00FF7F', // أخضر ليموني
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            $this->command->info("✅ تم إنشاء المنتج: {$product->name}");
            
            if (!$productData['is_available']) {
                $this->command->warn("⚠️ منتج غير متوفر للتيست: {$product->name} (ID: {$product->id}) - غير متاح");
            }
        }

        $this->command->info('🎉 تم إنشاء ' . count($products) . ' منتج بنجاح!');
    }

    /**
     * Update existing products
     */
    private function updateExistingProducts()
    {
        $productUpdates = [
            'سن توب برتقال طازج' => [
                'price' => 2.50,
                'is_available' => true,
                'back_color' => '#FF8C00',
            ],
            'سن توب مانجو استوائي' => [
                'price' => 2.75,
                'is_available' => true,
                'back_color' => '#FFD700',
            ],
            'سن توب تفاح أخضر' => [
                'price' => 4.25,
                'is_available' => true,
                'back_color' => '#32CD32',
            ],
            'سن توب عنب أحمر' => [
                'price' => 2.60,
                'is_available' => false, // غير متوفر للتيست
                'back_color' => '#8B0000',
            ],
            'سن توب ليمون نعناع' => [
                'price' => 2.40,
                'is_available' => true,
                'back_color' => '#00FF7F',
            ],
        ];

        foreach ($productUpdates as $productName => $updateData) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $product->update($updateData);
                $this->command->info("✅ تم تحديث المنتج: {$productName}");
                if (!$updateData['is_available']) {
                    $this->command->warn("⚠️ منتج غير متوفر للتيست: {$productName} (ID: {$product->id}) - غير متاح");
                }
            }
        }

        $this->command->info('🔄 تم تحديث المنتجات الموجودة بنجاح!');
    }
}