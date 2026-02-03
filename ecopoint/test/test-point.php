<?php
include_once 'includes/config.php';
if (!isset($_SESSION['username'])) {
    echo '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>กรุณาเข้าสู่ระบบ - Eco Point</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: "Noto Sans Thai", sans-serif;
            }
            .auth-container {
                min-height: 100vh;
                background: linear-gradient(45deg, #134e5e, #71b280); /* โทนเขียวธรรมชาติ */
                display: flex;
                align-items: center;
                overflow: hidden;
            }

            /* เอฟเฟกต์กระจกฝ้า Glassmorphism */
            .auth-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 24px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                transform: translateY(20px);
                opacity: 0;
                animation: slideUp 0.6s ease forwards;
            }

            @keyframes slideUp {
                to { transform: translateY(0); opacity: 1; }
            }

            .auth-icon {
                width: 90px;
                height: 90px;
                background: #198754;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: -45px auto 20px; /* ย้ายไอคอนขึ้นไปกึ่งกลางขอบบน */
                box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
                color: white;
                font-size: 40px;
            }

            .btn-success {
                padding: 12px;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s;
            }

            .btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(25, 135, 84, 0.4);
            }

            /* แถบโหลดถอยหลัง */
            .redirect-progress {
                height: 4px;
                width: 100%;
                background: #eee;
                border-radius: 10px;
                margin-top: 20px;
                overflow: hidden;
            }
            .progress-bar-fill {
                height: 100%;
                background: #198754;
                width: 100%;
                animation: countdown 3s linear forwards;
            }

            @keyframes countdown {
                from { width: 100%; }
                to { width: 0%; }
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="card auth-card">
                            <div class="card-body p-4 p-md-5 text-center">
                                <div class="auth-icon">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <h3 class="fw-bold mb-2">เข้าถึงจำกัด</h3>
                                <p class="text-muted mb-4">
                                    กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ<br>
                                    <small>ระบบกำลังนำท่านไปหน้าล็อกอิน...</small>
                                </p>
                                
                                <div class="d-grid gap-2">
                                    <a href="index.php?page=login" class="btn btn-success">
                                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบทันที
                                    </a>
                                    <a href="index.php?page=home" class="btn btn-link text-decoration-none text-muted mt-2">
                                        <i class="fas fa-arrow-left me-1"></i> กลับหน้าหลัก
                                    </a>
                                </div>

                                <div class="redirect-progress">
                                    <div class="progress-bar-fill"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                window.location.href = "index.php?page=login";
            }, 6000);
        </script>
    </body>
    </html>
    ';
    exit();
}

$uid = $_SESSION['uid'];
$point = $conn->query("SELECT * FROM users WHERE uid = '$uid'");
$_fetpoint = $point->fetch_assoc();
$total_point = $_fetpoint['u_total_point'];

// ดึงข้อมูลประวัติคะแนน
$sql1 = $conn->query("SELECT * FROM record_points WHERE uid = '$uid' ORDER BY p_date DESC");
$history_count = $sql1->num_rows;

if(isset($_SESSION['username'])){
    $read = $conn->query("UPDATE record_points SET p_noti = 0 WHERE uid = '$uid'");
}

