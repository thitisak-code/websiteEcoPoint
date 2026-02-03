<?php
// เชื่อมต่อฐานข้อมูล (สมมติว่าใช้ไฟล์ config.php เดิม)
include_once 'includes/config.php';

// ตรวจสอบ Session (ถ้ามีระบบ Login)
$allow_roles = ['Super admin'];

if (
    !isset($_SESSION['username'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], $allow_roles)
) {
    echo "<script>window.location='index.php?page=home';</script>";
    exit();
}

// --------------------------------------------------------
// SQL QUERIES: ดึงข้อมูลมาแสดงผล
// --------------------------------------------------------

// 1. นับจำนวนผู้ใช้งานทั้งหมด
$q_users = $conn->query("SELECT COUNT(*) as total FROM users WHERE u_role = 'member'");
$total_users = $q_users->fetch_assoc()['total'];

// 2. คำขอแลกของรางวัลที่รออนุมัติ (Pending Requests)
$q_req_pending = $conn->query("SELECT COUNT(*) as total FROM request WHERE req_status = 'panding'");
$total_req_pending = $q_req_pending->fetch_assoc()['total'];

// 3. กิจกรรมที่รอตรวจสอบ (Pending Activities)
$q_ac_pending = $conn->query("SELECT COUNT(*) as total FROM record_ac WHERE status = 'panding'");
$total_ac_pending = $q_ac_pending->fetch_assoc()['total'];

// 4. คะแนนรวมทั้งหมดในระบบที่แจกไปแล้ว
$q_points = $conn->query("SELECT SUM(p_total) as total FROM record_points");
$total_points_given = $q_points->fetch_assoc()['total'] ?? 0;

// 5. ดึงข้อมูลกราฟ: สถานะการแลกของรางวัล (Approved vs Pending)
$q_chart_req = $conn->query("SELECT req_status, COUNT(*) as count FROM request GROUP BY req_status");
$chart_req_data = [];
while($row = $q_chart_req->fetch_assoc()) {
    $chart_req_data[$row['req_status']] = $row['count'];
}

// 6. ดึงข้อมูล 5 อันดับข่าวที่มีคนดูเยอะที่สุด
$q_top_news = $conn->query("SELECT title, views FROM news ORDER BY views DESC LIMIT 5");

