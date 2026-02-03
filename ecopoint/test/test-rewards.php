<?php

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
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: "Sarabun", sans-serif;
            }
            .auth-container {
                min-height: 100vh;
                background: linear-gradient(45deg, #134e5e, #71b280);
                display: flex;
                align-items: center;
                overflow: hidden;
            }

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
                margin: -45px auto 20px;
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

// เชื่อมต่อฐานข้อมูล
include_once 'includes/config.php'; // เปลี่ยน path ตามโครงสร้างของคุณ

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$uid = $_SESSION['uid'];
$po = $conn->query("SELECT * FROM users WHERE uid = '$uid'");
$_Fet_ = $po->fetch_assoc();
$total_point = $_Fet_['u_total_point'];
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌿 ระบบแลกของรางวัล - Eco Point</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            /* สีธีมธรรมชาติ */
            --primary-green: #2E8B57;
            --secondary-green: #3CB371;
            --light-green: #98FB98;
            --dark-green: #228B22;
            --accent-yellow: #FFD700;
            --accent-orange: #FF8C00;
            --text-light: #F5F5F5;
            --text-dark: #2F4F4F;
            --card-bg: rgba(255, 255, 255, 0.95);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        body {
            font-family: 'Noto Sans Thai', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: var(--text-dark);
            min-height: 100vh;
            line-height: 1.6;
        }

        .rewards-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Section */
        .rewards-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--dark-green) 100%);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            color: white;
            position: relative;
            overflow: hidden;
        }

        .rewards-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" preserveAspectRatio="none"><path d="M0,0 L100,0 L100,100 Z" fill="rgba(255,255,255,0.1)"/></svg>');
            background-size: cover;
            opacity: 0.3;
        }

        .points-display {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
        }

        .points-icon {
            font-size: 3rem;
            color: var(--accent-yellow);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .points-value {
            font-size: 3.5rem;
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .points-label {
            font-size: 1.2rem;
            opacity: 0.9;
        }

        /* Products Section */
        .section-title {
            text-align: center;
            margin: 40px 0 30px;
            position: relative;
        }

        .section-title h2 {
            display: inline-block;
            background: var(--primary-green);
            color: white;
            padding: 15px 40px;
            border-radius: 50px;
            box-shadow: var(--shadow);
            font-size: 1.8rem;
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .product-card {
            background: var(--card-bg);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            border: 1px solid rgba(46, 139, 87, 0.1);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-bottom: 2px solid var(--light-green);
        }

        .product-info {
            padding: 20px;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: var(--dark-green);
            margin-bottom: 10px;
            min-height: 60px;
        }

        .product-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding: 10px 0;
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 0.9rem;
        }

        .meta-item i {
            color: var(--secondary-green);
        }

        .product-price {
            text-align: center;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
            margin: 15px 0;
        }

        .redeem-btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--secondary-green) 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .redeem-btn:hover {
            background: linear-gradient(135deg, var(--dark-green) 0%, var(--primary-green) 100%);
            transform: translateY(-2px);
        }

        .redeem-btn.disabled {
            background: #cccccc;
            cursor: not-allowed;
        }

        .redeem-btn.disabled:hover {
            transform: none;
        }

        /* History Section */
        .history-section {
            background: var(--card-bg);
            border-radius: 15px;
            padding: 30px;
            box-shadow: var(--shadow);
            margin-bottom: 40px;
        }

        .history-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .history-table th {
            background: var(--primary-green);
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        .history-table td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        .history-table tr:hover {
            background: rgba(46, 139, 87, 0.05);
        }

        .status-badge {
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .status-success {
            background: rgba(46, 139, 87, 0.2);
            color: var(--dark-green);
        }

        .status-pending {
            background: rgba(255, 140, 0, 0.2);
            color: var(--accent-orange);
        }

        /* Admin Controls */
        .admin-controls {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
            padding: 20px;
            background: rgba(46, 139, 87, 0.1);
            border-radius: 10px;
        }

        .admin-btn {
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-add {
            background: var(--primary-green);
            color: white;
        }

        .btn-add:hover {
            background: var(--dark-green);
            transform: translateY(-2px);
        }

        .btn-edit, .btn-delete {
            padding: 5px 10px;
            font-size: 0.9rem;
            margin: 5px;
        }

        .btn-edit {
            background: #3498db;
            color: white;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 15px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--light-green);
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: 'Noto Sans Thai', sans-serif;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary-green);
            box-shadow: 0 0 0 3px rgba(46, 139, 87, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
                gap: 20px;
            }
            
            .points-display {
                flex-direction: column;
                text-align: center;
            }
            
            .points-value {
                font-size: 2.5rem;
            }
            
            .rewards-header {
                padding: 20px;
            }
        }

        @media (max-width: 480px) {
            .products-grid {
                grid-template-columns: 1fr;
            }
            
            .history-table {
                display: block;
                overflow-x: auto;
            }
            
            .section-title h2 {
                font-size: 1.4rem;
                padding: 10px 20px;
            }
        }

        /* Animations */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stagger-animation > * {
            opacity: 0;
            animation: fadeInUp 0.5s ease forwards;
        }

        .stagger-animation > *:nth-child(1) { animation-delay: 0.1s; }
        .stagger-animation > *:nth-child(2) { animation-delay: 0.2s; }
        .stagger-animation > *:nth-child(3) { animation-delay: 0.3s; }
        .stagger-animation > *:nth-child(4) { animation-delay: 0.4s; }
        .stagger-animation > *:nth-child(5) { animation-delay: 0.5s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <div class="rewards-container fade-in">
        <!-- Header Section -->
        <div class="rewards-header">
            <h1 class="text-center mb-4">
                <i class="fas fa-gift me-2"></i>ระบบแลกของรางวัล
            </h1>
            
            <div class="points-display">
                <div class="points-icon">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="text-center">
                    <div class="points-value"><?php echo number_format($total_point); ?></div>
                    <div class="points-label">คะแนนสะสม</div>
                </div>
            </div>
            
            <p class="text-center mt-3" style="opacity: 0.9;">
                <i class="fas fa-leaf me-1"></i>
                ใช้คะแนนแลกของรางวัลเพื่อสิ่งแวดล้อมที่ดีกว่า
            </p>
        </div>

        <!-- Products Section -->
        <div class="section-title">
            <h2><i class="fas fa-gifts me-2"></i>ของรางวัลทั้งหมด</h2>
        </div>

        <?php if(isset($_SESSION['username']) && in_array($_SESSION['role'],['Super admin','partner','admin'])): ?>
        <div class="admin-controls">
            <button class="admin-btn btn-add" onclick="openAddModal()">
                <i class="fas fa-plus-circle"></i> เพิ่มของรางวัลใหม่
            </button>
        </div>
        <?php endif; ?>

        <div class="products-grid stagger-animation">
            <?php
            $raward = $conn->query("SELECT * FROM rewards ORDER BY rw_id DESC");
            $cardCount = 0;
            while($row = $raward->fetch_assoc()){
                $cardCount++;
            ?>
            <div class="product-card">
                <?php if(isset($_SESSION['username']) && in_array($_SESSION['role'],['Super admin','partner','admin'])): ?>
                <div class="d-flex justify-content-end p-2">
                    <a href="#" class="admin-btn btn-edit" 
                       onclick="openEditModal(
                           '<?php echo $row['rw_id']; ?>',
                           '<?php echo addslashes($row['rw_name']); ?>',
                           '<?php echo $row['rw_stock']; ?>',
                           '<?php echo $row['rw_price']; ?>',
                           '<?php echo $row['rw_image']; ?>'
                       )">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="?delete=<?php echo $row['rw_id']; ?>&page=reward" 
                       class="admin-btn btn-delete" 
                       onclick="return confirm('ยืนยันการลบรายการนี้?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
                <?php endif; ?>
                
                <img src="uploads/rewards/<?php echo $row['rw_image']; ?>" 
                     class="product-image" 
                     alt="<?php echo htmlspecialchars($row['rw_name']); ?>"
                     onerror="this.src='https://via.placeholder.com/300x200?text=No+Image'">
                
                <div class="product-info">
                    <h3 class="product-title"><?php echo $row['rw_name']; ?></h3>
                    
                    <div class="product-meta">
                        <div class="meta-item">
                            <i class="fas fa-box"></i>
                            <span>สต็อก: <?php echo $row['rw_stock']; ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span>รหัส: #<?php echo str_pad($row['rw_id'], 3, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>
                    
                    <div class="product-price">
                        <?php echo number_format($row['rw_price']); ?> คะแนน
                    </div>
                    
                    <?php 
                    if($row['rw_stock'] >= 1 && $total_point >= $row['rw_price']): 
                        if(isset($_SESSION['uid']) && $uid): ?>
                            <button onclick="confirmRedeem(
                                '<?php echo addslashes($row['rw_name']); ?>', 
                                <?php echo $row['rw_price']; ?>, 
                                <?php echo $total_point; ?>,
                                '?redeem=<?php echo $row['rw_id']; ?>&price=<?php echo $row['rw_price']; ?>&page=reward'
                            )" class="redeem-btn">
                                <i class="fas fa-exchange-alt"></i>
                                แลกของรางวัล
                            </button>
                        <?php else: ?>
                            <button class="redeem-btn disabled" disabled>
                                <i class="fas fa-sign-in-alt"></i>
                                กรุณาเข้าสู่ระบบ
                            </button>
                        <?php endif; ?>
                    <?php elseif($row['rw_stock'] < 1): ?>
                        <button class="redeem-btn disabled" disabled>
                            <i class="fas fa-times-circle"></i>
                            สินค้าหมด
                        </button>
                    <?php else: ?>
                        <button class="redeem-btn disabled" disabled>
                            <i class="fas fa-exclamation-triangle"></i>
                            คะแนนไม่เพียงพอ
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            <?php } ?>
            
            <?php if($cardCount == 0): ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
                <i class="fas fa-gift fa-4x mb-3" style="color: #ccc;"></i>
                <h3 class="mb-2">ยังไม่มีของรางวัล</h3>
                <p class="text-muted">รอการเพิ่มของรางวัลจากผู้ดูแลระบบ</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- History Section -->
        <div class="section-title">
            <h2><i class="fas fa-history me-2"></i>ประวัติการแลกของรางวัล</h2>
        </div>
        
        <div class="history-section">
            <div class="table-responsive">
                <table class="history-table">
                    <thead>
                        <tr>
                            <th width="80">ลำดับ</th>
                            <th>ชื่อสินค้า</th>
                            <th width="150">ราคาสินค้า</th>
                            <th width="200">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $record = $conn->prepare("SELECT rewards.rw_name, request.req_price, request.req_status
                                                FROM request 
                                                LEFT JOIN rewards ON rewards.rw_id = request.rw_id
                                                WHERE request.uid = ? ORDER BY request.req_id DESC");
                        $record->bind_param("i", $uid);
                        $record->execute();
                        $result = $record->get_result();
                        $num = 1;
                        if($result->num_rows > 0){
                            while($data = $result->fetch_assoc()){
                        ?>
                        <tr>
                            <td>#<?php echo str_pad($num++, 3, '0', STR_PAD_LEFT); ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-gift me-2" style="color: var(--primary-green);"></i>
                                    <span><?php echo $data['rw_name']; ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-coins me-2" style="color: var(--accent-yellow);"></i>
                                    <span><?php echo number_format($data['req_price']); ?> คะแนน</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo ($data['req_status'] == 'สำเร็จ') ? 'status-success' : 'status-pending'; ?>">
                                    <?php if($data['req_status'] == 'สำเร็จ'): ?>
                                        <i class="fas fa-check-circle me-1"></i>
                                    <?php else: ?>
                                        <i class="fas fa-clock me-1"></i>
                                    <?php endif; ?>
                                    <?php echo $data['req_status']; ?>
                                </span>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else { 
                        ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x mb-3" style="color: #ddd;"></i>
                                <h4 class="mb-2">ยังไม่มีประวัติการแลกของรางวัล</h4>
                                <p class="text-muted">เริ่มต้นการแลกของรางวัลเพื่อเก็บเกี่ยวประสบการณ์</p>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle me-2"></i>เพิ่มของรางวัลใหม่</h3>
                <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="addForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">ชื่อของรางวัล</label>
                        <input type="text" class="form-control" name="name" required placeholder="กรอกชื่อของรางวัล">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">จำนวนสต็อก</label>
                                <input type="number" class="form-control" name="stock" min="0" required placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">ราคาสินค้า (คะแนน)</label>
                                <input type="number" class="form-control" name="price" min="0" required placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">รูปภาพของรางวัล</label>
                        <input type="file" class="form-control" name="image" accept="image/*" required>
                        <small class="text-muted">แนะนำ: รูปภาพขนาด 500x500 พิกเซล</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="redeem-btn w-100" name="add-reward">
                        <i class="fas fa-plus-circle"></i> เพิ่มของรางวัล
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-edit me-2"></i>แก้ไขของรางวัล</h3>
                <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="edit_id" id="edit_id">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">ชื่อของรางวัล</label>
                        <input type="text" class="form-control" name="edit_name" id="edit_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">จำนวนสต็อก</label>
                                <input type="number" class="form-control" name="edit_stock" id="edit_stock" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">ราคาสินค้า (คะแนน)</label>
                                <input type="number" class="form-control" name="edit_price" id="edit_price" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">รูปภาพปัจจุบัน</label>
                        <div class="text-center mb-3">
                            <img src="" id="current_image" class="img-fluid rounded" style="max-height: 150px; display: none;">
                        </div>
                        <div id="image_filename" class="text-center text-muted mb-3"></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">เปลี่ยนรูปภาพ (เลือกเฉพาะต้องการเปลี่ยน)</label>
                        <input type="file" class="form-control" name="edit_image" accept="image/*">
                        <small class="text-muted">หากไม่ต้องการเปลี่ยนรูปภาพ ให้ปล่อยว่างไว้</small>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="submit" class="redeem-btn w-100" name="edit-reward">
                        <i class="fas fa-save"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        function openAddModal() {
            openModal('addModal');
        }

        function openEditModal(id, name, stock, price, image) {
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_stock').value = stock;
            document.getElementById('edit_price').value = price;
            
            const currentImage = document.getElementById('current_image');
            const imageFilename = document.getElementById('image_filename');
            
            currentImage.src = 'uploads/rewards/' + image;
            currentImage.style.display = 'block';
            imageFilename.textContent = 'ไฟล์ปัจจุบัน: ' + image;
            
            openModal('editModal');
        }

        // Close modal when clicking outside
        window.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
        });

        // Confirm Redeem Function
        function confirmRedeem(itemName, itemPrice, userPoints, redirectUrl) {
            if (userPoints < itemPrice) {
                alert('❌ คะแนนของคุณไม่เพียงพอ\n\nต้องการ: ' + itemPrice.toLocaleString() + ' คะแนน\nมีอยู่: ' + userPoints.toLocaleString() + ' คะแนน');
                return false;
            }
            
            const confirmMessage = 'ยืนยันการแลกของรางวัลนี้?\n\n' +
                                  'ของรางวัล: ' + itemName + '\n' +
                                  'ใช้คะแนน: ' + itemPrice.toLocaleString() + ' คะแนน\n' +
                                  'คะแนนคงเหลือ: ' + (userPoints - itemPrice).toLocaleString() + ' คะแนน';
            
            if (confirm(confirmMessage)) {
                window.location.href = redirectUrl;
            }
        }

        // Initialize animations
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade in animation to all product cards
            const cards = document.querySelectorAll('.product-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
            });

            // Smooth scroll for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });
        });
    </script>

    <!-- PHP Processing -->
    <?php 
    // Add rewards
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add-reward'])){
        $name = $conn->real_escape_string($_POST['name']);
        $stock = intval($_POST['stock']);
        $price = intval($_POST['price']);
        
        $dir = "uploads/rewards/";
        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        $image = basename($_FILES['image']['name']);
        $image = time() . '_' . $image; // Add timestamp to prevent duplicate names
        $move = $dir . $image;

        $stmt1 = $conn->prepare("INSERT INTO rewards (rw_name, rw_image, rw_price, rw_stock) VALUES (?, ?, ?, ?)");
        $stmt1->bind_param("ssii", $name, $image, $price, $stock);
        
        if($stmt1->execute()){
            if(move_uploaded_file($_FILES['image']['tmp_name'], $move)){
                echo "<script>
                alert('เพิ่มของรางวัลสำเร็จ ✅');
                window.location.href = 'index.php?page=reward';
                </script>";
            } else {
                echo "<script>
                alert('อัปโหลดรูปภาพล้มเหลว ❌');
                window.location.href = 'index.php?page=reward';
                </script>";
            }
        } else {
            echo "<script>
            alert('เกิดข้อผิดพลาดในการเพิ่มของรางวัล ❌');
            window.location.href = 'index.php?page=reward';
            </script>";
        }
    }

    // Edit rewards
    if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit-reward'])){
        $rw_id = intval($_POST['edit_id']);
        $name = $conn->real_escape_string($_POST['edit_name']);
        $stock = intval($_POST['edit_stock']);
        $price = intval($_POST['edit_price']);
        
        // Check if new image is uploaded
        if(!empty($_FILES['edit_image']['name'])) {
            $dir = "uploads/rewards/";
            $image = basename($_FILES['edit_image']['name']);
            $image = time() . '_' . $image;
            $move = $dir . $image;
            
            $stmt = $conn->prepare("UPDATE rewards SET rw_name=?, rw_image=?, rw_price=?, rw_stock=? WHERE rw_id=?");
            $stmt->bind_param("ssiii", $name, $image, $price, $stock, $rw_id);
            
            if($stmt->execute()){
                if(move_uploaded_file($_FILES['edit_image']['tmp_name'], $move)){
                    echo "<script>
                    alert('แก้ไขข้อมูลสำเร็จ ✅');
                    window.location.href = 'index.php?page=reward';
                    </script>";
                }
            }
        } else {
            // Don't change image
            $stmt = $conn->prepare("UPDATE rewards SET rw_name=?, rw_price=?, rw_stock=? WHERE rw_id=?");
            $stmt->bind_param("siii", $name, $price, $stock, $rw_id);
            
            if($stmt->execute()){
                echo "<script>
                alert('แก้ไขข้อมูลสำเร็จ ✅');
                window.location.href = 'index.php?page=reward';
                </script>";
            }
        }
    }

    // Delete rewards
    if(isset($_GET['delete'])){
        $rw_id = intval($_GET['delete']);

        $stmt = $conn->prepare("DELETE FROM rewards WHERE rw_id = ?");
        $stmt->bind_param("i", $rw_id);
        
        if($stmt->execute()){
            echo "<script>
            alert('ลบรายการสำเร็จ ✅');
            window.location.href = 'index.php?page=reward';
            </script>";
        } else {
            echo "<script>
            alert('เกิดข้อผิดพลาดในการลบ ❌');
            window.location.href = 'index.php?page=reward';
            </script>";
        }
    }

    // Redeem reward
    if(isset($_GET['redeem'])){
        $rw_id = intval($_GET['redeem']);
        $price = intval($_GET['price']);

        // Check if user is logged in
        if(!isset($uid) || empty($uid)){
            echo "<script>
            alert('กรุณาเข้าสู่ระบบก่อนทำการแลกของรางวัล ❌');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
        }

        // Check points
        $check1 = $conn->query("SELECT * FROM users WHERE uid = '$uid'");
        $check = $check1->fetch_assoc();

        if(!$check){
            echo "<script>
            alert('ไม่พบข้อมูลผู้ใช้ ❌');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
        }

        if($check['u_total_point'] < $price){
            echo "<script>
            alert('คะแนนของคุณไม่เพียงพอ ❌\\n\\nต้องการ: $price คะแนน\\nมีอยู่: {$check['u_total_point']} คะแนน');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
        }
        
        // Check stock
        $stockCheck = $conn->query("SELECT rw_stock, rw_name FROM rewards WHERE rw_id = '$rw_id'");
        $stockData = $stockCheck->fetch_assoc();
        
        if($stockData['rw_stock'] < 1){
            echo "<script>
            alert('สินค้าหมดสต็อก ❌');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
        }
        
        $productName = $stockData['rw_name'];
        
        // Begin transaction
        $conn->begin_transaction();
        
        try {
            // 1. Record the request
            $stmt1 = $conn->prepare("INSERT INTO request(uid, rw_id, req_price, req_status) VALUES(?, ?, ?, 'panding')");
            $stmt1->bind_param("iii", $uid, $rw_id, $price);
            
            if(!$stmt1->execute()){
                throw new Exception("บันทึกการขอแลกไม่สำเร็จ");
            }
            
            // 2. Deduct points
            $stmt2 = $conn->prepare("UPDATE users SET u_total_point = u_total_point - ? WHERE uid = ?");
            $stmt2->bind_param("ii", $price, $uid);
            
            if(!$stmt2->execute()){
                throw new Exception("หักคะแนนไม่สำเร็จ");
            }
            
            // 3. Reduce stock
            $stmt3 = $conn->prepare("UPDATE rewards SET rw_stock = rw_stock - 1 WHERE rw_id = ?");
            $stmt3->bind_param("i", $rw_id);
            
            if(!$stmt3->execute()){
                throw new Exception("ลดสต็อกไม่สำเร็จ");
            }
            
            // Commit transaction
            $conn->commit();
            
            echo "<script>
            alert('แลกเปลี่ยนสำเร็จ! ✅\\n\\nของรางวัล: $productName\\nใช้คะแนน: $price คะแนน\\n\\nกรุณารอการอนุมัติจากผู้ดูแลระบบ');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
            
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>
            alert('เกิดข้อผิดพลาดในการแลกของรางวัล ❌\\n" . addslashes($e->getMessage()) . "');
            window.location.href = 'index.php?page=reward';
            </script>";
            exit();
        }
    }
    ?>
</body>
</html>