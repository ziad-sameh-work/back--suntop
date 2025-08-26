<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Merchants\Models\Merchant;
use Illuminate\Support\Facades\DB;

class MerchantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // التحقق من وجود تجار مسبقاً
        if (Merchant::count() > 0) {
            $this->command->info('المرشحات موجودة مسبقاً - تخطي إنشاء البيانات التجريبية');
            return;
        }

        $this->command->info('بدء إنشاء بيانات التجار التجريبية...');

        DB::transaction(function () {
        $merchants = [
            [
                    'name' => 'أحمد محمد علي',
                    'email' => 'ahmed.mohammed@suntop.com',
                    'phone' => '+20 100 123 4567',
                    'business_name' => 'متجر أحمد للإلكترونيات',
                    'business_type' => 'إلكترونيات ومعدات',
                    'address' => 'شارع الجمهورية، حي النزهة، الدور الأول',
                'city' => 'القاهرة',
                    'description' => 'متجر متخصص في بيع أحدث الأجهزة الإلكترونية والهواتف الذكية مع ضمان شامل وخدمة ما بعد البيع.',
                    'commission_rate' => 5.0,
                    'is_active' => true,
                'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(30),
                    'updated_at' => now()->subDays(5),
                ],
                [
                    'name' => 'فاطمة سعد الدين',
                    'email' => 'fatma.saad@suntop.com',
                    'phone' => '+20 101 234 5678',
                    'business_name' => 'بوتيك فاطمة للأزياء',
                    'business_type' => 'ملابس وأزياء نسائية',
                    'address' => 'شارع التحرير، وسط البلد، بجوار البنك الأهلي',
                    'city' => 'الإسكندرية',
                    'description' => 'بوتيك أنيق يضم أحدث صيحات الموضة النسائية من ملابس وإكسسوارات عالمية وصناعة محلية راقية.',
                    'commission_rate' => 7.5,
                'is_active' => true,
                    'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(25),
                    'updated_at' => now()->subDays(3),
                ],
                [
                    'name' => 'محمود حسن طاهر',
                    'email' => 'mahmoud.hassan@suntop.com',
                    'phone' => '+20 102 345 6789',
                    'business_name' => 'مطعم أبو محمود',
                    'business_type' => 'مطعم وخدمات طعام',
                    'address' => 'شارع الهرم، الجيزة، بجوار مسجد الرحمن',
                'city' => 'الجيزة',
                    'description' => 'مطعم شعبي أصيل يقدم أشهى الأكلات المصرية التقليدية مع خدمة التوصيل السريع.',
                    'commission_rate' => 10.0,
                'is_active' => true,
                    'is_open' => false, // مغلق مؤقتاً
                    'logo' => null,
                    'created_at' => now()->subDays(20),
                    'updated_at' => now()->subDays(1),
                ],
                [
                    'name' => 'علي عبد الرحمن',
                    'email' => 'ali.abdelrahman@suntop.com',
                    'phone' => '+20 103 456 7890',
                    'business_name' => 'صيدلية النور',
                    'business_type' => 'صيدلية ومستلزمات طبية',
                    'address' => 'شارع الطيران، مدينة نصر، أمام المستشفى العام',
                'city' => 'القاهرة',
                    'description' => 'صيدلية حديثة تقدم جميع الأدوية والمستلزمات الطبية مع استشارة طبية مجانية.',
                    'commission_rate' => 3.0,
                    'is_active' => true,
                'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(18),
                    'updated_at' => now()->subDays(2),
                ],
                [
                    'name' => 'نورا إبراهيم محمد',
                    'email' => 'nora.ibrahim@suntop.com',
                    'phone' => '+20 104 567 8901',
                    'business_name' => 'معرض نورا للأثاث',
                    'business_type' => 'أثاث ومفروشات',
                    'address' => 'طريق الإسماعيلية الصحراوي، التجمع الخامس',
                    'city' => 'القاهرة الجديدة',
                    'description' => 'معرض أثاث عصري يضم أحدث تصميمات المفروشات المنزلية والمكتبية مع خدمة التركيب.',
                    'commission_rate' => 8.0,
                    'is_active' => false, // غير نشط
                    'is_open' => false,
                    'logo' => null,
                    'created_at' => now()->subDays(15),
                    'updated_at' => now()->subDays(4),
                ],
                [
                    'name' => 'خالد أشرف سليم',
                    'email' => 'khalid.ashraf@suntop.com',
                    'phone' => '+20 105 678 9012',
                    'business_name' => 'ورشة خالد للسيارات',
                    'business_type' => 'قطع غيار ومعدات سيارات',
                    'address' => 'شارع السودان، المهندسين، بجوار كوبري الجلاء',
                'city' => 'الجيزة',
                    'description' => 'ورشة متخصصة في صيانة السيارات وبيع قطع الغيار الأصلية مع ضمان الجودة.',
                    'commission_rate' => 6.0,
                    'is_active' => true,
                'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(12),
                    'updated_at' => now()->subDays(1),
                ],
                [
                    'name' => 'سارة محمد أحمد',
                    'email' => 'sara.mohamed@suntop.com',
                    'phone' => '+20 106 789 0123',
                    'business_name' => 'مخبز سارة الطازج',
                    'business_type' => 'مخبز وحلويات',
                    'address' => 'شارع عرابي، رمسيس، بجوار محطة الأتوبيس',
                    'city' => 'القاهرة',
                    'description' => 'مخبز يقدم أطيب المخبوزات الطازجة والحلويات الشرقية اليومية.',
                    'commission_rate' => 4.5,
                'is_active' => true,
                    'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(10),
                    'updated_at' => now()->subDays(3),
                ],
                [
                    'name' => 'عمرو حسام الدين',
                    'email' => 'amr.hussam@suntop.com',
                    'phone' => '+20 107 890 1234',
                    'business_name' => 'مكتبة عمرو للكتب',
                    'business_type' => 'كتب وقرطاسية',
                    'address' => 'شارع قصر العيني، وسط البلد، بجوار الجامعة الأمريكية',
                    'city' => 'القاهرة',
                    'description' => 'مكتبة شاملة تضم مختلف أنواع الكتب العلمية والأدبية والقرطاسية المتنوعة.',
                    'commission_rate' => 5.5,
                'is_active' => true,
                    'is_open' => false, // مغلق للجرد
                    'logo' => null,
                    'created_at' => now()->subDays(8),
                    'updated_at' => now()->subDays(2),
                ],
                [
                    'name' => 'مريم سامي فؤاد',
                    'email' => 'mariam.samy@suntop.com',
                    'phone' => '+20 108 901 2345',
                    'business_name' => 'مطبخ مريم للوجبات',
                    'business_type' => 'خدمات طعام وتوصيل',
                    'address' => 'شارع النيل، المعادي، فيلا رقم 15',
                'city' => 'القاهرة',
                    'description' => 'مطبخ منزلي متخصص في تحضير الوجبات الصحية والشعبية مع خدمة التوصيل.',
                    'commission_rate' => 12.0,
                'is_active' => true,
                    'is_open' => true,
                    'logo' => null,
                    'created_at' => now()->subDays(5),
                    'updated_at' => now()->subDay(),
                ],
                [
                    'name' => 'طارق عبد الله منصور',
                    'email' => 'tarek.abdullah@suntop.com',
                    'phone' => '+20 109 012 3456',
                    'business_name' => 'معرض طارق للهدايا',
                    'business_type' => 'هدايا وتحف',
                    'address' => 'شارع فيصل، الهرم، بجوار بنك مصر',
                    'city' => 'الجيزة',
                    'description' => 'معرض متنوع للهدايا والتحف والإكسسوارات المناسبة لجميع المناسبات.',
                    'commission_rate' => 9.0,
                    'is_active' => false, // تحت المراجعة
                    'is_open' => false,
                    'logo' => null,
                    'created_at' => now()->subDays(3),
                'updated_at' => now(),
            ],
        ];

        foreach ($merchants as $merchantData) {
            Merchant::create($merchantData);
                $this->command->info('تم إنشاء التاجر: ' . $merchantData['name']);
            }
        });

        $this->command->info('تم إنشاء ' . count($merchants ?? []) . ' تاجر بنجاح!');
    }
}