?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>คะแนนของฉัน - Eco Point</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #198754;
            --primary-light: #e8f5e9;
            --primary-dark: #157347;
            --secondary: #20c997;
            --accent: #ffc107;
            --gradient: linear-gradient(135deg, #198754 0%, #20c997 100%);
        }
        
        * {
            font-family: 'Noto Sans Thai', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            min-height: 100vh;
            padding-bottom: 30px;
        }
        
        .point-header {
            background: var(--gradient);
            border-radius: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            margin-bottom: 25px;
            border: none;
            position: relative;
            overflow: hidden;
        }
        
        .point-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: rgba(255, 255, 255, 0.3);
        }
        
        .point-circle {
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .point-circle i {
            font-size: 2.5rem;
            color: white;
        }
        
        .point-display {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 15px 30px;
            display: inline-block;
            margin: 15px 0;
            transition: all 0.3s;
        }
        
        .point-display:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
        }
        
        .history-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
            border: none;
            margin-bottom: 25px;
            transition: all 0.3s;
        }
        
        .history-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        
        .card-header-custom {
            background: var(--gradient);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
            border: none;
        }
        
        .table-history {
            margin-bottom: 0;
        }
        
        .table-history thead {
            background: var(--primary-light);
        }
        
        .table-history th {
            border: none;
            padding: 15px;
            font-weight: 500;
            color: var(--primary-dark);
        }
        
        .table-history tbody tr {
            transition: all 0.2s;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .table-history tbody tr:hover {
            background-color: rgba(25, 135, 84, 0.03);
        }
        
        .table-history td {
            padding: 15px;
            vertical-align: middle;
        }
        
        .badge-point {
            background: var(--gradient);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.85rem;
        }
        
        .badge-item {
            background: var(--primary-light);
            color: var(--primary-dark);
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-right: 5px;
        }
        
        .empty-history {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
        
        .empty-icon {
            font-size: 3.5rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .btn-point {
            background: var(--gradient);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-point:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
            color: white;
        }
        
        .btn-outline-point {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .btn-outline-point:hover {
            background: var(--primary);
            color: white;
            transform: translateY(-2px);
        }
        
        .stats-box {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            text-align: center;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        .stats-value {
            font-size: 2rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        
        .stats-label {
            font-size: 0.9rem;
            color: #6c757d;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .action-buttons {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 100;
        }
        
        .btn-action {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: var(--gradient);
            color: white;
            border: none;
            box-shadow: 0 5px 15px rgba(25, 135, 84, 0.3);
            font-size: 1.2rem;
            transition: all 0.3s;
            margin-bottom: 10px;
        }
        
        .btn-action:hover {
            transform: scale(1.1) rotate(10deg);
            box-shadow: 0 8px 20px rgba(25, 135, 84, 0.4);
        }
        
        @media (max-width: 768px) {
            .point-header {
                border-radius: 15px;
                margin-top: 10px;
            }
            
            .point-circle {
                width: 80px;
                height: 80px;
            }
            
            .point-circle i {
                font-size: 2rem;
            }
            
            .point-display {
                padding: 10px 20px;
            }
            
            .btn-action {
                width: 45px;
                height: 45px;
                font-size: 1rem;
            }
        }
        
        .date-badge {
            background: rgba(0, 0, 0, 0.05);
            color: #666;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 0.8rem;
            margin-right: 10px;
        }
        
        .giver-info {
            background: rgba(25, 135, 84, 0.08);
            padding: 8px 12px;
            border-radius: 8px;
            font-size: 0.85rem;
        }
        
        .giver-icon {
            background: rgba(25, 135, 84, 0.15);
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <!-- Action Buttons -->
    <div class="action-buttons">
        <button class="btn btn-action" onclick="window.location.href='index.php?page=reward'" title="แลกรางวัล">
            <i class="fas fa-gift"></i>
        </button>
        <button class="btn btn-action" onclick="window.location.href='index.php?page=home'" title="กลับหน้าหลัก" style="background: linear-gradient(135deg, #6c757d 0%, #adb5bd 100%);">
            <i class="fas fa-home"></i>
        </button>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="card point-header">
            <div class="card-body text-white text-center py-4">
                <div class="point-circle">
                    <i class="fas fa-coins"></i>
                </div>
                
                <h1 class="fw-bold mb-3">คะแนนของฉัน</h1>
                
                <div class="point-display">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-wallet fa-2x"></i>
                        </div>
                        <div class="text-start">
                            <div class="fs-6 opacity-75">คะแนนสะสมทั้งหมด</div>
                            <div class="display-4 fw-bold"><?php echo number_format($total_point); ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-3">
                    <p class="mb-0 opacity-75">
                        <i class="fas fa-info-circle me-2"></i>
                        จุดสะสมจากการรีไซเคิลและกิจกรรม
                    </p>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="row">
            <div class="col-md-6 mb-3">
                <div class="stats-box">
                    <div class="stats-value"><?php echo $history_count; ?></div>
                    <div class="stats-label">ประวัติการรับคะแนน</div>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="stats-box">
                    <div class="stats-value">
                        <?php 
                        $avg_per_day = $history_count > 0 ? round($total_point / $history_count, 1) : 0;
                        echo $avg_per_day;
                        ?>
                    </div>
                    <div class="stats-label">คะแนนเฉลี่ย/ครั้ง</div>
                </div>
            </div>
        </div>

        <!-- History -->
        <div class="card history-card">
            <div class="card-header card-header-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-history me-2"></i>ประวัติการรับคะแนน
                    </h5>
                    <span class="badge bg-white text-primary"><?php echo $history_count; ?> รายการ</span>
                </div>
            </div>
            
            <div class="card-body">
                <?php if($history_count > 0): ?>
                <div class="table-responsive">
                    <table class="table table-history">
                        <thead>
                            <tr>
                                <th width="20%">วันที่</th>
                                <th width="20%">รายการ</th>
                                <th width="15%">คะแนน</th>
                                <th width="25%">ผู้ให้คะแนน</th>
                                <th width="20%">เวลา</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            while($data = $sql1->fetch_assoc()): 
                                $date = new DateTime($data['p_date']);
                                $formatted_date = $date->format('d/m/Y');
                                $formatted_time = $date->format('H:i');
                            ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="date-badge">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo $formatted_date; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-wrap gap-1">
                                        <?php if($data['p_cup'] > 0): ?>
                                        <span class="badge-item">
                                            <i class="fas fa-glass-whiskey me-1"></i>แก้ว x<?php echo $data['p_cup']; ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if($data['p_bottle'] > 0): ?>
                                        <span class="badge-item">
                                            <i class="fas fa-wine-bottle me-1"></i>ขวด x<?php echo $data['p_bottle']; ?>
                                        </span>
                                        <?php endif; ?>
                                        <?php if($data['p_other'] > 0): ?>
                                        <span class="badge-item">
                                            <i class="fas fa-tasks me-1"></i>อื่นๆ
                                        </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge-point">
                                        <i class="fas fa-plus me-1"></i>+<?php echo $data['p_total']; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="giver-info d-flex align-items-center">
                                        <div class="giver-icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold small"><?php echo $data['p_giver']; ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        <i class="fas fa-clock me-1"></i><?php echo $formatted_time; ?> น.
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-history">
                    <div class="empty-icon">
                        <i class="fas fa-history"></i>
                    </div>
                    <h4 class="fw-bold text-muted mb-3">ยังไม่มีประวัติการรับคะแนน</h4>
                    <p class="text-muted mb-4">เริ่มต้นสะสมคะแนนด้วยการรีไซเคิลขวดและแก้วพลาสติก</p>
                    <a href="index.php?page=recycle" class="btn btn-point">
                        <i class="fas fa-recycle me-2"></i>เริ่มรีไซเคิล
                    </a>
                </div>
                <?php endif; ?>
            </div>
            
            <?php if($history_count > 0): ?>
            <div class="card-footer bg-white border-top">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-info-circle me-1"></i>
                        แก้ว = 1 คะแนน/ชิ้น, ขวด = 2 คะแนน/ชิ้น
                    </div>
                    <button class="btn btn-outline-point btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>พิมพ์รายงาน
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <button class="btn btn-point w-100 py-3" onclick="window.location.href='index.php?page=reward'">
                    <i class="fas fa-gift me-2"></i>แลกรางวัล
                </button>
            </div>
            <div class="col-md-6 mb-3">
                <button class="btn btn-outline-point w-100 py-3" onclick="window.location.href='index.php?page=leaderboard'">
                    <i class="fas fa-trophy me-2"></i>อันดับคะแนน
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add simple animation to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const tableRows = document.querySelectorAll('.table-history tbody tr');
            tableRows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.style.opacity = '0';
                row.style.transform = 'translateY(10px)';
                
                setTimeout(() => {
                    row.style.transition = 'all 0.3s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, 100);
            });

            // Add hover effect to action buttons
            const actionButtons = document.querySelectorAll('.btn-action');
            actionButtons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.1) rotate(10deg)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1) rotate(0)';
                });
            });

            // Add click effect to point buttons
            const pointButtons = document.querySelectorAll('.btn-point, .btn-outline-point');
            pointButtons.forEach(btn => {
                btn.addEventListener('mousedown', function() {
                    this.style.transform = 'translateY(-1px)';
                });
                
                btn.addEventListener('mouseup', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>