@extends('layouts.admin')

@section('title', 'إضافة عرض مميز جديد')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إضافة عرض مميز جديد</h1>
        <a href="{{ route('admin.featured-offers.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل العرض</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.featured-offers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="title">عنوان العرض <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="code">كود العرض <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('code') is-invalid @enderror" 
                                           id="code" name="code" value="{{ old('code') }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">وصف العرض <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="short_description">وصف مختصر (للعرض في الصفحة الرئيسية)</label>
                            <input type="text" class="form-control @error('short_description') is-invalid @enderror" 
                                   id="short_description" name="short_description" value="{{ old('short_description') }}">
                            @error('short_description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Offer Type and Discount -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="type">نوع العرض <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="">اختر نوع العرض</option>
                                        <option value="discount" {{ old('type') == 'discount' ? 'selected' : '' }}>خصم</option>
                                        <option value="bogo" {{ old('type') == 'bogo' ? 'selected' : '' }}>اشتري واحصل على مجاني</option>
                                        <option value="freebie" {{ old('type') == 'freebie' ? 'selected' : '' }}>منتج مجاني</option>
                                        <option value="cashback" {{ old('type') == 'cashback' ? 'selected' : '' }}>استرداد نقدي</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_percentage">نسبة الخصم (%)</label>
                                    <input type="number" class="form-control @error('discount_percentage') is-invalid @enderror" 
                                           id="discount_percentage" name="discount_percentage" min="0" max="100" step="0.01" 
                                           value="{{ old('discount_percentage') }}">
                                    @error('discount_percentage')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="discount_amount">مبلغ الخصم (ج.م)</label>
                                    <input type="number" class="form-control @error('discount_amount') is-invalid @enderror" 
                                           id="discount_amount" name="discount_amount" min="0" step="0.01" 
                                           value="{{ old('discount_amount') }}">
                                    @error('discount_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Conditions -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="minimum_amount">الحد الأدنى للطلب (ج.م)</label>
                                    <input type="number" class="form-control @error('minimum_amount') is-invalid @enderror" 
                                           id="minimum_amount" name="minimum_amount" min="0" step="0.01" 
                                           value="{{ old('minimum_amount') }}">
                                    @error('minimum_amount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="maximum_discount">الحد الأقصى للخصم (ج.م)</label>
                                    <input type="number" class="form-control @error('maximum_discount') is-invalid @enderror" 
                                           id="maximum_discount" name="maximum_discount" min="0" step="0.01" 
                                           value="{{ old('maximum_discount') }}">
                                    @error('maximum_discount')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="usage_limit">حد الاستخدام</label>
                                    <input type="number" class="form-control @error('usage_limit') is-invalid @enderror" 
                                           id="usage_limit" name="usage_limit" min="1" 
                                           value="{{ old('usage_limit') }}">
                                    @error('usage_limit')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Validity -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_from">تاريخ البداية <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('valid_from') is-invalid @enderror" 
                                           id="valid_from" name="valid_from" value="{{ old('valid_from') }}" required>
                                    @error('valid_from')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="valid_until">تاريخ الانتهاء <span class="text-danger">*</span></label>
                                    <input type="datetime-local" class="form-control @error('valid_until') is-invalid @enderror" 
                                           id="valid_until" name="valid_until" value="{{ old('valid_until') }}" required>
                                    @error('valid_until')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Design -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="background_color">لون الخلفية <span class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('background_color') is-invalid @enderror" 
                                           id="background_color" name="background_color" value="{{ old('background_color', '#FF6B35') }}" required>
                                    @error('background_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="text_color">لون النص <span class="text-danger">*</span></label>
                                    <input type="color" class="form-control @error('text_color') is-invalid @enderror" 
                                           id="text_color" name="text_color" value="{{ old('text_color', '#FFFFFF') }}" required>
                                    @error('text_color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="display_order">ترتيب العرض</label>
                                    <input type="number" class="form-control @error('display_order') is-invalid @enderror" 
                                           id="display_order" name="display_order" min="0" 
                                           value="{{ old('display_order', 0) }}">
                                    @error('display_order')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Tags and Categories -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="offer_tag">تصنيف العرض</label>
                                    <select class="form-control @error('offer_tag') is-invalid @enderror" id="offer_tag" name="offer_tag">
                                        <option value="">بدون تصنيف</option>
                                        @foreach($offerTags as $key => $tag)
                                            <option value="{{ $key }}" {{ old('offer_tag') == $key ? 'selected' : '' }}>{{ $tag }}</option>
                                        @endforeach
                                    </select>
                                    @error('offer_tag')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="image">صورة العرض</label>
                                    <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                                           id="image" name="image" accept="image/*">
                                    @error('image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Applicable Categories -->
                        <div class="form-group">
                            <label>الفئات المطبقة عليها (اختياري)</label>
                            <div class="row">
                                @foreach($categories as $category)
                                    <div class="col-md-4">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" 
                                                   id="category{{ $category->id }}" 
                                                   name="applicable_categories[]" 
                                                   value="{{ $category->name }}"
                                                   {{ in_array($category->name, old('applicable_categories', [])) ? 'checked' : '' }}>
                                            <label class="custom-control-label" for="category{{ $category->id }}">
                                                {{ $category->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Status Checkboxes -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" 
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">العرض نشط</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" 
                                           {{ old('is_featured', true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_featured">عرض مميز</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="first_order_only" name="first_order_only" 
                                           {{ old('first_order_only') ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="first_order_only">للطلب الأول فقط</label>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <!-- Submit Buttons -->
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> حفظ العرض المميز
                            </button>
                            <a href="{{ route('admin.featured-offers.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">معاينة العرض</h6>
                </div>
                <div class="card-body">
                    <div id="offer-preview" style="border-radius: 12px; padding: 20px; background: #FF6B35; color: #FFFFFF; text-align: center;">
                        <h5 id="preview-title">عنوان العرض</h5>
                        <p id="preview-description">وصف العرض سيظهر هنا</p>
                        <div id="preview-discount" class="h4 font-weight-bold">خصم %</div>
                        <small id="preview-validity">صالح حتى تاريخ معين</small>
                    </div>

                    <div class="mt-3">
                        <h6>نصائح:</h6>
                        <ul class="small">
                            <li>استخدم ألوان متباينة للنص والخلفية</li>
                            <li>اجعل العنوان واضحاً ومختصراً</li>
                            <li>حدد تاريخ انتهاء واقعي</li>
                            <li>راجع الشروط والأحكام</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Live preview
function updatePreview() {
    const title = document.getElementById('title').value || 'عنوان العرض';
    const description = document.getElementById('short_description').value || document.getElementById('description').value || 'وصف العرض';
    const backgroundColor = document.getElementById('background_color').value;
    const textColor = document.getElementById('text_color').value;
    const discountPercentage = document.getElementById('discount_percentage').value;
    const discountAmount = document.getElementById('discount_amount').value;
    const validUntil = document.getElementById('valid_until').value;
    
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-description').textContent = description;
    document.getElementById('offer-preview').style.background = backgroundColor;
    document.getElementById('offer-preview').style.color = textColor;
    
    let discountText = 'خصم رائع';
    if (discountPercentage) {
        discountText = `خصم ${discountPercentage}%`;
    } else if (discountAmount) {
        discountText = `خصم ${discountAmount} ج.م`;
    }
    document.getElementById('preview-discount').textContent = discountText;
    
    if (validUntil) {
        const date = new Date(validUntil).toLocaleDateString('ar-EG');
        document.getElementById('preview-validity').textContent = `صالح حتى ${date}`;
    }
}

// Add event listeners
['title', 'description', 'short_description', 'background_color', 'text_color', 'discount_percentage', 'discount_amount', 'valid_until'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', updatePreview);
});

// Generate code based on title
document.getElementById('title').addEventListener('input', function() {
    if (!document.getElementById('code').value) {
        const code = this.value.replace(/\s+/g, '').substring(0, 10).toUpperCase() + Math.floor(Math.random() * 100);
        document.getElementById('code').value = code;
    }
});

// Initial preview
updatePreview();
</script>
@endsection

