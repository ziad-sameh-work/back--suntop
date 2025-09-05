@extends('layouts.admin')

@section('title', 'إدارة المنتجات - SunTop')
@section('page-title', 'إدارة المنتجات')

@push('styles')
<style>
    /* Creative Products Management Styles */
    .products-container {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }

    /* Hero Section */
    .products-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 24px;
        padding: 60px 40px;
        margin-bottom: 30px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(102, 126, 234, 0.3);
    }

    .products-hero::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: heroFloat 6s ease-in-out infinite;
    }

    .products-hero::after {
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

    .products-hero-content {
        position: relative;
        z-index: 2;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
    }

    .products-hero-text h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin: 0 0 15px 0;
        background: linear-gradient(45deg, #ffffff, #f0f9ff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .products-hero-text p {
        font-size: 1.3rem;
        margin: 0;
        opacity: 0.9;
        font-weight: 400;
    }

    .products-hero-stats {
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

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
    }

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

    .stat-icon.orange { 
        background: linear-gradient(135deg, #ff6b35, #ff8c42, #ffa726);
        animation: pulse-orange 2s ease-in-out infinite alternate;
    }
    .stat-icon.blue { 
        background: linear-gradient(135deg, #4a90e2, #5ba3f5, #42a5f5);
        animation: pulse-blue 2s ease-in-out infinite alternate;
    }
    .stat-icon.green { 
        background: linear-gradient(135deg, #10b981, #34d399, #6ee7b7);
        animation: pulse-green 2s ease-in-out infinite alternate;
    }
    .stat-icon.purple { 
        background: linear-gradient(135deg, #8b5cf6, #a78bfa, #c4b5fd);
        animation: pulse-purple 2s ease-in-out infinite alternate;
    }
    .stat-icon.red { 
        background: linear-gradient(135deg, #ef4444, #f87171, #fca5a5);
        animation: pulse-red 2s ease-in-out infinite alternate;
    }

    @keyframes pulse-orange {
        0% { box-shadow: 0 8px 20px rgba(255, 107, 53, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(255, 107, 53, 0.5); }
    }
    @keyframes pulse-blue {
        0% { box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(74, 144, 226, 0.5); }
    }
    @keyframes pulse-green {
        0% { box-shadow: 0 8px 20px rgba(16, 185, 129, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(16, 185, 129, 0.5); }
    }
    @keyframes pulse-purple {
        0% { box-shadow: 0 8px 20px rgba(139, 92, 246, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(139, 92, 246, 0.5); }
    }
    @keyframes pulse-red {
        0% { box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3); }
        100% { box-shadow: 0 12px 30px rgba(239, 68, 68, 0.5); }
    }

    .stat-title {
        font-size: 16px;
        color: #64748b;
        margin: 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

    .stat-change {
        font-size: 14px;
        font-weight: 600;
        padding: 8px 12px;
        border-radius: 12px;
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
        backdrop-filter: blur(10px);
    }

    /* Enhanced Filters & Actions */
    .filters-section {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 35px;
        margin-bottom: 30px;
        box-shadow: 
            0 15px 35px rgba(0, 0, 0, 0.08),
            0 5px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .filters-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6b35, #4a90e2, #10b981);
        background-size: 200% 100%;
        animation: gradientMove 4s ease infinite;
    }

    @keyframes gradientMove {
        0%, 100% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
    }

    .filters-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .filters-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-dark));
        color: var(--white);
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        color: var(--white);
        text-decoration: none;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        align-items: end;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 500;
        color: var(--gray-700);
    }

    .form-input, .form-select {
        padding: 10px 12px;
        border: 2px solid var(--gray-200);
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: var(--white);
    }

    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
    }

    .btn-secondary {
        background: var(--gray-100);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: var(--gray-200);
        border-color: var(--gray-300);
    }

    /* Enhanced Products Table */
    .products-table-section {
        background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        border-radius: 24px;
        padding: 35px;
        box-shadow: 
            0 15px 35px rgba(0, 0, 0, 0.08),
            0 5px 15px rgba(0, 0, 0, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.9);
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .products-table-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #ff6b35, #4a90e2, #10b981);
        background-size: 200% 100%;
        animation: gradientMove 4s ease infinite;
    }

    .table-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .table-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .bulk-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .products-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .products-table th,
    .products-table td {
        padding: 15px 12px;
        text-align: right;
        border-bottom: 1px solid var(--gray-100);
        vertical-align: middle;
    }

    .products-table th {
        background: var(--gray-50);
        font-weight: 600;
        color: var(--gray-700);
        font-size: 14px;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .products-table td {
        font-size: 14px;
        color: var(--gray-600);
    }

    .products-table tbody tr:hover {
        background: var(--gray-50);
    }

    .product-image {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        object-fit: cover;
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
    }

    .product-image:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .product-image-fallback {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        background: linear-gradient(135deg, #ff6b35, #4a90e2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 28px;
        border: 3px solid rgba(255, 255, 255, 0.8);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .product-image-fallback:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
    }

    .product-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .product-details h4 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-800);
    }

    .product-details p {
        margin: 2px 0 0 0;
        font-size: 12px;
        color: var(--gray-500);
    }

    .product-sku {
        background: var(--gray-100);
        color: var(--gray-600);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
    }

    .status-badge {
        padding: 6px 16px;
        border-radius: 25px;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .status-badge:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .status-available {
        background: linear-gradient(135deg, #10b981, #34d399);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .status-unavailable {
        background: linear-gradient(135deg, #ef4444, #f87171);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .status-featured {
        background: linear-gradient(135deg, #ff6b35, #ff8c42);
        color: white;
        text-shadow: 0 1px 2px rgba(0,0,0,0.1);
    }

    .stock-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .stock-in { background: rgba(16, 185, 129, 0.1); color: #059669; }
    .stock-low { background: rgba(251, 191, 36, 0.1); color: #D97706; }
    .stock-out { background: rgba(239, 68, 68, 0.1); color: #DC2626; }

    .price-display {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .current-price {
        font-weight: 600;
        color: var(--gray-800);
        font-size: 14px;
    }

    .original-price {
        text-decoration: line-through;
        color: var(--gray-400);
        font-size: 12px;
    }

    .discount-badge {
        background: var(--danger);
        color: var(--white);
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        margin-top: 2px;
    }

    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-btn {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        border: 1px solid rgba(148, 163, 184, 0.3);
        color: #475569;
        font-size: 18px;
        cursor: pointer;
        padding: 12px;
        border-radius: 12px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        position: relative;
        overflow: hidden;
    }

    .actions-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        transition: left 0.5s;
    }

    .actions-btn:hover::before {
        left: 100%;
    }

    .actions-btn:hover {
        background: linear-gradient(135deg, #4a90e2, #60a5fa);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(74, 144, 226, 0.3);
    }

    .actions-menu {
        position: absolute;
        top: 100%;
        left: 0;
        background: var(--white);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        min-width: 180px;
        z-index: 100;
        display: none;
    }

    .actions-menu.show {
        display: block;
    }

    .actions-menu a,
    .actions-menu button {
        display: block;
        width: 100%;
        padding: 10px 15px;
        text-align: right;
        border: none;
        background: none;
        color: var(--gray-700);
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
        transition: background 0.3s ease;
    }

    .actions-menu a:hover,
    .actions-menu button:hover {
        background: var(--gray-50);
    }

    .actions-menu .danger {
        color: var(--danger);
    }

    /* Stock Update Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
    }

    .modal-overlay.show {
        display: flex;
    }

    .modal {
        background: var(--white);
        border-radius: 16px;
        padding: 25px;
        max-width: 400px;
        width: 90%;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    }

    .modal-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        margin: 0;
    }

    .stock-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin-bottom: 20px;
    }

    .stock-action-btn {
        padding: 10px;
        border: 2px solid var(--gray-200);
        background: var(--white);
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .stock-action-btn.active {
        border-color: var(--suntop-orange);
        background: rgba(255, 107, 53, 0.1);
        color: var(--suntop-orange);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .products-table {
            font-size: 12px;
        }

        .products-table th,
        .products-table td {
            padding: 10px 8px;
        }

        .product-info {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }

        .product-image {
            width: 50px;
            height: 50px;
        }
    }

    /* Loading & Empty States */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        display: none;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 107, 53, 0.3);
        border-radius: 50%;
        border-top-color: var(--suntop-orange);
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--gray-500);
    }

    .empty-state i {
        font-size: 64px;
        margin-bottom: 20px;
        opacity: 0.5;
    }
</style>
@endpush

@section('content')
<div class="products-container">
    <!-- Hero Section -->
    <div class="products-hero">
        <div class="products-hero-content">
            <div class="products-hero-text">
                <h1>إدارة المنتجات</h1>
                <p>تحكم شامل في كتالوج المنتجات وإدارة المخزون</p>
            </div>
            <div class="products-hero-stats">
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['total_products']) }}</span>
                    <span class="hero-stat-label">إجمالي المنتجات</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['available_products']) }}</span>
                    <span class="hero-stat-label">منتج متاح</span>
                </div>
                <div class="hero-stat">
                    <span class="hero-stat-number">{{ number_format($stats['recent_products']) }}</span>
                    <span class="hero-stat-label">منتج حديث</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon orange">
                    <i class="fas fa-boxes"></i>
                </div>
                <h3 class="stat-title">إجمالي المنتجات</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['total_products']) }}</div>
            <div class="stat-change">{{ $stats['availability_percentage'] }}% متاح</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon blue">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="stat-title">المنتجات المتاحة</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['available_products']) }}</div>
            <div class="stat-change">من إجمالي {{ number_format($stats['total_products']) }}</div>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon green">
                    <i class="fas fa-calendar-plus"></i>
                </div>
                <h3 class="stat-title">المنتجات الحديثة</h3>
            </div>
            <div class="stat-value">{{ number_format($stats['recent_products']) }}</div>
            <div class="stat-change">خلال آخر 30 يوم</div>
        </div>


    </div>

    <!-- Filters & Actions -->
    <div class="filters-section">
        <div class="filters-header">
            <h3 class="filters-title">البحث والتصفية</h3>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                <i class="fas fa-plus"></i>
                إضافة منتج جديد
            </a>
        </div>

        <form method="GET" action="{{ route('admin.products.index') }}" id="filtersForm">
            <div class="filters-grid">
                <div class="form-group">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-input" 
                           placeholder="البحث بالاسم، الوصف، SKU..."
                           value="{{ $search }}">
                </div>



                <div class="form-group">
                    <label class="form-label">الإتاحة</label>
                    <select name="availability" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="available" {{ $availability === 'available' ? 'selected' : '' }}>متاح</option>
                        <option value="unavailable" {{ $availability === 'unavailable' ? 'selected' : '' }}>غير متاح</option>
                    </select>
                </div>



                <div class="form-group">
                    <label class="form-label">نطاق السعر</label>
                    <select name="price_range" class="form-select">
                        <option value="">جميع الأسعار</option>
                        <option value="under_100" {{ $price_range === 'under_100' ? 'selected' : '' }}>أقل من 100 ج.م</option>
                        <option value="100_500" {{ $price_range === '100_500' ? 'selected' : '' }}>100 - 500 ج.م</option>
                        <option value="500_1000" {{ $price_range === '500_1000' ? 'selected' : '' }}>500 - 1000 ج.م</option>
                        <option value="over_1000" {{ $price_range === 'over_1000' ? 'selected' : '' }}>أكثر من 1000 ج.م</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">عدد النتائج</label>
                    <select name="per_page" class="form-select">
                        <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    </select>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-search"></i>
                        بحث
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Products Table -->
    <div class="products-table-section">
        <div class="table-header">
            <h3 class="table-title">قائمة المنتجات ({{ $products->total() }})</h3>
            <div class="bulk-actions">
                <select id="bulkAction" class="form-select">
                    <option value="">إجراءات جماعية</option>
                    <option value="activate">تفعيل المحدد</option>
                    <option value="deactivate">إخفاء المحدد</option>
                    <option value="delete">حذف المحدد</option>
                </select>
                <button type="button" class="btn-secondary" onclick="executeBulkAction()">تنفيذ</button>
            </div>
        </div>

        @if($products->count() > 0)
        <div style="overflow-x: auto;">
            <table class="products-table">
                                        <thead>
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" onchange="toggleAllProducts()">
                                </th>
                                <th>المنتج</th>
                                <th>الفئة</th>
                                <th>السعر</th>
                                <th>الحالة</th>
                                <th>تاريخ الإضافة</th>
                                <th style="width: 120px;">الإجراءات</th>
                            </tr>
                        </thead>
                <tbody>
                    @foreach($products as $product)
                    <tr>
                        <td>
                            <input type="checkbox" class="product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="product-info">
                                @if($product->hasValidImage())
                                    <img src="{{ $product->first_image }}" 
                                         alt="صورة المنتج" class="product-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                @endif
                                <div class="product-image-fallback" style="display:{{ $product->hasValidImage() ? 'none' : 'flex' }};">
                                    {{ $product->initial }}
                                </div>
                                <div class="product-details">
                                    <h4>{{ $product->name }}</h4>
                                    <p>{{ Str::limit($product->description, 50) }}</p>
                                    <span class="product-id">#{{ $product->id }}</span>
                                    @if($product->back_color)
                                        <div class="product-color" style="background-color: {{ $product->back_color }}; width: 20px; height: 20px; border-radius: 50%; display: inline-block; margin-top: 5px;"></div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        
                        <td>
                            @if($product->category)
                                <span class="category-badge">{{ $product->category->display_name }}</span>
                            @else
                                <span class="text-muted">غير محدد</span>
                            @endif
                        </td>

                        <td>
                            <div class="price-display">
                                <span class="current-price">{{ number_format($product->price, 2) }} ج.م</span>
                            </div>
                        </td>

                        <td>
                            <div style="display: flex; flex-direction: column; gap: 4px;">
                                <span class="status-badge status-{{ $product->is_available ? 'available' : 'unavailable' }}">
                                    {{ $product->is_available ? 'متاح' : 'غير متاح' }}
                                </span>
                                {{-- إزالة عرض حالة المنتج المميز --}}
                            </div>
                        </td>
                        <td>{{ $product->created_at->format('Y/m/d') }}</td>
                        <td>
                            <div class="actions-dropdown">
                                <button class="actions-btn" onclick="toggleActionsMenu({{ $product->id }})">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <div class="actions-menu" id="actionsMenu{{ $product->id }}">
                                    <a href="{{ route('admin.products.show', $product->id) }}">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}">
                                        <i class="fas fa-edit"></i> تعديل
                                    </a>
                                    <button class="danger" onclick="deleteProduct({{ $product->id }})">
                                        <i class="fas fa-trash"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="pagination-wrapper">
            <div class="pagination-info">
                عرض {{ $products->firstItem() ?? 0 }} إلى {{ $products->lastItem() ?? 0 }} 
                من أصل {{ $products->total() }} نتيجة
            </div>
            {{ $products->appends(request()->query())->links() }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-boxes"></i>
            <h3>لا توجد منتجات</h3>
            <p>لم يتم العثور على منتجات تطابق معايير البحث</p>
            <a href="{{ route('admin.products.create') }}" class="btn-primary">
                إضافة أول منتج
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Stock Update Modal -->
<div class="modal-overlay" id="stockModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">تحديث المخزون</h3>
        </div>
        
        <div class="stock-actions">
            <button class="stock-action-btn active" onclick="setStockAction('set')" id="setBtn">
                <i class="fas fa-edit"></i><br>تحديد
            </button>
            <button class="stock-action-btn" onclick="setStockAction('add')" id="addBtn">
                <i class="fas fa-plus"></i><br>إضافة
            </button>
            <button class="stock-action-btn" onclick="setStockAction('subtract')" id="subtractBtn">
                <i class="fas fa-minus"></i><br>خصم
            </button>
        </div>

        <div class="form-group">
            <label class="form-label">الكمية</label>
            <input type="number" id="stockQuantity" class="form-input" min="0" placeholder="أدخل الكمية">
        </div>

        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <button class="btn-primary" onclick="updateStock()">حفظ</button>
            <button class="btn-secondary" onclick="closeStockModal()">إلغاء</button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
</div>
@endsection

@push('scripts')
<script>
let currentProductId = null;
let currentStockAction = 'set';

// Actions Menu Toggle
function toggleActionsMenu(productId) {
    const menu = document.getElementById(`actionsMenu${productId}`);
    
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(m => {
            m.classList.remove('show');
        });
    }
});

