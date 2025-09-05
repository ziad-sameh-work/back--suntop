@extends('layouts.admin')

@section('title', 'إدارة العروض')

@section('content')
<style>
    :root {
        --suntop-orange: #ff6b35;
        --suntop-orange-light: #ff8c42;
        --suntop-orange-dark: #e55a2b;
        --suntop-blue: #4a90e2;
        --white: #ffffff;
        --black: #333333;
        --gray-50: #f8fafc;
        --gray-100: #f1f5f9;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
        --success: #22c55e;
        --success-light: #86efac;
        --warning: #f59e0b;
        --warning-light: #fbbf24;
        --danger: #ef4444;
        --danger-light: #fca5a5;
        --info: #3b82f6;
        --info-light: #93c5fd;
        --purple: #8b5cf6;
        --purple-light: #c4b5fd;
        --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        --shadow: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
        --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
        --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        --border-radius: 12px;
        --border-radius-lg: 16px;
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    body {
        background: linear-gradient(135deg, var(--gray-50) 0%, #fafbfc 50%, #f0f9ff 100%);
        font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: var(--gray-800);
        direction: rtl;
        line-height: 1.6;
        position: relative;
        overflow-x: hidden;
    }

    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            radial-gradient(circle at 20% 80%, rgba(255, 107, 53, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(74, 144, 226, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(34, 197, 94, 0.03) 0%, transparent 50%);
        pointer-events: none;
        z-index: -1;
    }

    .page-header {
        background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-light) 50%, #ffb366 100%);
        color: var(--white);
        padding: 40px;
        border-radius: var(--border-radius-lg);
        margin-bottom: 32px;
        box-shadow: var(--shadow-xl);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: 
            url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>'),
            linear-gradient(45deg, transparent 30%, rgba(255, 255, 255, 0.1) 50%, transparent 70%);
        pointer-events: none;
        animation: shimmer 3s ease-in-out infinite;
    }

    @keyframes shimmer {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .page-title {
        font-size: 32px;
        font-weight: 800;
        margin: 0 0 12px 0;
        display: flex;
        align-items: center;
        gap: 16px;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-title i {
        background: rgba(255, 255, 255, 0.2);
        padding: 12px;
        border-radius: 12px;
        backdrop-filter: blur(10px);
        animation: float 3s ease-in-out infinite;
        box-shadow: 0 4px 20px rgba(255, 255, 255, 0.3);
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-5px) rotate(2deg); }
    }

    .page-subtitle {
        opacity: 0.95;
        font-size: 18px;
        margin: 0;
        position: relative;
        z-index: 1;
        font-weight: 400;
    }

    .stats-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: var(--white);
        padding: 28px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
        transition: var(--transition);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--suntop-orange), var(--suntop-orange-light));
        transform: scaleX(0);
        transition: var(--transition);
        transform-origin: left;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.03) rotateX(5deg);
        box-shadow: var(--shadow-xl), 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card.orange { border-right: 4px solid var(--suntop-orange); }
    .stat-card.success { border-right: 4px solid var(--success); }
    .stat-card.warning { border-right: 4px solid var(--warning); }
    .stat-card.danger { border-right: 4px solid var(--danger); }
    .stat-card.info { border-right: 4px solid var(--info); }

    .stat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .stat-icon {
        width: 64px;
        height: 64px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: var(--white);
        position: relative;
        transition: var(--transition);
    }

    .stat-icon::before {
        content: '';
        position: absolute;
        inset: 0;
        border-radius: inherit;
        background: inherit;
        opacity: 0.1;
        transform: scale(1.2);
        transition: var(--transition);
    }

    .stat-card:hover .stat-icon::before {
        transform: scale(1.6) rotate(10deg);
        opacity: 0.3;
    }

    .stat-card:hover .stat-icon {
        animation: pulse-icon 0.6s ease-in-out;
    }

    @keyframes pulse-icon {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }

    .stat-icon.orange { 
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
    }
    .stat-icon.success { 
        background: linear-gradient(135deg, var(--success), var(--success-light));
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }
    .stat-icon.warning { 
        background: linear-gradient(135deg, var(--warning), var(--warning-light));
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }
    .stat-icon.danger { 
        background: linear-gradient(135deg, var(--danger), var(--danger-light));
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    .stat-icon.info { 
        background: linear-gradient(135deg, var(--info), var(--info-light));
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
    }
    .stat-icon.purple { 
        background: linear-gradient(135deg, var(--purple), var(--purple-light));
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }

    .stat-title {
        font-size: 16px;
        color: var(--gray-600);
        margin: 0;
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    .stat-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--gray-900);
        margin: 8px 0 0 0;
        line-height: 1.2;
    }

    .stat-change {
        font-size: 14px;
        margin-top: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
        font-weight: 500;
    }

    .stat-change.positive {
        color: var(--success);
    }

    .stat-change.negative {
        color: var(--danger);
    }

    .stat-change.neutral {
        color: var(--gray-500);
    }

    .filters-section {
        background: var(--white);
        padding: 32px;
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
        margin-bottom: 32px;
        position: relative;
        overflow: hidden;
    }

    .filters-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--suntop-orange), var(--suntop-orange-light), var(--info));
    }

    .filters-title {
        font-size: 20px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 24px 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .filters-title i {
        color: var(--suntop-orange);
        font-size: 22px;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .form-group label {
        font-size: 14px;
        font-weight: 600;
        color: var(--gray-700);
        letter-spacing: 0.025em;
    }

    .form-control {
        padding: 14px 16px;
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius);
        font-size: 15px;
        transition: var(--transition);
        background: var(--white);
        font-weight: 500;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--suntop-orange);
        box-shadow: 0 0 0 4px rgba(255, 107, 53, 0.1);
        transform: translateY(-1px);
    }

    .form-control:hover {
        border-color: var(--gray-300);
    }

    .filters-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        flex-wrap: wrap;
    }

    .btn {
        padding: 12px 20px;
        border-radius: var(--border-radius);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: var(--transition);
        border: none;
        cursor: pointer;
        font-size: 15px;
        letter-spacing: 0.025em;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: var(--transition);
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        box-shadow: var(--shadow-md);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--suntop-orange-dark), var(--suntop-orange));
        color: var(--white);
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-secondary {
        background: var(--white);
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
        box-shadow: var(--shadow-sm);
    }

    .btn-secondary:hover {
        background: var(--gray-50);
        border-color: var(--suntop-orange);
        color: var(--suntop-orange);
        text-decoration: none;
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .btn-success {
        background: linear-gradient(135deg, var(--success), var(--success-light));
        color: var(--white);
        box-shadow: var(--shadow-md);
    }

    .btn-success:hover {
        background: linear-gradient(135deg, #16a34a, var(--success));
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-warning {
        background: linear-gradient(135deg, var(--warning), var(--warning-light));
        color: var(--white);
        box-shadow: var(--shadow-md);
    }

    .btn-warning:hover {
        background: linear-gradient(135deg, #d97706, var(--warning));
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--danger), var(--danger-light));
        color: var(--white);
        box-shadow: var(--shadow-md);
    }

    .btn-danger:hover {
        background: linear-gradient(135deg, #dc2626, var(--danger));
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .table-section {
        background: var(--white);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-lg);
        border: 1px solid var(--gray-200);
        overflow: hidden;
        position: relative;
    }

    .table-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--suntop-orange), var(--info), var(--success));
    }

    /* Table Header Styling */
    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 28px 32px;
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
        border-bottom: 2px solid var(--gray-100);
        position: relative;
        overflow: hidden;
    }

    .table-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="%23ff6b35" opacity="0.03"/><circle cx="75" cy="75" r="1" fill="%234a90e2" opacity="0.03"/><circle cx="50" cy="10" r="0.5" fill="%2322c55e" opacity="0.02"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        pointer-events: none;
        z-index: 0;
    }

    .table-title {
        font-size: 22px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .table-title i {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        width: 44px;
        height: 44px;
        border-radius: var(--border-radius);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        box-shadow: var(--shadow-md);
        transition: var(--transition);
    }

    .table-title:hover i {
        transform: rotate(10deg) scale(1.1);
        box-shadow: var(--shadow-lg);
    }

    .table-actions {
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 1;
    }

    .btn-secondary {
        background: linear-gradient(135deg, var(--white), var(--gray-50));
        color: var(--gray-700);
        border: 2px solid var(--gray-200);
        padding: 12px 18px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(10px);
    }

    .btn-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: var(--transition);
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        border-color: var(--suntop-orange);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    .btn-secondary:hover::before {
        left: 100%;
    }

    .btn-secondary:active {
        transform: translateY(0);
        box-shadow: var(--shadow-md);
    }

    .btn-secondary i {
        font-size: 16px;
        transition: var(--transition);
    }

    .btn-secondary:hover i {
        transform: scale(1.1);
    }

    /* Enhanced Primary Button Styling */
    .btn-primary {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        border: 2px solid var(--suntop-orange);
        padding: 14px 24px;
        border-radius: var(--border-radius);
        font-size: 15px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        letter-spacing: 0.025em;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: var(--transition);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--suntop-orange-dark), var(--suntop-orange));
        border-color: var(--suntop-orange-dark);
        transform: translateY(-3px) scale(1.02);
        box-shadow: var(--shadow-xl);
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:active {
        transform: translateY(-1px) scale(1.01);
        box-shadow: var(--shadow-lg);
    }

    .btn-primary i {
        font-size: 16px;
        transition: var(--transition);
    }

    .btn-primary:hover i {
        transform: rotate(5deg) scale(1.15);
    }

    /* Action Button Group Styling */
    .table-actions {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
        z-index: 1;
    }

    .table-actions::before {
        content: '';
        position: absolute;
        left: -16px;
        top: -8px;
        right: -16px;
        bottom: -8px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: var(--border-radius-lg);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        z-index: -1;
        opacity: 0;
        transition: var(--transition);
    }

    .table-actions:hover::before {
        opacity: 1;
    }

    /* Responsive Button Improvements */
    @media (max-width: 1024px) {
        .table-actions {
            gap: 6px;
        }
        
        .btn-secondary,
        .btn-primary {
            padding: 10px 14px;
            font-size: 13px;
        }
        
        .btn-secondary span {
            display: none;
        }
        
        .table-title {
            font-size: 18px;
        }
        
        .table-title i {
            width: 36px;
            height: 36px;
            font-size: 16px;
        }
    }

    /* Button Loading State */
    .btn.loading {
        pointer-events: none;
        opacity: 0.7;
    }

    .btn.loading::after {
        content: '';
        position: absolute;
        width: 16px;
        height: 16px;
        margin: auto;
        border: 2px solid transparent;
        border-top-color: currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Enhanced Focus States */
    .btn-secondary:focus,
    .btn-primary:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.3);
        transform: translateY(-2px);
    }

    /* Subtle Pulse Animation for Primary Button */
    @keyframes pulse-glow {
        0%, 100% {
            box-shadow: var(--shadow-md);
        }
        50% {
            box-shadow: var(--shadow-lg), 0 0 20px rgba(255, 107, 53, 0.3);
        }
    }

    .btn-primary:not(:hover):not(:active) {
        animation: pulse-glow 3s ease-in-out infinite;
    }

    /* Pagination Wrapper */
    .pagination-wrapper {
        padding: 28px 32px;
        border-top: 3px solid var(--gray-100);
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--white) 100%);
        position: relative;
    }

    .pagination-wrapper::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, var(--suntop-orange), var(--info), var(--success));
    }

    .pagination-info {
        font-size: 15px;
        color: var(--gray-600);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .pagination-info i {
        color: var(--suntop-orange);
        font-size: 16px;
    }

    /* Custom Pagination Navigation */
    .custom-pagination {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .pagination-nav-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 14px 20px;
        background: linear-gradient(135deg, var(--white), var(--gray-50));
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius-lg);
        color: var(--gray-700);
        text-decoration: none;
        font-weight: 700;
        font-size: 15px;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
        min-width: 120px;
        gap: 10px;
    }

    .pagination-nav-btn:hover {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        border-color: var(--suntop-orange);
        color: var(--white);
        text-decoration: none;
        transform: translateY(-3px);
        box-shadow: var(--shadow-lg);
    }

    .pagination-nav-btn.disabled {
        background: var(--gray-100);
        color: var(--gray-400);
        border-color: var(--gray-200);
        cursor: not-allowed;
        opacity: 0.6;
    }

    .pagination-nav-btn.disabled:hover {
        transform: none;
        box-shadow: var(--shadow-sm);
        background: var(--gray-100);
        color: var(--gray-400);
        border-color: var(--gray-200);
    }

    .pagination-nav-btn .btn-icon {
        font-size: 18px;
        transition: var(--transition);
    }

    .pagination-nav-btn:hover .btn-icon {
        transform: scale(1.2);
    }

    .pagination-nav-btn.prev-btn .btn-icon {
        order: -1;
    }

    .pagination-nav-btn.next-btn .btn-icon {
        order: 1;
    }

    /* Page Numbers */
    .pagination-numbers {
        display: flex;
        gap: 8px;
        align-items: center;
        margin: 0 20px;
    }

    .page-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius);
        color: var(--gray-700);
        text-decoration: none;
        font-weight: 600;
        font-size: 14px;
        transition: var(--transition);
        box-shadow: var(--shadow-sm);
    }

    .page-number:hover {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        border-color: var(--suntop-orange);
        color: var(--white);
        text-decoration: none;
        transform: translateY(-2px) scale(1.05);
        box-shadow: var(--shadow-md);
    }

    .page-number.active {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        border-color: var(--suntop-orange);
        color: var(--white);
        box-shadow: var(--shadow-md);
        transform: scale(1.1);
    }

    .page-dots {
        color: var(--gray-400);
        font-weight: 700;
        font-size: 16px;
        padding: 0 8px;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filters-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 24px;
            text-align: center;
        }
        
        .page-title {
            font-size: 24px;
        }
        
        .stats-row {
            grid-template-columns: 1fr;
            gap: 16px;
        }
        
        .stat-card {
            padding: 20px;
        }
        
        .filters-section {
            padding: 20px;
        }
        
        .filters-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
            gap: 16px;
        }

        .table-actions {
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn-secondary span {
            display: none;
        }

        .btn-secondary {
            padding: 10px 12px;
        }

        .data-table {
            font-size: 13px;
        }

        .data-table th,
        .data-table td {
            padding: 12px 8px;
        }
        
        .offer-image {
            width: 40px;
            height: 40px;
        }
        
        .actions-menu {
            left: auto;
            right: 0;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 16px;
        }
        
        .page-title {
            font-size: 20px;
            flex-direction: column;
            gap: 8px;
        }
        
        .filters-section,
        .table-section {
            margin-left: -16px;
            margin-right: -16px;
            border-radius: 0;
        }
        
        .bulk-actions {
            padding: 16px 20px;
        }
        
        .pagination-wrapper {
            padding: 20px 16px;
            flex-direction: column;
            gap: 16px;
        }
        
        .custom-pagination {
            flex-direction: column;
            gap: 16px;
        }
        
        .pagination-numbers {
            margin: 0;
            order: 1;
        }
        
        .pagination-nav-btn {
            padding: 12px 16px;
            min-width: 100px;
            font-size: 14px;
        }
        
        .page-number {
            width: 40px;
            height: 40px;
            font-size: 13px;
        }
    }

    /* Loading Animation */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
    }

    .loading {
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    /* Smooth Scrolling */
    html {
        scroll-behavior: smooth;
    }

    /* Focus States */
    .btn:focus,
    .form-control:focus,
    .actions-btn:focus {
        outline: 2px solid var(--suntop-orange);
        outline-offset: 2px;
    }

    .bulk-actions {
        display: none;
        align-items: center;
        gap: 20px;
        padding: 20px 32px;
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        border-bottom: 2px solid var(--suntop-orange-dark);
        color: var(--white);
    }

    .bulk-actions.show {
        display: flex;
        animation: slideDown 0.3s ease-out;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .selected-count {
        font-size: 16px;
        color: var(--white);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .selected-count i {
        font-size: 18px;
    }

    .bulk-actions-dropdown {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .bulk-actions .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        color: var(--white);
        padding: 10px 16px;
        font-size: 14px;
        backdrop-filter: blur(10px);
    }

    .bulk-actions .btn-secondary:hover {
        background: var(--white);
        color: var(--suntop-orange);
        border-color: var(--white);
        transform: translateY(-2px);
    }

    .table-responsive {
        overflow-x: auto;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 15px;
    }

    .data-table th,
    .data-table td {
        padding: 20px 16px;
        text-align: right;
        border-bottom: 1px solid var(--gray-200);
        vertical-align: middle;
    }

    .data-table th {
        background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
        font-weight: 700;
        color: var(--gray-800);
        font-size: 15px;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        position: sticky;
        top: 0;
        z-index: 10;
        border-bottom: 2px solid var(--suntop-orange);
    }

    .data-table th:first-child {
        border-top-right-radius: var(--border-radius);
    }

    .data-table th:last-child {
        border-top-left-radius: var(--border-radius);
    }

    .data-table tbody tr {
        transition: var(--transition);
        position: relative;
    }

    .data-table tbody tr::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--suntop-orange);
        transform: scaleY(0);
        transition: var(--transition);
    }

    .data-table tbody tr:hover {
        background: linear-gradient(135deg, var(--gray-50), rgba(255, 107, 53, 0.05));
        transform: translateX(-4px);
        box-shadow: var(--shadow-md);
    }

    .data-table tbody tr:hover::before {
        transform: scaleY(1);
    }

    .data-table td {
        font-size: 15px;
        color: var(--gray-800);
        font-weight: 500;
    }

    .data-table .checkbox-cell {
        width: 50px;
        text-align: center;
    }

    .data-table .checkbox-cell input[type="checkbox"] {
        width: 18px;
        height: 18px;
        accent-color: var(--suntop-orange);
        cursor: pointer;
    }

    .offer-image {
        width: 60px;
        height: 60px;
        border-radius: var(--border-radius);
        background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 2px solid var(--gray-200);
        transition: var(--transition);
    }

    .offer-image:hover {
        transform: scale(1.15) rotate(3deg);
        border-color: var(--suntop-orange);
        box-shadow: var(--shadow-lg), 0 0 20px rgba(255, 107, 53, 0.4);
    }

    .offer-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: var(--transition);
    }

    .offer-image:hover img {
        transform: scale(1.1);
    }

    .offer-image i {
        color: var(--gray-400);
        font-size: 24px;
    }

    .offer-details h4 {
        font-size: 16px;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0 0 6px 0;
        line-height: 1.3;
    }

    .offer-details p {
        font-size: 14px;
        color: var(--gray-600);
        margin: 0;
        line-height: 1.4;
    }

    .offer-code {
        background: linear-gradient(135deg, var(--gray-100), var(--gray-200));
        color: var(--gray-800);
        padding: 8px 12px;
        border-radius: var(--border-radius);
        font-size: 14px;
        font-weight: 700;
        font-family: 'Courier New', monospace;
        border: 1px solid var(--gray-300);
        letter-spacing: 1px;
        transition: var(--transition);
    }

    .offer-code:hover {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
    }

    .offer-type {
        background: linear-gradient(135deg, var(--purple), var(--purple-light));
        color: var(--white);
        padding: 6px 12px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
        transition: var(--transition);
    }

    .offer-type:hover {
        transform: scale(1.08) rotate(-2deg);
        box-shadow: 0 8px 20px rgba(139, 92, 246, 0.5);
    }

    .user-category {
        background: linear-gradient(135deg, var(--suntop-orange), var(--suntop-orange-light));
        color: var(--white);
        padding: 6px 12px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(255, 107, 53, 0.3);
        transition: var(--transition);
    }

    .user-category:hover {
        transform: scale(1.08) rotate(2deg);
        box-shadow: 0 8px 20px rgba(255, 107, 53, 0.5);
    }

    .offer-discount {
        font-weight: 800;
        color: var(--suntop-orange);
        font-size: 18px;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: var(--border-radius);
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.75px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: var(--transition);
    }

    .status-badge.active {
        background: linear-gradient(135deg, var(--success), var(--success-light));
        color: var(--white);
        box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
    }

    .status-badge.active:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 20px rgba(34, 197, 94, 0.5);
    }

    .status-badge.inactive {
        background: linear-gradient(135deg, var(--gray-400), var(--gray-500));
        color: var(--white);
        box-shadow: 0 2px 8px rgba(156, 163, 175, 0.3);
    }

    .status-badge.inactive:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 20px rgba(156, 163, 175, 0.5);
    }

    .status-badge.expired {
        background: linear-gradient(135deg, var(--danger), var(--danger-light));
        color: var(--white);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
    }

    .status-badge.expired:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.5);
    }

    .status-badge.upcoming {
        background: linear-gradient(135deg, var(--warning), var(--warning-light));
        color: var(--white);
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    .status-badge.upcoming:hover {
        transform: scale(1.08) translateY(-2px);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.5);
    }

    .actions-dropdown {
        position: relative;
        display: inline-block;
    }

    .actions-btn {
        background: linear-gradient(135deg, var(--white), var(--gray-50));
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius);
        padding: 12px 16px;
        cursor: pointer;
        color: var(--gray-600);
        transition: var(--transition);
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        box-shadow: var(--shadow-sm);
    }

    .actions-btn:hover {
        background: linear-gradient(135deg, var(--gray-50), var(--gray-100));
        color: var(--suntop-orange);
        border-color: var(--suntop-orange);
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .actions-menu {
        position: absolute;
        top: calc(100% + 8px);
        left: 0;
        background: var(--white);
        border: 2px solid var(--gray-200);
        border-radius: var(--border-radius-lg);
        box-shadow: var(--shadow-xl);
        min-width: 180px;
        z-index: 1000;
        display: none;
        overflow: hidden;
    }

    .actions-menu.show {
        display: block;
        animation: fadeInUp 0.2s ease-out;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .actions-menu a,
    .actions-menu button {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
        padding: 14px 18px;
        text-decoration: none;
        color: var(--gray-700);
        border: none;
        background: none;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition);
        border-bottom: 1px solid var(--gray-100);
    }

    .actions-menu a:last-child,
    .actions-menu button:last-child {
        border-bottom: none;
    }

    .actions-menu a:hover,
    .actions-menu button:hover {
        background: linear-gradient(135deg, var(--gray-50), rgba(255, 107, 53, 0.05));
        color: var(--suntop-orange);
        transform: translateX(4px);
    }

    .actions-menu button.danger:hover {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.05));
        color: var(--danger);
    }

    .actions-menu i {
        font-size: 16px;
        width: 20px;
        text-align: center;
    }

        transform: rotate(45deg);
    }

    .custom-checkbox input:checked ~ .checkmark:after {
        display: block;
    }

    @media (max-width: 768px) {
        .filters-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .table-actions {
            justify-content: center;
        }

        .btn-secondary span {
            display: none;
        }

        .btn-secondary {
            padding: 8px 10px;
        }

        .data-table {
            font-size: 12px;
        }

        .data-table th,
        .data-table td {
            padding: 10px 8px;
        }
    }
