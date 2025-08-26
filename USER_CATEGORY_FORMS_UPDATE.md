# 📝 تحديث نماذج فئات المستخدمين - إضافة وتعديل

## 🎯 **الهدف من التحديث**
تم تحديث صفحات إضافة وتعديل فئات المستخدمين لتستخدم نظام الكراتين والعلب الجديد بدلاً من نطاق الشراء القديم.

## 📋 **الملفات المُحدثة**

### 1. **صفحة إضافة فئة جديدة** (`resources/views/admin/user-categories/create.blade.php`)

#### **التحديثات الرئيسية:**
- ✅ **تغيير العنوان:** "نطاق الشراء" → "متطلبات الكراتين والعلب"
- ✅ **تغيير الأيقونة:** `fa-calculator` → `fa-boxes`
- ✅ **إضافة حقول الكراتين:**
  - الحد الأدنى للكراتين (مطلوب)
  - الحد الأقصى للكراتين (اختياري)
- ✅ **إضافة حقول العلب:**
  - الحد الأدنى للعلب (مطلوب)  
  - الحد الأقصى للعلب (اختياري)
- ✅ **إضافة نسب الخصم:**
  - خصم الكراتين (مطلوب)
  - خصم العلب (مطلوب)
  - خصم القطع الفردية (اختياري)
- ✅ **إضافة متطلبات الشراء:**
  - يجب شراء كراتين كاملة فقط
  - يجب شراء علب كاملة فقط

#### **الحقول الجديدة:**
```html
<!-- Carton Requirements -->
<input type="number" name="min_cartons" required>
<input type="number" name="max_cartons">

<!-- Package Requirements -->  
<input type="number" name="min_packages" required>
<input type="number" name="max_packages">

<!-- Discount Rates -->
<input type="number" name="carton_discount_percentage" required>
<input type="number" name="package_discount_percentage" required>  
<input type="number" name="unit_discount_percentage">

<!-- Purchase Requirements -->
<input type="checkbox" name="requires_carton_purchase">
<input type="checkbox" name="requires_package_purchase">
```

### 2. **صفحة تعديل الفئة** (`resources/views/admin/user-categories/edit.blade.php`)

#### **التحديثات الرئيسية:**
- ✅ **تحديث العنوان والأيقونة** مثل صفحة الإضافة
- ✅ **تحديث رسالة التحذير:**
  - "تغيير نطاق الشراء" → "تغيير متطلبات الكراتين والعلب"
- ✅ **إضافة نفس الحقول** مع قيم من قاعدة البيانات
- ✅ **تحديث المعاينة المباشرة**

#### **المعاينة المحدثة:**
**قبل:**
```
نطاق الشراء: 0 - غير محدد ج.م
خصم: 5%
```

**بعد:**
```
🟠 كراتين: 5+ كرتون (خصم 5%)
🟢 علب: 10+ علبة (خصم 3%)  
🔵 قطع: خصم 0%
```

## 🔧 **التحديثات التقنية**

### **1. JavaScript المُحدث:**

#### **معاينة مباشرة:**
```javascript
function updatePreview() {
    const minCartons = document.getElementById('min_cartons').value || '0';
    const maxCartons = document.getElementById('max_cartons').value;
    const cartonDiscount = document.getElementById('carton_discount_percentage').value || '0';
    
    const cartonRangeText = maxCartons ? 
        `${parseInt(minCartons)} - ${parseInt(maxCartons)}` : 
        `${parseInt(minCartons)}+ `;
    
    document.getElementById('previewDetails').innerHTML = `
        <div>🟠 كراتين: ${cartonRangeText} كرتون (خصم ${parseFloat(cartonDiscount)}%)</div>
        <div>🟢 علب: ${packageRangeText} علبة (خصم ${parseFloat(packageDiscount)}%)</div>
        <div>🔵 قطع: خصم ${parseFloat(unitDiscount)}%</div>
    `;
}
```

