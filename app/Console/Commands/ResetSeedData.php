<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Modules\Users\Models\UserCategory;
use App\Models\User;
use App\Modules\Merchants\Models\Merchant;
use App\Modules\Products\Models\Product;
use App\Modules\Orders\Models\Order;
use Illuminate\Support\Facades\DB;

class ResetSeedData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'suntop:reset-data {--force : Force deletion without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all seeded data for SunTop API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (!$this->option('force')) {
            if (!$this->confirm('هل أنت متأكد من حذف جميع البيانات؟ هذا الأمر لا يمكن التراجع عنه!')) {
                $this->info('تم إلغاء العملية.');
                return 0;
            }
        }

        $this->info('🗑️ بدء حذف البيانات...');

        try {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Delete orders first (due to foreign keys)
            $this->info('حذف الطلبات...');
            DB::table('order_trackings')->truncate();
            DB::table('order_items')->truncate();
            DB::table('orders')->truncate();

            // Delete other data
            $this->info('حذف المنتجات...');
            DB::table('products')->truncate();

            $this->info('حذف التجار...');
            DB::table('merchants')->truncate();

            $this->info('حذف المستخدمين...');
            DB::table('users')->truncate();

            $this->info('حذف فئات المستخدمين...');
            DB::table('user_categories')->truncate();

            // Reset personal access tokens
            $this->info('حذف رموز الوصول...');
            DB::table('personal_access_tokens')->truncate();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('✅ تم حذف جميع البيانات بنجاح!');
            $this->info('💡 يمكنك الآن تشغيل: php artisan db:seed');

            return 0;
        } catch (\Exception $e) {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            $this->error('❌ خطأ أثناء حذف البيانات: ' . $e->getMessage());
            return 1;
        }
    }
}
