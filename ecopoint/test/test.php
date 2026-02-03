<?php
session_start();
include_once 'includes/config.php';

$notificationCount = 0;
if(isset($_SESSION['username'])){
    $uid = $_SESSION['uid'];
    $noti = $conn->query("
        SELECT COUNT(*) AS Noti 
        FROM record_points 
        WHERE p_noti = 1 AND uid = '$uid'
    ");
    $_fet_ = $noti->fetch_assoc();
    $notificationCount = $_fet_['Noti'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#10b981">
    <title>Eco Point - ระบบสะสมคะแนนเพื่อสิ่งแวดล้อม</title>
    
    <!-- Fonts & Icons -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #10b981;
            --primary-dark: #059669;
            --primary-light: #d1fae5;
            --secondary: #3b82f6;
            --accent: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1f2937;
            --light: #f9fafb;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-600: #4b5563;
            --gray-700: #374151;
            --white: #ffffff;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-md: 0 6px 12px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 25px -3px rgba(0, 0, 0, 0.1);
            --shadow-xl: 0 20px 40px -5px rgba(0, 0, 0, 0.1);
            --radius-sm: 0.375rem;
            --radius: 0.5rem;
            --radius-md: 0.75rem;
            --radius-lg: 1rem;
            --radius-xl: 1.5rem;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Prompt', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            overflow-x: hidden;
            min-height: 100vh;
        }

        /* App Container */
        .app-container {
            display: flex;
            min-height: 100vh;
            position: relative;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--white);
            border-right: 1px solid var(--gray-200);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 1000;
            transition: transform 0.3s ease;
            overflow-y: auto;
            box-shadow: var(--shadow-md);
        }

        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
        }

        .sidebar-header .logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .sidebar-header .logo-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .sidebar-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .sidebar-header p {
            font-size: 0.875rem;
            opacity: 0.9;
            margin: 0;
        }

        .nav-menu {
            padding: 1rem 0;
        }

        .nav-section {
            margin-bottom: 1.5rem;
        }

        .nav-title {
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--gray-600);
            padding: 0 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .nav-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: var(--transition);
            position: relative;
        }

        .nav-item:hover {
            background: var(--primary-light);
            color: var(--primary-dark);
        }

        .nav-item.active {
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 500;
        }

        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            background: var(--primary);
            border-radius: 0 var(--radius) var(--radius) 0;
        }

        .nav-icon {
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            font-size: 1.125rem;
        }

        .nav-badge {
            margin-left: auto;
            background: var(--accent);
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 9999px;
            min-width: 20px;
            text-align: center;
            font-weight: 600;
        }

        .sidebar-footer {
            padding: 1.5rem;
            background: var(--gray-100);
            margin-top: auto;
        }

        .eco-tip {
            background: var(--white);
            border-radius: var(--radius);
            padding: 1rem;
            border-left: 4px solid var(--warning);
        }

        .eco-tip i {
            color: var(--warning);
            margin-right: 0.5rem;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Bar */
        .top-bar {
            height: 70px;
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
            padding: 0 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
            box-shadow: var(--shadow-sm);
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-toggle:hover {
            background: var(--gray-100);
        }

        .search-box {
            flex: 1;
            max-width: 500px;
            margin-left: 2rem;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 1rem 0.75rem 3rem;
            border: 1px solid var(--gray-300);
            border-radius: var(--radius);
            font-family: 'Prompt', sans-serif;
            transition: var(--transition);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-600);
        }

        .top-bar-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .notification-btn {
            position: relative;
            background: none;
            border: none;
            width: 40px;
            height: 40px;
            border-radius: var(--radius);
            color: var(--gray-700);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .notification-btn:hover {
            background: var(--gray-100);
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: var(--accent);
            color: white;
            font-size: 0.75rem;
            width: 18px;
            height: 18px;
            border-radius: 9999px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid var(--white);
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: var(--radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .user-profile:hover {
            background: var(--gray-100);
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            overflow: hidden;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-600);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            width: 240px;
            background: var(--white);
            border-radius: var(--radius);
            box-shadow: var(--shadow-lg);
            padding: 0.5rem 0;
            margin-top: 0.5rem;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: var(--transition);
            z-index: 1000;
            border: 1px solid var(--gray-200);
        }

        .dropdown-menu.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--gray-700);
            text-decoration: none;
            transition: var(--transition);
        }

        .dropdown-item:hover {
            background: var(--gray-100);
            color: var(--primary);
        }

        .dropdown-item i {
            width: 20px;
            margin-right: 0.75rem;
        }

        /* Content Area */
        .content-area {
            flex: 1;
            padding: 2rem;
            background: var(--light);
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-title {
            font-size: 2rem;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .page-subtitle {
            color: var(--gray-600);
            font-size: 1rem;
        }

        /* Cards */
        .card {
            background: var(--white);
            border-radius: var(--radius-lg);
            border: 1px solid var(--gray-200);
            box-shadow: var(--shadow);
            transition: var(--transition);
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .card-header {
            padding: 1.5rem;
            border-bottom: 1px solid var(--gray-200);
            background: var(--white);
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--dark);
            margin: 0;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Auth Buttons */
        .auth-buttons {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: var(--radius);
            font-weight: 600;
            font-family: 'Prompt', sans-serif;
            cursor: pointer;
            transition: var(--transition);
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary);
            border: 2px solid var(--primary);
        }

        .btn-outline:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Mobile Search */
        .mobile-search {
            display: none;
            padding: 1rem;
            background: var(--white);
            border-bottom: 1px solid var(--gray-200);
        }

        /* Mobile Overlay */
        .mobile-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
        }

        .mobile-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
            }
            
            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }
            
            .mobile-search {
                display: block;
            }
            
            .search-box {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .top-bar {
                padding: 0 1rem;
            }
            
            .content-area {
                padding: 1rem;
            }
            
            .page-title {
                font-size: 1.5rem;
            }
            
            .user-info {
                display: none;
            }
            
            .dropdown-menu {
                position: fixed;
                top: auto;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                border-radius: var(--radius-lg) var(--radius-lg) 0 0;
                margin: 0;
                transform: translateY(100%);
            }
            
            .dropdown-menu.show {
                transform: translateY(0);
            }
        }

        @media (max-width: 640px) {
            .auth-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
            }
            
            .card {
                border-radius: var(--radius);
            }
            
            .card-header,
            .card-body {
                padding: 1rem;
            }
        }

        @media (max-width: 480px) {
            .sidebar {
                width: 100%;
            }
            
            .top-bar-actions {
                gap: 0.5rem;
            }
            
            .notification-btn,
            .user-profile {
                width: 36px;
                height: 36px;
            }
        }

        /* Loading */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 200px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid var(--gray-200);
            border-top-color: var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Utilities */
        .text-success { color: var(--success); }
        .text-warning { color: var(--warning); }
        .text-danger { color: var(--danger); }
        .text-muted { color: var(--gray-600); }

        .bg-success { background: var(--success); }
        .bg-warning { background: var(--warning); }
        .bg-danger { background: var(--danger); }
        .bg-light { background: var(--light); }

        .rounded { border-radius: var(--radius); }
        .rounded-lg { border-radius: var(--radius-lg); }
        .rounded-full { border-radius: 9999px; }

        .shadow { box-shadow: var(--shadow); }
        .shadow-lg { box-shadow: var(--shadow-lg); }

        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }

        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 0.75rem; }
        .mt-4 { margin-top: 1rem; }

        .p-4 { padding: 1rem; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }

        .text-center { text-align: center; }
        .text-right { text-align: right; }

        .d-flex { display: flex; }
        .d-none { display: none; }
        .d-block { display: block; }

        .align-items-center { align-items: center; }
        .justify-content-between { justify-content: space-between; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }

        .w-100 { width: 100%; }
        .h-100 { height: 100%; }

        .overflow-hidden { overflow: hidden; }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Mobile Overlay -->
        <div class="mobile-overlay" id="mobileOverlay"></div>

        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div>
                        <h1>Eco Point</h1>
                        <p>ระบบสะสมคะแนนเพื่อโลกสีเขียว</p>
                    </div>
                </div>
            </div>

            <nav class="nav-menu">
                <div class="nav-section">
                    <div class="nav-title">เมนูหลัก</div>
                    <a href="?page=home" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'home') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-home"></i></span>
                        <span>หน้าหลัก</span>
                    </a>
                    <a href="?page=mypoint" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'mypoint') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-star"></i></span>
                        <span>คะแนนของฉัน</span>
                        <?php if($notificationCount > 0): ?>
                            <span class="nav-badge"><?= $notificationCount ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="?page=reward" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'reward') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-gift"></i></span>
                        <span>แลกของรางวัล</span>
                    </a>
                </div>

                <?php if(isset($_SESSION['username']) && isset($_SESSION['role']) && $_SESSION['role'] === 'Super admin'): ?>
                <div class="nav-section">
                    <div class="nav-title">ผู้ดูแลระบบ</div>
                    <a href="?page=addpoint" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'addpoint') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-plus-circle"></i></span>
                        <span>เพิ่มคะแนน</span>
                    </a>
                    <a href="?page=request" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'request') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="bi bi-bag-check-fill"></i></span>
                        <span>จัดการคำขอ</span>
                    </a>
                    <a href="?page=member" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'member') ? 'active' : '' ?>">
                        <span class="nav-icon"><i class="fas fa-cogs"></i></span>
                        <span>จัดการระบบ</span>
                    </a>
                </div>
                <?php endif; ?>

                <div class="nav-section">
                    <div class="nav-title">บัญชีผู้ใช้</div>
                    <?php if(isset($_SESSION['username'])): ?>
                        <a href="?page=profile" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'profile') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-user"></i></span>
                            <span>โปรไฟล์ของฉัน</span>
                        </a>
                        <a href="function/logout.php" class="nav-item">
                            <span class="nav-icon"><i class="fas fa-sign-out-alt"></i></span>
                            <span>ออกจากระบบ</span>
                        </a>
                    <?php else: ?>
                        <a href="?page=login" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'login') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-sign-in-alt"></i></span>
                            <span>เข้าสู่ระบบ</span>
                        </a>
                        <a href="?page=register" class="nav-item <?= (isset($_GET['page']) && $_GET['page'] == 'register') ? 'active' : '' ?>">
                            <span class="nav-icon"><i class="fas fa-user-plus"></i></span>
                            <span>สมัครสมาชิก</span>
                        </a>
                    <?php endif; ?>
                </div>
            </nav>

            <div class="sidebar-footer">
                <div class="eco-tip">
                    <p class="mb-2"><i class="fas fa-lightbulb"></i> <strong>เคล็ดลับประจำวัน</strong></p>
                    <p class="text-muted mb-0">พกแก้วน้ำส่วนตัววันนี้ +10 คะแนน!</p>
                </div>
                <div class="text-center mt-3">
                    <small class="text-muted">© 2024 Eco Point</small>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Top Bar -->
            <header class="top-bar">
                <div class="d-flex align-items-center">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="search-box d-none d-lg-block">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="ค้นหากิจกรรม รางวัล หรือผู้ใช้...">
                    </div>
                </div>

                <div class="top-bar-actions">
                    <?php if(isset($_SESSION['username'])): ?>
                        <button class="notification-btn" id="notificationBtn">
                            <i class="fas fa-bell"></i>
                            <?php if($notificationCount > 0): ?>
                                <span class="notification-badge"><?= $notificationCount ?></span>
                            <?php endif; ?>
                        </button>

                        <div class="dropdown">
                            <div class="user-profile" id="userProfileBtn">
                                <div class="avatar">
                                    <?php if(!empty($_SESSION['image'])): ?>
                                        <img src="uploads/users/<?= htmlspecialchars($_SESSION['image']) ?>" alt="Profile">
                                    <?php else: ?>
                                        <?= substr($_SESSION['firstname'] ?? '', 0, 1) ?>
                                    <?php endif; ?>
                                </div>
                                <div class="user-info">
                                    <span class="user-name"><?= htmlspecialchars($_SESSION['firstname'] ?? '') ?></span>
                                    <span class="user-role"><?= htmlspecialchars($_SESSION['role'] ?? '') ?></span>
                                </div>
                                <i class="fas fa-chevron-down"></i>
                            </div>
                            <div class="dropdown-menu" id="dropdownMenu">
                                <a href="?page=profile" class="dropdown-item">
                                    <i class="fas fa-user"></i> โปรไฟล์ของฉัน
                                </a>
                                <a href="?page=edit-profile" class="dropdown-item">
                                    <i class="fas fa-cog"></i> ตั้งค่าบัญชี
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="function/logout.php" class="dropdown-item text-danger">
                                    <i class="fas fa-sign-out-alt"></i> ออกจากระบบ
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="auth-buttons">
                            <a href="?page=login" class="btn btn-outline">
                                <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                            </a>
                            <a href="?page=register" class="btn btn-primary">
                                <i class="fas fa-user-plus"></i> สมัครสมาชิก
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </header>

            <!-- Mobile Search -->
            <div class="mobile-search d-lg-none">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="ค้นหากิจกรรม รางวัล หรือผู้ใช้...">
                </div>
            </div>

            <!-- Content Area -->
            <main class="content-area">
                <div class="container-fluid">
                    <div class="page-header mb-6">
                        <?php
                        $page = $_GET['page'] ?? 'home';
                        $titles = [
                            'home' => 'หน้าหลัก',
                            'mypoint' => 'คะแนนของฉัน',
                            'reward' => 'แลกของรางวัล',
                            'activities' => 'กิจกรรมรักษ์โลก',
                            'profile' => 'โปรไฟล์ของฉัน',
                            'login' => 'เข้าสู่ระบบ',
                            'register' => 'สมัครสมาชิก',
                            'addpoint' => 'เพิ่มคะแนน',
                            'admin' => 'จัดการระบบ',
                            'edit-profile' => 'แก้ไขโปรไฟล์',
                            'about' => 'เกี่ยวกับเรา',
                            'history' => 'ประวัติการใช้งาน'
                        ];
                        ?>
                        <h1 class="page-title"><?= $titles[$page] ?? 'Eco Point' ?></h1>
                        <p class="page-subtitle"><?= $page == 'home' ? 'ยินดีต้อนรับสู่ระบบ Eco Point' : '' ?></p>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <?php
                            switch ($page) {
                                case 'home':
                                    include 'pages/home.php';
                                    break;
                                case 'mypoint':
                                    include 'pages/point.php';
                                    break;
                                case 'reward':
                                    include 'pages/rewards.php';
                                    break;
                                case 'activities':
                                    include 'pages/activities.php';
                                    break;
                                case 'profile':
                                    include 'pages/profile.php';
                                    break;
                                case 'login':
                                    include 'pages/login.php';
                                    break;
                                case 'register':
                                    include 'pages/register.php';
                                    break;
                                case 'addpoint':
                                    include 'pages/add-point.php';
                                    break;
                                case 'member':
                                    include 'pages/member.php';
                                    break;
                                case 'edit-profile':
                                    include 'pages/edit-profile.php';
                                    break;
                                case 'about':
                                    include 'pages/about.php';
                                    break;
                                case 'request':
                                    include 'pages/request.php';
                                    break;
                                default:
                                    if ($page === '' || $page === null) {
                                        include 'pages/home.php';
                                    } else {
                                        include 'pages/404.php';
                                    }
                                    break;
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const menuToggle = document.getElementById('menuToggle');
            const mobileOverlay = document.getElementById('mobileOverlay');
            const userProfileBtn = document.getElementById('userProfileBtn');
            const dropdownMenu = document.getElementById('dropdownMenu');
            const notificationBtn = document.getElementById('notificationBtn');

            // Toggle sidebar on mobile
            function toggleSidebar() {
                sidebar.classList.toggle('show');
                mobileOverlay.classList.toggle('show');
                document.body.style.overflow = sidebar.classList.contains('show') ? 'hidden' : '';
            }

            // Toggle dropdown
            function toggleDropdown() {
                dropdownMenu.classList.toggle('show');
            }

            // Close dropdown when clicking outside
            function closeDropdown(e) {
                if (!userProfileBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.classList.remove('show');
                }
            }

            // Close sidebar on mobile
            function closeSidebar() {
                if (window.innerWidth <= 1024) {
                    sidebar.classList.remove('show');
                    mobileOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
            }

            // Event Listeners
            menuToggle.addEventListener('click', toggleSidebar);
            mobileOverlay.addEventListener('click', closeSidebar);

            if (userProfileBtn) {
                userProfileBtn.addEventListener('click', toggleDropdown);
                document.addEventListener('click', closeDropdown);
            }

            if (notificationBtn) {
                notificationBtn.addEventListener('click', function() {
                    window.location.href = '?page=mypoint';
                });
            }

            // Close sidebar when clicking nav items on mobile
            document.querySelectorAll('.nav-item').forEach(item => {
                item.addEventListener('click', closeSidebar);
            });

            // Handle window resize
            window.addEventListener('resize', function() {
                if (window.innerWidth > 1024) {
                    closeSidebar();
                }
            });

            // Notification badge animation
            const notificationBadge = document.querySelector('.notification-badge');
            if (notificationBadge) {
                setInterval(() => {
                    notificationBadge.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        notificationBadge.style.transform = 'scale(1)';
                    }, 300);
                }, 3000);
            }
        });
    </script>
</body>
</html>