// Select All Products
function toggleAllProducts() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.product-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Toggle Product Availability
async function toggleAvailability(productId) {
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}/toggle-availability`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث الحالة', 'error');
    } finally {
        hideLoading();
    }
}

// Toggle Product Featured - DISABLED (feature removed)
async function toggleFeatured(productId) {
    showNotification('ميزة المنتجات المميزة لم تعد متاحة', 'error');
}

// Stock Modal Functions
function openStockModal(productId, currentStock) {
    currentProductId = productId;
    document.getElementById('stockQuantity').value = currentStock;
    document.getElementById('stockModal').classList.add('show');
}

function closeStockModal() {
    document.getElementById('stockModal').classList.remove('show');
    currentProductId = null;
}

function setStockAction(action) {
    currentStockAction = action;
    
    // Update button states
    document.querySelectorAll('.stock-action-btn').forEach(btn => btn.classList.remove('active'));
    document.getElementById(action + 'Btn').classList.add('active');
    
    // Update placeholder
    const input = document.getElementById('stockQuantity');
    switch(action) {
        case 'set':
            input.placeholder = 'أدخل الكمية الجديدة';
            break;
        case 'add':
            input.placeholder = 'أدخل الكمية المراد إضافتها';
            break;
        case 'subtract':
            input.placeholder = 'أدخل الكمية المراد خصمها';
            break;
    }
}

async function updateStock() {
    if (!currentProductId) return;
    
    const quantity = document.getElementById('stockQuantity').value;
    if (!quantity || quantity < 0) {
        showNotification('الرجاء إدخال كمية صحيحة', 'error');
        return;
    }
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${currentProductId}/update-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                stock_quantity: parseInt(quantity),
                action: currentStockAction
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            closeStockModal();
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تحديث المخزون', 'error');
    } finally {
        hideLoading();
    }
}

// Delete Product
async function deleteProduct(productId) {
    if (!confirm('هل أنت متأكد من حذف هذا المنتج؟ هذا الإجراء لا يمكن التراجع عنه.')) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/${productId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء حذف المنتج', 'error');
    } finally {
        hideLoading();
    }
}

// Bulk Actions
async function executeBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const selectedProducts = Array.from(document.querySelectorAll('.product-checkbox:checked')).map(cb => cb.value);
    
    if (!action) {
        showNotification('الرجاء اختيار إجراء', 'error');
        return;
    }
    
    if (selectedProducts.length === 0) {
        showNotification('الرجاء اختيار منتجات على الأقل', 'error');
        return;
    }
    
    const actionText = {
        'activate': 'تفعيل',
        'deactivate': 'إخفاء',
        'feature': 'إضافة للمميزة',
        'unfeature': 'إزالة من المميزة',
        'delete': 'حذف'
    };
    
    if (!confirm(`هل أنت متأكد من ${actionText[action]} ${selectedProducts.length} منتج؟`)) return;
    
    showLoading();
    
    try {
        const response = await fetch(`{{ route('admin.products.index') }}/bulk-action`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                action: action,
                product_ids: selectedProducts
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showNotification(data.message, 'success');
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification(data.message, 'error');
        }
    } catch (error) {
        showNotification('حدث خطأ أثناء تنفيذ العملية', 'error');
    } finally {
        hideLoading();
    }
}

// Utility Functions
function showLoading() {
    document.getElementById('loadingOverlay').style.display = 'flex';
}

function hideLoading() {
    document.getElementById('loadingOverlay').style.display = 'none';
}

function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const notification = document.createElement('div');
    notification.className = `alert ${alertClass}`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        z-index: 10000;
        max-width: 400px;
        background: ${type === 'success' ? '#10B981' : '#EF4444'};
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Auto-submit filters on change
document.querySelectorAll('#filtersForm select').forEach(select => {
    select.addEventListener('change', () => {
        document.getElementById('filtersForm').submit();
    });
});

// Search input debounce
let searchTimeout;
document.querySelector('input[name="search"]').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        document.getElementById('filtersForm').submit();
    }, 500);
});

// Close stock modal when clicking outside
document.getElementById('stockModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeStockModal();
    }
});
</script>
@endpush
