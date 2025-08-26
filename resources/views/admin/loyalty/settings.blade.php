@extends('layouts.admin')

@section('title', 'إعدادات نظام الولاء')

@section('content')
<style>
    :root {
        --suntop-orange: #ff6b35;
        --suntop-blue: #4a90e2;
        --white: #ffffff;
        --black: #333333;
        --gray-50: #f9fafb;
        --gray-100: #f3f4f6;
        --gray-200: #e5e7eb;
        --gray-300: #d1d5db;
        --gray-400: #9ca3af;
        --gray-500: #6b7280;
        --gray-600: #4b5563;
        --gray-700: #374151;
        --gray-800: #1f2937;
        --gray-900: #111827;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --info: #3b82f6;
    }

    body {
        background: var(--gray-50);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--gray-800);
        direction: rtl;
    }

    .page-header {
        background: linear-gradient(135deg, var(--suntop-orange), #ff8c42);
        color: var(--white);
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(255, 107, 53, 0.2);
    }

    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .page-subtitle {
        opacity: 0.9;
        font-size: 16px;
        margin: 0;
    }

    .settings-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 30px;
    }

    .settings-form {
        background: var(--white);
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        overflow: hidden;
    }

    .form-header {
        padding: 25px 30px;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-50);
    }

    .form-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-content {
        padding: 30px;
    }

    .form-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--gray-200);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-icon {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 14px;
    }

    .section-icon.orange { background: var(--suntop-orange); }
    .section-icon.info { background: var(--info); }
    .section-icon.success { background: var(--success); }
    .section-icon.warning { background: var(--warning); }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-control {
        padding: 12px 15px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
        font-family: inherit;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .form-help {
        font-size: 12px;
        color: var(--gray-500);
        margin-top: 4px;
    }

    .input-group {
        position: relative;
    }

    .input-addon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-500);
        font-size: 12px;
        font-weight: 600;
    }

    .input-group .form-control {
        padding-left: 40px;
    }

    .form-actions {
        padding: 25px 30px;
        border-top: 1px solid var(--gray-200);
        background: var(--gray-50);
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-primary {
        background: var(--suntop-orange);
        color: var(--white);
        border: 2px solid var(--suntop-orange);
    }

    .btn-primary:hover {
        background: #e55a2b;
        border-color: #e55a2b;
        color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
        color: var(--gray-800);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .info-sidebar {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }

    .info-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
    }

    .info-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .info-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 16px;
    }

    .info-card-icon.orange { background: var(--suntop-orange); }
    .info-card-icon.info { background: var(--info); }
    .info-card-icon.success { background: var(--success); }
    .info-card-icon.warning { background: var(--warning); }

    .info-card-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        padding: 8px 0;
        display: flex;
        align-items: center;
        gap: 8px;
        color: var(--gray-700);
        font-size: 14px;
    }

    .info-list li i {
        color: var(--suntop-orange);
        width: 16px;
    }

    .preview-section {
        background: var(--gray-50);
        padding: 20px;
        border-radius: 8px;
        border: 1px solid var(--gray-200);
    }

    .preview-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0 0 15px 0;
    }

    .preview-example {
        background: var(--white);
        padding: 15px;
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        font-size: 13px;
        color: var(--gray-700);
        line-height: 1.5;
    }

    .formula {
        background: var(--info);
        color: var(--white);
        padding: 4px 8px;
        border-radius: 4px;
        font-family: monospace;
        font-size: 12px;
        font-weight: 600;
    }

    @media (max-width: 1024px) {
        .settings-container {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
        
        .form-actions {
            flex-direction: column;
        }
        
        .btn {
            justify-content: center;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-cog"></i>
        إعدادات نظام الولاء
    </h1>
    <p class="page-subtitle">تخصيص معاملات وقواعد نظام نقاط الولاء</p>
</div>

<div class="settings-container">
    <!-- Settings Form -->
    <div class="settings-form">
        <div class="form-header">
            <h2 class="form-title">
                <i class="fas fa-star"></i>
                إعدادات النقاط
            </h2>
        </div>

        <form action="{{ route('admin.loyalty.settings.update') }}" method="POST">
            @csrf
            
            <div class="form-content">
                <!-- Earning Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon orange">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        إعدادات كسب النقاط
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                معدل كسب النقاط <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="earn_rate" class="form-control" 
                                       min="1" max="100" value="{{ $settings['earn_rate'] }}" required>
                                <div class="input-addon">ج.م</div>
                            </div>
                            <div class="form-help">كم جنيه مصري يحتاج العميل لإنفاقه لكسب نقطة واحدة</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">مكافأة التسجيل</label>
                            <div class="input-group">
                                <input type="number" name="signup_bonus" class="form-control" 
                                       min="0" max="1000" value="{{ $settings['signup_bonus'] }}">
                                <div class="input-addon">نقطة</div>
                            </div>
                            <div class="form-help">عدد النقاط التي يحصل عليها العميل عند التسجيل</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">مكافأة التقييم</label>
                            <div class="input-group">
                                <input type="number" name="review_bonus" class="form-control" 
                                       min="0" max="100" value="{{ $settings['review_bonus'] }}">
                                <div class="input-addon">نقطة</div>
                            </div>
                            <div class="form-help">نقاط مقابل تقييم المنتجات</div>
                        </div>
                    </div>
                </div>

                <!-- Redemption Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon info">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        إعدادات استرداد النقاط
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                معدل استرداد النقاط <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="redeem_rate" class="form-control" 
                                       min="1" max="1000" value="{{ $settings['redeem_rate'] }}" required>
                                <div class="input-addon">نقطة</div>
                            </div>
                            <div class="form-help">كم نقطة تساوي 1 جنيه مصري عند الاسترداد</div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                الحد الأدنى للاسترداد <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="min_redeem" class="form-control" 
                                       min="1" max="1000" value="{{ $settings['min_redeem'] }}" required>
                                <div class="input-addon">نقطة</div>
                            </div>
                            <div class="form-help">أقل عدد نقاط يمكن استردادها في مرة واحدة</div>
                        </div>
                    </div>
                </div>

                <!-- Expiry Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon warning">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        إعدادات انتهاء الصلاحية
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label">
                                مدة انتهاء النقاط <span class="required">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="expiry_months" class="form-control" 
                                       min="1" max="60" value="{{ $settings['expiry_months'] }}" required>
                                <div class="input-addon">شهر</div>
                            </div>
                            <div class="form-help">عدد الشهور قبل انتهاء صلاحية النقاط</div>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <div class="section-icon success">
                            <i class="fas fa-eye"></i>
                        </div>
                        معاينة الإعدادات
                    </h3>
                    
                    <div class="preview-section">
                        <div class="preview-title">أمثلة على النظام الحالي:</div>
                        <div class="preview-example">
                            <strong>كسب النقاط:</strong><br>
                            • عميل ينفق 100 ج.م = <span class="formula" id="earnExample">10 نقاط</span><br>
                            • عميل جديد يحصل على <span class="formula" id="signupExample">50 نقطة</span> ترحيبية<br>
                            • تقييم منتج = <span class="formula" id="reviewExample">10 نقاط</span><br><br>
                            
                            <strong>استرداد النقاط:</strong><br>
                            • <span class="formula" id="redeemExample">100 نقطة</span> = 1 ج.م خصم<br>
                            • الحد الأدنى للاسترداد: <span class="formula" id="minRedeemExample">100 نقطة</span><br><br>
                            
                            <strong>انتهاء الصلاحية:</strong><br>
                            • النقاط تنتهي بعد <span class="formula" id="expiryExample">12 شهر</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    حفظ الإعدادات
                </button>
                <a href="{{ route('admin.loyalty.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    إلغاء
                </a>
            </div>
        </form>
    </div>

    <!-- Information Sidebar -->
    <div class="info-sidebar">
        <!-- How it Works -->
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon orange">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="info-card-title">كيف يعمل النظام؟</h3>
            </div>
            <ul class="info-list">
                <li>
                    <i class="fas fa-shopping-cart"></i>
                    العملاء يكسبون نقاط مع كل عملية شراء
                </li>
                <li>
                    <i class="fas fa-gift"></i>
                    يمكن استرداد النقاط كخصم على الطلبات
                </li>
                <li>
                    <i class="fas fa-clock"></i>
                    النقاط لها تاريخ انتهاء صلاحية محدد
                </li>
                <li>
                    <i class="fas fa-star"></i>
                    مكافآت إضافية للأنشطة الخاصة
                </li>
            </ul>
        </div>

        <!-- Best Practices -->
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon success">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <h3 class="info-card-title">أفضل الممارسات</h3>
            </div>
            <ul class="info-list">
                <li>
                    <i class="fas fa-balance-scale"></i>
                    اجعل معدل كسب النقاط معقولاً (5-20 ج.م/نقطة)
                </li>
                <li>
                    <i class="fas fa-percentage"></i>
                    معدل الاسترداد يجب أن يكون مربحاً (50-200 نقطة/ج.م)
                </li>
                <li>
                    <i class="fas fa-calendar"></i>
                    انتهاء الصلاحية يشجع الاستخدام (6-18 شهر)
                </li>
                <li>
                    <i class="fas fa-users"></i>
                    مكافآت التسجيل تجذب عملاء جدد
                </li>
            </ul>
        </div>

        <!-- Current Statistics -->
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon info">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="info-card-title">إحصائيات حالية</h3>
            </div>
            <div style="space-y: 10px;">
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600); font-size: 14px;">إجمالي النقاط النشطة</span>
                    <span style="font-weight: 600; color: var(--suntop-orange);">25,420</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600); font-size: 14px;">المستخدمين النشطين</span>
                    <span style="font-weight: 600; color: var(--success);">342</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--gray-200);">
                    <span style="color: var(--gray-600); font-size: 14px;">معدل الاسترداد</span>
                    <span style="font-weight: 600; color: var(--info);">23.5%</span>
                </div>
                <div style="display: flex; justify-content: space-between; padding: 8px 0;">
                    <span style="color: var(--gray-600); font-size: 14px;">النقاط المنتهية</span>
                    <span style="font-weight: 600; color: var(--warning);">1,250</span>
                </div>
            </div>
        </div>

        <!-- Tips -->
        <div class="info-card">
            <div class="info-card-header">
                <div class="info-card-icon warning">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="info-card-title">تنبيهات مهمة</h3>
            </div>
            <ul class="info-list">
                <li>
                    <i class="fas fa-save"></i>
                    احفظ الإعدادات قبل المغادرة
                </li>
                <li>
                    <i class="fas fa-users"></i>
                    التغييرات تؤثر على جميع المستخدمين
                </li>
                <li>
                    <i class="fas fa-history"></i>
                    النقاط الموجودة لن تتأثر بالتغييرات
                </li>
                <li>
                    <i class="fas fa-test-tube"></i>
                    اختبر الإعدادات قبل التطبيق
                </li>
            </ul>
        </div>
    </div>
