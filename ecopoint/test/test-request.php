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
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: "Sarabun", sans-serif;
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

?>

<!-- ********************************************* -->
<!-- *****************content********************* -->
<!-- ********************************************* -->

<div class="container mt-4">

    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body bg-success text-center text-white rounded">
            <h4 class="mb-0">
                <i class="bi bi-gift-fill me-2"></i>
                รายการคำขอแลกของรางวัล
            </h4>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-center">
                <input type="hidden" name="page" value="request">

                <div class="col-md-4">
                    <select name="search" class="form-select">
                        <option value="">📋 รายการทั้งหมด</option>
                        <option value="approved">✅ อนุมัติแล้ว</option>
                        <option value="panding">⏳ รอการอนุมัติ</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>ชื่อผู้ใช้</th>
                        <th>รายการสินค้า</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <?php
                if(!empty($_GET['search'])){
                    $key = "%".$_GET['search']."%";
                    $stmt = $conn->prepare("SELECT users.uid, users.firstname, users.lastname, rewards.rw_name,
                    request.req_status, request.req_id
                    FROM request 
                    INNER JOIN users ON users.uid = request.uid
                    LEFT JOIN rewards ON rewards.rw_id = request.rw_id
                    WHERE  request.req_status LIKE ?
                    ORDER BY request.req_date DESC");
                    $stmt->bind_param("s", $key);
                }else{
                    $stmt = $conn->prepare("SELECT users.uid, users.firstname, users.lastname, rewards.rw_name,
                    request.req_status, request.req_id
                    FROM request 
                    INNER JOIN users ON users.uid = request.uid
                    LEFT JOIN rewards ON rewards.rw_id = request.rw_id
                    ORDER BY request.req_date DESC");
                }

                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0){
                    while($data = $result->fetch_assoc()){
                ?>
                <tbody>
                    <!-- ตัวอย่างข้อมูล -->
                    <tr>
                        <td><?php echo $data['uid'];?></td>
                        <td><?php echo $data['firstname']. " ". $data['lastname'];?></td>
                        <td><?php echo $data['rw_name'];?></td>
                        <td>
                            <span class="badge bg-warning text-dark"><?php echo $data['req_status'];?></span>
                        </td>
                        <?php
                        if($data['req_status'] == "panding"){ ?>
                             <td>
                                <a href="?approved=<?php echo $data['req_id'];?>&page=request"
                                onclick="return confirm('👆ยืนยันการอนุมัติการการ ?')" class="btn btn-warning"
                                >
                                    👆อนุมัติรายการ
                                </a>
                            </td>
                        <?php  }else{ ?>
                            <td>
                                ✅อนุมัติแล้ว
                            </td>
                        <?php } ?>
                        
                    </tr>
                </tbody>
                <?php
                    }
                }
                ?>
            </table>
        </div>
    </div>
</div>

<?php 
if(isset($_GET['approved'])){
    $req_id = $_GET['approved'];
    $page = $_GET['page'];

    $stmt1 = $conn->prepare("UPDATE request SET req_status = 'approved' WHERE req_id = ?");
    $stmt1->bind_param("i", $req_id);
    if($stmt1->execute()){
        echo "
        <script>
        window.location='index.php?page={$page}';
        </script>
        ";
    }
}
