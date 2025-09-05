@extends('layouts.admin')

@section('title', 'إدارة الطلبات')
@section('page-title', 'إدارة الطلبات')

@push('styles')
<style>
    /* Creative Orders Management Styles */
    .orders-container { 
        padding: 20px; 
        max-width: 1400px; 
        margin: 0 auto; 
    }

    /* Hero Section */
    .orders-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 60px 40px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }

    .orders-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: heroFloat 6s ease-in-out infinite;
    }

    .orders-hero::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -30%;
        width: 150%;
        height: 150%;
        background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 60%);
        animation: heroFloat 8s ease-in-out infinite reverse;
    }

    @keyframes heroFloat {
        0%, 100% { transform: translate(0, 0) rotate(0deg); }
        33% { transform: translate(30px, -30px) rotate(1deg); }
        66% { transform: translate(-20px, 20px) rotate(-1deg); }
    }

    .orders-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    .orders-hero-text h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin: 0 0 15px 0;
        background: linear-gradient(45deg, #ffffff, #f0f9ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .orders-hero-text p {
        font-size: 1.3rem;
        margin: 0;
        opacity: 0.9;
        font-weight: 400;
    }

    .orders-hero-stats {
        display: flex;
        gap: 40px;
        flex-wrap: wrap;
    }

    .hero-stat {
        text-align: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        min-width: 120px;
        transition: all 0.3s ease;
    }

    .hero-stat:hover {
        transform: translateY(-5px);
        background: rgba(255, 255, 255, 0.15);
    }

    .hero-stat-number {
        display: block;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 8px;
        color: #ffffff;
    }

    .hero-stat-label {
        display: block;
        font-size: 0.9rem;
        opacity: 0.8;
        font-weight: 500;
    }

    /* Enhanced Stats Cards */
    .stats-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); 
        gap: 25px; 
        margin-bottom: 40px; 
    }
    
    .stat-card { 
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%); 
        border-radius: 20px; 
        padding: 30px; 
        box-shadow: 
            0 10px 30px rgba(0, 0, 0, 0.1),
            0 1px 8px rgba(0, 0, 0, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.8);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }
    
    .stat-card::before { 
        content: ''; 
        position: absolute; 
        top: 0; 
        left: 0; 
        right: 0;
        height: 5px; 
        background: linear-gradient(90deg, #ff6b35, #4a90e2, #10b981, #8b5cf6);
        background-size: 300% 100%;
        animation: gradientShift 3s ease infinite;
    }
    
    @keyframes gradientShift {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }
    
    .stat-card:hover { 
        transform: translateY(-10px) scale(1.02);
        box-shadow: 
            0 20px 40px rgba(0, 0, 0, 0.15),
            0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .stat-card.clickable { cursor: pointer; }
    .stat-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
    .stat-icon { 
        width: 70px; 
        height: 70px; 
        border-radius: 20px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        font-size: 28px; 
        color: white;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        inset: -2px;
        border-radius: 22px;
        padding: 2px;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.3), transparent);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
    }
    .stat-icon.pending { 
        background: linear-gradient(135deg, #F59E0B, #D97706, #FBBF24);
        animation: pulse-yellow 2s ease-in-out infinite alternate;
    }
    .stat-icon.confirmed { 
        background: linear-gradient(135deg, #10B981, #059669, #34D399);
        animation: pulse-green 2s ease-in-out infinite alternate;
    }
    .stat-icon.processing { 
        background: linear-gradient(135deg, #ff6b35, #ff8c42, #ffa726);
        animation: pulse-orange 2s ease-in-out infinite alternate;
    }
    .stat-icon.revenue { 
        background: linear-gradient(135deg, #4a90e2, #5ba3f5, #42a5f5);
        animation: pulse-blue 2s ease-in-out infinite alternate;
    }

    @keyframes pulse-yellow {
        0% { box-shadow: 0 8px 20px rgba(251, 191, 36, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(251, 191, 36, 0.5); }
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(16, 185, 129, 0.5); }
    }
    @keyframes pulse-orange {
        0% { box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(255, 107, 53, 0.5); }
    }
    @keyframes pulse-blue {
        0% { box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(74, 144, 226, 0.5); }
    }
    .stat-value { 
        font-size: 2.8rem; 
        font-weight: 800;
        background: linear-gradient(135deg, #1e293b, #475569);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin: 15px 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .stat-label { 
        font-size: 16px; 
        color: #64748b;
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-change { font-size: 12px; font-weight: 500; }
    .stat-change.positive { color: var(--success); }
    .stat-change.negative { color: var(--danger); }

    .filters-card { background: var(--white); border-radius: 16px; padding: 25px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); margin-bottom: 25px; }
    .filters-header { display: flex; align-items: center; justify-content: between; margin-bottom: 20px; }
    .filters-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; display: flex; align-items: center; gap: 10px; }
    .search-section { margin-bottom: 20px; }
    .search-input { width: 100%; padding: 12px 15px 12px 45px; border: 2px solid var(--gray-200); border-radius: 10px; font-size: 14px; transition: all 0.3s ease; background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="%236B7280"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>') no-repeat 15px center; background-size: 20px; }
    .search-input:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .filters-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; }
    .filter-group { display: flex; flex-direction: column; gap: 5px; }
    .filter-label { font-size: 12px; font-weight: 500; color: var(--gray-600); text-transform: uppercase; letter-spacing: 0.5px; }
    .filter-select, .filter-input { padding: 10px 12px; border: 2px solid var(--gray-200); border-radius: 8px; font-size: 14px; transition: all 0.3s ease; background: var(--white); }
    .filter-select:focus, .filter-input:focus { outline: none; border-color: var(--suntop-orange); box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1); }
    .filters-actions { display: flex; gap: 10px; margin-top: 20px; }
    .btn-filter { background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark)); color: var(--white); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; }
    .btn-filter:hover { transform: translateY(-2px); box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3); }
    .btn-clear { background: var(--gray-100); color: var(--gray-700); border: 2px solid var(--gray-200); padding: 10px 20px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; }
    .btn-clear:hover { background: var(--gray-200); }

    .orders-table-card { background: var(--white); border-radius: 16px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05); border: 1px solid var(--gray-100); overflow: hidden; }
    .table-header { padding: 20px 25px; border-bottom: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: space-between; }
    .table-title { font-size: 18px; font-weight: 600; color: var(--gray-800); margin: 0; }
    .table-actions { display: flex; gap: 10px; }
    .btn-secondary { background: var(--gray-100); color: var(--gray-700); border: 2px solid var(--gray-200); padding: 10px 16px; border-radius: 8px; font-weight: 500; cursor: pointer; transition: all 0.3s ease; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 14px; }
    .btn-secondary:hover { background: var(--gray-200); border-color: var(--gray-300); color: var(--gray-800); text-decoration: none; transform: translateY(-1px); }
    .btn-secondary i { font-size: 13px; }
    .orders-table { width: 100%; border-collapse: collapse; }
    .orders-table th { background: var(--gray-50); padding: 15px 20px; text-align: right; font-size: 14px; font-weight: 600; color: var(--gray-700); border-bottom: 1px solid var(--gray-200); white-space: nowrap; }
    .orders-table td { padding: 20px; border-bottom: 1px solid var(--gray-100); vertical-align: middle; }
    .orders-table tr:hover { background: rgba(255, 107, 53, 0.02); }

    .order-id { font-weight: 600; color: var(--suntop-orange); text-decoration: none; }
    .order-id:hover { text-decoration: underline; }
    .customer-info { display: flex; flex-direction: column; gap: 5px; }
    .customer-name { font-weight: 500; color: var(--gray-800); }
    .customer-email { font-size: 13px; color: var(--gray-600); }
    .customer-phone { font-size: 13px; color: var(--gray-500); }
    .order-amount { font-size: 16px; font-weight: 600; color: var(--gray-800); }
    .order-items { font-size: 13px; color: var(--gray-600); }
    .order-date { font-size: 13px; color: var(--gray-600); }

    .status-badge { padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: 500; text-align: center; min-width: 80px; }
    .status-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .status-confirmed { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .status-processing { background: rgba(255, 107, 53, 0.1); color: #EA580C; }
    .status-shipped { background: rgba(139, 92, 246, 0.1); color: #7C3AED; }
    .status-delivered { background: rgba(34, 197, 94, 0.1); color: #16A34A; }
    .status-cancelled { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .status-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    .payment-badge { padding: 4px 8px; border-radius: 15px; font-size: 11px; font-weight: 500; }
    .payment-pending { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .payment-paid { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .payment-failed { background: rgba(239, 68, 68, 0.1); color: #DC2626; }
    .payment-refunded { background: rgba(107, 114, 128, 0.1); color: #6B7280; }

    .actions-dropdown { position: relative; display: inline-block; }
    .actions-btn { background: var(--gray-100); border: 1px solid var(--gray-200); border-radius: 8px; padding: 8px 12px; cursor: pointer; display: flex; align-items: center; gap: 5px; color: var(--gray-700); transition: all 0.3s ease; }
    .actions-btn:hover { background: var(--gray-200); }
    .actions-menu { position: absolute; left: 0; top: 100%; background: var(--white); border: 1px solid var(--gray-200); border-radius: 8px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); z-index: 1000; min-width: 180px; display: none; }
    .actions-menu.show { display: block; }
    .actions-menu a, .actions-menu button { display: block; width: 100%; padding: 10px 15px; text-decoration: none; color: var(--gray-700); border: none; background: none; text-align: right; cursor: pointer; transition: all 0.3s ease; font-size: 14px; }
    .actions-menu a:hover, .actions-menu button:hover { background: var(--gray-50); }
    .actions-menu .danger { color: var(--danger); }
    .actions-menu .danger:hover { background: rgba(239, 68, 68, 0.05); }

    .bulk-actions { background: linear-gradient(135deg, #f8fafc, #e2e8f0); padding: 15px 25px; border-bottom: 1px solid var(--gray-200); display: none; border-radius: 0 0 16px 16px; }
    .bulk-actions.show { display: flex; align-items: center; gap: 15px; }
    .bulk-select-all { margin-left: 10px; width: 18px; height: 18px; cursor: pointer; }
    .bulk-actions-dropdown { display: flex; gap: 10px; }
    .bulk-actions .btn-secondary { background: var(--white); border: 2px solid var(--suntop-orange); color: var(--suntop-orange); padding: 8px 14px; font-size: 13px; }
    .bulk-actions .btn-secondary:hover { background: var(--suntop-orange); color: var(--white); }
    .selected-count { font-size: 14px; color: var(--gray-600); font-weight: 500; background: var(--white); padding: 8px 12px; border-radius: 6px; border: 1px solid var(--gray-300); }

    .pagination-wrapper { padding: 20px 25px; border-top: 1px solid var(--gray-100); display: flex; align-items: center; justify-content: between; }
    .pagination-info { font-size: 14px; color: var(--gray-600); }
    .pagination-controls { display: flex; gap: 10px; align-items: center; }
    .per-page-select { padding: 8px 12px; border: 1px solid var(--gray-200); border-radius: 6px; font-size: 14px; }

    .empty-state { text-align: center; padding: 60px 20px; }
    .empty-icon { font-size: 64px; color: var(--gray-300); margin-bottom: 20px; }
    .empty-title { font-size: 20px; font-weight: 600; color: var(--gray-600); margin: 0 0 10px 0; }
    .empty-description { font-size: 16px; color: var(--gray-500); margin: 0; }

    /* Modals */
    .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1050; display: none; align-items: center; justify-content: center; }
    .modal-overlay.show { display: flex; }
    .modal { background: var(--white); border-radius: 16px; padding: 30px; max-width: 500px; width: 90%; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2); }
    .modal-header { margin-bottom: 20px; }
    .modal-title { font-size: 20px; font-weight: 600; color: var(--gray-800); margin: 0; }
    .modal-body { margin-bottom: 25px; }
    .modal-footer { display: flex; gap: 10px; justify-content: flex-end; }

    /* Custom Checkbox Styles */
    .custom-checkbox { position: relative; display: inline-block; }
    .custom-checkbox input[type="checkbox"] { opacity: 0; position: absolute; width: 18px; height: 18px; cursor: pointer; }
    .custom-checkbox .checkmark { position: absolute; top: 0; left: 0; height: 18px; width: 18px; background-color: var(--white); border: 2px solid var(--gray-300); border-radius: 4px; transition: all 0.3s ease; }
    .custom-checkbox:hover input ~ .checkmark { border-color: var(--suntop-orange); }
    .custom-checkbox input:checked ~ .checkmark { background-color: var(--suntop-orange); border-color: var(--suntop-orange); }
    .custom-checkbox .checkmark:after { content: ""; position: absolute; display: none; left: 5px; top: 2px; width: 6px; height: 10px; border: solid white; border-width: 0 2px 2px 0; transform: rotate(45deg); }
    .custom-checkbox input:checked ~ .checkmark:after { display: block; }

    /* New order animation */
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .new-order-highlight {
        animation: newOrderPulse 2s ease-in-out;
        background: rgba(16, 185, 129, 0.1) !important;
    }

    @keyframes newOrderPulse {
        0%, 100% { background: rgba(16, 185, 129, 0.1); }
        50% { background: rgba(16, 185, 129, 0.2); }
    }

    .urgent-order {
        border-right: 4px solid #EF4444 !important;
        background: rgba(239, 68, 68, 0.05) !important;
    }

    .auto-refresh-indicator {
        position: fixed;
        bottom: 20px;
        left: 20px;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 25px;
        padding: 8px 15px;
        font-size: 12px;
        color: var(--gray-600);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .status-badge {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .status-badge:hover {
        transform: scale(1.05);
    }

    @media (max-width: 768px) {
        .orders-container { padding: 15px; }
        .stats-grid { grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .stat-card { padding: 20px; }
        .stat-value { font-size: 24px; }
        .filters-grid { grid-template-columns: 1fr; }
        .table-header { flex-direction: column; gap: 15px; align-items: stretch; }
        .orders-table { font-size: 13px; }
        .orders-table th, .orders-table td { padding: 10px 8px; }
        .pagination-wrapper { flex-direction: column; gap: 15px; }
        .bulk-actions-dropdown { flex-direction: column; }
        .btn-secondary span { display: none; }
        .btn-secondary { padding: 8px 10px; }
        .table-actions { flex-direction: column; }
        .filters-header { flex-direction: column; gap: 10px; }
        .auto-refresh-indicator { bottom: 10px; left: 10px; }
    }
</style>
@endpush

@section('content')
<div class="orders-container">
    <!-- Hero Section -->
    <div class="orders-hero">
        <div class="orders-hero-content">
            <div class="orders-hero-text">
                <h1>إدارة الطلبات</h1>
                <p>تحكم شامل في جميع الطلبات ومتابعة المبيعات</p>
            </div>
            <div class="orders-hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['total_orders']) }}</span>
                    <span class="hero-stat-label">إجمالي الطلبات</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['pending_orders']) }}</span>
                    <span class="hero-stat-label">طلب معلق</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['delivered_orders']) }}</span>
                    <span class="hero-stat-label">طلب مكتمل</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card clickable" onclick="filterByStatus('all')">
            <div class="stat-header">
                <div class="stat-icon pending">
                    <i class="fas fa-shopping-cart"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_orders']) }}</div>
            <div class="stat-label">إجمالي الطلبات</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('pending')">
            <div class="stat-header">
                <div class="stat-icon pending">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['pending_orders']) }}</div>
            <div class="stat-label">طلبات معلقة</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('processing')">
            <div class="stat-header">
                <div class="stat-icon processing">
                    <i class="fas fa-cog"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['processing_orders']) }}</div>
            <div class="stat-label">قيد التجهيز</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon revenue">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['total_revenue'], 2) }}</div>
            <div class="stat-label">إجمالي الإيرادات (ج.م)</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('delivered')">
            <div class="stat-header">
                <div class="stat-icon confirmed">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['delivered_orders']) }}</div>
            <div class="stat-label">طلبات مكتملة</div>
        </div>

        <div class="stat-card clickable" onclick="filterByStatus('cancelled')">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #EF4444, #DC2626);">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['cancelled_orders']) }}</div>
            <div class="stat-label">طلبات ملغية</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['pending_payments']) }}</div>
            <div class="stat-label">مدفوعات معلقة (ج.م)</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon" style="background: linear-gradient(135deg, var(--suntop-blue), var(--suntop-blue-dark));">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div class="stat-value">{{ number_format($stats['today_orders']) }}</div>
            <div class="stat-label">طلبات اليوم</div>
        </div>
    </div>

            <!-- Filters -->
    <div class="filters-card">
        <div class="filters-header">
            <h3 class="filters-title">
                <i class="fas fa-filter" style="color: var(--suntop-orange);"></i>
                البحث والتصفية
            </h3>
            <div style="display: flex; gap: 10px; align-items: center;">
                <button type="button" onclick="checkForNewOrders()" class="btn-secondary" style="padding: 8px 12px; font-size: 13px;">
                    <i class="fas fa-sync-alt"></i>
                    تحديث
                </button>
                <div id="lastUpdate" style="font-size: 12px; color: var(--gray-500);">
                    آخر تحديث: الآن
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.orders.index') }}" id="filtersForm">
            <div class="search-section">
                <input type="text" name="search" class="search-input" 
                       placeholder="البحث برقم الطلب، اسم العميل، البريد الإلكتروني، أو رقم الهاتف..." 
                       value="{{ request('search') }}">
            </div>

            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">حالة الطلب</label>
                    <select name="status" class="filter-select">
                        <option value="all">جميع الحالات</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>مؤكد</option>
                        <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>قيد التجهيز</option>
                        <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>تم الشحن</option>
                        <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>تم التسليم</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>ملغي</option>
                        <option value="refunded" {{ request('status') === 'refunded' ? 'selected' : '' }}>مسترد</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label class="filter-label">حالة الدفع</label>
                    <select name="payment_status" class="filter-select">
                        <option value="all">جميع حالات الدفع</option>
                        <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>معلق</option>
                        <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>مدفوع</option>
                        <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>فشل</option>
                        <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>مسترد</option>
                    </select>
                </div>


                <div class="filter-group">
                    <label class="filter-label">من تاريخ</label>
                    <input type="date" name="date_from" class="filter-input" value="{{ request('date_from') }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label">إلى تاريخ</label>
                    <input type="date" name="date_to" class="filter-input" value="{{ request('date_to') }}">
                </div>

                <div class="filter-group">
                    <label class="filter-label">من مبلغ</label>
                    <input type="number" name="amount_from" class="filter-input" placeholder="0.00" 
                           value="{{ request('amount_from') }}" step="0.01">
                </div>

                <div class="filter-group">
                    <label class="filter-label">إلى مبلغ</label>
                    <input type="number" name="amount_to" class="filter-input" placeholder="0.00" 
                           value="{{ request('amount_to') }}" step="0.01">
                </div>

                <div class="filter-group">
                    <label class="filter-label">عدد النتائج</label>
                    <select name="per_page" class="filter-select">
                        <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                    بحث وتصفية
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                    مسح المرشحات
                </a>
            </div>
        </form>
    </div>

    <!-- Orders Table -->
    <div class="orders-table-card">
        <div class="table-header">
            <h3 class="table-title">
                الطلبات 
                @if($orders->total() > 0)
                    <span style="color: var(--gray-500); font-weight: normal;">({{ number_format($orders->total()) }} طلب)</span>
                @endif
            </h3>
            <div class="table-actions">
                <button class="btn-secondary" onclick="toggleBulkActions()">
                    <i class="fas fa-tasks"></i>
                    <span>إجراءات جماعية</span>
                </button>
                <button class="btn-secondary" onclick="exportOrders()">
                    <i class="fas fa-download"></i>
                    <span>تصدير</span>
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions">
            <label class="custom-checkbox">
                <input type="checkbox" id="selectAllOrders" onchange="toggleAllOrders()">
                <span class="checkmark"></span>
            </label>
            <span class="selected-count" id="selectedCount">0 طلب محدد</span>
            
            <div class="bulk-actions-dropdown">
                <button class="btn-secondary" onclick="bulkUpdateStatus()">
                    <i class="fas fa-edit"></i> تحديث الحالة
                </button>
                <button class="btn-secondary" onclick="bulkCancel()">
                    <i class="fas fa-times"></i> إلغاء
                </button>
            </div>
        </div>

        @if($orders->count() > 0)
            <table class="orders-table">
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <label class="custom-checkbox">
                                <input type="checkbox" onchange="toggleAllOrders()">
                                <span class="checkmark"></span>
                            </label>
                        </th>
                        <th>رقم الطلب</th>
                        <th>العميل</th>
                        <th>المبلغ</th>
                        <th>المنتجات</th>
                        <th>حالة الطلب</th>
                        <th>حالة الدفع</th>
                        <th>التاريخ</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                        <tr>
                            <td>
                                <label class="custom-checkbox">
                                    <input type="checkbox" class="order-checkbox" value="{{ $order->id }}" onchange="updateSelectedCount()">
                                    <span class="checkmark"></span>
                                </label>
                            </td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="order-id">
                                    #{{ $order->id }}
                                </a>
                            </td>
                            <td>
                                <div class="customer-info">
                                    <div class="customer-name">{{ $order->user->name }}</div>
                                    <div class="customer-email">{{ $order->user->email }}</div>
                                    @if($order->user->phone)
                                        <div class="customer-phone">{{ $order->user->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="order-amount">{{ number_format($order->total_amount, 2) }} ج.م</div>
                                @if($order->category_discount > 0)
                                    <div style="font-size: 12px; color: var(--success);">
                                        خصم فئة: {{ number_format($order->category_discount, 2) }} ج.م
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="order-items">
                                    {{ $order->items ? $order->items->count() : 0 }} منتج
                                    ({{ $order->items ? $order->items->sum('quantity') : 0 }} قطعة)
                                </div>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $order->status }}">
                                    @switch($order->status)
                                        @case('pending') معلق @break
                                        @case('confirmed') مؤكد @break
                                        @case('processing') قيد التجهيز @break
                                        @case('shipped') تم الشحن @break
                                        @case('delivered') تم التسليم @break
                                        @case('cancelled') ملغي @break
                                        @case('refunded') مسترد @break
                                        @default {{ $order->status }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span class="payment-badge payment-{{ $order->payment_status }}">
                                    @switch($order->payment_status)
                                        @case('pending') معلق @break
                                        @case('paid') مدفوع @break
                                        @case('failed') فشل @break
                                        @case('refunded') مسترد @break
                                        @default {{ $order->payment_status }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <div class="order-date">{{ $order->created_at->format('Y/m/d') }}</div>
                                <div style="font-size: 11px; color: var(--gray-500);">
                                    {{ $order->created_at->format('H:i') }}
                                </div>
                            </td>
                            <td>
                                <div class="actions-dropdown">
                                    <button class="actions-btn" onclick="toggleActionsMenu({{ $order->id }})">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="actions-menu" id="actionsMenu{{ $order->id }}">
                                        <a href="{{ route('admin.orders.show', $order->id) }}">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                        @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                                            <button onclick="openStatusModal({{ $order->id }}, '{{ $order->status }}')">
                                                <i class="fas fa-edit"></i> تحديث الحالة
                                            </button>
                                        @endif
                                        @if($order->payment_status !== 'paid')
                                            <button onclick="openPaymentModal({{ $order->id }}, '{{ $order->payment_status }}')">
                                                <i class="fas fa-credit-card"></i> تحديث الدفع
                                            </button>
                                        @endif
                                        @if(!in_array($order->status, ['delivered', 'cancelled', 'refunded']))
                                            <button onclick="cancelOrder({{ $order->id }})" class="danger">
                                                <i class="fas fa-times"></i> إلغاء الطلب
                                            </button>
                                        @endif
                                        <button onclick="printOrder({{ $order->id }})">
                                            <i class="fas fa-print"></i> طباعة
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    عرض {{ $orders->firstItem() ?? 0 }} إلى {{ $orders->lastItem() ?? 0 }} 
                    من أصل {{ number_format($orders->total()) }} طلب
                </div>
                <div class="pagination-controls">
                    {{ $orders->links() }}
                </div>
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-title">لا توجد طلبات</h3>
                <p class="empty-description">
                    @if(request()->hasAny(['search', 'status', 'payment_status', 'date_from', 'date_to', 'amount_from', 'amount_to']))
                        لم يتم العثور على طلبات تطابق معايير البحث المحددة.
                    @else
                        لم يتم إنشاء أي طلبات بعد.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

<!-- Auto-refresh indicator -->
<div class="auto-refresh-indicator" id="autoRefreshIndicator">
    <i class="fas fa-sync-alt" id="refreshIcon" style="color: var(--success);"></i>
    <span>تحديث تلقائي كل 30 ثانية</span>
</div>

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
@endsection

@push('scripts')
<script>
let currentOrderId = null;
let selectedOrders = [];

// Filter functions
function filterByStatus(status) {
    const form = document.getElementById('filtersForm');
    const statusSelect = form.querySelector('select[name="status"]');
    statusSelect.value = status;
    form.submit();
}

// Actions menu toggle
function toggleActionsMenu(orderId) {
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `actionsMenu${orderId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current menu
    const menu = document.getElementById(`actionsMenu${orderId}`);
    menu.classList.toggle('show');
}

// Status modal functions
function openStatusModal(orderId, currentStatus) {
    currentOrderId = orderId;
    document.getElementById('newStatus').value = currentStatus;
    document.getElementById('statusNotes').value = '';
    document.getElementById('statusModal').classList.add('show');
}

function closeStatusModal() {
    document.getElementById('statusModal').classList.remove('show');
    currentOrderId = null;
}

async function updateOrderStatus() {
    if (!currentOrderId) return;
    
    const status = document.getElementById('newStatus').value;
    const notes = document.getElementById('statusNotes').value;
    
    try {
        const response = await fetch(`/admin/orders/${currentOrderId}/update-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: status,
                notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closeStatusModal();
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث الحالة');
    }
}

// Payment modal functions
function openPaymentModal(orderId, currentStatus) {
    currentOrderId = orderId;
    document.getElementById('newPaymentStatus').value = currentStatus;
    document.getElementById('paymentNotes').value = '';
    document.getElementById('paymentModal').classList.add('show');
}

function closePaymentModal() {
    document.getElementById('paymentModal').classList.remove('show');
    currentOrderId = null;
}

async function updatePaymentStatus() {
    if (!currentOrderId) return;
    
    const paymentStatus = document.getElementById('newPaymentStatus').value;
    const notes = document.getElementById('paymentNotes').value;
    
    try {
        const response = await fetch(`/admin/orders/${currentOrderId}/update-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                payment_status: paymentStatus,
                payment_notes: notes
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(data.message);
            closePaymentModal();
            location.reload();
        } else {
            alert(data.message);
        }
    } catch (error) {
        alert('حدث خطأ أثناء تحديث حالة الدفع');
    }
}

// Cancel order
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

// Print order
function printOrder(orderId) {
    window.open(`/admin/orders/${orderId}/print`, '_blank');
}

// Bulk actions
function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllOrders() {
    const selectAll = document.getElementById('selectAllOrders');
    const checkboxes = document.querySelectorAll('.order-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.order-checkbox:checked');
    selectedOrders = Array.from(checkboxes).map(cb => cb.value);
    
    const count = selectedOrders.length;
    document.getElementById('selectedCount').textContent = `${count} طلب محدد`;
    
    // Update select all checkbox
    const allCheckboxes = document.querySelectorAll('.order-checkbox');
    const selectAll = document.getElementById('selectAllOrders');
    selectAll.checked = count === allCheckboxes.length;
    selectAll.indeterminate = count > 0 && count < allCheckboxes.length;
}

// Export function
function exportOrders() {
    alert('ميزة التصدير قيد التطوير');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
    
    if (e.target.classList.contains('modal-overlay')) {
        e.target.classList.remove('show');
    }
});

// Auto-submit search with debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        if (e.target.value.length >= 3 || e.target.value.length === 0) {
            document.getElementById('filtersForm').submit();
        }
    }, 500);
});

// Real-time updates for new orders
function checkForNewOrders() {
    // Show loading animation
    const refreshIcon = document.getElementById('refreshIcon');
    if (refreshIcon) {
        refreshIcon.style.animation = 'spin 1s linear infinite';
    }
    
    fetch('/admin/orders/dashboard', {
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const pendingCount = data.data.dashboard.today.pending_orders;
            const urgentCount = data.data.dashboard.urgent_pending;
            
            // Update pending orders badge in sidebar
            updatePendingOrdersBadge(pendingCount);
            
            // Show notification for urgent orders
            if (urgentCount > 0) {
                showUrgentOrdersNotification(urgentCount);
            }
            
            // Update last update time
            updateLastUpdateTime();
        }
    })
    .catch(error => console.log('Error checking for new orders:', error))
    .finally(() => {
        // Stop loading animation
        if (refreshIcon) {
            refreshIcon.style.animation = '';
        }
    });
}

function updateLastUpdateTime() {
    const lastUpdate = document.getElementById('lastUpdate');
    if (lastUpdate) {
        const now = new Date();
        const timeString = now.toLocaleTimeString('ar-EG', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
        lastUpdate.textContent = `آخر تحديث: ${timeString}`;
    }
}

function updatePendingOrdersBadge(count) {
    const badge = document.querySelector('.nav-link .nav-badge');
    if (count > 0) {
        if (badge) {
            badge.textContent = count;
        } else {
            // Create badge if it doesn't exist
            const navLink = document.querySelector('a[href*="orders"] .nav-text');
            if (navLink && navLink.parentNode) {
                const newBadge = document.createElement('span');
                newBadge.className = 'nav-badge';
                newBadge.textContent = count;
                navLink.parentNode.appendChild(newBadge);
            }
        }
    } else if (badge) {
        badge.remove();
    }
}

function showUrgentOrdersNotification(count) {
    // Create floating notification
    const notification = document.createElement('div');
    notification.innerHTML = `
        <div style="position: fixed; top: 20px; right: 20px; background: linear-gradient(135deg, #EF4444, #DC2626); 
                    color: white; padding: 15px 20px; border-radius: 12px; box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
                    z-index: 9999; animation: slideInRight 0.3s ease;">
            <div style="display: flex; align-items: center; gap: 10px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 18px;"></i>
                <div>
                    <div style="font-weight: 600; margin-bottom: 5px;">طلبات عاجلة!</div>
                    <div style="font-size: 14px; opacity: 0.9;">${count} طلب في انتظار التأكيد لأكثر من ساعتين</div>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" 
                        style="background: none; border: none; color: white; font-size: 18px; cursor: pointer; margin-right: 10px;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Auto-refresh every 30 seconds
setInterval(checkForNewOrders, 30000);

// Sound notification for new orders (optional)
function playNewOrderSound() {
    // Create audio element for notification sound
    const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmUeBjiR2O/NeSsFJHfH8N2QQAoUXrTp66hVFAo=');
    audio.volume = 0.3;
    audio.play().catch(e => console.log('Could not play notification sound'));
}

// Initialize
updateSelectedCount();
checkForNewOrders();
</script>
@endpush