</style>

<div class="page-header">
    <h1 class="page-title">
        <i class="fas fa-gift"></i>
        إدارة العروض
    </h1>
    <p class="page-subtitle">إدارة العروض الترويجية وأكواد الخصم</p>
</div>

<!-- Statistics Row -->
<div class="stats-row">
    <div class="stat-card orange">
        <div class="stat-header">
            <div class="stat-icon orange">
                <i class="fas fa-gift"></i>
            </div>
            <h3 class="stat-title">إجمالي العروض</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-tags"></i>
            <span>عرض مسجل</span>
        </div>
    </div>

    <div class="stat-card success">
        <div class="stat-header">
            <div class="stat-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <h3 class="stat-title">العروض النشطة</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['active_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-arrow-up"></i>
            <span>نشط حالياً</span>
        </div>
    </div>

    <div class="stat-card warning">
        <div class="stat-header">
            <div class="stat-icon warning">
                <i class="fas fa-clock"></i>
            </div>
            <h3 class="stat-title">العروض المنتهية</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['expired_offers']) }}</div>
        <div class="stat-change">
            <i class="fas fa-calendar-times"></i>
            <span>منتهي الصلاحية</span>
        </div>
    </div>

    <div class="stat-card info">
        <div class="stat-header">
            <div class="stat-icon info">
                <i class="fas fa-chart-line"></i>
            </div>
            <h3 class="stat-title">إجمالي الاستخدام</h3>
        </div>
        <div class="stat-value">{{ number_format($stats['total_usage']) }}</div>
        <div class="stat-change">
            <i class="fas fa-users"></i>
            <span>مرة استخدام</span>
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="filters-section">
    <h3 class="filters-title">
        <i class="fas fa-filter"></i>
        البحث والتصفية
    </h3>
    
    <form method="GET" action="{{ route('admin.offers.index') }}">
        <div class="filters-grid">
            <div class="form-group">
                <label>البحث</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="البحث في العنوان أو الوصف..." 
                       value="{{ request('search') }}">
            </div>

            <div class="form-group">
                <label>حالة العرض</label>
                <select name="status" class="form-control">
                    <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>جميع الحالات</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي الصلاحية</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>قادم</option>
                </select>
            </div>

            <div class="form-group">
                <label>فئة المستخدمين</label>
                <select name="user_category_id" class="form-control">
                    <option value="all" {{ request('user_category_id') === 'all' ? 'selected' : '' }}>جميع الفئات</option>
                    @if(isset($userCategories))
                        @foreach($userCategories as $category)
                            <option value="{{ $category->id }}" {{ request('user_category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->display_name }}
                            </option>
                        @endforeach
                    @endif
                </select>
            </div>

            <div class="form-group">
                <label>من تاريخ</label>
                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
            </div>

            <div class="form-group">
                <label>إلى تاريخ</label>
                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
            </div>

            <div class="form-group">
                <label>عدد النتائج</label>
                <select name="per_page" class="form-control">
                    <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                </select>
            </div>
        </div>

        <div class="filters-actions">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i>
                بحث
            </button>
            <a href="{{ route('admin.offers.index') }}" class="btn btn-secondary">
                <i class="fas fa-undo"></i>
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Table Section -->
<div class="table-section">
    <div class="table-header">
        <h3 class="table-title">
            <i class="fas fa-list"></i>
            قائمة العروض ({{ $offers->total() }})
        </h3>
        <div class="table-actions">
            <button class="btn-secondary" onclick="toggleBulkActions()">
                <i class="fas fa-tasks"></i>
                <span>إجراءات جماعية</span>
            </button>
            <button class="btn-secondary" onclick="exportOffers()">
                <i class="fas fa-download"></i>
                <span>تصدير</span>
            </button>
            <a href="{{ route('admin.offers.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                عرض جديد
            </a>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <label class="custom-checkbox">
            <input type="checkbox" id="selectAllOffers" onchange="toggleAllOffers()">
            <span class="checkmark"></span>
        </label>
        <span class="selected-count" id="selectedCount">0 عرض محدد</span>
        <div class="bulk-actions-dropdown">
            <button class="btn-secondary" onclick="bulkUpdateStatus('activate')">
                <i class="fas fa-check"></i> تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkUpdateStatus('deactivate')">
                <i class="fas fa-times"></i> إلغاء تفعيل
            </button>
            <button class="btn-secondary" onclick="bulkDelete()">
                <i class="fas fa-trash"></i> حذف
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>
                        <label class="custom-checkbox">
                            <input type="checkbox" onchange="toggleAllOffers()">
                            <span class="checkmark"></span>
                        </label>
                    </th>
                    <th>.</th>

                    <th>العرض</th>
                    <th>الخصم</th>
                    <th>فئة المستخدمين</th>
                    <th>النوع</th>
                    <th>صالح من</th>
                    <th>صالح حتى</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($offers as $offer)
                <tr>
                    <td>
                        <label class="custom-checkbox">
                            <input type="checkbox" class="offer-checkbox" value="{{ $offer->id }}" onchange="updateSelectedCount()">
                            <span class="checkmark"></span>
                        </label>
                    </td>
                    <td>
                        <div class="offer-info">
                            <div class="offer-image">
                                @if($offer->image_url)
                                    <img src="{{ asset('storage/' . $offer->image_url) }}" alt="{{ $offer->title }}">
                                @else
                                    <i class="fas fa-gift"></i>
                                @endif
                            </div>
                            <div class="offer-details">
                                <h4>{{ $offer->title }}</h4>
                                <p>{{ Str::limit($offer->description, 50) }}</p>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="offer-discount">
                            @if($offer->discount_percentage)
                                {{ $offer->discount_percentage }}%
                            @elseif($offer->discount_amount)
                                {{ number_format($offer->discount_amount) }} ج.م
                            @else
                                غير محدد
                            @endif
                        </span>
                    </td>
                    <td>
                        <span class="user-category">
                            {{ $offer->userCategory->display_name ?? 'جميع الفئات' }}
                        </span>
                    </td>
                    <td>
                        <span class="offer-type">
                            {{ $offer->type ?? 'غير محدد' }}
                        </span>
                    </td>
                    <td>{{ $offer->valid_from->format('Y-m-d') }}</td>
                    <td>{{ $offer->valid_until->format('Y-m-d') }}</td>
                    <td>
                        @php
                            $now = now();
                            if (!$offer->is_active) {
                                $status = 'inactive';
                                $statusText = 'غير نشط';
                            } elseif ($offer->valid_until < $now) {
                                $status = 'expired';
                                $statusText = 'منتهي';
                            } elseif ($offer->valid_from > $now) {
                                $status = 'upcoming';
                                $statusText = 'قادم';
                            } else {
                                $status = 'active';
                                $statusText = 'نشط';
                            }
                        @endphp
                        <span class="status-badge {{ $status }}">{{ $statusText }}</span>
                    </td>
                    <td>
                        <div class="actions-dropdown">
                            <button class="actions-btn" onclick="toggleActionsMenu({{ $offer->id }})">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="actions-menu" id="actionsMenu{{ $offer->id }}">
                                <a href="{{ route('admin.offers.edit', $offer->id) }}">
                                    <i class="fas fa-edit"></i> تعديل
                                </a>
                                <button onclick="deleteOffer({{ $offer->id }})" class="danger">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: var(--gray-500);">
                        <i class="fas fa-gift" style="font-size: 48px; opacity: 0.3; margin-bottom: 15px; display: block;"></i>
                        <p>لا توجد عروض</p>
                        <a href="{{ route('admin.offers.create') }}" class="btn btn-primary" style="margin-top: 15px;">
                            <i class="fas fa-plus"></i> إضافة أول عرض
                        </a>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($offers->hasPages())
    <div class="pagination-wrapper">
        <div class="pagination-info">
            <i class="fas fa-info-circle"></i>
            عرض {{ $offers->firstItem() }} إلى {{ $offers->lastItem() }} من {{ $offers->total() }} نتيجة
        </div>
        
        <div class="custom-pagination">
            @if ($offers->onFirstPage())
                <span class="pagination-nav-btn prev-btn disabled">
                    <i class="fas fa-chevron-right btn-icon"></i>
                    السابق
                </span>
            @else
                <a href="{{ $offers->previousPageUrl() }}" class="pagination-nav-btn prev-btn">
                    <i class="fas fa-chevron-right btn-icon"></i>
                    السابق
                </a>
            @endif

            <div class="pagination-numbers">
                @if($offers->currentPage() > 3)
                    <a href="{{ $offers->url(1) }}" class="page-number">1</a>
                    @if($offers->currentPage() > 4)
                        <span class="page-dots">...</span>
                    @endif
                @endif

                @foreach(range(max(1, $offers->currentPage() - 2), min($offers->lastPage(), $offers->currentPage() + 2)) as $page)
                    @if ($page == $offers->currentPage())
                        <span class="page-number active">{{ $page }}</span>
                    @else
                        <a href="{{ $offers->url($page) }}" class="page-number">{{ $page }}</a>
                    @endif
                @endforeach

                @if($offers->currentPage() < $offers->lastPage() - 2)
                    @if($offers->currentPage() < $offers->lastPage() - 3)
                        <span class="page-dots">...</span>
                    @endif
                    <a href="{{ $offers->url($offers->lastPage()) }}" class="page-number">{{ $offers->lastPage() }}</a>
                @endif
            </div>

            @if ($offers->hasMorePages())
                <a href="{{ $offers->nextPageUrl() }}" class="pagination-nav-btn next-btn">
                    التالي
                    <i class="fas fa-chevron-left btn-icon"></i>
                </a>
            @else
                <span class="pagination-nav-btn next-btn disabled">
                    التالي
                    <i class="fas fa-chevron-left btn-icon"></i>
                </span>
            @endif
        </div>
    </div>
    @endif
</div>

<script>
// Bulk actions functionality
let selectedOffers = [];

function toggleBulkActions() {
    const bulkActions = document.getElementById('bulkActions');
    bulkActions.classList.toggle('show');
}

function toggleAllOffers() {
    const checkboxes = document.querySelectorAll('.offer-checkbox');
    const selectAll = document.getElementById('selectAllOffers');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateSelectedCount();
}

function updateSelectedCount() {
    selectedOffers = Array.from(document.querySelectorAll('.offer-checkbox:checked')).map(cb => cb.value);
    document.getElementById('selectedCount').textContent = `${selectedOffers.length} عرض محدد`;
    
    const selectAll = document.getElementById('selectAllOffers');
    const checkboxes = document.querySelectorAll('.offer-checkbox');
    selectAll.checked = selectedOffers.length === checkboxes.length;
}

function toggleActionsMenu(offerId) {
    // Close all other menus
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `actionsMenu${offerId}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current menu
    const menu = document.getElementById(`actionsMenu${offerId}`);
    menu.classList.toggle('show');
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Offer management functions
function toggleOfferStatus(offerId, newStatus) {
    if (confirm('هل أنت متأكد من تغيير حالة العرض؟')) {
        fetch(`/admin/offers/${offerId}/toggle-status`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}

function deleteOffer(offerId) {
    if (confirm('هل أنت متأكد من حذف هذا العرض؟ لا يمكن التراجع عن هذا الإجراء.')) {
        fetch(`/admin/offers/${offerId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}

function bulkUpdateStatus(action) {
    if (selectedOffers.length === 0) {
        alert('يرجى تحديد العروض أولاً');
        return;
    }

    const actionText = action === 'activate' ? 'تفعيل' : 'إلغاء تفعيل';
    if (confirm(`هل أنت متأكد من ${actionText} ${selectedOffers.length} عرض؟`)) {
        fetch('/admin/offers/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: action,
                offer_ids: selectedOffers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}

function bulkDelete() {
    if (selectedOffers.length === 0) {
        alert('يرجى تحديد العروض أولاً');
        return;
    }

    if (confirm(`هل أنت متأكد من حذف ${selectedOffers.length} عرض؟ لا يمكن التراجع عن هذا الإجراء.`)) {
        fetch('/admin/offers/bulk-action', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'delete',
                offer_ids: selectedOffers
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('حدث خطأ: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ في الاتصال');
        });
    }
}

function exportOffers() {
    window.open('/admin/offers/export?' + new URLSearchParams(window.location.search), '_blank');
}
</script>
@endsection