</div>

<script>
// Update preview examples when inputs change
function updatePreview() {
    const earnRate = document.querySelector('input[name="earn_rate"]').value || 10;
    const signupBonus = document.querySelector('input[name="signup_bonus"]').value || 50;
    const reviewBonus = document.querySelector('input[name="review_bonus"]').value || 10;
    const redeemRate = document.querySelector('input[name="redeem_rate"]').value || 100;
    const minRedeem = document.querySelector('input[name="min_redeem"]').value || 100;
    const expiryMonths = document.querySelector('input[name="expiry_months"]').value || 12;
    
    // Update examples
    document.getElementById('earnExample').textContent = `${Math.floor(100 / earnRate)} نقاط`;
    document.getElementById('signupExample').textContent = `${signupBonus} نقطة`;
    document.getElementById('reviewExample').textContent = `${reviewBonus} نقاط`;
    document.getElementById('redeemExample').textContent = `${redeemRate} نقطة`;
    document.getElementById('minRedeemExample').textContent = `${minRedeem} نقطة`;
    document.getElementById('expiryExample').textContent = `${expiryMonths} شهر`;
}

// Add event listeners to all input fields
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
    });
    
    // Initial preview update
    updatePreview();
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const earnRate = parseInt(document.querySelector('input[name="earn_rate"]').value);
    const redeemRate = parseInt(document.querySelector('input[name="redeem_rate"]').value);
    
    if (earnRate <= 0 || redeemRate <= 0) {
        e.preventDefault();
        alert('يرجى إدخال قيم صحيحة للمعدلات');
        return false;
    }
    
    // Calculate profitability ratio
    const profitRatio = redeemRate / earnRate;
    if (profitRatio < 5) {
        if (!confirm('تحذير: معدل الربحية منخفض جداً. هل أنت متأكد من المتابعة؟')) {
            e.preventDefault();
            return false;
        }
    }
});
</script>
@endsection