// 7. รายการขอแลกรางวัลล่าสุด (5 รายการ)
$q_recent_req = $conn->query("
    SELECT r.*, u.firstname, u.lastname, rw.rw_name, rw.rw_image 
    FROM request r 
    JOIN users u ON r.uid = u.uid 
    JOIN rewards rw ON r.rw_id = rw.rw_id 
    ORDER BY r.req_date DESC LIMIT 5
");

// 8. ผู้ใช้งานที่มีคะแนนสูงสุด (Top Spenders/Savers)
$q_top_users = $conn->query("SELECT firstname, lastname, u_total_point, image FROM users ORDER BY u_total_point DESC LIMIT 5");

// 9. ข้อมูลสถิติเพิ่มเติม
$q_total_rewards = $conn->query("SELECT COUNT(*) as total FROM rewards");
$total_rewards = $q_total_rewards->fetch_assoc()['total'] ?? 0;

$q_total_news = $conn->query("SELECT COUNT(*) as total FROM news");
$total_news = $q_total_news->fetch_assoc()['total'] ?? 0;

// 10. คะแนนเฉลี่ยต่อผู้ใช้
$q_avg_points = $conn->query("SELECT AVG(u_total_point) as avg_points FROM users WHERE u_role = 'member'");
$avg_points = round($q_avg_points->fetch_assoc()['avg_points'] ?? 0, 1);

?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Point - Admin Dashboard</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #134e5e;
            --secondary-color: #71b280;
            --accent-color: #198754;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #0dcaf0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --gray-color: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --box-shadow: 0 6px 15px rgba(0, 0, 0, 0.08);
            --box-shadow-light: 0 4px 8px rgba(0, 0, 0, 0.05);
            --transition: all 0.3s ease;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: var(--light);
            color: var(--dark);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
        }
        
        /* Header Styles */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .header-left h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .header-left h1 i {
            color: var(--accent-color);
        }
        
        .header-left p {
            color: var(--gray-color);
            margin: 8px 0 0;
            font-size: 15px;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 25px;
        }
        
        .date-display {
            background: white;
            padding: 12px 20px;
            border-radius: 50px;
            box-shadow: var(--box-shadow-light);
            font-weight: 500;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .date-display i {
            color: var(--accent-color);
        }
        
        .admin-profile {
            display: flex;
            align-items: center;
            gap: 15px;
            background-color: white;
            padding: 10px 20px;
            border-radius: 50px;
            box-shadow: var(--box-shadow-light);
            transition: var(--transition);
        }
        
        .admin-profile:hover {
            box-shadow: var(--box-shadow);
        }
        
        .admin-avatar {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 20px;
        }
        
        .admin-info h4 {
            font-size: 16px;
            margin: 0;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .admin-info p {
            font-size: 13px;
            color: var(--gray-color);
            margin: 0;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
            margin-bottom: 35px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow-light);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            border-left: 5px solid;
        }
        
        .stat-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--box-shadow);
        }
        
        .stat-card.users {
            border-left-color: var(--info-color);
        }
        
        .stat-card.pending-rewards {
            border-left-color: var(--danger-color);
        }
        
        .stat-card.pending-activities {
            border-left-color: var(--warning-color);
        }
        
        .stat-card.points {
            border-left-color: var(--accent-color);
        }
        
        .stat-card.rewards {
            border-left-color: #9d4edd;
        }
        
        .stat-card.news {
            border-left-color: #4361ee;
        }
        
        .stat-card.avg-points {
            border-left-color: #ff6b6b;
        }
        
        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
        }
        
        .stat-card.users .stat-icon {
            background-color: rgba(13, 202, 240, 0.15);
            color: var(--info-color);
        }
        
        .stat-card.pending-rewards .stat-icon {
            background-color: rgba(220, 53, 69, 0.15);
            color: var(--danger-color);
        }
        
        .stat-card.pending-activities .stat-icon {
            background-color: rgba(255, 193, 7, 0.15);
            color: var(--warning-color);
        }
        
        .stat-card.points .stat-icon {
            background-color: rgba(25, 135, 84, 0.15);
            color: var(--accent-color);
        }
        
        .stat-card.rewards .stat-icon {
            background-color: rgba(157, 78, 221, 0.15);
            color: #9d4edd;
        }
        
        .stat-card.news .stat-icon {
            background-color: rgba(67, 97, 238, 0.15);
            color: #4361ee;
        }
        
        .stat-card.avg-points .stat-icon {
            background-color: rgba(255, 107, 107, 0.15);
            color: #ff6b6b;
        }
        
        .stat-info h3 {
            font-size: 36px;
            font-weight: 700;
            margin: 0;
            line-height: 1;
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--gray-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .stat-info p {
            color: var(--gray-color);
            margin: 10px 0 0;
            font-size: 16px;
            font-weight: 500;
        }
        
        .stat-trend {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: 14px;
            padding: 6px 12px;
            border-radius: 20px;
            margin-top: 12px;
            font-weight: 500;
        }
        
        .stat-trend.up {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--accent-color);
        }
        
        .stat-trend.down {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        /* Charts Section */
        .charts-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
            margin-bottom: 35px;
        }
        
        @media (max-width: 1200px) {
            .charts-section {
                grid-template-columns: 1fr;
            }
        }
        
        .chart-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--box-shadow-light);
            transition: var(--transition);
        }
        
        .chart-card:hover {
            box-shadow: var(--box-shadow);
        }
        
        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
            width: 100%;
        }
        
        /* Tables Section */
        .tables-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 25px;
        }
        
        @media (max-width: 1200px) {
            .tables-section {
                grid-template-columns: 1fr;
            }
        }
        
        .table-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow-light);
            overflow: hidden;
            transition: var(--transition);
        }
        
        .table-card:hover {
            box-shadow: var(--box-shadow);
        }
        
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 25px;
            border-bottom: 1px solid var(--light-gray);
            background-color: rgba(248, 249, 250, 0.7);
        }
        
        .table-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--dark-color);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .table-header h3 i {
            color: var(--accent-color);
        }
        
        .btn-view-all {
            background-color: transparent;
            border: 1px solid var(--accent-color);
            color: var(--accent-color);
            padding: 8px 18px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-view-all:hover {
            background-color: var(--accent-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .table-body {
            padding: 0;
        }
        
        .data-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .data-table thead {
            background-color: var(--light-gray);
        }
        
        .data-table th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--gray-color);
            font-size: 14px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .data-table tbody tr {
            border-bottom: 1px solid #f1f1f1;
            transition: var(--transition);
        }
        
        .data-table tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.03);
        }
        
        .data-table td {
            padding: 18px 20px;
            font-size: 14.5px;
        }
        
        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            background: linear-gradient(135deg, #f1f1f1 0%, #e1e1e1 100%);
            border: 2px solid white;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .reward-cell {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .reward-image {
            width: 45px;
            height: 45px;
            border-radius: 8px;
            object-fit: cover;
            background-color: #f8f9fa;
            border: 2px solid white;
            box-shadow: 0 3px 6px rgba(0,0,0,0.05);
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            min-width: 100px;
            text-align: center;
        }
        
        .status-pending {
            background-color: rgba(255, 193, 7, 0.15);
            color: #b58a00;
            border: 1px solid rgba(255, 193, 7, 0.3);
        }
        
        .status-approved {
            background-color: rgba(25, 135, 84, 0.15);
            color: var(--accent-color);
            border: 1px solid rgba(25, 135, 84, 0.3);
        }
        
        .action-buttons {
            display: flex;
            gap: 8px;
        }
        
        .btn-action {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: var(--transition);
            font-size: 14px;
        }
        
        .btn-approve {
            background-color: rgba(25, 135, 84, 0.1);
            color: var(--accent-color);
        }
        
        .btn-approve:hover {
            background-color: var(--accent-color);
            color: white;
            transform: scale(1.1);
        }
        
        .btn-reject {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--danger-color);
        }
        
        .btn-reject:hover {
            background-color: var(--danger-color);
            color: white;
            transform: scale(1.1);
        }
        
        /* Top Users List */
        .users-list {
            list-style: none;
            padding: 0;
        }
        
        .user-item {
            display: flex;
            align-items: center;
            padding: 18px 25px;
            border-bottom: 1px solid #f1f1f1;
            transition: var(--transition);
        }
        
        .user-item:hover {
            background-color: rgba(25, 135, 84, 0.03);
        }
        
        .user-item:last-child {
            border-bottom: none;
        }
        
        .user-rank {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            margin-right: 15px;
            font-size: 15px;
            border: 2px solid white;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        
        .rank-1 {
            background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
            color: #8a6d00;
        }
        
        .rank-2 {
            background: linear-gradient(135deg, #C0C0C0 0%, #A0A0A0 100%);
            color: #5a5a5a;
        }
        
        .rank-3 {
            background: linear-gradient(135deg, #CD7F32 0%, #A05A1B 100%);
            color: #5a3a12;
        }
        
        .rank-other {
            background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
            color: var(--gray-color);
        }
        
        .user-info {
            flex: 1;
        }
        
        .user-info h4 {
            font-size: 15px;
            font-weight: 600;
            margin: 0 0 4px;
        }
        
        .user-info p {
            font-size: 13px;
            color: var(--gray-color);
            margin: 0;
        }
        
        .user-points {
            font-weight: 700;
            color: var(--accent-color);
            font-size: 16px;
            background-color: rgba(25, 135, 84, 0.1);
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        /* Footer */
        .dashboard-footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid rgba(0, 0, 0, 0.08);
            text-align: center;
            color: var(--gray-color);
            font-size: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .footer-stats {
            display: flex;
            gap: 20px;
            font-size: 13px;
        }
        
        .footer-stat {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--gray-color);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }
            
            .header-right {
                width: 100%;
                justify-content: space-between;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-card {
                padding: 20px;
            }
            
            .header-right {
                flex-direction: column;
                gap: 15px;
            }
            
            .date-display, .admin-profile {
                width: 100%;
                justify-content: center;
            }
            
            .footer-stats {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="dashboard-header">
        <div class="header-left">
            <h1><i class="fas fa-tachometer-alt"></i> แดชบอร์ดระบบ Eco Point</h1>
            <p>ภาพรวมและการจัดการข้อมูลระบบสะสมแต้มรักษ์โลก</p>
        </div>
        
        <div class="header-right">
            <div class="date-display">
                <i class="far fa-calendar-alt"></i>
                <span id="current-date"></span>
            </div>
            
            <div class="admin-profile">
                <div class="admin-avatar">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div class="admin-info">
                    <h4>ผู้ดูแลระบบ</h4>
                    <p>Super Admin</p>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card users">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_users); ?></h3>
                <p>สมาชิกทั้งหมด</p>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 12% จากเดือนที่แล้ว
                </span>
            </div>
        </div>
        
        <div class="stat-card pending-rewards">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-gift"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_req_pending); ?></h3>
                <p>รอแลกรางวัล</p>
                <span class="stat-trend up">
                    <i class="fas fa-exclamation-circle"></i> ต้องการดำเนินการ
                </span>
            </div>
        </div>
        
        <div class="stat-card pending-activities">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_ac_pending); ?></h3>
                <p>รอตรวจกิจกรรม</p>
                <span class="stat-trend up">
                    <i class="fas fa-clock"></i> รอการตรวจสอบ
                </span>
            </div>
        </div>
        
        <div class="stat-card points">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_points_given); ?></h3>
                <p>แต้มที่แจกจ่าย</p>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 5% จากเดือนที่แล้ว
                </span>
            </div>
        </div>
        
        <div class="stat-card rewards">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-award"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_rewards); ?></h3>
                <p>ของรางวัลทั้งหมด</p>
                <span class="stat-trend down">
                    <i class="fas fa-arrow-down"></i> 2% จากเดือนที่แล้ว
                </span>
            </div>
</div>
        
        <div class="stat-card news">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-newspaper"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($total_news); ?></h3>
                <p>ข่าวสารทั้งหมด</p>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 8% จากเดือนที่แล้ว
                </span>
            </div>
        </div>
        
        <div class="stat-card avg-points">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($avg_points, 1); ?></h3>
                <p>คะแนนเฉลี่ยต่อผู้ใช้</p>
                <span class="stat-trend up">
                    <i class="fas fa-arrow-up"></i> 3% จากเดือนที่แล้ว
                </span>
            </div>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="charts-section">
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> ยอดเข้าชมข่าวสาร (Top 5)</h3>
            </div>
            <div class="chart-container">
                <canvas id="newsChart"></canvas>
            </div>
        </div>
        
        <div class="chart-card">
            <div class="chart-header">
                <h3><i class="fas fa-chart-pie"></i> สถานะการแลกของรางวัล</h3>
            </div>
            <div class="chart-container">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Tables Section -->
    <div class="tables-section">
        <!-- Recent Requests Table -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-exchange-alt"></i> รายการแลกของรางวัลล่าสุด</h3>
                <a href="#" class="btn-view-all">
                    <i class="fas fa-list"></i> ดูทั้งหมด
                </a>
            </div>
            <div class="table-body">
                <?php if ($q_recent_req->num_rows > 0): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ผู้ใช้งาน</th>
                            <th>ของรางวัล</th>
                            <th>ราคา (แต้ม)</th>
                            <th>สถานะ</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 0;
                        while($req = $q_recent_req->fetch_assoc()) { 
                            $counter++;
                            $status_class = ($req['req_status'] == 'approved') ? 'status-approved' : 'status-pending';
                            $status_text = ($req['req_status'] == 'panding') ? 'รออนุมัติ' : 'อนุมัติแล้ว';
                            $user_img = !empty($req['image']) ? "uploads/users/".$req['image'] : "https://ui-avatars.com/api/?name=".$req['firstname']."&background=".substr(md5($req['firstname']), 0, 6)."&color=fff";
                        ?>
                        <tr>
                            <td>
                                <div class="user-cell">
                                    <img src="<?php echo $user_img; ?>" class="user-avatar" alt="User">
                                    <div>
                                        <div class="fw-bold"><?php echo $req['firstname'] . ' ' . $req['lastname']; ?></div>
                                        <div class="text-muted small"><?php echo date('d/m/Y', strtotime($req['req_date'])); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="reward-cell">
                                    <img src="uploads/rewards/<?php echo $req['rw_image']; ?>" class="reward-image" alt="Reward">
                                    <div><?php echo $req['rw_name']; ?></div>
                                </div>
                            </td>
                            <td class="fw-bold text-danger">-<?php echo $req['req_price']; ?></td>
                            <td>
                                <span class="status-badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if($req['req_status'] == 'panding'): ?>
                                    <button class="btn-action btn-approve" title="อนุมัติ">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn-action btn-reject" title="ปฏิเสธ">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    <?php else: ?>
                                    <span class="text-success small"><i class="fas fa-check-circle"></i> เรียบร้อย</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>ไม่มีข้อมูลรายการแลกรางวัล</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Top Users List -->
        <div class="table-card">
            <div class="table-header">
                <h3><i class="fas fa-trophy"></i> Top Users (แต้มสะสม)</h3>
            </div>
            <div class="table-body">
                <?php if ($q_top_users->num_rows > 0): ?>
                <ul class="users-list">
                    <?php 
                    $rank = 1;
                    $q_top_users->data_seek(0); // Reset pointer
                    while($user = $q_top_users->fetch_assoc()) { 
                        $img = !empty($user['image']) ? "uploads/users/".$user['image'] : "https://ui-avatars.com/api/?name=".$user['firstname']."&background=".substr(md5($user['firstname']), 0, 6)."&color=fff";
                        $rank_class = $rank == 1 ? 'rank-1' : ($rank == 2 ? 'rank-2' : ($rank == 3 ? 'rank-3' : 'rank-other'));
                    ?>
                    <li class="user-item">
                        <div class="user-rank <?php echo $rank_class; ?>">
                            <?php echo $rank; ?>
                        </div>
                        <img src="<?php echo $img; ?>" class="user-avatar" alt="User">
                        <div class="user-info">
                            <h4><?php echo $user['firstname'] . ' ' . $user['lastname']; ?></h4>
                            <p>Member</p>
                        </div>
                        <div class="user-points"><?php echo number_format($user['u_total_point']); ?> P</div>
                    </li>
                    <?php 
                        $rank++;
                    } 
                    ?>
                </ul>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users"></i>
                    <p>ไม่มีข้อมูลผู้ใช้งาน</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Footer -->
    <footer class="dashboard-footer">
        <div class="footer-stats">
            <div class="footer-stat">
                <i class="fas fa-database"></i>
                <span>ข้อมูลอัปเดตล่าสุด: <?php echo date('H:i'); ?> น.</span>
            </div>
            <div class="footer-stat">
                <i class="fas fa-server"></i>
                <span>สถานะ: <span class="text-success">ปกติ</span></span>
            </div>
        </div>
        <p>ระบบจัดการ Eco Point &copy; <?php echo date('Y'); ?> | พัฒนาโดยทีมงานรักษ์โลก</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // แสดงวันที่ปัจจุบัน
        const now = new Date();
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        document.getElementById('current-date').textContent = now.toLocaleDateString('th-TH', options);
        
        // ข้อมูลสำหรับกราฟ
        const chartReqData = <?php echo json_encode($chart_req_data); ?>;
        const newsLabels = [];
        const newsData = [];

        <?php 
        $q_top_news->data_seek(0); // Reset pointer
        while($n = $q_top_news->fetch_assoc()){
            // ตัดชื่อข่าวถ้ายาวเกิน
            $title = strlen($n['title']) > 20 ? substr($n['title'], 0, 20) . '...' : $n['title'];
            echo "newsLabels.push('".addslashes($title)."');";
            echo "newsData.push(".$n['views'].");";
        }
        ?>

        // Chart 1: News Views (Bar)
        const newsCtx = document.getElementById('newsChart').getContext('2d');
        const newsChart = new Chart(newsCtx, {
            type: 'bar',
            data: {
                labels: newsLabels,
                datasets: [{
                    label: 'จำนวนการเข้าชม',
                    data: newsData,
                    backgroundColor: [
                        'rgba(67, 97, 238, 0.8)',
                        'rgba(58, 12, 163, 0.8)',
                        'rgba(114, 9, 183, 0.8)',
                        'rgba(247, 37, 133, 0.8)',
                        'rgba(76, 201, 240, 0.8)'
                    ],
                    borderRadius: 10,
                    borderWidth: 0,
                    hoverBackgroundColor: [
                        'rgba(67, 97, 238, 1)',
                        'rgba(58, 12, 163, 1)',
                        'rgba(114, 9, 183, 1)',
                        'rgba(247, 37, 133, 1)',
                        'rgba(76, 201, 240, 1)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { family: "'Sarabun', sans-serif", size: 14 },
                        bodyFont: { family: "'Sarabun', sans-serif", size: 14 },
                        padding: 12,
                        cornerRadius: 8
                    }
                },
                scales: { 
                    y: { 
                        beginAtZero: true,
                        ticks: { 
                            font: { family: "'Sarabun', sans-serif", size: 13 },
                            callback: function(value) {
                                if (value >= 1000) {
                                    return (value/1000).toFixed(1) + 'k';
                                }
                                return value;
                            }
                        },
                        grid: { 
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: { 
                            font: { family: "'Sarabun', sans-serif", size: 13 }
                        },
                        grid: { display: false }
                    }
                }
            }
        });

        // Chart 2: Request Status (Doughnut)
        const pendingCount = chartReqData['panding'] || 0;
        const approvedCount = chartReqData['approved'] || 0;
        
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        const statusChart = new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['รออนุมัติ', 'อนุมัติแล้ว'],
                datasets: [{
                    data: [pendingCount, approvedCount],
                    backgroundColor: [
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(25, 135, 84, 0.8)'
                    ],
                    borderWidth: 0,
                    hoverBackgroundColor: [
                        'rgba(255, 193, 7, 1)',
                        'rgba(25, 135, 84, 1)'
                    ],
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: { 
                            padding: 25,
                            font: { family: "'Sarabun', sans-serif", size: 14 },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleFont: { family: "'Sarabun', sans-serif", size: 14 },
                        bodyFont: { family: "'Sarabun', sans-serif", size: 14 },
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += context.parsed;
                                return label;
                            }
                        }
                    }
                }
            }
        });

        // เพิ่มการทำงานให้กับปุ่มอนุมัติ/ปฏิเสธ
        document.querySelectorAll('.btn-approve').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('คุณต้องการอนุมัติรายการแลกรางวัลนี้ใช่หรือไม่?')) {
                    const row = this.closest('tr');
                    row.querySelector('.status-badge').className = 'status-badge status-approved';
                    row.querySelector('.status-badge').textContent = 'อนุมัติแล้ว';
                    row.querySelector('.action-buttons').innerHTML = '<span class="text-success small"><i class="fas fa-check-circle"></i> เรียบร้อย</span>';
                    
                    // แสดงการแจ้งเตือน
                    showNotification('อนุมัติรายการแลกรางวัลเรียบร้อยแล้ว', 'success');
                }
            });
        });

        document.querySelectorAll('.btn-reject').forEach(button => {
            button.addEventListener('click', function() {
                if (confirm('คุณแน่ใจที่จะปฏิเสธคำขอนี้? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
                    const row = this.closest('tr');
                    row.style.opacity = '0.5';
                    setTimeout(() => {
                        row.remove();
                        // แสดงการแจ้งเตือน
                        showNotification('ปฏิเสธคำขอแลกรางวัลเรียบร้อยแล้ว', 'danger');
                    }, 300);
                }
            });
        });
        
        // ฟังก์ชันแสดงการแจ้งเตือน
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 25px;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            `;
            
            if (type === 'success') {
                notification.style.backgroundColor = 'var(--accent-color)';
            } else if (type === 'danger') {
                notification.style.backgroundColor = 'var(--danger-color)';
            }
            
            notification.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                ${message}
            `;
            
            document.body.appendChild(notification);
            
            // สร้าง animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(100%); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes fadeOut {
                    from { opacity: 1; }
                    to { opacity: 0; }
                }
            `;
            document.head.appendChild(style);
            
            // ลบ notification หลังจาก 3 วินาที
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                if (style.parentNode) {
                    style.parentNode.removeChild(style);
                }
            }, 3000);
        }
        
        // เพิ่มเอฟเฟกต์ hover ให้กับการ์ด
        document.querySelectorAll('.stat-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });
    </script>
</body>
</html>