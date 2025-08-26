<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Orders\Models\Order;
use App\Modules\Orders\Models\OrderItem;
use App\Modules\Products\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود طلبات مسبقاً
        if (Order::count() > 0) {
            $this->command->info('الطلبات موجودة مسبقاً - تحديث بيانات الكراتين والعلب...');
            $this->updateExistingOrders();
            return;
        }

        $this->command->info('بدء إنشاء بيانات الطلبات التجريبية...');

        // التأكد من وجود مستخدمين ومنتجات وتجار
        $users = User::where('role', 'customer')->get();
        $products = Product::all();
        $merchants = \App\Modules\Merchants\Models\Merchant::where('is_active', true)->get();

        if ($users->count() === 0) {
            $this->command->error('لا يوجد عملاء في قاعدة البيانات. يرجى تشغيل UserSeeder أولاً');
            return;
        }

        if ($products->count() === 0) {
            $this->command->error('لا يوجد منتجات في قاعدة البيانات. يرجى تشغيل ProductSeeder أولاً');
            return;
        }

        if ($merchants->count() === 0) {
            $this->command->error('لا يوجد تجار في قاعدة البيانات. يرجى تشغيل MerchantSeeder أولاً');
            return;
        }

        DB::transaction(function () use ($users, $products, $merchants) {
            $statuses = ['pending', 'confirmed', 'preparing', 'shipped', 'delivered', 'cancelled'];
            $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];
            $paymentMethods = ['cash', 'credit_card', 'bank_transfer', 'digital_wallet'];

            // إنشاء 50 طلب متنوع
            for ($i = 1; $i <= 50; $i++) {
                $user = $users->random();
                $status = $this->weightedRandomStatus();
                $paymentStatus = $this->getPaymentStatusByOrderStatus($status);
                $paymentMethod = $paymentMethods[array_rand($paymentMethods)];
                
                // تاريخ عشوائي خلال آخر 60 يوم
                $createdAt = Carbon::now()->subDays(rand(0, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                
                $order = Order::create([
                    'order_number' => Order::generateOrderNumber(),
                    'user_id' => $user->id,
                    'merchant_id' => $merchants->random()->id,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'payment_method' => $this->mapPaymentMethod($paymentMethod),
                    'subtotal' => 0, // سيتم حسابه لاحقاً
                    'tax' => 0,
                    'delivery_fee' => rand(0, 1) ? rand(20, 50) : 0, // أحياناً شحن مجاني
                    'discount' => rand(0, 1) ? rand(10, 100) : 0, // أحياناً خصم
                    'total_amount' => 0, // سيتم حسابه لاحقاً
                    'notes' => $this->getRandomOrderNotes(),
                    'delivery_address' => $this->getRandomShippingAddressArray(),
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt->addMinutes(rand(5, 180)),
                ]);

                // إضافة عناصر للطلب (1-5 منتجات)
                $orderProducts = $products->random(rand(1, 5));
                $subtotal = 0;

                foreach ($orderProducts as $product) {
                    $quantity = rand(1, 3);
                    $price = $product->price;
                    $total = $price * $quantity;
                    $subtotal += $total;

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'product_image' => $product->image_url,
                        'quantity' => $quantity,
                        'unit_price' => $price,
                        'total_price' => $total,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                }

                // حساب الضرائب والإجمالي
                $taxAmount = $subtotal * 0.14; // ضريبة 14%
                $totalAmount = $subtotal + $taxAmount + $order->delivery_fee - $order->discount;

                // تحديث الطلب بالمبالغ الصحيحة
                $order->update([
                    'subtotal' => $subtotal,
                    'tax' => $taxAmount,
                    'total_amount' => $totalAmount,
                ]);

                $this->command->info("تم إنشاء الطلب رقم {$order->id} للعميل {$user->name}");
            }

            // إنشاء طلبات إضافية للأيام الأخيرة (لإظهار النشاط الحديث)
            $this->createRecentOrders($users, $products, $merchants);
        });

        $this->command->info('تم إنشاء الطلبات بنجاح!');
    }

    /**
     * إنشاء طلبات حديثة لإظهار النشاط
     */
    private function createRecentOrders($users, $products, $merchants)
    {
        $recentStatuses = ['pending', 'confirmed', 'preparing'];
        
        // 10 طلبات في آخر 3 أيام
        for ($i = 1; $i <= 10; $i++) {
            $user = $users->random();
            $status = $recentStatuses[array_rand($recentStatuses)];
            $paymentStatus = $status === 'pending' ? 'pending' : 'paid';
            
            $createdAt = Carbon::now()->subDays(rand(0, 3))->subHours(rand(0, 12));
            
            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $user->id,
                'merchant_id' => $merchants->random()->id,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'payment_method' => $this->mapPaymentMethod(['cash', 'credit_card', 'digital_wallet'][array_rand(['cash', 'credit_card', 'digital_wallet'])]),
                'subtotal' => 0,
                'tax' => 0,
                'delivery_fee' => rand(0, 1) ? 30 : 0,
                'discount' => 0,
                'total_amount' => 0,
                'notes' => 'طلب حديث - ' . $this->getRandomOrderNotes(),
                'delivery_address' => $this->getRandomShippingAddressArray(),
                'created_at' => $createdAt,
                'updated_at' => $createdAt->addMinutes(rand(1, 30)),
            ]);

            // إضافة منتجات للطلب
            $orderProducts = $products->random(rand(1, 3));
            $subtotal = 0;

            foreach ($orderProducts as $product) {
                $quantity = rand(1, 2);
                $price = $product->price;
                $total = $price * $quantity;
                $subtotal += $total;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'product_image' => $product->image_url,
                    'quantity' => $quantity,
                    'unit_price' => $price,
                    'total_price' => $total,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
            }

            $taxAmount = $subtotal * 0.14;
            $totalAmount = $subtotal + $taxAmount + $order->delivery_fee;

            $order->update([
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'total_amount' => $totalAmount,
            ]);
        }
    }

    /**
     * اختيار حالة طلب بناءً على الوزن (أكثر واقعية)
     */
    private function weightedRandomStatus()
    {
        $statuses = [
            'delivered' => 40,  // 40% من الطلبات مكتملة
            'confirmed' => 20,  // 20% مؤكدة
            'preparing' => 15,  // 15% قيد التجهيز
            'shipped' => 10,    // 10% تم شحنها
            'pending' => 10,    // 10% معلقة
            'cancelled' => 5,   // 5% ملغية
        ];

        $rand = rand(1, 100);
        $sum = 0;

        foreach ($statuses as $status => $weight) {
            $sum += $weight;
            if ($rand <= $sum) {
                return $status;
            }
        }

        return 'pending';
    }

    /**
     * تحديد حالة الدفع بناءً على حالة الطلب
     */
    private function getPaymentStatusByOrderStatus($orderStatus)
    {
        switch ($orderStatus) {
            case 'pending':
                return 'pending';
            case 'cancelled':
                return rand(0, 1) ? 'failed' : 'refunded';
            case 'delivered':
            case 'shipped':
            case 'preparing':
            case 'confirmed':
                return 'paid';
            default:
                return 'pending';
        }
    }

    /**
     * ملاحظات طلب عشوائية
     */
    private function getRandomOrderNotes()
    {
        $notes = [
            'يرجى التأكد من التغليف الجيد',
            'طلب عاجل - يرجى التوصيل سريعاً',
            'لا يوجد ملاحظات خاصة',
            'يرجى الاتصال قبل التوصيل',
            'يفضل التوصيل في المساء',
            'طلب هدية - يرجى التغليف الخاص',
            'العنوان بجوار المسجد الكبير',
            'الدور الثالث بدون أسانسير',
            'يرجى عدم الطرق بقوة',
            'متوفر طوال اليوم',
        ];

        return $notes[array_rand($notes)];
    }

    /**
     * عناوين توصيل عشوائية (نص)
     */
    private function getRandomShippingAddress()
    {
        $addresses = [
            "شارع النيل، المعادي، القاهرة، شقة 12، الدور الرابع",
            "شارع التحرير، وسط البلد، الإسكندرية، فيلا رقم 25",
            "طريق الهرم، الجيزة، عمارة النور، شقة 8",
            "شارع الجمهورية، حي النزهة، القاهرة، الدور الثاني",
            "كورنيش النيل، الزمالك، القاهرة، شقة 15A",
            "شارع عرابي، رمسيس، القاهرة، عمارة السلام",
            "شارع السودان، المهندسين، الجيزة، شقة 22",
            "طريق الإسماعيلية، التجمع الخامس، القاهرة الجديدة",
            "شارع قصر العيني، وسط البلد، القاهرة، شقة 7",
            "شارع فيصل، الهرم، الجيزة، الدور الأول",
        ];

        return $addresses[array_rand($addresses)];
    }

    /**
     * عناوين توصيل عشوائية (مصفوفة للجدول)
     */
    private function getRandomShippingAddressArray()
    {
        $cities = ['القاهرة', 'الجيزة', 'الإسكندرية', 'القاهرة الجديدة'];
        $streets = ['شارع النيل', 'شارع التحرير', 'طريق الهرم', 'شارع الجمهورية', 'كورنيش النيل'];
        $areas = ['المعادي', 'وسط البلد', 'حي النزهة', 'الزمالك', 'المهندسين'];

        return [
            'street' => $streets[array_rand($streets)],
            'area' => $areas[array_rand($areas)],
            'city' => $cities[array_rand($cities)],
            'building_number' => rand(1, 100),
            'apartment_number' => rand(1, 50),
            'floor' => rand(1, 10),
            'landmark' => 'بجوار ' . ['المسجد', 'البنك', 'الصيدلية', 'المدرسة'][array_rand(['المسجد', 'البنك', 'الصيدلية', 'المدرسة'])]
        ];
    }

    /**
     * تحويل طرق الدفع للتوافق مع قاعدة البيانات
     */
    private function mapPaymentMethod($method)
    {
        $mapping = [
            'cash' => 'cash_on_delivery',
            'credit_card' => 'credit_card',
            'digital_wallet' => 'wallet',
            'bank_transfer' => 'credit_card'
        ];

        return $mapping[$method] ?? 'cash_on_delivery';
    }

    /**
     * Update existing order items with carton/package data
     */
    private function updateExistingOrders()
    {
        $orderItems = OrderItem::all();
        $sellingTypes = ['unit', 'package', 'carton'];
        
        foreach ($orderItems as $item) {
            // Random selling type for existing orders
            $sellingType = $sellingTypes[array_rand($sellingTypes)];
            
            // Calculate counts based on selling type
            $quantity = $item->quantity;
            $cartonsCount = 0;
            $packagesCount = 0;
            $unitsCount = 0;
            
            switch ($sellingType) {
                case 'carton':
                    $cartonsCount = max(1, floor($quantity / 24)); // Assume 24 units per carton
                    $unitsCount = $cartonsCount * 24;
                    break;
                case 'package':
                    $packagesCount = max(1, floor($quantity / 6)); // Assume 6 units per package
                    $unitsCount = $packagesCount * 6;
                    break;
                default: // unit
                    $unitsCount = $quantity;
                    break;
            }
            
            $item->update([
                'selling_type' => $sellingType,
                'cartons_count' => $cartonsCount,
                'packages_count' => $packagesCount,
                'units_count' => $unitsCount,
            ]);
        }

        $this->command->info("✅ تم تحديث {$orderItems->count()} عنصر طلب بنجاح!");
        $this->command->info('📦 الآن جميع عناصر الطلبات تحتوي على بيانات الكراتين والعلب');
    }
}