#### **التحقق من صحة البيانات:**
```javascript
function validateCartonRange() {
    const minCartons = parseInt(document.getElementById('min_cartons').value) || 0;
    const maxCartons = parseInt(document.getElementById('max_cartons').value);
    
    if (maxCartons && maxCartons <= minCartons) {
        alert('الحد الأقصى للكراتين يجب أن يكون أكبر من الحد الأدنى');
        return false;
    }
    return true;
}

function validatePackageRange() {
    // نفس منطق التحقق للعلب
}

function validateDiscounts() {
    // التحقق من أن نسب الخصم بين 0 و 100
}
```

### **2. Event Listeners المُحدثة:**
```javascript
const previewFields = [
    'name', 'display_name', 
    'min_cartons', 'max_cartons', 
    'min_packages', 'max_packages',
    'carton_discount_percentage', 
    'package_discount_percentage', 
    'unit_discount_percentage'
];

// Validation Events
document.getElementById('max_cartons').addEventListener('blur', validateCartonRange);
document.getElementById('max_packages').addEventListener('blur', validatePackageRange);
document.getElementById('carton_discount_percentage').addEventListener('blur', validateDiscounts);
```

## 💾 **حقول التوافق (Legacy)**

تم الاحتفاظ بحقول النظام القديم كـ `hidden fields` للتوافق:
```html
<input type="hidden" name="min_purchase_amount" value="0">
<input type="hidden" name="max_purchase_amount" value="">
<input type="hidden" name="discount_percentage" value="0">
```

## 🎨 **التصميم والواجهة**

### **عرض الحقول:**
- 📦 **كراتين:** أيقونة برتقالية + وحدة "كرتون"
- 📦 **علب:** أيقونة خضراء + وحدة "علبة"  
- 🔢 **نسب الخصم:** أيقونة النسبة المئوية + وحدة "%"
- ☑️ **متطلبات الشراء:** Checkboxes مع أيقونات

### **المعاينة المباشرة:**
```
🟠 كراتين: 5-19 كرتون (خصم 5%)
🟢 علب: 10-49 علبة (خصم 3%)
🔵 قطع: خصم 0%
```

## ✅ **نتائج التحديث**

### **المميزات الجديدة:**
1. **🎯 واجهة واضحة:** عرض متطلبات الكراتين والعلب منفصلة
2. **📊 نسب خصم مرنة:** خصم مختلف لكل نوع بيع
3. **⚡ معاينة مباشرة:** تحديث فوري للتغييرات
4. **🔒 تحقق ذكي:** التأكد من صحة النطاقات والنسب
5. **🔄 متطلبات مرنة:** إمكانية تحديد شراء كراتين/علب فقط

### **تحسينات UX:**
- **وضوح أكبر:** مصطلحات مفهومة (كرتون/علبة)
- **تنظيم أفضل:** تجميع الحقول المترابطة
- **تحقق فوري:** التحقق أثناء الإدخال
- **معاينة واقعية:** عرض كما سيظهر للمستخدمين

## 🚀 **الاستخدام**

**إضافة فئة جديدة:**
1. انتقل إلى "إدارة فئات المستخدمين"
2. اضغط "إضافة فئة جديدة"
3. أدخل متطلبات الكراتين والعلب
4. حدد نسب الخصم لكل نوع
5. اختر متطلبات الشراء (إن وُجدت)
6. شاهد المعاينة المباشرة
7. احفظ الفئة

**تعديل فئة موجودة:**
1. اختر الفئة من القائمة
2. اضغط "تعديل"
3. غيّر المتطلبات حسب الحاجة
4. شاهد المعاينة المحدثة
5. احفظ التغييرات
6. سيتم إعادة تصنيف المستخدمين تلقائياً

## 🎊 **النتيجة النهائية**

**✅ نماذج محدثة بالكامل للنظام الجديد**
**✅ معاينة مباشرة وتحقق ذكي** 
**✅ واجهة واضحة ومفهومة**
**✅ توافق كامل مع النظام القديم**
**✅ تجربة مستخدم محسنة**

**🚀 الآن يمكن إضافة وتعديل فئات المستخدمين بسهولة باستخدام نظام الكراتين والعلب!**
