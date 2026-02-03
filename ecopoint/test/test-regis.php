<?php
include_once 'includes/config.php';

$step = 1;
$error = "";

// STEP 1 → STEP 2
if (isset($_POST['next'])) {
    if (!empty($_POST['firstname']) && !empty($_POST['lastname']) && 
        !empty($_POST['phone']) && !empty($_POST['email'])) {
        $step = 2;
    } else {
        $error = "กรุณากรอกข้อมูลให้ครบทุกช่อง";
    }
}

// REGISTER
if (isset($_POST['register'])) {
    $firstname = $_POST['firstname'];
    $lastname  = $_POST['lastname'];
    $phone     = $_POST['phone'];
    $email     = $_POST['email'];
    $user      = $_POST['username'];
    $pass      = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // upload รูป
    $dir = "uploads/users/";
    $image = "";
    
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $move = $dir . $image;
    }

    $stmt = $conn->prepare(
        "INSERT INTO users(firstname,lastname,username,password,phone,email,image)
         VALUES (?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "sssssss",
        $firstname, $lastname, $user, $pass, $phone, $email, $image
    );

    if ($stmt->execute()) {
        if ($image) {
            move_uploaded_file($_FILES['image']['tmp_name'], $move);
        }
        echo "<script>
            alert('สมัครสมาชิกสำเร็จ');
            window.location='index.php?page=login';
        </script>";
        exit;
    } else {
        $error = "เกิดข้อผิดพลาดในการสมัครสมาชิก";
        $step = 2;
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

.register-wrapper {
    background: linear-gradient(135deg, #4fa167ff 0%, #cfcbd3ff 100%);
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.register-container {
    width: 100%;
    max-width: 800px;
    animation: fadeIn 0.8s ease-out;
}

.register-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
    transition: transform 0.3s ease;
}

.register-card:hover {
    transform: translateY(-5px);
}

.card-header {
    background: linear-gradient(to right, #4caf50, #2e7d32);
    color: white;
    padding: 30px;
    text-align: center;
}

.card-header h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.card-header p {
    font-size: 1.1rem;
    opacity: 0.9;
}

.card-body {
    padding: 40px;
}

/* Progress Bar */
.progress-container {
    margin-bottom: 40px;
}

.progress-steps {
    display: flex;
    justify-content: space-between;
    position: relative;
    margin-bottom: 20px;
}

.progress-steps::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 4px;
    background: #e0e0e0;
    transform: translateY(-50%);
    z-index: 1;
}

.progress-bar {
    position: absolute;
    top: 50%;
    left: 0;
    height: 4px;
    background: #4caf50;
    transform: translateY(-50%);
    transition: width 0.4s ease;
    z-index: 2;
}

.step {
    width: 40px;
    height: 40px;
    background: #fff;
    border: 3px solid #e0e0e0;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #666;
    position: relative;
    z-index: 3;
    transition: all 0.3s ease;
}

.step.active {
    border-color: #4caf50;
    background: #4caf50;
    color: white;
}

.step-label {
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    white-space: nowrap;
    color: #666;
    font-size: 0.9rem;
}

.step.active .step-label {
    color: #4caf50;
    font-weight: 600;
}

/* Form Styles */
.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 25px;
    margin-bottom: 30px;
}

.form-group {
    margin-bottom: 25px;
}

.form-group.full-width {
    grid-column: span 2;
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

.form-control {
    width: 100%;
    padding: 15px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background-color: #f9f9f9;
}

.form-control:focus {
    outline: none;
    border-color: #4caf50;
    background-color: #fff;
    box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
}

.password-container {
    position: relative;
}

.toggle-password {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    background: none;
    border: none;
    font-size: 1.2rem;
}

/* File Upload Styling */
.file-upload {
    position: relative;
    border: 2px dashed #4caf50;
    border-radius: 12px;
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8fff8;
}

.file-upload:hover {
    background: #f0fff0;
    border-color: #388e3c;
}

.file-upload input[type="file"] {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    opacity: 0;
    cursor: pointer;
}

.file-upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 15px;
}

.file-upload-icon {
    font-size: 3rem;
    color: #4caf50;
}

.file-upload-text {
    color: #333;
    font-size: 1.1rem;
}

.file-upload-subtext {
    color: #666;
    font-size: 0.9rem;
}

.preview-container {
    margin-top: 20px;
    text-align: center;
}

.image-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #4caf50;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

/* Buttons */
.button-group {
    display: flex;
    gap: 15px;
    margin-top: 30px;
}

.btn {
    flex: 1;
    padding: 16px;
    border: none;
    border-radius: 12px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-primary {
    background: linear-gradient(to right, #4caf50, #2e7d32);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(to right, #43a047, #1b5e20);
    transform: translateY(-2px);
    box-shadow: 0 7px 14px rgba(76, 175, 80, 0.3);
}

.btn-secondary {
    background: #f5f5f5;
    color: #666;
    border: 2px solid #e0e0e0;
}

.btn-secondary:hover {
    background: #e0e0e0;
    transform: translateY(-2px);
}

.btn-success {
    background: linear-gradient(to right, #2196F3, #1976D2);
    color: white;
}

.btn-success:hover {
    background: linear-gradient(to right, #1976D2, #0D47A1);
    transform: translateY(-2px);
    box-shadow: 0 7px 14px rgba(33, 150, 243, 0.3);
}

/* Error & Success Messages */
.alert {
    padding: 15px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
    animation: shake 0.5s ease;
}

.alert-danger {
    background: linear-gradient(to right, #ffebee, #ffcdd2);
    color: #c62828;
    border-left: 5px solid #c62828;
}

/* Login Link */
.login-link {
    text-align: center;
    margin-top: 30px;
    color: #666;
    font-size: 1rem;
}

.login-link a {
    color: #4caf50;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
}

.login-link a:hover {
    color: #2e7d32;
    text-decoration: underline;
}

/* Animations */
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

/* Responsive */
@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-group.full-width {
        grid-column: span 1;
    }
    
    .register-container {
        max-width: 100%;
    }
    
    .card-body {
        padding: 30px 20px;
    }
    
    .button-group {
        flex-direction: column;
    }
    
    .step-label {
        font-size: 0.8rem;
        bottom: -25px;
    }
}

@media (max-width: 576px) {
    .card-header h1 {
        font-size: 2rem;
    }
    
    .step {
        width: 35px;
        height: 35px;
        font-size: 0.9rem;
    }
}
</style>

<div class="register-wrapper">
    <div class="register-container">
        <div class="register-card">
            <div class="card-header">
                <h1><i class="fas fa-user-plus"></i> สมัครสมาชิก</h1>
                <p>กรอกข้อมูลของคุณเพื่อสร้างบัญชีใหม่</p>
            </div>
            
            <div class="card-body">
                <!-- Progress Bar -->
                <div class="progress-container">
                    <div class="progress-steps">
                        <div class="progress-bar" style="width: <?= $step == 1 ? '0%' : '50%' ?>;"></div>
                        <div class="step <?= $step == 1 ? 'active' : '' ?>">
                            1
                            <span class="step-label">ข้อมูลส่วนตัว</span>
                        </div>
                        <div class="step <?= $step == 2 ? 'active' : '' ?>">
                            2
                            <span class="step-label">ข้อมูลเข้าสู่ระบบ</span>
                        </div>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?= $error ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <!-- STEP 1 -->
                    <?php if ($step == 1): ?>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="firstname"><i class="fas fa-user"></i> ชื่อ</label>
                                <input type="text" 
                                       name="firstname" 
                                       id="firstname" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" 
                                       placeholder="กรุณากรอกชื่อ" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="lastname"><i class="fas fa-user-tag"></i> นามสกุล</label>
                                <input type="text" 
                                       name="lastname" 
                                       id="lastname" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" 
                                       placeholder="กรุณากรอกนามสกุล" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> เบอร์โทรศัพท์</label>
                                <input type="text" 
                                       name="phone" 
                                       id="phone" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                                       placeholder="กรุณากรอกเบอร์โทรศัพท์" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> อีเมล</label>
                                <input type="email" 
                                       name="email" 
                                       id="email" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                       placeholder="กรุณากรอกอีเมล" 
                                       required>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="submit" name="next" class="btn btn-primary">
                                <i class="fas fa-arrow-right"></i> ถัดไป
                            </button>
                        </div>
                    <?php endif; ?>

                    <!-- STEP 2 -->
                    <?php if ($step == 2): ?>
                        <input type="hidden" name="firstname" value="<?= htmlspecialchars($_POST['firstname']) ?>">
                        <input type="hidden" name="lastname" value="<?= htmlspecialchars($_POST['lastname']) ?>">
                        <input type="hidden" name="phone" value="<?= htmlspecialchars($_POST['phone']) ?>">
                        <input type="hidden" name="email" value="<?= htmlspecialchars($_POST['email']) ?>">

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="username"><i class="fas fa-user-circle"></i> ชื่อผู้ใช้</label>
                                <input type="text" 
                                       name="username" 
                                       id="username" 
                                       class="form-control" 
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" 
                                       placeholder="กรุณากรอกชื่อผู้ใช้" 
                                       required>
                            </div>

                            <div class="form-group">
                                <label for="password"><i class="fas fa-lock"></i> รหัสผ่าน</label>
                                <div class="password-container">
                                    <input type="password" 
                                           name="password" 
                                           id="password" 
                                           class="form-control" 
                                           placeholder="กรุณากรอกรหัสผ่าน" 
                                           required>
                                    <button type="button" class="toggle-password" onclick="togglePassword()">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label><i class="fas fa-image"></i> รูปโปรไฟล์</label>
                                <div class="file-upload">
                                    <input type="file" 
                                           name="image" 
                                           id="image" 
                                           accept="image/*" 
                                           onchange="previewImage(this)">
                                    <div class="file-upload-content">
                                        <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                        <div class="file-upload-text">คลิกเพื่อเลือกรูปภาพ</div>
                                        <div class="file-upload-subtext">รองรับไฟล์ JPG, PNG, GIF, WebP</div>
                                    </div>
                                </div>
                                
                                <div class="preview-container" id="previewContainer" style="display: none;">
                                    <img class="image-preview" id="imagePreview" src="" alt="Preview">
                                    <p class="text-muted mt-2">ตัวอย่างภาพโปรไฟล์</p>
                                </div>
                            </div>
                        </div>

                        <div class="button-group">
                            <button type="button" onclick="history.back()" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> ย้อนกลับ
                            </button>
                            <button type="submit" name="register" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> สมัครสมาชิก
                            </button>
                        </div>
                    <?php endif; ?>
                </form>

                <div class="login-link">
                    มีบัญชีอยู่แล้ว? <a href="index.php?page=login">เข้าสู่ระบบ</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle Password Visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.querySelector('.toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'fas fa-eye-slash';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'fas fa-eye';
    }
}

// Image Preview
function previewImage(input) {
    const previewContainer = document.getElementById('previewContainer');
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewContainer.style.display = 'none';
        preview.src = '';
    }
}

// Add animation on load
document.addEventListener('DOMContentLoaded', function() {
    const card = document.querySelector('.register-card');
    if (card) {
        card.style.opacity = '0';
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transition = 'opacity 0.5s ease';
        }, 100);
    }
});
</script>