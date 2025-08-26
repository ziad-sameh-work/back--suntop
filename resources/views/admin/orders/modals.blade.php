<!-- Status Update Modal -->
<div class="modal-overlay" id="statusModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">تحديث حالة الطلب</h3>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">الحالة الجديدة</label>
                <select id="newStatus" class="form-select">
                    <option value="pending">معلق</option>
                    <option value="confirmed">مؤكد</option>
                    <option value="processing">قيد التجهيز</option>
                    <option value="shipped">تم الشحن</option>
                    <option value="delivered">تم التسليم</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">ملاحظات (اختياري)</label>
                <textarea id="statusNotes" class="form-textarea" placeholder="أدخل ملاحظات إضافية..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-primary" onclick="updateOrderStatus()">حفظ</button>
            <button class="btn-secondary" onclick="closeStatusModal()">إلغاء</button>
        </div>
    </div>
</div>

<!-- Payment Update Modal -->
<div class="modal-overlay" id="paymentModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">تحديث حالة الدفع</h3>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label class="form-label">حالة الدفع الجديدة</label>
                <select id="newPaymentStatus" class="form-select">
                    <option value="pending">معلق</option>
                    <option value="paid">مدفوع</option>
                    <option value="failed">فشل</option>
                    <option value="refunded">مسترد</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">ملاحظات الدفع (اختياري)</label>
                <textarea id="paymentNotes" class="form-textarea" placeholder="أدخل ملاحظات حول الدفع..."></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-primary" onclick="updatePaymentStatus()">حفظ</button>
            <button class="btn-secondary" onclick="closePaymentModal()">إلغاء</button>
        </div>
    </div>
</div>

<style>
/* Form Styles for Modals */
.form-group { margin-bottom: 20px; }
.form-label { display: block; margin-bottom: 8px; font-weight: 500; color: var(--gray-700); }
.form-select, .form-textarea { width: 100%; padding: 12px 15px; border: 2px solid var(--gray-200); border-radius: 8px; font-size: 14px; transition: all 0.3s ease; }
.form-select:focus, .form-textarea:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
.form-textarea { resize: vertical; min-height: 100px; font-family: inherit; }
</style>
