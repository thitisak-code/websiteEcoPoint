<?php
include 'includes/config.php';

$login_error = "";

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['Login'])) {
    $user = $_POST['username'];
    $pass = $_POST['password']; // ❗ ไม่ hash ตรงนี้

    // ดึงข้อมูลจาก username อย่างเดียว
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $data = $result->fetch_assoc();
        
        // ตรวจสอบว่าผู้ใช้ถูกระงับหรือไม่
        if ($data['u_role'] == 'suspend') {
            $login_error = "บัญชีผู้ใช้นี้ถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ";
        } else {
            // ตรวจรหัสผ่านที่ hash ไว้
            if (password_verify($pass, $data['password'])) {
                $_SESSION['uid'] = $data['uid'];
                $_SESSION['firstname'] = $data['firstname'];
                $_SESSION['lastname'] = $data['lastname'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['role'] = $data['u_role'];
                $_SESSION['image'] = $data['image'];

                echo "<script>
                    window.location='index.php?page=home';
                </script>";
                exit;
            } else {
                $login_error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
            }
        }
    } else {
        $login_error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
if(isset($_SESSION['uid'])) {
    // ถ้าเข้าสู่ระบบแล้วให้ไปหน้า home
    echo "<script>
        window.location='index.php?page=home';
    </script>";
    exit;
}
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Noto Sans Thai', sans-serif;
    }

    .login-wrapper {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    .login-container {
        width: 100%;
        max-width: 450px;
        animation: fadeIn 0.8s ease-out;
    }

    .login-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-header h1 {
        color: #2e7d32;
        font-size: 2.5rem;
        margin-bottom: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .login-header p {
        color: #666;
        font-size: 1.1rem;
    }

    .login-box {
        background: #fff;
        padding: 40px;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .login-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(50, 50, 93, 0.15), 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .form-group {
        margin-bottom: 25px;
        position: relative;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-input {
        width: 100%;
        padding: 15px 15px 15px 45px;
        border: 2px solid #e0e0e0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background-color: #f9f9f9;
    }

    .form-input:focus {
        outline: none;
        border-color: #4caf50;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
    }

    .password-container {
        position: relative;
    }

    .show-pass {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        color: #4caf50;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
        background: white;
        padding: 5px 10px;
        border-radius: 8px;
        border: 1px solid #e0e0e0;
    }

    .show-pass:hover {
        color: #388e3c;
        background: #f5f5f5;
    }

    .btn-login {
        width: 100%;
        padding: 16px;
        background: linear-gradient(to right, #4caf50, #2e7d32);
        border: none;
        color: #fff;
        border-radius: 12px;
        cursor: pointer;
        font-size: 1.1rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        margin-top: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-login:hover {
        background: linear-gradient(to right, #43a047, #1b5e20);
        transform: translateY(-2px);
        box-shadow: 0 7px 14px rgba(76, 175, 80, 0.3);
    }

    .btn-login:active {
        transform: translateY(0);
    }

    .error {
        background: linear-gradient(to right, #ffebee, #ffcdd2);
        color: #c62828;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 0.95rem;
        border-left: 5px solid #c62828;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: shake 0.5s ease;
    }

    .error i {
        font-size: 1.2rem;
    }
    
    .suspend-error {
        background: linear-gradient(to right, #fff3e0, #ffe0b2);
        color: #ef6c00;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 25px;
        font-size: 0.95rem;
        border-left: 5px solid #ef6c00;
        display: flex;
        align-items: center;
        gap: 10px;
        animation: shake 0.5s ease;
    }

    .register-link {
        text-align: center;
        margin-top: 25px;
        color: #666;
        font-size: 1rem;
    }

    .register-link a {
        color: #4caf50;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .register-link a:hover {
        color: #2e7d32;
        text-decoration: underline;
    }

    .input-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #1ca046ff;
        font-size: 1.2rem;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    /* Responsive Design */
    @media (max-width: 576px) {
        .login-box {
            padding: 30px 25px;
        }
        
        .login-header h1 {
            font-size: 2rem;
        }
        
        .login-header p {
            font-size: 1rem;
        }
    }

    /* Decorative elements */
    .leaf-decoration {
        position: fixed;
        font-size: 1.5rem;
        color: #4caf50;
        opacity: 0.7;
        z-index: -1;
    }

    .leaf-1 {
        top: 10%;
        left: 5%;
        transform: rotate(45deg);
    }

    .leaf-2 {
        bottom: 15%;
        right: 5%;
        transform: rotate(-20deg);
    }

    .leaf-3 {
        top: 20%;
        right: 8%;
        transform: rotate(15deg);
    }

    .form-input::placeholder {
        color: #aaa;
    }
    
    /* Admin Panel Link (for debugging) */
    .admin-link {
        text-align: center;
        margin-top: 15px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 10px;
        border: 1px dashed #dee2e6;
    }
    
    .admin-link a {
        color: #6c757d;
        text-decoration: none;
        font-size: 0.9rem;
    }
    
    .admin-link a:hover {
        color: #4caf50;
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <!-- Decorative elements -->
    <div class="leaf-decoration leaf-1">
        <i class="fas fa-leaf"></i>
    </div>
    <div class="leaf-decoration leaf-2">
        <i class="fas fa-leaf"></i>
    </div>
    <div class="leaf-decoration leaf-3">
        <i class="fas fa-leaf"></i>
    </div>
    
    <div class="login-container">
        <div class="login-header">
            <h1><i class="fas fa-user-circle"></i> เข้าสู่ระบบ</h1>
            <p>กรุณากรอกข้อมูลเพื่อเข้าสู่ระบบ</p>
        </div>
        
        <div class="login-box">
            <?php 
            // แสดงข้อความแจ้งเตือนต่างๆ
            if(isset($_GET['message'])) {
                $message = $_GET['message'];
                if($message == 'suspended') {
                    echo '<div class="suspend-error">
                        <i class="fas fa-user-slash"></i>
                        <span>บัญชีของคุณถูกระงับการใช้งาน กรุณาติดต่อผู้ดูแลระบบ</span>
                    </div>';
                } elseif($message == 'loggedout') {
                    echo '<div class="error" style="background:linear-gradient(to right, #e8f5e9, #c8e6c9); color:#2e7d32; border-left-color:#2e7d32;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>คุณได้ออกจากระบบเรียบร้อยแล้ว</span>
                    </div>';
                }
            }
            
            if($login_error != ""): 
                $error_class = (strpos($login_error, 'ระงับ') !== false) ? 'suspend-error' : 'error';
            ?>
                <div class="<?php echo $error_class; ?>">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= $login_error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> ชื่อผู้ใช้</label>
                    <div class="input-container password-container">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-input" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                               placeholder="กรุณากรอกชื่อผู้ใช้" 
                               required
                               autofocus>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> รหัสผ่าน</label>
                    <div class="input-container password-container">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               class="form-input" 
                               placeholder="กรุณากรอกรหัสผ่าน" 
                               required>
                        <div class="show-pass" onclick="togglePassword()">
                            <i class="fas fa-eye"></i>
                            <span>แสดงรหัสผ่าน</span>
                        </div>
                    </div>
                </div>

                <button class="btn-login" type="submit" name="Login">
                    <i class="fas fa-sign-in-alt"></i> เข้าสู่ระบบ
                </button>
            </form>

            <div class="register-link">
                ยังไม่มีบัญชี? <a href="?page=register">สมัครสมาชิก</a>
            </div>
            
            <!-- ลิงก์ไปหน้าจัดการผู้ใช้ (สำหรับทดสอบ) -->
            <?php 
            // ตรวจสอบว่ามีแอดมินในระบบหรือไม่
            $admin_check = $conn->query("SELECT COUNT(*) as count FROM users WHERE u_role IN ('admin', 'Super admin')");
            $admin_count = $admin_check->fetch_assoc()['count'];
            
            if($admin_count == 0): ?>
            <div class="admin-link">
                <a href="?page=admin-setup">ตั้งค่าผู้ดูแลระบบ</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.querySelector('.show-pass i');
        const eyeText = document.querySelector('.show-pass span');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.className = 'fas fa-eye-slash';
            eyeText.textContent = 'ซ่อนรหัสผ่าน';
        } else {
            passwordInput.type = 'password';
            eyeIcon.className = 'fas fa-eye';
            eyeText.textContent = 'แสดงรหัสผ่าน';
        }
    }

    // เพิ่ม animation เมื่อโหลดหน้าเสร็จ
    document.addEventListener('DOMContentLoaded', function() {
        const loginBox = document.querySelector('.login-box');
        if (loginBox) {
            loginBox.style.opacity = '0';
            setTimeout(() => {
                loginBox.style.opacity = '1';
                loginBox.style.transition = 'opacity 0.5s ease';
            }, 100);
        }
        
        // เพิ่มฟังก์ชันกด Enter เพื่อ submit form
        document.getElementById('password').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('form').submit();
            }
        });
        
        // Auto-focus ที่ช่อง username
        document.getElementById('username').focus();
    });
</script>

<?php
// ส่วนโค้ดสำหรับหน้าจัดการผู้ใช้ (สำหรับแอดมิน)
if(isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'Super admin'])) {
    echo '<style>
        .admin-panel {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
        }
        
        .admin-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 600;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .admin-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
        }
        
        .admin-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1001;
            justify-content: center;
            align-items: center;
        }
        
        .admin-modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
        }
        
        .admin-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .close-admin {
            font-size: 28px;
            cursor: pointer;
            color: #666;
        }
        
        .close-admin:hover {
            color: #333;
        }
    </style>';
    
    echo '<div class="admin-panel">
        <button class="admin-btn" onclick="openAdminPanel()">
            <i class="fas fa-users-cog"></i> จัดการผู้ใช้
        </button>
    </div>
    
    <div class="admin-modal" id="adminModal">
        <div class="admin-modal-content">
            <div class="admin-modal-header">
                <h3><i class="fas fa-users"></i> จัดการผู้ใช้งานระบบ</h3>
                <span class="close-admin" onclick="closeAdminPanel()">&times;</span>
            </div>';
    
    // ดึงข้อมูลผู้ใช้ทั้งหมด
    $users_query = $conn->query("SELECT * FROM users ORDER BY u_role DESC, uid DESC");
    
    echo '<div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>ชื่อ - นามสกุล</th>
                    <th>ชื่อผู้ใช้</th>
                    <th>สถานะ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>';
    
    while($user = $users_query->fetch_assoc()) {
        $status_class = '';
        $status_text = '';
        $status_icon = '';
        
        switch($user['u_role']) {
            case 'suspend':
                $status_class = 'bg-danger';
                $status_text = 'ระงับการใช้งาน';
                $status_icon = 'fa-user-slash';
                break;
            case 'admin':
            case 'Super admin':
                $status_class = 'bg-warning text-dark';
                $status_text = 'ผู้ดูแลระบบ';
                $status_icon = 'fa-user-shield';
                break;
            case 'partner':
                $status_class = 'bg-info';
                $status_text = 'พาร์ทเนอร์';
                $status_icon = 'fa-handshake';
                break;
            default:
                $status_class = 'bg-success';
                $status_text = 'สมาชิกทั่วไป';
                $status_icon = 'fa-user';
        }
        
        echo '<tr>
            <td>#' . $user['uid'] . '</td>
            <td>' . $user['firstname'] . ' ' . $user['lastname'] . '</td>
            <td>' . $user['username'] . '</td>
            <td>
                <span class="badge ' . $status_class . ' rounded-pill">
                    <i class="fas ' . $status_icon . '"></i> ' . $status_text . '
                </span>
            </td>
            <td>';
        
        // ปุ่มจัดการ
        if($user['u_role'] == 'suspend') {
            echo '<a href="includes/unsuspend_user.php?uid=' . $user['uid'] . '" class="btn btn-sm btn-success">
                <i class="fas fa-user-check"></i> กู้คืน
            </a>';
        } else {
            echo '<a href="includes/suspend_user.php?uid=' . $user['uid'] . '" class="btn btn-sm btn-warning">
                <i class="fas fa-user-slash"></i> ระงับ
            </a>';
        }
        
        if(!in_array($user['u_role'], ['admin', 'Super admin'])) {
            echo '<a href="includes/delete_user.php?uid=' . $user['uid'] . '" class="btn btn-sm btn-danger" onclick="return confirm(\'แน่ใจหรือไม่ที่จะลบผู้ใช้นี้?\')">
                <i class="fas fa-trash"></i> ลบ
            </a>';
        }
        
        echo '</td></tr>';
    }
    
    echo '</tbody>
        </table>
    </div>
    <div class="text-center mt-3">
        <button class="btn btn-secondary" onclick="closeAdminPanel()">ปิด</button>
    </div>
    </div>
</div>
    
    <script>
    function openAdminPanel() {
        document.getElementById("adminModal").style.display = "flex";
    }
    
    function closeAdminPanel() {
        document.getElementById("adminModal").style.display = "none";
    }
    
    // ปิด modal เมื่อคลิกนอกพื้นที่
    window.onclick = function(event) {
        const modal = document.getElementById("adminModal");
        if (event.target == modal) {
            closeAdminPanel();
        }
    }
    </script>';
}

// สร้างไฟล์สำหรับจัดการผู้ใช้ (ถ้ายังไม่มี)
if (!file_exists('includes/suspend_user.php')) {
    $suspend_code = '<?php
session_start();
include "config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    // ตรวจสอบว่าไม่ใช่การระงับตัวเอง
    if($_SESSION["uid"] != $uid) {
        $stmt = $conn->prepare("UPDATE users SET u_role = ? WHERE uid = ?");
        $suspend_role = "suspend";
        $stmt->bind_param("si", $suspend_role, $uid);
        
        if($stmt->execute()) {
            echo "<script>alert(\'ระงับผู้ใช้งานสำเร็จ\'); window.history.back();</script>";
        } else {
            echo "<script>alert(\'เกิดข้อผิดพลาด\'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert(\'ไม่สามารถระงับบัญชีตัวเองได้\'); window.history.back();</script>";
    }
} else {
    echo "<script>alert(\'คุณไม่มีสิทธิ์ในการดำเนินการนี้\'); window.history.back();</script>";
}
?>';
    
    file_put_contents('includes/suspend_user.php', $suspend_code);
}

if (!file_exists('includes/unsuspend_user.php')) {
    $unsuspend_code = '<?php
session_start();
include "config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    $stmt = $conn->prepare("UPDATE users SET u_role = ? WHERE uid = ?");
    $member_role = "member";
    $stmt->bind_param("si", $member_role, $uid);
    
    if($stmt->execute()) {
        echo "<script>alert(\'กู้คืนผู้ใช้งานสำเร็จ\'); window.history.back();</script>";
    } else {
        echo "<script>alert(\'เกิดข้อผิดพลาด\'); window.history.back();</script>";
    }
} else {
    echo "<script>alert(\'คุณไม่มีสิทธิ์ในการดำเนินการนี้\'); window.history.back();</script>";
}
?>';
    
    file_put_contents('includes/unsuspend_user.php', $unsuspend_code);
}

