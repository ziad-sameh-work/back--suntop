# 🔧 تشغيل Migration لإصلاح مشكلة order_items

## المشكلة
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'selling_type' in 'field list'
```

جدول `order_items` لا يحتوي على الأعمدة المطلوبة:
- `selling_type`
- `cartons_count`  
- `packages_count`
- `units_count`

## الحل
تم إنشاء migration جديد لإضافة هذه الأعمدة.

## خطوات التشغيل:

### 1. تشغيل Migration:
```bash
php artisan migrate
```

### 2. أو تشغيل Migration محدد:
```bash
php artisan migrate --path=database/migrations/2025_08_28_170900_add_selling_type_fields_to_order_items_table.php
```

### 3. التحقق من قاعدة البيانات:
تأكد من أن جدول `order_items` يحتوي الآن على:
- `selling_type` (enum: unit, package, carton)
- `cartons_count` (integer)
- `packages_count` (integer) 
- `units_count` (integer)

## بعد تشغيل Migration:

يمكنك الآن اختبار Orders API مرة أخرى من Postman:

```json
POST http://127.0.0.1:8000/api/orders
{
    "merchant_id": "1",
    "items": [
        {
            "product_id": "1",
            "quantity": 2,
            "unit_price": 2.50
        }
    ],
    "delivery_address": {
        "street": "شارع النيل",
        "building": "رقم 15",
        "apartment": "شقة 3",
        "city": "القاهرة",
        "district": "المعادي", 
        "phone": "+20 109 999 9999"
    },
    "payment_method": "cash_on_delivery"
}
```

## نتيجة متوقعة:
✅ Status 201 Created مع تفاصيل الطلب
