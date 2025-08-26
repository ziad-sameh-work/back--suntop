<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'لوحة إدارة SunTop')</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom CSS -->
    <style>
        :root {
            --suntop-orange: #FF6B35;
            --suntop-orange-light: #FF8B5A;
            --suntop-orange-dark: #E5521B;
            --suntop-blue: #4A90E2;
            --suntop-blue-light: #6BA3E8;
            --suntop-blue-dark: #357ABD;
            --dark-bg: #1A1D23;
            --dark-sidebar: #252A34;
            --dark-card: #2F3542;
            --white: #FFFFFF;
            --gray-50: #F8FAFC;
            --gray-100: #F1F5F9;
            --gray-200: #E2E8F0;
            --gray-300: #CBD5E1;
            --gray-400: #94A3B8;
            --gray-500: #64748B;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1E293B;
            --gray-900: #0F172A;
            --success: #10B981;
            --warning: #F59E0B;
            --danger: #EF4444;
            --sidebar-width: 280px;
            --header-height: 70px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            right: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, var(--dark-sidebar) 0%, var(--dark-bg) 100%);
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
        }

        .sidebar.collapsed {
            transform: translateX(100%);
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            color: var(--white);
            text-decoration: none;
        }

        .sidebar-logo i {
            font-size: 28px;
            color: var(--suntop-orange);
        }

        .sidebar-logo span {
            font-size: 24px;
            font-weight: 700;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-item {
            margin: 5px 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .nav-link:hover {
            background: rgba(255, 107, 53, 0.1);
            color: var(--white);
            transform: translateX(-5px);
        }

        .nav-link.active {
            background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
            color: var(--white);
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }

        .nav-link i {
            width: 24px;
            margin-left: 15px;
            text-align: center;
            font-size: 18px;
        }

        .nav-text {
            font-size: 15px;
            font-weight: 500;
        }

        .nav-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            background: #ef4444;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 11px;
            font-weight: 700;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(239, 68, 68, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
            }
        }

        /* Header Styles */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: var(--sidebar-width);
            height: var(--header-height);
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            z-index: 999;
            transition: right 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .header.expanded {
            right: 0;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
            padding: 0 30px;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .sidebar-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 20px;
            color: var(--gray-600);
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .sidebar-toggle:hover {
            background: var(--gray-100);
            color: var(--suntop-orange);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--gray-800);
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .notifications-btn,
        .profile-btn {
            position: relative;
            background: none;
            border: none;
            padding: 10px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
            color: var(--gray-600);
        }

        .notifications-btn:hover,
        .profile-btn:hover {
            background: var(--gray-100);
            color: var(--suntop-orange);
        }

        .notifications-btn i,
        .profile-btn i {
            font-size: 18px;
        }

        .notification-badge {
            position: absolute;
            top: 5px;
            right: 5px;
            background: var(--danger);
            color: var(--white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .profile-dropdown {
            position: relative;
        }

        .profile-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            min-width: 180px;
            z-index: 1000;
            display: none;
            margin-top: 5px;
        }

        .profile-menu.show {
            display: block;
        }

        .profile-menu-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            color: var(--gray-700);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            border-bottom: 1px solid var(--gray-100);
        }

        .profile-menu-item:last-child {
            border-bottom: none;
        }

        .profile-menu-item:hover {
            background: var(--gray-50);
            color: var(--suntop-orange);
        }

        .profile-menu-divider {
            height: 1px;
            background: var(--gray-200);
            margin: 8px 0;
        }

        /* Main Content */
        .main-content {
            margin-right: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 30px;
            min-height: calc(100vh - var(--header-height));
            transition: margin-right 0.3s ease;
        }

        .main-content.expanded {
            margin-right: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            :root {
                --sidebar-width: 100vw;
            }

            .sidebar {
                transform: translateX(100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .header {
                right: 0;
            }

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                margin-right: 0;
                padding: 20px 15px;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5);
                z-index: 999;
                opacity: 0;
                pointer-events: none;
                transition: opacity 0.3s ease;
            }

            .sidebar-overlay.show {
                opacity: 1;
                pointer-events: auto;
            }
        }

        @media (max-width: 480px) {
            .header-content {
                padding: 0 15px;
            }

            .page-title {
                font-size: 18px;
            }
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 107, 53, 0.3);
            border-radius: 50%;
            border-top-color: var(--suntop-orange);
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Utilities */
        .text-gradient {
            background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-blue) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .btn-gradient {
            background: linear-gradient(135deg, var(--suntop-orange) 0%, var(--suntop-orange-dark) 100%);
            border: none;
            color: var(--white);
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(255, 107, 53, 0.3);
        }
    </style>

    @stack('styles')
</head>
<body>
    <!-- Sidebar Overlay for Mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
                <i class="fas fa-sun"></i>
                <span>SunTop</span>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line"></i>
                    <span class="nav-text">لوحة التحكم</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i>
                    <span class="nav-text">المستخدمين</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.products.index') }}" class="nav-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                    <i class="fas fa-box"></i>
                    <span class="nav-text">المنتجات</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.orders.index') }}" class="nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-text">الطلبات</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.merchants.index') }}" class="nav-link {{ request()->routeIs('admin.merchants*') ? 'active' : '' }}">
                    <i class="fas fa-store"></i>
                    <span class="nav-text">التجار</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.offers.index') }}" class="nav-link {{ request()->routeIs('admin.offers*') ? 'active' : '' }}">
                    <i class="fas fa-gift"></i>
                    <span class="nav-text">العروض</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.loyalty.index') }}" class="nav-link {{ request()->routeIs('admin.loyalty*') ? 'active' : '' }}">
                    <i class="fas fa-star"></i>
                    <span class="nav-text">نقاط الولاء</span>
                </a>
            </div>

            <div class="nav-item">
                <a href="{{ route('admin.user-categories.index') }}" class="nav-link {{ request()->routeIs('admin.user-categories*') ? 'active' : '' }}">
                    <i class="fas fa-layer-group"></i>
                    <span class="nav-text">فئات المستخدمين</span>
                </a>
            </div>

                            <div class="nav-item">
                    <a href="{{ route('admin.analytics.index') }}" class="nav-link {{ request()->routeIs('admin.analytics*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span class="nav-text">التحليلات</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('admin.chats.index') }}" class="nav-link {{ request()->routeIs('admin.chats*') ? 'active' : '' }}">
                        <i class="fas fa-comments"></i>
                        <span class="nav-text">الدردشة والدعم</span>
                        @php
                            $unreadChats = \App\Models\Chat::where('admin_unread_count', '>', 0)->count();
                        @endphp
                        @if($unreadChats > 0)
                            <span class="nav-badge">{{ $unreadChats }}</span>
                        @endif
                    </a>
                </div>
        </nav>
    </div>

    <!-- Header -->
    <header class="header" id="header">
        <div class="header-content">
            <div class="header-left">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title">@yield('page-title', 'لوحة التحكم')</h1>
            </div>

            <div class="header-right">
                <!-- Notifications -->
                <button class="notifications-btn" id="notificationsBtn">
                    <i class="fas fa-bell"></i>
                    @if(isset($notifications_count) && $notifications_count > 0)
                        <span class="notification-badge">{{ $notifications_count }}</span>
                    @endif
                </button>

                <!-- Profile -->
                <div class="profile-dropdown">
                    <button class="profile-btn" id="profileBtn">
                        <i class="fas fa-user"></i>
                    </button>
                    <div class="profile-menu" id="profileMenu">
                        <a href="#" class="profile-menu-item">
                            <i class="fas fa-user"></i>
                            الملف الشخصي
                        </a>
                        <a href="#" class="profile-menu-item">
                            <i class="fas fa-cog"></i>
                            الإعدادات
                        </a>
                        <div class="profile-menu-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="profile-menu-item" style="background: none; border: none; width: 100%; text-align: right; cursor: pointer;">
                                <i class="fas fa-sign-out-alt"></i>
                                تسجيل الخروج
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        @yield('content')
    </main>

    <!-- JavaScript -->
    <script>
        // Sidebar toggle functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const header = document.getElementById('header');
        const mainContent = document.getElementById('mainContent');

        function toggleSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.toggle('show');
                sidebarOverlay.classList.toggle('show');
            } else {
                sidebar.classList.toggle('collapsed');
                header.classList.toggle('expanded');
                mainContent.classList.toggle('expanded');
            }
        }

        sidebarToggle.addEventListener('click', toggleSidebar);
        sidebarOverlay.addEventListener('click', toggleSidebar);

        // Close sidebar on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth > 768) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
            }
        });

        // CSRF Token for AJAX requests
        window.axios = window.axios || {};
        window.axios.defaults = window.axios.defaults || {};
        window.axios.defaults.headers = window.axios.defaults.headers || {};
        window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
        window.axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Notifications functionality
        document.getElementById('notificationsBtn').addEventListener('click', function() {
            // TODO: Implement notifications dropdown
            alert('تطبيق الإشعارات قيد التطوير');
        });

        // Profile dropdown functionality
        const profileBtn = document.getElementById('profileBtn');
        const profileMenu = document.getElementById('profileMenu');

        profileBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('show');
        });

        // Close profile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!profileBtn.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('show');
            }
        });

        // Auto-refresh data every 5 minutes
        setInterval(function() {
            if (typeof refreshDashboardData === 'function') {
                refreshDashboardData();
            }
        }, 300000);

        // Loading state helper
        function showLoading(element) {
            element.innerHTML = '<div class="loading-spinner"></div>';
        }

        function hideLoading(element, content) {
            element.innerHTML = content;
        }
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
