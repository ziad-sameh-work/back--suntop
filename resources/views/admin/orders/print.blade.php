<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طباعة الطلب #{{ $order->id }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; color: #333; line-height: 1.6; padding: 20px; background: white; }
        .print-container { max-width: 800px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #FF6B35; padding-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #FF6B35; margin-bottom: 10px; }
        .header-info { font-size: 14px; color: #666; }
        .order-info { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .info-section h3 { color: #FF6B35; margin-bottom: 10px; font-size: 18px; }
        .info-item { margin-bottom: 8px; }
        .info-label { font-weight: bold; display: inline-block; width: 120px; }
        .items-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        .items-table th, .items-table td { padding: 12px; text-align: right; border: 1px solid #ddd; }
        .items-table th { background: #f8f9fa; font-weight: bold; color: #333; }
        .items-table tr:nth-child(even) { background: #f9f9f9; }
        .summary { background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 30px; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .summary-row.total { font-weight: bold; font-size: 18px; color: #FF6B35; border-top: 1px solid #ddd; padding-top: 10px; }
        .footer { text-align: center; color: #666; font-size: 12px; margin-top: 30px; border-top: 1px solid #ddd; padding-top: 20px; }
        .status-badge { padding: 4px 12px; border-radius: 15px; font-size: 12px; font-weight: bold; display: inline-block; }
        .status-pending { background: #FFF3CD; color: #856404; }
        .status-confirmed { background: #D1ECF1; color: #0C5460; }
        .status-processing { background: #FFE8D4; color: #E65100; }
        .status-shipped { background: #E1BEE7; color: #6A1B9A; }
        .status-delivered { background: #C8E6C9; color: #2E7D32; }
        .status-cancelled { background: #F8D7DA; color: #721C24; }
        @media print {
            body { padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <!-- Header -->
        <div class="header">
            <div class="logo">SunTop - سن توب</div>
            <div class="header-info">
                منصة التجارة الإلكترونية الرائدة في مصر<br>
                www.suntop.com | info@suntop.com | 01000000000
            </div>
        </div>

        <!-- Order & Customer Info -->
        <div class="order-info">
            <div class="info-section">
                <h3>معلومات الطلب</h3>
                <div class="info-item">
                    <span class="info-label">رقم الطلب:</span>
                    #{{ $order->id }}
                </div>
                <div class="info-item">
                    <span class="info-label">تاريخ الطلب:</span>
                    {{ $order->created_at->format('Y/m/d H:i') }}
                </div>
                <div class="info-item">
                    <span class="info-label">حالة الطلب:</span>
                    <span class="status-badge status-{{ $order->status }}">
                        @switch($order->status)
                            @case('pending') معلق @break
                            @case('confirmed') مؤكد @break
                            @case('processing') قيد التجهيز @break
                            @case('shipped') تم الشحن @break
                            @case('delivered') تم التسليم @break
                            @case('cancelled') ملغي @break
                            @default {{ $order->status }}
                        @endswitch
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">حالة الدفع:</span>
                    <span class="status-badge status-{{ $order->payment_status }}">
                        @switch($order->payment_status)
                            @case('pending') معلق @break
                            @case('paid') مدفوع @break
                            @case('failed') فشل @break
                            @case('refunded') مسترد @break
                            @default {{ $order->payment_status }}
                        @endswitch
                    </span>
                </div>
            </div>

            <div class="info-section">
                <h3>معلومات العميل</h3>
                <div class="info-item">
                    <span class="info-label">الاسم:</span>
                    {{ $order->user->name }}
                </div>
                <div class="info-item">
                    <span class="info-label">البريد الإلكتروني:</span>
                    {{ $order->user->email }}
                </div>
                @if($order->user->phone)
                <div class="info-item">
                    <span class="info-label">رقم الهاتف:</span>
                    {{ $order->user->phone }}
                </div>
                @endif
                @if($order->user->userCategory)
                <div class="info-item">
                    <span class="info-label">فئة العميل:</span>
                    {{ $order->user->userCategory->display_name }}
                </div>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <h3 style="color: #FF6B35; margin-bottom: 15px;">منتجات الطلب</h3>
        <table class="items-table">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>السعر</th>
                    <th>الكمية</th>
                    <th>الإجمالي</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items ?? [] as $item)
                <tr>
                    <td>
                        <strong>{{ $item->product->name ?? 'منتج محذوف' }}</strong>
                        @if($item->product && isset($item->product->sku))
                            <br><small style="color: #666;">كود: {{ $item->product->sku }}</small>
                        @endif
                    </td>
                    <td>{{ number_format($item->unit_price, 2) }} ج.م</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ number_format($item->unit_price * $item->quantity, 2) }} ج.م</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Order Summary -->
        <div class="summary">
            <h3 style="color: #FF6B35; margin-bottom: 15px;">ملخص الطلب</h3>
            
            <div class="summary-row">
                <span>المجموع الفرعي:</span>
                <span>{{ number_format($order->items ? $order->items->sum(function($item) { return $item->unit_price * $item->quantity; }) : 0, 2) }} ج.م</span>
            </div>
            
            @if($order->category_discount > 0)
            <div class="summary-row">
                <span>خصم الفئة:</span>
                <span>-{{ number_format($order->category_discount, 2) }} ج.م</span>
            </div>
            @endif
            
            <div class="summary-row">
                <span>الشحن:</span>
                <span>{{ number_format($order->shipping_cost ?? 0, 2) }} ج.م</span>
            </div>
            
            <div class="summary-row total">
                <span>المجموع الإجمالي:</span>
                <span>{{ number_format($order->total_amount, 2) }} ج.م</span>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>شكراً لك على اختيار سن توب</p>
            <p>تم طباعة هذا التقرير في {{ now()->format('Y/m/d H:i') }}</p>
        </div>

        <!-- Print Button (hidden in print) -->
        <div class="no-print" style="text-align: center; margin-top: 30px;">
            <button onclick="window.print()" style="background: #FF6B35; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; cursor: pointer;">
                طباعة الطلب
            </button>
            <button onclick="window.close()" style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 16px; cursor: pointer; margin-right: 10px;">
                إغلاق
            </button>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.addEventListener('load', function() {
            // Small delay to ensure content is loaded
            setTimeout(function() {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
