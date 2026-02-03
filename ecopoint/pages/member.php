<?php
$allow_roles = ['Super admin'];

if (
    !isset($_SESSION['username'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], $allow_roles)
) {
    echo "<script>window.location='index.php?page=home';</script>";
    exit();
}

if(isset($_SESSION['username']) && in_array($_SESSION['role'],['Super admin'])){
    $updata_notifY = $conn->query("UPDATE users SET u_noti = 0 WHERE u_noti = 1");
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    *{
        font-family: 'Noto Sans Thai', sans-serif;
    }
    
    .badge-member { background-color: #17a2b8 !important; }
    .badge-admin { background-color: #ffc107 !important; color: #212529; }
    .badge-suspend { background-color: #dc3545 !important; }
    .badge-partner { background-color: #6f42c1 !important; }
    .badge-super-admin { background-color: #fd7e14 !important; color: #212529; }
    
    .btn-action {
        padding: 5px 10px;
        font-size: 14px;
        margin: 2px;
    }
    
    .search-form {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #dee2e6;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>

<?php
// ฟังก์ชันแจ้งเตือนด้วย JavaScript
function showAlert($type, $title, $message) {
    echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            })
            
            Toast.fire({
                icon: '$type',
                title: '$message'
            })
        });
    </script>";
}

// ประมวลผลการกระทำต่างๆ
if(isset($_GET['action']) && isset($_GET['uid'])) {
    $uid = intval($_GET['uid']);
    $action = $_GET['action'];
    
    if($action == 'suspend') {
        $stmt = $conn->prepare("UPDATE users SET u_role = 'suspend' WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        if($stmt->execute()) {
            showAlert('warning', 'ระงับการใช้งานสำเร็จ', 'ผู้ใช้งานถูกระงับการใช้งานแล้ว');
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถระงับการใช้งานได้');
        }
    } 
    elseif($action == 'unsuspend') {
        $stmt = $conn->prepare("UPDATE users SET u_role = 'member' WHERE uid = ?");
        $stmt->bind_param("i", $uid);
        if($stmt->execute()) {
            showAlert('success', 'กู้คืนการใช้งานสำเร็จ', 'ผู้ใช้งานสามารถใช้งานได้ตามปกติ');
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถกู้คืนการใช้งานได้');
        }
    } 
    elseif($action == 'delete') {
        // ตรวจสอบก่อนว่าผู้ใช้นี้ไม่ใช่แอดมิน
        $checkStmt = $conn->prepare("SELECT u_role FROM users WHERE uid = ?");
        $checkStmt->bind_param("i", $uid);
        $checkStmt->execute();
        $result = $checkStmt->get_result();
        
        if($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // ไม่สามารถลบแอดมินหรือซุปเปอร์แอดมินได้
            if(!in_array($user['u_role'], ['admin', 'Super admin'])) {
                $deleteStmt = $conn->prepare("DELETE FROM users WHERE uid = ?");
                $deleteStmt->bind_param("i", $uid);
                
                if($deleteStmt->execute()) {
                    showAlert('success', 'ลบผู้ใช้งานสำเร็จ', 'ผู้ใช้งานถูกลบออกจากระบบแล้ว');
                } else {
                    showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถลบผู้ใช้งานได้');
                }
            } else {
                showAlert('error', 'ไม่สามารถลบได้', 'ไม่สามารถลบผู้ดูแลระบบได้');
            }
        }
    }
}

// ประมวลผลการแก้ไขข้อมูล
if(isset($_POST['edit_user'])) {
    $uid = intval($_POST['uid']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $u_total_point = intval($_POST['u_total_point']);
    $u_role = trim($_POST['u_role']);
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if(empty($firstname) || empty($lastname) || empty($username) || empty($phone) || empty($email) || empty($u_role)) {
        showAlert('error', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'ข้อมูลบางส่วนยังไม่ครบถ้วน');
    } else {
        // ตรวจสอบรูปแบบอีเมล
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            showAlert('error', 'รูปแบบอีเมลไม่ถูกต้อง', 'กรุณากรอกอีเมลให้ถูกต้อง');
        } else {
            // ตรวจสอบว่าอีเมลซ้ำกับผู้ใช้อื่นหรือไม่
            $check_email = $conn->prepare("SELECT uid FROM users WHERE email = ? AND uid != ?");
            $check_email->bind_param("si", $email, $uid);
            $check_email->execute();
            $check_email->store_result();
            
            if($check_email->num_rows > 0) {
                showAlert('error', 'อีเมลซ้ำ', 'อีเมลนี้มีผู้ใช้งานแล้ว');
            } else {
                // ตรวจสอบว่าชื่อผู้ใช้ซ้ำกับผู้ใช้อื่นหรือไม่
                $check_username = $conn->prepare("SELECT uid FROM users WHERE username = ? AND uid != ?");
                $check_username->bind_param("si", $username, $uid);
                $check_username->execute();
                $check_username->store_result();
                
                if($check_username->num_rows > 0) {
                    showAlert('error', 'ชื่อผู้ใช้ซ้ำ', 'ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว');
                } else {
                    // อัปเดตข้อมูล
                    $sql = "UPDATE users SET firstname = ?, lastname = ?, username = ?, phone = ?, email = ?, u_total_point = ?, u_role = ? WHERE uid = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sssssisi", $firstname, $lastname, $username, $phone, $email, $u_total_point, $u_role, $uid);
                    
                    if($stmt->execute()) {
                        showAlert('success', 'อัปเดตข้อมูลสำเร็จ', 'ข้อมูลผู้ใช้งานถูกอัปเดตแล้ว');
                    } else {
                        showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถอัปเดตข้อมูลได้');
                    }
                }
            }
        }
    }
}

// ประมวลผลการเพิ่มผู้ใช้ใหม่
if(isset($_POST['add_user'])) {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $u_total_point = isset($_POST['u_total_point']) ? intval($_POST['u_total_point']) : 10;
    $u_role = trim($_POST['u_role']);
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if(empty($firstname) || empty($lastname) || empty($username) || empty($phone) || empty($email) || empty($password) || empty($u_role)) {
        showAlert('error', 'กรุณากรอกข้อมูลให้ครบถ้วน', 'ข้อมูลบางส่วนยังไม่ครบถ้วน');
    } elseif($password !== $confirm_password) {
        showAlert('error', 'รหัสผ่านไม่ตรงกัน', 'กรุณากรอกรหัสผ่านให้ตรงกัน');
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        showAlert('error', 'รูปแบบอีเมลไม่ถูกต้อง', 'กรุณากรอกอีเมลให้ถูกต้อง');
    } else {
        // ตรวจสอบว่าอีเมลซ้ำหรือไม่
        $check_email = $conn->prepare("SELECT uid FROM users WHERE email = ?");
        $check_email->bind_param("s", $email);
        $check_email->execute();
        $check_email->store_result();
        
        if($check_email->num_rows > 0) {
            showAlert('error', 'อีเมลซ้ำ', 'อีเมลนี้มีผู้ใช้งานแล้ว');
        } else {
            // ตรวจสอบว่าชื่อผู้ใช้ซ้ำหรือไม่
            $check_username = $conn->prepare("SELECT uid FROM users WHERE username = ?");
            $check_username->bind_param("s", $username);
            $check_username->execute();
            $check_username->store_result();
            
            if($check_username->num_rows > 0) {
                showAlert('error', 'ชื่อผู้ใช้ซ้ำ', 'ชื่อผู้ใช้นี้มีผู้ใช้งานแล้ว');
            } else {
                // เข้ารหัสรหัสผ่าน
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // เพิ่มผู้ใช้ใหม่
                $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, username, phone, email, password, u_total_point, u_role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssis", $firstname, $lastname, $username, $phone, $email, $hashed_password, $u_total_point, $u_role);
                
                if($stmt->execute()) {
                    showAlert('success', 'เพิ่มผู้ใช้งานสำเร็จ', 'ผู้ใช้งานใหม่ถูกเพิ่มเข้าสู่ระบบแล้ว');
                } else {
                    showAlert('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถเพิ่มผู้ใช้งานได้');
                }
            }
        }
    }
}

// ดึงข้อมูลสถิติ
$sum = $conn->query("SELECT COUNT(*) AS sum_member FROM users");
$ss = $sum->fetch_assoc();
$total_member = $ss['sum_member'];

$active_members = $conn->query("SELECT COUNT(*) AS active FROM users WHERE u_role = 'member'")->fetch_assoc()['active'];
$suspended_members = $conn->query("SELECT COUNT(*) AS suspended FROM users WHERE u_role = 'suspend'")->fetch_assoc()['suspended'];
$admin_members = $conn->query("SELECT COUNT(*) AS admins FROM users WHERE u_role IN ('admin', 'Super admin', 'partner')")->fetch_assoc()['admins'];
?>

<div class="container my-4">

    <!-- การ์ดสรุป -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-people fs-1 text-success"></i>
                    <h5 class="mt-2">ผู้ใช้ทั้งหมด</h5>
                    <h2 class="fw-bold text-success"><?php echo number_format($total_member); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-person-check fs-1 text-primary"></i>
                    <h5 class="mt-2">ใช้งานปกติ</h5>
                    <h2 class="fw-bold text-primary"><?php echo number_format($active_members); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-person-x fs-1 text-danger"></i>
                    <h5 class="mt-2">ระงับการใช้งาน</h5>
                    <h2 class="fw-bold text-danger"><?php echo number_format($suspended_members); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card shadow-sm text-center h-100">
                <div class="card-body">
                    <i class="bi bi-shield-check fs-1 text-warning"></i>
                    <h5 class="mt-2">ผู้ดูแลระบบ</h5>
                    <h2 class="fw-bold text-warning"><?php echo number_format($admin_members); ?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- ค้นหาผู้ใช้ -->
    <div class="card shadow-sm mb-4">
        <div class="card-body search-form">
            <h5 class="mb-3">
                <i class="bi bi-search"></i> ค้นหาผู้ใช้
            </h5>
            <form method="post" class="row g-3">
                <div class="col-md-8">
                    <input type="text" name="search" class="form-control form-control-lg"
                        placeholder="ค้นหาจากชื่อ, นามสกุล, ชื่อผู้ใช้, อีเมล หรือเบอร์โทรศัพท์..."
                        value="<?php echo isset($_POST['search']) ? htmlspecialchars($_POST['search']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-success btn-lg w-100">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
                <div class="col-md-2">
                    <?php if(isset($_POST['search'])): ?>
                    <a href="?page=<?php echo $_GET['page'] ?? ''; ?>" class="btn btn-outline-secondary btn-lg w-100">
                        <i class="bi bi-x-circle"></i> ล้าง
                    </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางข้อมูล -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">
                    <i class="bi bi-table"></i> รายการผู้ใช้ทั้งหมด
                </h5>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus"></i> เพิ่มผู้ใช้ใหม่
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-center"><i class="bi bi-key"></i> ID</th>
                            <th><i class="bi bi-person"></i> ชื่อ - นามสกุล</th>
                            <th>ชื่อผู้ใช้</th>
                            <th>เบอร์โทร</th>
                            <th>อีเมล</th>
                            <th class="text-center">คะแนน</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $search_query = "";
                        $params = [];
                        $types = "";
                        
                        if(isset($_POST['search']) && !empty($_POST['search'])){
                            $search = $_POST['search'];
                            $search_query = "WHERE uid LIKE ? OR firstname LIKE ? OR lastname LIKE ? OR username LIKE ? OR email LIKE ? OR phone LIKE ?";
                            $types = str_repeat("s", 6);
                            $search_param = "%" . $search . "%";
                            $params = array_fill(0, 6, $search_param);
                        }
                        
                        $sql = "SELECT uid, firstname, lastname, username, phone, email, u_total_point, u_role, u_deta FROM users $search_query ORDER BY uid DESC";
                        $stmt = $conn->prepare($sql);
                        
                        if(!empty($params)){
                            $stmt->bind_param($types, ...$params);
                        }
                        
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if($result->num_rows > 0){
                            while($data = $result->fetch_assoc()){
                                // กำหนดสี badge ตามสถานะ
                                $badge_class = "";
                                $role_icon = "";
                                
                                switch($data['u_role']) {
                                    case 'member':
                                        $badge_class = 'badge-member';
                                        $role_icon = 'bi-person';
                                        $role_text = 'สมาชิก';
                                        break;
                                    case 'admin':
                                        $badge_class = 'badge-admin';
                                        $role_icon = 'bi-shield-check';
                                        $role_text = 'แอดมิน';
                                        break;
                                    case 'Super admin':
                                        $badge_class = 'badge-super-admin';
                                        $role_icon = 'bi-shield-fill-check';
                                        $role_text = 'ซุปเปอร์แอดมิน';
                                        break;
                                    case 'partner':
                                        $badge_class = 'badge-partner';
                                        $role_icon = 'bi-handshake';
                                        $role_text = 'พาร์ทเนอร์';
                                        break;
                                    case 'suspend':
                                        $badge_class = 'badge-suspend';
                                        $role_icon = 'bi-person-x';
                                        $role_text = 'ระงับการใช้งาน';
                                        break;
                                    default:
                                        $badge_class = 'badge-secondary';
                                        $role_icon = 'bi-person';
                                        $role_text = $data['u_role'];
                                }
                        ?>
                        <tr>
                            <td class="text-center fw-bold">
                                #<?php echo str_pad($data['uid'], 4, "0", STR_PAD_LEFT); ?>
                            </td>
                            <td>
                                <?php echo htmlspecialchars($data['firstname'] . ' ' . $data['lastname']); ?>
                                <br>
                                <small class="text-muted">สมัคร: <?php echo date('d/m/Y', strtotime($data['u_deta'])); ?></small>
                            </td>
                            <td>
                                <span class="fw-medium"><?php echo htmlspecialchars($data['username']); ?></span>
                            </td>
                            <td>
                                <span class="text-muted"><?php echo htmlspecialchars($data['phone']); ?></span>
                            </td>
                            <td>
                                <span class="text-muted"><?php echo htmlspecialchars($data['email']); ?></span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success rounded-pill">
                                    <?php echo number_format($data['u_total_point']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge <?php echo $badge_class; ?> rounded-pill">
                                    <i class="bi <?php echo $role_icon; ?>"></i>
                                    <?php echo $role_text; ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex flex-wrap justify-content-center gap-2">
                                    <!-- ปุ่มแก้ไข - เปิด Modal -->
                                    <button class="btn btn-warning btn-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUserModal"
                                            data-uid="<?php echo $data['uid']; ?>"
                                            data-firstname="<?php echo htmlspecialchars($data['firstname']); ?>"
                                            data-lastname="<?php echo htmlspecialchars($data['lastname']); ?>"
                                            data-username="<?php echo htmlspecialchars($data['username']); ?>"
                                            data-phone="<?php echo htmlspecialchars($data['phone']); ?>"
                                            data-email="<?php echo htmlspecialchars($data['email']); ?>"
                                            data-points="<?php echo $data['u_total_point']; ?>"
                                            data-role="<?php echo $data['u_role']; ?>">
                                        <i class="bi bi-pencil"></i> แก้ไข
                                    </button>
                                    
                                    <!-- ปุ่มระงับ/กู้คืน -->
                                    <?php if($data['u_role'] == 'suspend'): ?>
                                        <a href="?page=<?php echo $_GET['page'] ?? ''; ?>&action=unsuspend&uid=<?php echo $data['uid']; ?>" 
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('คุณต้องการกู้คืนการใช้งานผู้ใช้นี้ใช่หรือไม่?')">
                                            <i class="bi bi-person-check"></i> กู้คืน
                                        </a>
                                    <?php else: ?>
                                        <a href="?page=<?php echo $_GET['page'] ?? ''; ?>&action=suspend&uid=<?php echo $data['uid']; ?>" 
                                           class="btn btn-warning btn-sm text-white"
                                           onclick="return confirm('คุณต้องการระงับการใช้งานผู้ใช้นี้ใช่หรือไม่?')">
                                            <i class="bi bi-person-x"></i> ระงับ
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- ปุ่มลบ (ไม่แสดงสำหรับแอดมิน) -->
                                    <?php if(!in_array($data['u_role'], ['admin', 'Super admin'])): ?>
                                    <a href="?page=<?php echo $_GET['page'] ?? ''; ?>&action=delete&uid=<?php echo $data['uid']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?\\nการลบจะไม่สามารถกู้คืนได้')">
                                        <i class="bi bi-trash"></i> ลบ
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-people display-4 text-muted mb-3"></i>
                                <h5 class="text-muted">ไม่พบข้อมูลผู้ใช้งาน</h5>
                                <?php if(isset($_POST['search'])): ?>
                                <p class="text-muted">ผลการค้นหา: "<?php echo htmlspecialchars($_POST['search']); ?>"</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php 
                        }
                        $stmt->close();
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal แก้ไขข้อมูลผู้ใช้ -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square"></i> แก้ไขข้อมูลผู้ใช้งาน
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <input type="hidden" id="editUid" name="uid">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" id="editFirstName" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" id="editLastName" name="lastname" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                            <input type="text" id="editUsername" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" id="editPhone" name="phone" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" id="editEmail" name="email" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">คะแนนสะสม</label>
                            <input type="number" id="editPoints" name="u_total_point" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select id="editRole" name="u_role" class="form-select" required>
                                <option value="member">สมาชิกทั่วไป</option>
                                <option value="admin">ผู้ดูแลระบบ</option>
                                <option value="partner">พาร์ทเนอร์</option>
                                <option value="suspend">ระงับการใช้งาน</option>
                                <option value="Super admin">ซุปเปอร์แอดมิน</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">กรอกเฉพาะเมื่อต้องการเปลี่ยนรหัสผ่าน</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ยกเลิก
                    </button>
                    <button type="submit" name="edit_user" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> บันทึกการเปลี่ยนแปลง
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal เพิ่มผู้ใช้ใหม่ -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus"></i> เพิ่มผู้ใช้งานใหม่
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="post">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" name="firstname" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" name="lastname" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อผู้ใช้ <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">อีเมล <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ยืนยันรหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">คะแนนเริ่มต้น</label>
                            <input type="number" name="u_total_point" class="form-control" min="0" value="10">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สถานะ <span class="text-danger">*</span></label>
                            <select name="u_role" class="form-select" required>
                                <option value="member" selected>สมาชิกทั่วไป</option>
                                <option value="admin">ผู้ดูแลระบบ</option>
                                <option value="partner">พาร์ทเนอร์</option>
                                <option value="suspend">ระงับการใช้งาน</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> ยกเลิก
                    </button>
                    <button type="submit" name="add_user" class="btn btn-success">
                        <i class="bi bi-person-plus"></i> เพิ่มผู้ใช้งาน
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript Libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// JavaScript สำหรับโหลดข้อมูลเข้า Modal แก้ไข
document.addEventListener('DOMContentLoaded', function() {
    var editModal = document.getElementById('editUserModal');
    
    editModal.addEventListener('show.bs.modal', function(event) {
        var button = event.relatedTarget;
        
        // ดึงข้อมูลจากปุ่มที่คลิก
        var uid = button.getAttribute('data-uid');
        var firstname = button.getAttribute('data-firstname');
        var lastname = button.getAttribute('data-lastname');
        var username = button.getAttribute('data-username');
        var phone = button.getAttribute('data-phone');
        var email = button.getAttribute('data-email');
        var points = button.getAttribute('data-points');
        var role = button.getAttribute('data-role');
        
        // ใส่ข้อมูลลงในฟอร์ม
        document.getElementById('editUid').value = uid;
        document.getElementById('editFirstName').value = firstname;
        document.getElementById('editLastName').value = lastname;
        document.getElementById('editUsername').value = username;
        document.getElementById('editPhone').value = phone;
        document.getElementById('editEmail').value = email;
        document.getElementById('editPoints').value = points;
        document.getElementById('editRole').value = role;
    });
    
    // ฟังก์ชันยืนยันการลบ
    document.querySelectorAll('a[href*="action=delete"]').forEach(link => {
        link.addEventListener('click', function(e) {
            if(!confirm('คุณแน่ใจหรือไม่ว่าต้องการลบผู้ใช้นี้?')) {
                e.preventDefault();
            }
        });
    });
});
</script>