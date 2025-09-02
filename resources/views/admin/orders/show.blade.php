@extends('layouts.admin')

@section('title', 'تفاصيل الطلب #' . $order->id)
@section('page-title', 'تفاصيل الطلب #' . $order->id)

@push('styles')
<style>
    .order-details-container { display: grid; gap: 25px; max-width: 1400px; margin: 0 auto; }
    
    /* Header Section */
    .order-header { background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%); border-radius: 16px; padding: 30px; color: var(--white); position: relative; overflow: hidden; }
    .order-header::before { content: ''; position: absolute; top: -50%; right: -50%; width: 200%; height: 200%; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="white" fill-opacity="0.1"/></svg>') repeat; animation: float 20s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateX(0px) translateY(0px); } 50% { transform: translateX(-20px) translateY(-20px); } }
    
    @keyframes slideInRight {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOutRight {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
    .order-header-content { position: relative; z-index: 2; display: grid; grid-template-columns: auto 1fr auto; gap: 25px; align-items: center; }
    .order-icon { width: 80px; height: 80px; border-radius: 50%; background: rgba(255, 255, 255, 0.2); display: flex; align-items: center; justify-content: center; font-size: 32px; }
    .order-info-header h1 { font-size: 32px; font-weight: 700; margin: 0 0 8px 0; }
    .order-info-header p { font-size: 16px; opacity: 0.9; margin: 0 0 15px 0; }
    .order-badges { display: flex; gap: 10px; flex-wrap: wrap; }
    .badge { 
        padding: 8px 18px; 
        border-radius: 25px; 
        font-size: 13px; 
        font-weight: 600; 
        background: rgba(255, 255, 255, 0.25); 
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge:hover {
        background: rgba(255, 255, 255, 0.35);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    .header-actions { display: flex; flex-direction: column; gap: 12px; }
    .btn-white { background: var(--white); color: var(--suntop-orange); border: 2px solid transparent; padding: 14px 24px; border-radius: 12px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 10px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; box-shadow: 0 4px 12px rgba(255, 107, 53, 0.15); position: relative; overflow: hidden; }
    .btn-white::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent); transition: left 0.5s ease; }
    .btn-white:hover { background: var(--suntop-orange); color: var(--white); border-color: var(--suntop-orange); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3); }
    .btn-white:hover::before { left: 100%; }
    .btn-white:active { transform: translateY(0); }
    .btn-white i { font-size: 16px; transition: transform 0.3s ease; }
    .btn-white:hover i { transform: scale(1.1); }
    
    /* Button Variants */
    .btn-success { background: linear-gradient(135deg, #10B981, #059669); color: var(--white); border: 2px solid #10B981; }
    .btn-success:hover { background: linear-gradient(135deg, #059669, #047857); border-color: #059669; box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3); }
    
    .btn-warning { background: linear-gradient(135deg, #F59E0B, #D97706); color: var(--white); border: 2px solid #F59E0B; }
    .btn-warning:hover { background: linear-gradient(135deg, #D97706, #B45309); border-color: #D97706; box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3); }
    
    .btn-info { background: linear-gradient(135deg, #3B82F6, #2563EB); color: var(--white); border: 2px solid #3B82F6; }
    .btn-info:hover { background: linear-gradient(135deg, #2563EB, #1D4ED8); border-color: #2563EB; box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3); }
    
    .btn-secondary { background: linear-gradient(135deg, #6B7280, #4B5563); color: var(--white); border: 2px solid #6B7280; }
    .btn-secondary:hover { background: linear-gradient(135deg, #4B5563, #374151); border-color: #4B5563; box-shadow: 0 8px 25px rgba(107, 114, 128, 0.3); }
    
    .btn-primary { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); border: 2px solid var(--suntop-orange); padding: 12px 20px; border-radius: 10px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; transition: all 0.3s ease; cursor: pointer; white-space: nowrap; box-shadow: 0 4px 12px rgba(255, 107, 53, 0.2); position: relative; overflow: hidden; }
    .btn-primary::before { content: ''; position: absolute; top: 0; left: -100%; width: 100%; height: 100%; background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent); transition: left 0.5s ease; }
    .btn-primary:hover { background: linear-gradient(135deg, var(--suntop-orange-dark), #cc5a30); border-color: var(--suntop-orange-dark); transform: translateY(-2px); box-shadow: 0 8px 25px rgba(255, 107, 53, 0.4); }
    .btn-primary:hover::before { left: 100%; }
    .btn-primary:active { transform: translateY(0); }
    .btn-primary i { font-size: 14px; transition: transform 0.3s ease; }
    .btn-primary:hover i { transform: scale(1.1); }
    
    /* Enhanced Quick Actions Layout */
    .quick-actions-card { margin-top: 25px; }
    .enhanced-quick-actions { padding: 10px 0; }
    
    /* Current Status Display */
    .current-status-display {
        background: linear-gradient(135deg, #F8FAFC, #E2E8F0);
        border: 2px solid var(--gray-200);
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 25px;
        text-align: center;
    }
    
    .status-indicator {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }
    
    .status-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--white);
        font-size: 24px;
        box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    
    .status-text {
        text-align: right;
    }
    
    .current-label {
        display: block;
        font-size: 14px;
        color: var(--gray-600);
        margin-bottom: 5px;
    }
    
    .current-value {
        display: block;
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-800);
    }
    
    .actions-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 20px; 
        margin-bottom: 20px; 
    }
    
    .action-btn {
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .action-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: left 0.6s ease;
    }
    
    .action-btn:hover::before {
        left: 100%;
    }
    
    .action-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
    }
    
    .action-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        color: var(--white);
        position: relative;
        z-index: 2;
    }
    
    .action-content {
        flex: 1;
        text-align: right;
        position: relative;
        z-index: 2;
    }
    
    .action-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-800);
        margin-bottom: 4px;
    }
    
    .action-subtitle {
        font-size: 13px;
        color: var(--gray-500);
        line-height: 1.4;
    }
    
    .action-next {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gray-500);
        font-size: 12px;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    
    .action-btn:hover .action-next {
        background: rgba(0, 0, 0, 0.2);
        color: var(--gray-700);
        transform: translateX(-3px);
    }
    
    /* Action Button Variants */
    .action-btn-success {
        border-color: #10B981;
    }
    .action-btn-success .action-icon {
        background: linear-gradient(135deg, #10B981, #059669);
    }
    .action-btn-success:hover {
        border-color: #059669;
        background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
    }
    
    .action-btn-warning {
        border-color: #F59E0B;
    }
    .action-btn-warning .action-icon {
        background: linear-gradient(135deg, #F59E0B, #D97706);
    }
    .action-btn-warning:hover {
        border-color: #D97706;
        background: linear-gradient(135deg, #FFFBEB, #FEF3C7);
    }
    
    .action-btn-info {
        border-color: #3B82F6;
    }
    .action-btn-info .action-icon {
        background: linear-gradient(135deg, #3B82F6, #2563EB);
    }
    .action-btn-info:hover {
        border-color: #2563EB;
        background: linear-gradient(135deg, #EFF6FF, #DBEAFE);
    }
    
    .action-btn-primary {
        border-color: var(--suntop-orange);
    }
    .action-btn-primary .action-icon {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
    }
    .action-btn-primary:hover {
        border-color: var(--suntop-orange-dark);
        background: linear-gradient(135deg, #FFF7ED, #FFEDD5);
    }
    
    .action-btn-purple {
        border-color: #8B5CF6;
    }
    .action-btn-purple .action-icon {
        background: linear-gradient(135deg, #8B5CF6, #7C3AED);
    }
    .action-btn-purple:hover {
        border-color: #7C3AED;
        background: linear-gradient(135deg, #F5F3FF, #EDE9FE);
    }
    
    /* Secondary Actions */
    .secondary-actions {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid var(--gray-200);
    }
    
    .action-btn-secondary {
        background: var(--gray-100);
        color: var(--gray-600);
        border: 2px solid var(--gray-300);
        border-radius: 10px;
        padding: 12px 24px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .action-btn-secondary:hover {
        background: var(--gray-200);
        color: var(--gray-700);
        border-color: var(--gray-400);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }
    
    /* Completed Message */
    .completed-message {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        padding: 30px;
        background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
        border: 2px solid #10B981;
        border-radius: 16px;
        color: #059669;
        font-size: 16px;
        font-weight: 600;
    }
    
    .completed-message i {
        font-size: 24px;
    }
    
    /* Responsive Design */
    @media (max-width: 768px) {
        .actions-grid {
            grid-template-columns: 1fr;
        }
        
        .action-btn {
            padding: 16px;
        }
        
        .action-icon {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
        
        .action-title {
            font-size: 15px;
        }
        
        .action-subtitle {
            font-size: 12px;
        }
    }
    
    /* Enhanced Status & Payment Badges */
    .status-badge, .payment-badge { 
        padding: 8px 16px; 
        border-radius: 20px; 
        font-size: 13px; 
        font-weight: 600; 
        text-transform: uppercase; 
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        position: relative;
        overflow: hidden;
    }
    
    .status-badge::before, .payment-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.6s ease;
    }
    
    .status-badge:hover::before, .payment-badge:hover::before {
        left: 100%;
    }
    
    .status-badge:hover, .payment-badge:hover {
        transform: translateY(-2px) scale(1.05);
        cursor: default;
    }
    
    .status-badge i, .payment-badge i {
        transition: transform 0.3s ease;
    }
    
    .status-badge:hover i, .payment-badge:hover i {
        transform: rotate(360deg);
    }
    
    /* Status Badge Colors */
    .status-pending { background: linear-gradient(135deg, #F59E0B, #D97706); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
    .status-confirmed { background: linear-gradient(135deg, #3B82F6, #2563EB); color: white; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3); }
    .status-processing { background: linear-gradient(135deg, #8B5CF6, #7C3AED); color: white; box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3); }
    .status-shipped { background: linear-gradient(135deg, #06B6D4, #0891B2); color: white; box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3); }
    .status-delivered { background: linear-gradient(135deg, #10B981, #059669); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .status-cancelled { background: linear-gradient(135deg, #EF4444, #DC2626); color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
    .status-refunded { background: linear-gradient(135deg, #6B7280, #4B5563); color: white; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3); }
    
    /* Payment Badge Colors */
    .payment-pending { background: linear-gradient(135deg, #F59E0B, #D97706); color: white; box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3); }
    .payment-paid { background: linear-gradient(135deg, #10B981, #059669); color: white; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); }
    .payment-failed { background: linear-gradient(135deg, #EF4444, #DC2626); color: white; box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); }
    .payment-refunded { background: linear-gradient(135deg, #6B7280, #4B5563); color: white; box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3); }

    /* Main Grid */
    .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 25px; }
    .content-card { background: var(--white); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); transition: all 0.3s ease; }
    .content-card:hover { transform: translateY(-3px); box-shadow: 0 8px 30px rgba(0, 0, 0, 0.1); }
    .card-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid var(--gray-100); }
    .card-icon { width: 45px; height: 45px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--white); }
    .card-icon.orange { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); }
    .card-icon.blue { background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark)); }
    .card-icon.green { background: linear-gradient(135deg, var(--success), #0D9488); }
    .card-icon.purple { background: linear-gradient(135deg, #8B5CF6, #7C3AED); }
    .card-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; }

    /* Order Items */
    .order-items-list { display: flex; flex-direction: column; gap: 15px; }
    .order-item { display: flex; align-items: center; gap: 15px; padding: 15px; background: var(--gray-50); border-radius: 12px; transition: all 0.3s ease; }
    .order-item:hover { background: var(--gray-100); }
    .item-image { width: 60px; height: 60px; border-radius: 8px; object-fit: cover; border: 2px solid var(--gray-200); }
    .item-image-fallback { width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--gray-200); background: linear-gradient(135deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center; color: var(--suntop-orange); font-size: 22px; transition: all 0.3s ease; }
    .item-image-fallback:hover { background: linear-gradient(135deg, #e9ecef, #dee2e6); transform: scale(1.05); }
    .item-image-loading { width: 60px; height: 60px; border-radius: 8px; border: 2px solid var(--gray-200); background: linear-gradient(135deg, #f8f9fa, #e9ecef); display: flex; align-items: center; justify-content: center; color: var(--gray-400); font-size: 18px; }
    .spinner { animation: spin 1s linear infinite; }
    @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .image-container { width: 60px; height: 60px; position: relative; }
    .item-details { flex: 1; }
    .item-name { font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .item-info { font-size: 14px; color: var(--gray-600); margin: 0; }
    .item-price { text-align: left; }
    .item-unit-price { font-size: 14px; color: var(--gray-600); }
    .item-total-price { font-size: 16px; font-weight: 600; color: var(--gray-800); }

    /* Customer Info */
    .customer-card { background: linear-gradient(135deg, #F8FAFC, #E2E8F0); border: 1px solid var(--gray-200); }
    .customer-avatar { width: 60px; height: 60px; border-radius: 50%; background: var(--suntop-orange); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 24px; font-weight: bold; }
    .customer-details { flex: 1; }
    .customer-name { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .customer-info-item { font-size: 14px; color: var(--gray-600); margin: 2px 0; }

    /* Order Summary */
    .order-summary { background: linear-gradient(135deg, #10B981, #059669); color: var(--white); border-radius: 12px; padding: 20px; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
    .summary-row:last-child { margin-bottom: 0; font-size: 18px; font-weight: 600; padding-top: 15px; border-top: 1px solid rgba(255, 255, 255, 0.2); }
    .summary-label { opacity: 0.9; }
    .summary-value { font-weight: 600; }

    /* Timeline */
    .timeline { position: relative; }
    .timeline::before { content: ''; position: absolute; right: 22px; top: 0; bottom: 0; width: 2px; background: var(--gray-200); }
    .timeline-item { position: relative; padding-right: 60px; margin-bottom: 30px; }
    .timeline-item:last-child { margin-bottom: 0; }
    .timeline-icon { position: absolute; right: 10px; top: 0; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 12px; z-index: 2; }
    .timeline-icon.blue { background: var(--suntop-blue); }
    .timeline-icon.green { background: var(--success); }
    .timeline-icon.orange { background: var(--suntop-orange); }
    .timeline-icon.purple { background: #8B5CF6; }
    .timeline-icon.red { background: var(--danger); }
    .timeline-content { background: var(--white); border: 1px solid var(--gray-200); border-radius: 8px; padding: 15px; }
    .timeline-title { font-weight: 600; color: var(--gray-800); margin: 0 0 5px 0; }
    .timeline-description { font-size: 14px; color: var(--gray-600); margin: 0 0 10px 0; }
    .timeline-time { font-size: 12px; color: var(--gray-500); }

    /* Status badges */
    .status-badge { padding: 8px 16px; border-radius: 20px; font-size: 14px; font-weight: 500; text-align: center; }
    .status-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-processing { background: rgba(255, 107, 53, 0.1); color: #EA580C; }
    .status-shipped { background: rgba(139, 92, 246, 0.1); color: #7C3AED; }
    .status-delivered { background: rgba(34, 197, 94, 0.1); color: #16A34A; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .status-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    /* Payment badges */
    .payment-badge { padding: 6px 12px; border-radius: 15px; font-size: 12px; font-weight: 500; }
    .payment-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .payment-paid { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .payment-failed { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .payment-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    /* Statistics */
    .stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px; }
    .stat-item { background: var(--gray-50); border-radius: 8px; padding: 15px; text-align: center; }
    .stat-value { font-size: 20px; font-weight: 700; color: var(--suntop-orange); margin: 0 0 5px 0; }
    .stat-label { font-size: 12px; color: var(--gray-600); margin: 0; }

    /* Info Lists */
    .info-list { list-style: none; padding: 0; margin: 0; }
    .info-item { display: flex; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--gray-50); }
    .info-item:last-child { border-bottom: none; }
    .info-label { font-weight: 500; color: var(--gray-600); width: 120px; flex-shrink: 0; }
    .info-value { color: var(--gray-800); flex: 1; }

    /* Quick Actions */
    .quick-actions { display: grid; gap: 10px; }

    /* Responsive */
    @media (max-width: 1024px) { .content-grid { grid-template-columns: 1fr; } }
    @media (max-width: 768px) {
        .order-header-content { grid-template-columns: 1fr; text-align: center; gap: 20px; }
        .order-icon { width: 60px; height: 60px; font-size: 24px; }
        .order-info-header h1 { font-size: 24px; }
        .header-actions { flex-direction: row; justify-content: center; }
        .order-item { flex-direction: column; text-align: center; }
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
<div class="order-details-container">
    <!-- Order Header -->
    <div class="order-header">
        <div class="order-header-content">
            <div class="order-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            
            <div class="order-info-header">
                <h1>طلب رقم #{{ $order->id }}</h1>
                <p>تم الإنشاء في {{ $order->created_at->format('Y/m/d H:i') }}</p>
                <div class="order-badges">
                    <span class="badge"><i class="fas fa-box"></i> {{ $orderStats['total_items'] }} منتج</span>
                    <span class="badge"><i class="fas fa-cubes"></i> {{ $orderStats['total_quantity'] }} قطعة</span>
                    <span class="badge"><i class="fas fa-money-bill-wave"></i> {{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
            </div>

            <div class="header-actions">
                @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                    <button class="btn-warning" onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')">
                        <i class="fas fa-edit"></i> تحديث الحالة
                    </button>
                @endif
                @if($order->payment_status !== 'paid')
                    <button class="btn-success" onclick="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')">
                        <i class="fas fa-credit-card"></i> تحديث الدفع
                    </button>
                @endif
                <button class="btn-info" onclick="printOrder({{ $order->id }})">
                    <i class="fas fa-print"></i> طباعة
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn-secondary">
                    <i class="fas fa-arrow-right"></i> العودة
                </a>
            </div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Order Items -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3 class="card-title">منتجات الطلب ({{ $order->items ? $order->items->count() : 0 }})</h3>
                </div>

                <div class="order-items-list">
                    @foreach($order->items ?? [] as $item)
                        <div class="order-item">
                            <div class="image-container">
                                @if($item->product && $item->product->images && count($item->product->images) > 0)
                                    <!-- Loading spinner -->
                                    <div class="item-image-loading" id="loading-{{ $loop->index }}">
                                        <i class="fas fa-spinner spinner"></i>
                                    </div>
                                    <!-- Actual image -->
                                    <img src="{{ asset($item->product->images[0]) }}" 
                                         alt="صورة المنتج" class="item-image" style="display: none;"
                                         onload="this.style.display='block'; document.getElementById('loading-{{ $loop->index }}').style.display='none'; this.nextElementSibling.style.display='none';"
                                         onerror="this.style.display='none'; document.getElementById('loading-{{ $loop->index }}').style.display='none'; this.nextElementSibling.style.display='flex';">
                                    @php
                                        $productName = strtolower($item->product->name ?? '');
                                        $icon = 'fas fa-cube'; // default
                                        if (str_contains($productName, 'مشروب') || str_contains($productName, 'عصير') || str_contains($productName, 'شاي') || str_contains($productName, 'قهوة')) {
                                            $icon = 'fas fa-glass-whiskey';
                                        } elseif (str_contains($productName, 'طعام') || str_contains($productName, 'أكل') || str_contains($productName, 'وجبة')) {
                                            $icon = 'fas fa-utensils';
                                        } elseif (str_contains($productName, 'حلوى') || str_contains($productName, 'شوكولاتة') || str_contains($productName, 'كيك')) {
                                            $icon = 'fas fa-candy-cane';
                                        } elseif (str_contains($productName, 'خضار') || str_contains($productName, 'فاكهة')) {
                                            $icon = 'fas fa-apple-alt';
                                        } elseif (str_contains($productName, 'لحم') || str_contains($productName, 'دجاج') || str_contains($productName, 'سمك')) {
                                            $icon = 'fas fa-drumstick-bite';
                                        }
                                    @endphp
                                    <!-- Fallback icon -->
                                    <div class="item-image-fallback" style="display: none;">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                @else
                                    @php
                                        $productName = strtolower($item->product->name ?? 'منتج محذوف');
                                        $icon = 'fas fa-cube'; // default
                                        if (str_contains($productName, 'مشروب') || str_contains($productName, 'عصير') || str_contains($productName, 'شاي') || str_contains($productName, 'قهوة')) {
                                            $icon = 'fas fa-glass-whiskey';
                                        } elseif (str_contains($productName, 'طعام') || str_contains($productName, 'أكل') || str_contains($productName, 'وجبة')) {
                                            $icon = 'fas fa-utensils';
                                        } elseif (str_contains($productName, 'حلوى') || str_contains($productName, 'شوكولاتة') || str_contains($productName, 'كيك')) {
                                            $icon = 'fas fa-candy-cane';
                                        } elseif (str_contains($productName, 'خضار') || str_contains($productName, 'فاكهة')) {
                                            $icon = 'fas fa-apple-alt';
                                        } elseif (str_contains($productName, 'لحم') || str_contains($productName, 'دجاج') || str_contains($productName, 'سمك')) {
                                            $icon = 'fas fa-drumstick-bite';
                                        } elseif ($productName === 'منتج محذوف') {
                                            $icon = 'fas fa-exclamation-triangle';
                                        }
                                    @endphp
                                    <div class="item-image-fallback">
                                        <i class="{{ $icon }}"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="item-details">
                                <h4 class="item-name">{{ $item->product->name ?? 'منتج محذوف' }}</h4>
                                <p class="item-info">
                                    الكمية: {{ $item->quantity }} قطعة
                                    @if($item->product && isset($item->product->sku))
                                        | كود المنتج: {{ $item->product->sku }}
                                    @endif
                                </p>
                            </div>
                            
                            <div class="item-price">
                                <div class="item-unit-price">{{ number_format($item->unit_price, 2) }} ج.م / قطعة</div>
                                <div class="item-total-price">{{ number_format($item->unit_price * $item->quantity, 2) }} ج.م</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Order Timeline -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon purple">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="card-title">تتبع الطلب</h3>
                </div>

                <div class="timeline">
                    @foreach($timeline as $event)
                        <div class="timeline-item">
                            <div class="timeline-icon {{ $event['color'] }}">
                                <i class="{{ $event['icon'] }}"></i>
                            </div>
                            <div class="timeline-content">
                                <h4 class="timeline-title">{{ $event['title'] }}</h4>
                                <p class="timeline-description">{{ $event['description'] }}</p>
                                <div class="timeline-time">{{ $event['timestamp']->format('Y/m/d H:i') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Enhanced Quick Actions -->
            <div class="content-card quick-actions-card">
                <div class="card-header">
                    <div class="card-icon orange">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="card-title">إجراءات سريعة</h3>
                </div>

                <div class="enhanced-quick-actions">
                    @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                        <!-- Current Status Display -->
                        <div class="current-status-display">
                            <div class="status-indicator">
                                <div class="status-icon">
                                    @switch($order->status)
                                        @case('pending') <i class="fas fa-clock"></i> @break
                                        @case('confirmed') <i class="fas fa-check"></i> @break
                                        @case('processing') <i class="fas fa-cog"></i> @break
                                        @case('shipping') <i class="fas fa-shipping-fast"></i> @break
                                        @case('shipped') <i class="fas fa-truck"></i> @break
                                        @case('delivered') <i class="fas fa-check-circle"></i> @break
                                        @case('cancelled') <i class="fas fa-times"></i> @break
                                        @default <i class="fas fa-question"></i>
                                    @endswitch
                                </div>
                                <div class="status-text">
                                    <span class="current-label">الحالة الحالية:</span>
                                    <span class="current-value">
                                        @switch($order->status)
                                            @case('pending') معلق @break
                                            @case('confirmed') مؤكد @break
                                            @case('processing') قيد التجهيز @break
                                            @case('shipping') جاري الشحن @break
                                            @case('shipped') تم الشحن @break
                                            @case('delivered') تم التسليم @break
                                            @case('cancelled') ملغي @break
                                            @default {{ $order->status }}
                                        @endswitch
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="actions-grid">
                            @if($order->status === 'pending')
                                <button class="action-btn action-btn-success" onclick="updateOrderStatusWithNotification('{{ $order->id }}', 'confirmed', 'تأكيد الطلب', 'تم تأكيد طلبكم وسيتم البدء في تجهيزه قريباً')">
                                    <div class="action-icon">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">تأكيد الطلب</div>
                                        <div class="action-subtitle">موافقة على معالجة الطلب</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                            
                            @if($order->status === 'confirmed')
                                <button class="action-btn action-btn-warning" onclick="updateOrderStatusWithNotification('{{ $order->id }}', 'preparing', 'بدء التجهيز', 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز')">
                                    <div class="action-icon">
                                        <i class="fas fa-cog"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">بدء التجهيز</div>
                                        <div class="action-subtitle">تحضير وتجهيز المنتجات</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                            
                            @if($order->status === 'preparing')
                                <button class="action-btn action-btn-purple" onclick="updateOrderStatusWithNotification('{{ $order->id }}', 'shipped', 'تم الشحن', 'تم شحن طلبكم وهو في الطريق إليكم')">
                                    <div class="action-icon">
                                        <i class="fas fa-shipping-fast"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">تم الشحن</div>
                                        <div class="action-subtitle">الطلب في الطريق للعميل</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                            

                            
                            @if($order->status === 'shipped')
                                <button class="action-btn action-btn-primary" onclick="updateOrderStatusWithNotification('{{ $order->id }}', 'delivered', 'تم التسليم', 'تم تسليم طلبكم بنجاح، شكراً لثقتكم بنا')">
                                    <div class="action-icon">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">تم التسليم</div>
                                        <div class="action-subtitle">وصل الطلب للعميل بنجاح</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                            
                            @if($order->status === 'delivered' && $order->payment_status === 'pending')
                                <button class="action-btn action-btn-success" onclick="updatePaymentStatusWithNotification('{{ $order->id }}', 'paid', 'تأكيد الدفع', 'تم تأكيد استلام الدفع، شكراً لكم')">
                                    <div class="action-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">تأكيد الدفع</div>
                                        <div class="action-subtitle">تم استلام المبلغ</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                            
                            @if($order->payment_status === 'pending' && in_array($order->status, ['confirmed', 'processing', 'shipping', 'shipped']))
                                <button class="action-btn action-btn-success" onclick="updatePaymentStatusWithNotification('{{ $order->id }}', 'paid', 'تأكيد الدفع', 'تم تأكيد استلام الدفع، شكراً لكم')">
                                    <div class="action-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div class="action-content">
                                        <div class="action-title">تأكيد الدفع</div>
                                        <div class="action-subtitle">تم استلام المبلغ</div>
                                    </div>
                                    <div class="action-next">
                                        <i class="fas fa-arrow-left"></i>
                                    </div>
                                </button>
                            @endif
                        </div>
                        
                        <!-- Secondary Actions -->
                        <div class="secondary-actions">
                            <button class="action-btn-secondary" onclick="cancelOrder({{ $order->id }})">
                                <i class="fas fa-times"></i> إلغاء الطلب
                            </button>
                        </div>
                    @else
                        <div class="completed-message">
                            <i class="fas fa-check-circle"></i>
                            <span>تم إكمال جميع إجراءات هذا الطلب</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Customer Information -->
            <div class="content-card customer-card">
                <div class="card-header">
                    <div class="card-icon blue">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="card-title">معلومات العميل</h3>
                </div>

                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                    <div class="customer-avatar">
                        {{ strtoupper(substr($order->user->name, 0, 2)) }}
                    </div>
                    <div class="customer-details">
                        <h4 class="customer-name">{{ $order->user->name }}</h4>
                        <div class="customer-info-item">{{ $order->user->email }}</div>
                        @if($order->user->phone)
                            <div class="customer-info-item">{{ $order->user->phone }}</div>
                        @endif
                    </div>
                </div>

                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-value">{{ $orderStats['customer_orders_count'] }}</div>
                        <div class="stat-label">إجمالي الطلبات</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-value">{{ number_format($orderStats['customer_total_spent'], 0) }}</div>
                        <div class="stat-label">إجمالي المشتريات</div>
                    </div>
                </div>

                @if($order->user->userCategory)
                    <div style="margin-top: 15px; text-align: center;">
                        <span class="status-badge" style="background: rgba(255, 107, 53, 0.1); color: #EA580C;">
                            فئة {{ $order->user->userCategory->display_name }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Order Summary -->
            <div class="order-summary">
                <h3 style="margin: 0 0 20px 0; color: white;">ملخص الطلب</h3>
                
                <div class="summary-row">
                    <span class="summary-label">المجموع الفرعي:</span>
                    <span class="summary-value">{{ number_format($order->items ? $order->items->sum(function($item) { return $item->unit_price * $item->quantity; }) : 0, 2) }} ج.م</span>
                </div>
                
                @if($order->category_discount > 0)
                    <div class="summary-row">
                        <span class="summary-label">خصم الفئة:</span>
                        <span class="summary-value">-{{ number_format($order->category_discount, 2) }} ج.م</span>
                    </div>
                @endif
                
                <div class="summary-row">
                    <span class="summary-label">الشحن:</span>
                    <span class="summary-value">{{ number_format($order->shipping_cost ?? 0, 2) }} ج.م</span>
                </div>
                
                <div class="summary-row">
                    <span class="summary-label">المجموع الإجمالي:</span>
                    <span class="summary-value">{{ number_format($order->total_amount, 2) }} ج.م</span>
                </div>
            </div>

            <!-- Order Status -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-icon green">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <h3 class="card-title">حالة الطلب</h3>
                </div>

                <ul class="info-list">
                    <li class="info-item">
                        <span class="info-label">الحالة:</span>
                        <span class="info-value">
                            <span class="status-badge status-{{ $order->status }}">
                                @switch($order->status)
                                    @case('pending') <i class="fas fa-clock"></i> معلق @break
                                    @case('confirmed') <i class="fas fa-check"></i> مؤكد @break
                                    @case('processing') <i class="fas fa-cog"></i> قيد التجهيز @break
                                    @case('shipped') <i class="fas fa-truck"></i> تم الشحن @break
                                    @case('delivered') <i class="fas fa-check-circle"></i> تم التسليم @break
                                    @case('cancelled') <i class="fas fa-times"></i> ملغي @break
                                    @case('refunded') <i class="fas fa-undo"></i> مسترد @break
                                    @default <i class="fas fa-question"></i> {{ $order->status }}
                                @endswitch
                            </span>
                        </span>
                    </li>
                    
                    <li class="info-item">
                        <span class="info-label">الدفع:</span>
                        <span class="info-value">
                            <span class="payment-badge payment-{{ $order->payment_status }}">
                                @switch($order->payment_status)
                                    @case('pending') <i class="fas fa-clock"></i> معلق @break
                                    @case('paid') <i class="fas fa-check-circle"></i> مدفوع @break
                                    @case('failed') <i class="fas fa-times-circle"></i> فشل @break
                                    @case('refunded') <i class="fas fa-undo"></i> مسترد @break
                                    @default <i class="fas fa-question"></i> {{ $order->payment_status }}
                                @endswitch
                            </span>
                        </span>
                    </li>
                    
                    @if($order->paid_at)
                    <li class="info-item">
                        <span class="info-label">تاريخ الدفع:</span>
                        <span class="info-value">{{ $order->paid_at->format('Y/m/d H:i') }}</span>
                    </li>
                    @endif
                    
                    @if($order->cancelled_at)
                    <li class="info-item">
                        <span class="info-label">تاريخ الإلغاء:</span>
                        <span class="info-value">{{ $order->cancelled_at->format('Y/m/d H:i') }}</span>
                    </li>
                    @endif
                </ul>
            </div>


        </div>
    </div>
</div>

<!-- Include modals from orders index -->
@include('admin.orders.modals')
@endsection

@push('scripts')
<script>
// Image loading timeout handler
document.addEventListener('DOMContentLoaded', function() {
    // Set timeout for all product images
    const productImages = document.querySelectorAll('.item-image');
    productImages.forEach((img, index) => {
        const loadingElement = document.getElementById(`loading-${index}`);
        const timeout = setTimeout(() => {
            if (!img.complete && img.style.display === 'none') {
                // Hide loading spinner and show fallback icon
                if (loadingElement) loadingElement.style.display = 'none';
                const fallback = img.nextElementSibling;
                if (fallback && fallback.classList.contains('item-image-fallback')) {
                    fallback.style.display = 'flex';
                }
            }
        }, 5000); // 5 second timeout
        
        img.addEventListener('load', () => {
            clearTimeout(timeout);
        });
        
        img.addEventListener('error', () => {
            clearTimeout(timeout);
        });
    });
});

// Enhanced Functions with Notifications
async function updateOrderStatusWithNotification(orderId, status, title, message) {
    if (!confirm(`هل أنت متأكد من ${title}؟`)) return;
    
    // Show loading state
    showLoadingState();
    
    try {
        const response = await fetch(`/admin/orders/${orderId}/update-status-with-notification`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status,
                title: title,
                message: message,
                notes: `${title} - ${message}`
            })
        });
        
        const data = await response.json();
        
        hideLoadingState();
        
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh page after short delay to show success
            setTimeout(() => {
            location.reload();
            }, 1500);
        } else {
            showErrorMessage(data.message);
        }
    } catch (error) {
        hideLoadingState();
        showErrorMessage('حدث خطأ أثناء تحديث الحالة');
    }
}

async function updatePaymentStatusWithNotification(orderId, status, title, message) {
    if (!confirm(`هل أنت متأكد من ${title}؟`)) return;
    
    // Show loading state
    showLoadingState();
    
    try {
        const response = await fetch(`/admin/orders/${orderId}/update-payment-with-notification`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_status: status,
                title: title,
                message: message,
                payment_notes: `${title} - ${message}`
            })
        });
        
        const data = await response.json();
        
        hideLoadingState();
        
        if (data.success) {
            showSuccessMessage(data.message);
            // Refresh page after short delay to show success
            setTimeout(() => {
            location.reload();
            }, 1500);
        } else {
            showErrorMessage(data.message);
        }
    } catch (error) {
        hideLoadingState();
        showErrorMessage('حدث خطأ أثناء تحديث حالة الدفع');
    }
}

// UI Helper Functions
function showLoadingState() {
    // Disable all action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.disabled = true;
        btn.style.opacity = '0.6';
    });
    
    // Show loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.id = 'loadingOverlay';
    loadingOverlay.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 30px; border-radius: 15px; text-align: center; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);">
                <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: var(--suntop-orange); margin-bottom: 15px;"></i>
                <div style="font-size: 16px; font-weight: 600; color: var(--gray-800);">جاري التحديث...</div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
}

function hideLoadingState() {
    // Re-enable action buttons
    document.querySelectorAll('.action-btn').forEach(btn => {
        btn.disabled = false;
        btn.style.opacity = '1';
    });
    
    // Hide loading overlay
    const loadingOverlay = document.getElementById('loadingOverlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

function showSuccessMessage(message) {
    showNotification(message, 'success');
}

function showErrorMessage(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? 'linear-gradient(135deg, #10B981, #059669)' : 'linear-gradient(135deg, #EF4444, #DC2626)'};
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        font-size: 16px;
        font-weight: 600;
        z-index: 10000;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: slideInRight 0.3s ease;
        min-width: 300px;
        text-align: center;
    `;
    
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}" style="margin-left: 10px;"></i>
        ${message}
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Legacy functions for backward compatibility
async function quickStatusUpdate(orderId, status) {
    const statusTexts = {
        'confirmed': 'تأكيد الطلب',
        'preparing': 'بدء التجهيز',
        'processing': 'بدء التجهيز',
        'shipping': 'جاري الشحن',
        'shipped': 'تم الشحن',
        'delivered': 'تم التسليم'
    };
    
    const statusMessages = {
        'confirmed': 'تم تأكيد طلبكم وسيتم البدء في تجهيزه قريباً',
        'preparing': 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
        'processing': 'تم البدء في تجهيز طلبكم، سيتم إشعاركم عند اكتمال التجهيز',
        'shipping': 'طلبكم جاري الشحن وسيصل إليكم في أقرب وقت',
        'shipped': 'تم شحن طلبكم وهو في الطريق إليكم',
        'delivered': 'تم تسليم طلبكم بنجاح، شكراً لثقتكم بنا'
    };
    
    return updateOrderStatusWithNotification(orderId, status, statusTexts[status], statusMessages[status]);
}

async function quickPaymentUpdate(orderId, status) {
    return updatePaymentStatusWithNotification(orderId, status, 'تأكيد الدفع', 'تم تأكيد استلام الدفع، شكراً لكم');
}

function getStatusText(status) {
    const statusTexts = {
        'pending': 'معلق',
        'confirmed': 'مؤكد',
        'processing': 'قيد التجهيز',
        'shipped': 'تم الشحن',
        'delivered': 'تم التسليم',
        'cancelled': 'ملغي',
        'refunded': 'مسترد'
    };
    return statusTexts[status] || status;
}

// Print function
function printOrder(orderId) {
    window.open(`/admin/orders/${orderId}/print`, '_blank');
}

// Modal functions (if needed)
let currentOrderId = {{ $order->id }};

function openStatusModal(orderId, currentStatus) {
    // Implementation similar to orders index
    alert('تحديث الحالة - هذه الوظيفة قيد التطوير');
}

function openPaymentModal(orderId, currentStatus) {
    // Implementation similar to orders index
    alert('تحديث الدفع - هذه الوظيفة قيد التطوير');
}

async function cancelOrder(orderId) {
    const reason = prompt('الرجاء إدخال سبب الإلغاء:');
    if (!reason) return;
    
    if (!confirm('هل أنت متأكد من إلغاء هذا الطلب؟ سيتم إعادة المخزون تلقائياً.')) return;
    
    try {
        const response = await fetch(`/admin/orders/${orderId}/cancel`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                cancellation_reason: reason
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء إلغاء الطلب');
    }
}
</script>
@endpush
