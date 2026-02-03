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
    } else {
        $login_error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง";
    }
}
?>

<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            <?php if($login_error != ""): ?>
                <div class="error">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span><?= $login_error ?></span>
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> ชื่อผู้ใช้</label>
                    <div class="input-container">
                        <i class="fas fa-user input-icon"></i>
                        <input type="text" 
                               name="username" 
                               id="username" 
                               class="form-input" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                               placeholder="กรุณากรอกชื่อผู้ใช้" 
                               required>
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
    });
</script>