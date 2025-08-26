<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Products\Models\Product;

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
            $this->command->info('⚠️ المنتجات موجودة بالفعل، تحديث بيانات الكراتين والعلب...');
            $this->updateExistingProducts();
            return;
        }

        $products = [
            [
                'name' => 'سن توب برتقال طازج',
                'description' => 'عصير برتقال طازج فاخر - زجاجة 500 مل مع فيتامين سي طبيعي',
                'image_url' => 'products/suntop-orange-500ml.jpg',
                'gallery' => [
                    'products/suntop-orange-500ml-1.jpg',
                    'products/suntop-orange-500ml-2.jpg'
                ],
                'price' => 2.50,
                'original_price' => 3.00,
                // Carton & Package settings
                'carton_size' => 24,
                'carton_price' => 55.00, // خصم للكرتون الكامل
                'is_full_carton' => false,
                'package_size' => 6,
                'package_price' => 14.50, // خصم للحزمة
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 15,
                'package_loyalty_points' => 4,
                'unit_loyalty_points' => 1,
                'currency' => 'EGP',
                'category' => 'Citrus',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 150,
                'rating' => 4.9,
                'review_count' => 89,
                'tags' => ['Popular', 'Fresh', 'Vitamin C', 'Natural'],
                'ingredients' => ['برتقال طبيعي 100%', 'ماء مفلتر', 'فيتامين سي', 'حامض الستريك'],
                'nutrition_facts' => [
                    'calories' => 120,
                    'sugar' => '25g',
                    'vitamin_c' => '100%',
                    'sodium' => '10mg',
                    'carbohydrates' => '30g'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 12 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890001',
                'is_featured' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب مانجو استوائي',
                'description' => 'عصير مانجو استوائي لذيذ - زجاجة 500 مل بطعم المانجو الطبيعي',
                'image_url' => 'products/suntop-mango-500ml.jpg',
                'gallery' => [
                    'products/suntop-mango-500ml-1.jpg',
                    'products/suntop-mango-500ml-2.jpg'
                ],
                'price' => 2.75,
                'original_price' => 3.25,
                // Carton & Package settings
                'carton_size' => 24,
                'carton_price' => 60.00,
                'is_full_carton' => false,
                'package_size' => 6,
                'package_price' => 15.50,
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 18,
                'package_loyalty_points' => 5,
                'unit_loyalty_points' => 1,
                'currency' => 'EGP',
                'category' => 'Tropical',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 120,
                'rating' => 4.8,
                'review_count' => 67,
                'tags' => ['Popular', 'Tropical', 'Sweet', 'Natural'],
                'ingredients' => ['مانجو طبيعي', 'ماء مفلتر', 'سكر طبيعي', 'حامض الستريك'],
                'nutrition_facts' => [
                    'calories' => 140,
                    'sugar' => '32g',
                    'vitamin_a' => '80%',
                    'sodium' => '5mg',
                    'carbohydrates' => '35g'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 12 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890002',
                'is_featured' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب تفاح أخضر',
                'description' => 'عصير تفاح أخضر منعش - زجاجة 1 لتر للعائلة الكبيرة',
                'image_url' => 'products/suntop-apple-1l.jpg',
                'gallery' => [
                    'products/suntop-apple-1l-1.jpg',
                    'products/suntop-apple-1l-2.jpg'
                ],
                'price' => 4.50,
                'original_price' => 5.00,
                // Carton & Package settings
                'carton_size' => 12, // أقل لأنها زجاجات أكبر
                'carton_price' => 50.00,
                'is_full_carton' => false,
                'package_size' => 4,
                'package_price' => 17.00,
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 25,
                'package_loyalty_points' => 8,
                'unit_loyalty_points' => 2,
                'currency' => 'EGP',
                'category' => 'Classic',
                'size' => '1L',
                'volume_category' => '1L',
                'is_available' => true,
                'stock_quantity' => 80,
                'rating' => 4.7,
                'review_count' => 45,
                'tags' => ['Family Size', 'Fresh', 'Green Apple', 'Healthy'],
                'ingredients' => ['تفاح أخضر طبيعي', 'ماء مفلتر', 'فيتامين سي', 'حامض الماليك'],
                'nutrition_facts' => [
                    'calories' => 200,
                    'sugar' => '45g',
                    'vitamin_c' => '90%',
                    'sodium' => '15mg',
                    'fiber' => '2g'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 15 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890003',
                'is_featured' => false,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب كوكتيل فواكه',
                'description' => 'مزيج منعش من الفواكه المختارة - زجاجة 500 مل بطعم استثنائي',
                'image_url' => 'products/suntop-cocktail-500ml.jpg',
                'gallery' => [
                    'products/suntop-cocktail-500ml-1.jpg'
                ],
                'price' => 2.99,
                'original_price' => 3.49,
                'currency' => 'EGP',
                'category' => 'Mixed',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 95,
                'rating' => 4.6,
                'review_count' => 38,
                'tags' => ['Mixed Fruits', 'Refreshing', 'Colorful', 'Kids Favorite'],
                'ingredients' => ['مزيج فواكه طبيعية', 'ماء مفلتر', 'سكر طبيعي', 'حامض الستريك'],
                'nutrition_facts' => [
                    'calories' => 135,
                    'sugar' => '30g',
                    'vitamin_c' => '85%',
                    'sodium' => '8mg',
                    'antioxidants' => 'عالي'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 12 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890004',
                'is_featured' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب ليمون بالنعناع',
                'description' => 'مشروب ليمون منعش بالنعناع الطبيعي - مثالي للصيف',
                'image_url' => 'products/suntop-lemon-mint-500ml.jpg',
                'gallery' => [],
                'price' => 2.25,
                'original_price' => 2.75,
                'currency' => 'EGP',
                'category' => 'Summer',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 110,
                'rating' => 4.5,
                'review_count' => 52,
                'tags' => ['Summer', 'Refreshing', 'Mint', 'Citrus'],
                'ingredients' => ['ليمون طبيعي', 'نعناع طبيعي', 'ماء مفلتر', 'سكر قليل'],
                'nutrition_facts' => [
                    'calories' => 80,
                    'sugar' => '18g',
                    'vitamin_c' => '120%',
                    'sodium' => '5mg',
                    'natural_mint' => 'نعم'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 10 شهور من تاريخ الإنتاج',
                'barcode' => '1234567890005',
                'is_featured' => false,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب فراولة',
                'description' => 'عصير فراولة طبيعي حلو المذاق - مفضل الأطفال',
                'image_url' => 'products/suntop-strawberry-500ml.jpg',
                'gallery' => [
                    'products/suntop-strawberry-500ml-1.jpg'
                ],
                'price' => 2.80,
                'original_price' => 3.30,
                'currency' => 'EGP',
                'category' => 'Berry',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 75,
                'rating' => 4.8,
                'review_count' => 71,
                'tags' => ['Berry', 'Sweet', 'Kids Favorite', 'Natural'],
                'ingredients' => ['فراولة طبيعية', 'ماء مفلتر', 'سكر طبيعي', 'حامض الستريك'],
                'nutrition_facts' => [
                    'calories' => 125,
                    'sugar' => '28g',
                    'vitamin_c' => '95%',
                    'folate' => '12%',
                    'antioxidants' => 'عالي'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 12 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890006',
                'is_featured' => false,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب أناناس استوائي',
                'description' => 'طعم الأناناس الاستوائي الأصيل - منعش ولذيذ',
                'image_url' => 'products/suntop-pineapple-500ml.jpg',
                'gallery' => [],
                'price' => 2.90,
                'original_price' => 3.40,
                'currency' => 'EGP',
                'category' => 'Exotic',
                'size' => '500ml',
                'volume_category' => '250ml',
                'is_available' => true,
                'stock_quantity' => 60,
                'rating' => 4.4,
                'review_count' => 29,
                'tags' => ['Exotic', 'Tropical', 'Sweet', 'Pineapple'],
                'ingredients' => ['أناناس طبيعي', 'ماء مفلتر', 'سكر طبيعي', 'حامض الستريك'],
                'nutrition_facts' => [
                    'calories' => 150,
                    'sugar' => '35g',
                    'vitamin_c' => '100%',
                    'manganese' => '25%',
                    'bromelain' => 'طبيعي'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 12 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890007',
                'is_featured' => false,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'سن توب عنب أحمر',
                'description' => 'عصير عنب أحمر طبيعي غني بمضادات الأكسدة',
                'image_url' => 'products/suntop-grape-1l.jpg',
                'gallery' => [
                    'products/suntop-grape-1l-1.jpg',
                    'products/suntop-grape-1l-2.jpg'
                ],
                'price' => 4.75,
                'original_price' => 5.25,
                'currency' => 'EGP',
                'category' => 'Classic',
                'size' => '1L',
                'volume_category' => '1L',
                'is_available' => false, // Out of stock for testing
                'stock_quantity' => 0,
                'rating' => 4.3,
                'review_count' => 18,
                'tags' => ['Classic', 'Antioxidants', 'Family Size', 'Red Grape'],
                'ingredients' => ['عنب أحمر طبيعي', 'ماء مفلتر', 'حامض الطرطريك'],
                'nutrition_facts' => [
                    'calories' => 180,
                    'sugar' => '42g',
                    'antioxidants' => 'عالي جداً',
                    'resveratrol' => 'طبيعي',
                    'potassium' => '15%'
                ],
                'storage_instructions' => 'يُحفظ في مكان بارد وجاف، يُحفظ في الثلاجة بعد الفتح',
                'expiry_info' => 'صالح لمدة 15 شهر من تاريخ الإنتاج',
                'barcode' => '1234567890008',
                'is_featured' => false,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($products as $productData) {
            Product::create($productData);
        }

        $this->command->info('✅ تم إنشاء ' . count($products) . ' منتج بنجاح!');
        $this->command->info('📦 المنتجات المتاحة للتيست:');
        $this->command->info('');
        $this->command->info('🌟 المنتجات المميزة:');
        $this->command->info('1. سن توب برتقال طازج (ID: 1) - 2.50 EGP');
        $this->command->info('2. سن توب مانجو استوائي (ID: 2) - 2.75 EGP');
        $this->command->info('3. سن توب كوكتيل فواكه (ID: 4) - 2.99 EGP');
        $this->command->info('');
        $this->command->info('📋 فئات المنتجات:');
        $this->command->info('- Citrus: برتقال');
        $this->command->info('- Tropical: مانجو');
        $this->command->info('- Classic: تفاح، عنب');
        $this->command->info('- Mixed: كوكتيل فواكه');
        $this->command->info('- Summer: ليمون بالنعناع');
        $this->command->info('- Berry: فراولة');
        $this->command->info('- Exotic: أناناس');
        $this->command->info('');
        $this->command->info('⚠️ منتج غير متوفر للتيست:');
        $this->command->info('- سن توب عنب أحمر (ID: 8) - نفد المخزون');
    }

    /**
     * Update existing products with carton/package data
     */
    private function updateExistingProducts()
    {
        $productUpdates = [
            'سن توب برتقال طازج' => [
                'carton_size' => 24,
                'carton_price' => 55.00,
                'is_full_carton' => false,
                'package_size' => 6,
                'package_price' => 14.50,
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 15,
                'package_loyalty_points' => 4,
                'unit_loyalty_points' => 1,
            ],
            'سن توب مانجو استوائي' => [
                'carton_size' => 24,
                'carton_price' => 60.00,
                'is_full_carton' => false,
                'package_size' => 6,
                'package_price' => 15.50,
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 18,
                'package_loyalty_points' => 5,
                'unit_loyalty_points' => 1,
            ],
            'سن توب تفاح أخضر' => [
                'carton_size' => 12,
                'carton_price' => 50.00,
                'is_full_carton' => false,
                'package_size' => 4,
                'package_price' => 17.00,
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 25,
                'package_loyalty_points' => 8,
                'unit_loyalty_points' => 2,
            ],
        ];

        foreach ($productUpdates as $productName => $updateData) {
            $product = Product::where('name', $productName)->first();
            if ($product) {
                $product->update($updateData);
                $this->command->info("✅ تم تحديث المنتج: {$productName}");
                $this->command->info("   📦 كرتون: {$updateData['carton_size']} قطعة بـ {$updateData['carton_price']} ج.م");
                $this->command->info("   📦 علبة: {$updateData['package_size']} قطع بـ {$updateData['package_price']} ج.م");
            }
        }

        // Add default carton/package data to all other products
        $productsWithoutCartons = Product::whereNull('carton_size')->get();
        foreach ($productsWithoutCartons as $product) {
            $product->update([
                'carton_size' => 24, // Default carton size
                'carton_price' => $product->price * 22, // Small discount for carton
                'is_full_carton' => false,
                'package_size' => 6, // Default package size
                'package_price' => $product->price * 5.5, // Small discount for package
                'is_full_package' => false,
                'allow_individual_units' => true,
                'carton_loyalty_points' => 10,
                'package_loyalty_points' => 3,
                'unit_loyalty_points' => 1,
            ]);
            $this->command->info("✅ تم إضافة بيانات الكراتين والعلب لـ: {$product->name}");
        }

        $this->command->info('🔄 تم تحديث جميع المنتجات بنجاح!');
    }
}
