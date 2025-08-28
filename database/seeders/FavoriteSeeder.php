<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Favorite;
use App\Models\User;
use App\Modules\Products\Models\Product;

class FavoriteSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('❤️ إنشاء المفضلة التجريبية...');

        // Get customers and products
        $customers = User::where('role', 'customer')->get();
        $products = Product::where('is_available', true)->get();

        if ($customers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️ لا توجد عملاء أو منتجات لإنشاء مفضلة لهم');
            return;
        }

        $favoritesData = [];
        $createdCount = 0;

        foreach ($customers as $customer) {
            // Each customer will have 2-6 favorite products
            $favoriteCount = rand(2, 6);
            $customerProducts = $products->random($favoriteCount);

            foreach ($customerProducts as $product) {
                // Avoid duplicates
                $exists = collect($favoritesData)->contains(function($item) use ($customer, $product) {
                    return $item['user_id'] == $customer->id && $item['product_id'] == $product->id;
                });

                if (!$exists) {
                    $addedAt = now()->subDays(rand(1, 60))->subHours(rand(0, 23))->subMinutes(rand(0, 59));
                    
                    $favoritesData[] = [
                        'user_id' => $customer->id,
                        'product_id' => $product->id,
                        'added_at' => $addedAt,
                        'created_at' => $addedAt,
                        'updated_at' => $addedAt,
                    ];
                    $createdCount++;
                }
            }
        }

        // Insert favorites in batches for better performance
        $chunks = array_chunk($favoritesData, 100);
        foreach ($chunks as $chunk) {
            Favorite::insert($chunk);
        }

        $this->command->info("✅ تم إنشاء {$createdCount} مفضلة تجريبية بنجاح");
        
        // Show statistics
        $userFavorites = [];
        foreach ($customers as $customer) {
            $count = Favorite::where('user_id', $customer->id)->count();
            if ($count > 0) {
                $userFavorites[] = "{$customer->name}: {$count} منتج";
            }
        }

        $this->command->info("📊 إحصائيات المفضلة:");
        foreach (array_slice($userFavorites, 0, 5) as $stat) {
            $this->command->info("   - {$stat}");
        }

        // Show popular products
        $popularProducts = Favorite::select('product_id', \DB::raw('COUNT(*) as favorites_count'))
                                  ->groupBy('product_id')
                                  ->orderBy('favorites_count', 'desc')
                                  ->take(3)
                                  ->with('product:id,name')
                                  ->get();

        $this->command->info("🌟 المنتجات الأكثر إضافة للمفضلة:");
        foreach ($popularProducts as $item) {
            $this->command->info("   - {$item->product->name}: {$item->favorites_count} مستخدم");
        }
    }
}