if (!file_exists('includes/delete_user.php')) {
    $delete_code = '<?php
session_start();
include "config.php";

if(isset($_SESSION["role"]) && in_array($_SESSION["role"], ["admin", "Super admin"]) && isset($_GET["uid"])) {
    $uid = intval($_GET["uid"]);
    
    // ตรวจสอบก่อนว่าผู้ใช้นี้ไม่ใช่แอดมิน
    $checkStmt = $conn->prepare("SELECT u_role FROM users WHERE uid = ?");
    $checkStmt->bind_param("i", $uid);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    
    if($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        if(!in_array($user["u_role"], ["admin", "Super admin"])) {
            $deleteStmt = $conn->prepare("DELETE FROM users WHERE uid = ?");
            $deleteStmt->bind_param("i", $uid);
            
            if($deleteStmt->execute()) {
                echo "<script>alert(\'ลบผู้ใช้งานสำเร็จ\'); window.history.back();</script>";
            } else {
                echo "<script>alert(\'เกิดข้อผิดพลาด\'); window.history.back();</script>";
            }
        } else {
            echo "<script>alert(\'ไม่สามารถลบผู้ดูแลระบบได้\'); window.history.back();</script>";
        }
    }
} else {
    echo "<script>alert(\'คุณไม่มีสิทธิ์ในการดำเนินการนี้\'); window.history.back();</script>";
}
?>';
    
    file_put_contents('includes/delete_user.php', $delete_code);
}
?>