@extends('layouts.admin')

@section('title', 'تفاصيل العرض - ' . $offer->title)

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

    .header-content {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 30px;
        align-items: start;
    }

    .offer-header {
        display: flex;
        align-items: center;
        gap: 20px;
        margin-bottom: 20px;
    }

    .offer-image {
        width: 80px;
        height: 80px;
        border-radius: 12px;
        background: var(--white);
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    .offer-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .offer-image i {
        font-size: 30px;
        color: var(--suntop-orange);
    }

    .offer-info h1 {
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .offer-info .offer-code {
        background: rgba(255, 255, 255, 0.2);
        padding: 6px 12px;
        border-radius: 8px;
        font-family: monospace;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .offer-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        opacity: 0.9;
    }

    .header-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .btn-white {
        background: var(--white);
        color: var(--suntop-orange);
        border: 2px solid var(--white);
        padding: 10px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        font-size: 14px;
        text-align: center;
        justify-content: center;
    }

    .btn-white:hover {
        background: transparent;
        color: var(--white);
        border-color: var(--white);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .content-card {
        background: var(--white);
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--gray-200);
        margin-bottom: 25px;
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--gray-200);
    }

    .card-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 16px;
    }

    .card-icon.orange { background: var(--suntop-orange); }
    .card-icon.success { background: var(--success); }
    .card-icon.warning { background: var(--warning); }
    .card-icon.info { background: var(--info); }
    .card-icon.purple { background: #8b5cf6; }

    .card-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 12px;
        font-weight: 500;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: var(--gray-900);
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 20px;
    }

    .stat-item {
        text-align: center;
        padding: 20px;
        background: var(--gray-50);
        border-radius: 10px;
        border: 1px solid var(--gray-200);
    }

    .stat-number {
        font-size: 24px;
        font-weight: 700;
        color: var(--suntop-orange);
        margin-bottom: 8px;
    }

    .stat-label {
        font-size: 12px;
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .status-badge.active {
        background: rgba(16, 185, 129, 0.1);
        color: var(--success);
    }

    .status-badge.inactive {
        background: rgba(156, 163, 175, 0.2);
        color: var(--gray-600);
    }

    .status-badge.expired {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .status-badge.upcoming {
        background: rgba(245, 158, 11, 0.1);
        color: var(--warning);
    }

    .progress-bar {
        width: 100%;
        height: 8px;
        background: var(--gray-200);
        border-radius: 4px;
        overflow: hidden;
        margin-top: 10px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--suntop-orange), #ff8c42);
        border-radius: 4px;
        transition: width 0.3s ease;
    }

    .recent-usage {
        max-height: 400px;
        overflow-y: auto;
    }

    .usage-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        background: var(--gray-50);
        transition: all 0.3s ease;
    }

    .usage-item:hover {
        background: var(--gray-100);
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--suntop-orange);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-weight: 600;
        font-size: 14px;
    }

    .usage-info {
        flex: 1;
    }

    .usage-info h4 {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-900);
        margin: 0 0 4px 0;
    }

    .usage-info p {
        font-size: 12px;
        color: var(--gray-600);
        margin: 0;
    }

    .usage-amount {
        font-size: 14px;
        font-weight: 600;
        color: var(--success);
    }

    .quick-actions {
        display: grid;
        gap: 10px;
    }

    .action-btn {
        width: 100%;
        padding: 12px;
        border: 2px solid var(--gray-200);
        background: var(--white);
        color: var(--gray-700);
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
    }

    .action-btn:hover {
        border-color: var(--suntop-orange);
        color: var(--suntop-orange);
        text-decoration: none;
        transform: translateY(-1px);
    }

    .action-btn.danger:hover {
        border-color: var(--danger);
        color: var(--danger);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .header-content {
            grid-template-columns: 1fr;
        }

        .header-actions {
            flex-direction: row;
            justify-content: center;
        }

        .offer-header {
            flex-direction: column;
            text-align: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="page-header">
    <div class="header-content">
        <div>
            <div class="offer-header">
                <div class="offer-image">
                    @if($offer->image_url)
                        <img src="{{ asset('storage/' . $offer->image_url) }}" alt="{{ $offer->title }}">
                    @else
                        <i class="fas fa-gift"></i>
                    @endif
                </div>
                <div class="offer-info">
                    <h1>{{ $offer->title }}</h1>
                    <div class="offer-code">{{ $offer->code }}</div>
                    <div class="offer-meta">
                        <div class="meta-item">
                            <i class="fas fa-tag"></i>
                            <span>{{ $offer->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span>{{ $offer->valid_from->format('Y-m-d') }} - {{ $offer->valid_until->format('Y-m-d') }}</span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-users"></i>
                            <span>{{ $offer->used_count }} استخدام</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Status Badge -->
            @php
                $now = now();
                if (!$offer->is_active) {
                    $status = 'inactive';
                    $statusText = 'غير نشط';
                    $statusIcon = 'ban';
                } elseif ($offer->valid_until < $now) {
                    $status = 'expired';
                    $statusText = 'منتهي الصلاحية';
                    $statusIcon = 'calendar-times';
                } elseif ($offer->valid_from > $now) {
                    $status = 'upcoming';
                    $statusText = 'قادم';
                    $statusIcon = 'clock';
                } else {
                    $status = 'active';
                    $statusText = 'نشط';
                    $statusIcon = 'check-circle';
                }
            @endphp
            <span class="status-badge {{ $status }}">
                <i class="fas fa-{{ $statusIcon }}"></i>
                {{ $statusText }}
            </span>
        </div>

        <div class="header-actions">
            <a href="{{ route('admin.offers.analytics', $offer->id) }}" class="btn-white">
                <i class="fas fa-chart-line"></i> التحليلات
            </a>
            <a href="{{ route('admin.offers.edit', $offer->id) }}" class="btn-white">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <button class="btn-white" onclick="toggleOfferStatus({{ $offer->id }}, {{ $offer->is_active ? 'false' : 'true' }})">
                <i class="fas fa-{{ $offer->is_active ? 'ban' : 'check' }}"></i>
                {{ $offer->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
            </button>
            <a href="{{ route('admin.offers.index') }}" class="btn-white">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>
</div>

<!-- Content Grid -->
<div class="content-grid">
    <!-- Main Content -->
    <div>
        <!-- Offer Information -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-info-circle"></i>
                </div>
                <h3 class="card-title">معلومات العرض</h3>
            </div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">نوع الخصم</div>
                    <div class="info-value">{{ $offer->type === 'percentage' ? 'نسبة مئوية' : 'مبلغ ثابت' }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">قيمة الخصم</div>
                    <div class="info-value">
                        @if($offer->type === 'percentage')
                            {{ $offer->discount_percentage }}%
                        @else
                            {{ number_format($offer->discount_amount) }} ج.م
                        @endif
                    </div>
                </div>
                <div class="info-item">
                    <div class="info-label">الحد الأدنى للطلب</div>
                    <div class="info-value">{{ number_format($offer->minimum_amount) }} ج.م</div>
                </div>
                @if($offer->maximum_discount)
                <div class="info-item">
                    <div class="info-label">الحد الأقصى للخصم</div>
                    <div class="info-value">{{ number_format($offer->maximum_discount) }} ج.م</div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">صالح من</div>
                    <div class="info-value">{{ $offer->valid_from->format('Y-m-d H:i') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">صالح حتى</div>
                    <div class="info-value">{{ $offer->valid_until->format('Y-m-d H:i') }}</div>
                </div>
                @if($offer->usage_limit)
                <div class="info-item">
                    <div class="info-label">حد الاستخدام</div>
                    <div class="info-value">{{ number_format($offer->usage_limit) }} مرة</div>
                </div>
                @endif
                <div class="info-item">
                    <div class="info-label">عدد مرات الاستخدام</div>
                    <div class="info-value">{{ number_format($offer->used_count) }} مرة</div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($offer->description)
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-file-text"></i>
                </div>
                <h3 class="card-title">وصف العرض</h3>
            </div>
            <div style="line-height: 1.6; color: var(--gray-700);">
                {!! nl2br(e($offer->description)) !!}
            </div>
        </div>
        @endif

        <!-- Applicable Categories/Products -->
        @if($offer->applicable_categories || $offer->applicable_products)
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon purple">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="card-title">التطبيق على</h3>
            </div>
            @if($offer->applicable_categories)
                <div class="info-item" style="margin-bottom: 15px;">
                    <div class="info-label">الفئات</div>
                    <div class="info-value">{{ implode(', ', $offer->applicable_categories) }}</div>
                </div>
            @endif
            @if($offer->applicable_products)
                <div class="info-item">
                    <div class="info-label">المنتجات المحددة</div>
                    <div class="info-value">{{ count($offer->applicable_products) }} منتج</div>
                </div>
            @endif
            @if($offer->first_order_only)
                <div class="info-item" style="margin-top: 15px;">
                    <div class="info-label">خاص بـ</div>
                    <div class="info-value">الطلب الأول فقط</div>
                </div>
            @endif
        </div>
        @endif

        <!-- Recent Usage -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon success">
                    <i class="fas fa-history"></i>
                </div>
                <h3 class="card-title">الاستخدام الأخير</h3>
            </div>
            <div class="recent-usage">
                @if($recentUsage->count() > 0)
                    @foreach($recentUsage as $usage)
                    <div class="usage-item">
                        <div class="user-avatar">
                            {{ substr($usage->user->name, 0, 1) }}
                        </div>
                        <div class="usage-info">
                            <h4>{{ $usage->user->name }}</h4>
                            <p>{{ $usage->created_at->diffForHumans() }}</p>
                        </div>
                        <div class="usage-amount">
                            {{ number_format($usage->discount) }} ج.م خصم
                        </div>
                    </div>
                    @endforeach
                @else
                    <div style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="fas fa-history" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                        <p>لم يتم استخدام هذا العرض بعد</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div>
        <!-- Statistics -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon orange">
                    <i class="fas fa-chart-bar"></i>
                </div>
                <h3 class="card-title">إحصائيات العرض</h3>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($offerStats['total_usage']) }}</div>
                    <div class="stat-label">إجمالي الاستخدام</div>
                </div>
                @if($offerStats['remaining_usage'] !== null)
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($offerStats['remaining_usage']) }}</div>
                    <div class="stat-label">المتبقي</div>
                </div>
                @endif
                <div class="stat-item">
                    <div class="stat-number">{{ $offerStats['days_remaining'] }}</div>
                    <div class="stat-label">{{ $offerStats['days_remaining'] >= 0 ? 'يوم متبقي' : 'يوم منذ انتهاء' }}</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ number_format($offerStats['total_savings']) }}</div>
                    <div class="stat-label">إجمالي التوفير (ج.م)</div>
                </div>
            </div>
            
            <!-- Usage Progress -->
            @if($offer->usage_limit)
            <div style="margin-top: 20px;">
                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 8px;">
                    <span style="font-size: 12px; color: var(--gray-600);">معدل الاستخدام</span>
                    <span style="font-size: 12px; font-weight: 600; color: var(--gray-800);">
                        {{ number_format($offerStats['usage_percentage'], 1) }}%
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width: {{ min(100, $offerStats['usage_percentage']) }}%"></div>
                </div>
            </div>
            @endif
        </div>

        <!-- Quick Actions -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon info">
                    <i class="fas fa-tools"></i>
                </div>
                <h3 class="card-title">إجراءات سريعة</h3>
            </div>
            <div class="quick-actions">
                <button class="action-btn" onclick="copyOfferCode()">
                    <i class="fas fa-copy"></i> نسخ الكود
                </button>
                <button class="action-btn" onclick="toggleOfferStatus({{ $offer->id }}, {{ $offer->is_active ? 'false' : 'true' }})">
                    <i class="fas fa-{{ $offer->is_active ? 'ban' : 'check' }}"></i>
                    {{ $offer->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                </button>
                <a href="{{ route('admin.offers.edit', $offer->id) }}" class="action-btn">
                    <i class="fas fa-edit"></i> تعديل العرض
                </a>
                <button class="action-btn danger" onclick="deleteOffer({{ $offer->id }})">
                    <i class="fas fa-trash"></i> حذف العرض
                </button>
            </div>
        </div>

        <!-- Offer Timeline -->
        <div class="content-card">
            <div class="card-header">
                <div class="card-icon warning">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="card-title">الجدول الزمني</h3>
            </div>
            <div style="space-y: 15px;">
                <div style="padding: 12px; background: var(--gray-50); border-radius: 8px; margin-bottom: 10px;">
                    <div style="font-size: 12px; color: var(--gray-600); margin-bottom: 4px;">تاريخ الإنشاء</div>
                    <div style="font-weight: 600;">{{ $offer->created_at->format('Y-m-d H:i') }}</div>
                </div>
                <div style="padding: 12px; background: var(--success); color: white; border-radius: 8px; margin-bottom: 10px;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">بداية العرض</div>
                    <div style="font-weight: 600;">{{ $offer->valid_from->format('Y-m-d H:i') }}</div>
                </div>
                <div style="padding: 12px; background: var(--danger); color: white; border-radius: 8px;">
                    <div style="font-size: 12px; opacity: 0.9; margin-bottom: 4px;">نهاية العرض</div>
                    <div style="font-weight: 600;">{{ $offer->valid_until->format('Y-m-d H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyOfferCode() {
    const code = '{{ $offer->code }}';
    navigator.clipboard.writeText(code).then(function() {
        alert('تم نسخ الكود: ' + code);
    }, function(err) {
        console.error('فشل في نسخ الكود: ', err);
        alert('فشل في نسخ الكود');
    });
}

function toggleOfferStatus(offerId, newStatus) {
    if (confirm('هل أنت متأكد من تغيير حالة العرض؟')) {
        fetch(`/admin/offers/${offerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}

function deleteOffer(offerId) {
    if (confirm('هل أنت متأكد من حذف هذا العرض؟ لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/admin/offers/${offerId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("admin.offers.index") }}';
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}
</script>
@endsection
