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
/* Enhanced Modal Styles */
.modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: none; align-items: center; justify-content: center; animation: fadeIn 0.3s ease; }
.modal-overlay.show { display: flex; }

.modal { background: var(--white); border-radius: 20px; width: 90%; max-width: 500px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3); animation: slideUp 0.3s ease; position: relative; overflow: hidden; }

.modal-header { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); padding: 25px 30px; position: relative; }
.modal-header::before { content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" fill-opacity="0.1"/></svg>') repeat; animation: float 20s ease-in-out infinite; }

.modal-title { font-size: 20px; font-weight: 700; margin: 0; position: relative; z-index: 2; }

.modal-body { padding: 30px; }

.modal-footer { padding: 20px 30px; background: var(--gray-50); display: flex; gap: 15px; justify-content: flex-end; border-bottom-left-radius: 20px; border-bottom-right-radius: 20px; }

/* Enhanced Form Styles */
.form-group { margin-bottom: 25px; }
.form-label { display: block; margin-bottom: 10px; font-weight: 600; color: var(--gray-800); font-size: 14px; }

.form-select, .form-textarea { 
    width: 100%; 
    padding: 15px 18px; 
    border: 2px solid var(--gray-200); 
    border-radius: 12px; 
    font-size: 15px; 
    transition: all 0.3s ease; 
    background: var(--gray-50);
    color: var(--gray-800);
    font-family: inherit;
}

.form-select:focus, .form-textarea:focus { 
    outline: none; 
    border-color: var(--suntop-orange); 
    box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1); 
    background: var(--white);
    transform: translateY(-1px);
}

.form-select:hover, .form-textarea:hover {
    border-color: var(--suntop-orange);
    background: var(--white);
}

.form-textarea { 
    resize: vertical; 
    min-height: 120px; 
    line-height: 1.6;
}

/* Modal Button Styles */
.modal .btn-primary, .modal .btn-secondary {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: 2px solid transparent;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.modal .btn-primary {
    background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
    color: var(--white);
    border-color: var(--suntop-orange);
}

.modal .btn-primary:hover {
    background: linear-gradient(135deg, var(--suntop-orange-dark), #cc5a30);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
}

.modal .btn-secondary {
    background: var(--gray-100);
    color: var(--gray-700);
    border-color: var(--gray-300);
}

.modal .btn-secondary:hover {
    background: var(--gray-200);
    color: var(--gray-800);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

/* Animations */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes slideUp {
    from { transform: translateY(50px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

@keyframes float {
    0%, 100% { transform: translateX(0px) translateY(0px); }
    50% { transform: translateX(-20px) translateY(-20px); }
}
</style>
