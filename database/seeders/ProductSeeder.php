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
                'stock_quantity' => 150,
                'is_available' => true,
                'is_featured' => true,
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب مانجو استوائي',
                'description' => 'عصير مانجو استوائي لذيذ - زجاجة 500 مل بطعم المانجو الطبيعي',
                'price' => 2.75,
                'stock_quantity' => 125,
                'is_available' => true,
                'is_featured' => true,
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب تفاح أخضر',
                'description' => 'عصير تفاح أخضر منعش - زجاجة 1 لتر بطعم التفاح الأخضر الطبيعي',
                'price' => 4.25,
                'stock_quantity' => 180,
                'is_available' => true,
                'is_featured' => false,
                'category_id' => $category1L ? $category1L->id : null,
            ],
            [
                'name' => 'سن توب عنب أحمر',
                'description' => 'عصير عنب أحمر فاخر - زجاجة 500 مل بطعم العنب الأحمر الطبيعي',
                'price' => 2.60,
                'stock_quantity' => 0, // Out of stock for testing
                'is_available' => false,
                'is_featured' => false,
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
            [
                'name' => 'سن توب ليمون نعناع',
                'description' => 'عصير ليمون بالنعناع منعش - زجاجة 500 مل بطعم الليمون والنعناع',
                'price' => 2.40,
                'stock_quantity' => 95,
                'is_available' => true,
                'is_featured' => false,
                'category_id' => $category250ml ? $category250ml->id : null,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);
            $this->command->info("✅ تم إنشاء المنتج: {$product->name}");
            
            if ($productData['stock_quantity'] == 0) {
                $this->command->warn("⚠️ منتج غير متوفر للتيست: {$product->name} (ID: {$product->id}) - نفد المخزون");
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
                'stock_quantity' => 150,
                'price' => 2.50,
                'is_available' => true,
            ],
            'سن توب مانجو استوائي' => [
                'stock_quantity' => 125,
                'price' => 2.75,
                'is_available' => true,
            ],
            'سن توب تفاح أخضر' => [
                'stock_quantity' => 180,
                'price' => 4.25,
                'is_available' => true,
            ],
            'سن توب عنب أحمر' => [
                'stock_quantity' => 0, // Out of stock for testing
                'price' => 2.60,
                'is_available' => false,
            ],
            'سن توب ليمون نعناع' => [
                'stock_quantity' => 95,
                'price' => 2.40,
                'is_available' => true,
            ],
        ];

        foreach ($productUpdates as $productName => $updateData) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $product->update($updateData);
                $this->command->info("✅ تم تحديث المنتج: {$productName}");
                if ($updateData['stock_quantity'] == 0) {
                    $this->command->warn("⚠️ منتج غير متوفر للتيست: {$productName} (ID: {$product->id}) - نفد المخزون");
                }
            }
        }

        $this->command->info('🔄 تم تحديث المنتجات الموجودة بنجاح!');
    }
}