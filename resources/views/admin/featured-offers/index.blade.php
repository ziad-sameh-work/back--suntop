@extends('layouts.admin')

@section('title', 'إدارة العروض المميزة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة العروض المميزة</h1>
        <div>
            <a href="{{ route('admin.featured-offers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إضافة عرض مميز جديد
            </a>
            <a href="{{ route('admin.featured-offers.update-trends') }}" class="btn btn-info">
                <i class="fas fa-chart-line"></i> تحديث نقاط الرواج
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي العروض المميزة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_featured'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                العروض المميزة النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_featured'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                إجمالي العروض
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_offers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                معدل التميز
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['total_offers'] > 0 ? round(($stats['total_featured'] / $stats['total_offers']) * 100, 1) : 0 }}%
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Featured Offers Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">العروض المميزة</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الترتيب</th>
                            <th>الصورة</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>الخصم</th>
                            <th>الحالة</th>
                            <th>مميز</th>
                            <th>الاستخدام</th>
                            <th>انتهاء الصلاحية</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody id="sortable-offers">
                        @foreach($featuredOffers as $offer)
                        <tr data-id="{{ $offer->id }}">
                            <td class="drag-handle" style="cursor: move;">
                                <i class="fas fa-grip-vertical"></i>
                                <span class="ml-2">{{ $offer->display_order }}</span>
                            </td>
                            <td>
                                @if($offer->image_url)
                                    <img src="{{ Storage::url($offer->image_url) }}" alt="{{ $offer->title }}" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <div style="width: 50px; height: 50px; background: {{ $offer->background_color }}; 
                                                border-radius: 8px; display: flex; align-items: center; justify-content: center; 
                                                color: {{ $offer->text_color }}; font-size: 12px;">
                                        عرض
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $offer->title }}</strong>
                                @if($offer->offer_tag)
                                    <span class="badge badge-info">{{ $offer->formatted_offer_tag }}</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ Str::limit($offer->short_description ?: $offer->description, 50) }}</small>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ App\Modules\Offers\Models\Offer::TYPES[$offer->type] ?? $offer->type }}
                                </span>
                            </td>
                            <td>
                                @if($offer->discount_percentage)
                                    {{ $offer->discount_percentage }}%
                                @elseif($offer->discount_amount)
                                    {{ $offer->discount_amount }} ج.م
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($offer->is_active && $offer->valid_until > now())
                                    <span class="badge badge-success">نشط</span>
                                @elseif($offer->valid_until < now())
                                    <span class="badge badge-danger">منتهي</span>
                                @else
                                    <span class="badge badge-warning">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input toggle-featured" 
                                           id="featured{{ $offer->id }}" data-id="{{ $offer->id }}" 
                                           {{ $offer->is_featured ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="featured{{ $offer->id }}"></label>
                                </div>
                            </td>
                            <td>
                                {{ $offer->used_count }}
                                @if($offer->usage_limit)
                                    / {{ $offer->usage_limit }}
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: {{ ($offer->used_count / $offer->usage_limit) * 100 }}%"></div>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <small>{{ $offer->valid_until->format('Y-m-d') }}</small>
                                <br>
                                @if($offer->valid_until > now())
                                    <small class="text-success">{{ $offer->valid_until->diffForHumans() }}</small>
                                @else
                                    <small class="text-danger">منتهي</small>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.featured-offers.edit', $offer) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                            onclick="deleteOffer({{ $offer->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $featuredOffers->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                هل أنت متأكد من حذف هذا العرض المميز؟ لا يمكن التراجع عن هذا الإجراء.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
// Initialize sortable
var sortable = Sortable.create(document.getElementById('sortable-offers'), {
    handle: '.drag-handle',
    animation: 150,
    onEnd: function (evt) {
        let offers = [];
        document.querySelectorAll('#sortable-offers tr').forEach((row, index) => {
            offers.push({
                id: row.dataset.id,
                order: index + 1
            });
        });

        fetch('{{ route("admin.featured-offers.update-order") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ offers: offers })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
            }
        });
    }
});

// Toggle featured status
document.querySelectorAll('.toggle-featured').forEach(toggle => {
    toggle.addEventListener('change', function() {
        const offerId = this.dataset.id;
        
        fetch(`/admin/featured-offers/${offerId}/toggle-featured`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                toastr.success(data.message);
            }
        });
    });
});

// Delete offer function
function deleteOffer(offerId) {
    document.getElementById('deleteForm').action = `/admin/featured-offers/${offerId}`;
    $('#deleteModal').modal('show');
}
</script>
@endsection

