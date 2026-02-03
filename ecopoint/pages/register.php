<?php
// สมมติว่าใน config.php มีการเชื่อมต่อฐานข้อมูลตัวแปร $conn
// include_once 'includes/config.php'; 

// ==========================================
// ส่วน Logic PHP & Database Insert
// ==========================================

$step = 1;
$error = "";
$success = "";
$lang = $_GET['lang'] ?? $_SESSION['lang'] ?? 'th';

// --- เนื้อหาข้อความ (Language Config) ---
$content = [
    'th' => [
        'title' => 'สมัครสมาชิก',
        'subtitle' => 'กรอกข้อมูลของคุณเพื่อสร้างบัญชีใหม่',
        'steps' => ['ข้อมูลส่วนตัว', 'ข้อมูลเข้าสู่ระบบ'],
        'labels' => [
            'firstname' => 'ชื่อ', 'lastname' => 'นามสกุล', 'phone' => 'เบอร์โทรศัพท์',
            'email' => 'อีเมล', 'username' => 'ชื่อผู้ใช้', 'password' => 'รหัสผ่าน',
            'confirm_password' => 'ยืนยันรหัสผ่าน', 'image' => 'รูปโปรไฟล์'
        ],
        'placeholders' => [
            'firstname' => 'ระบุชื่อจริง', 'lastname' => 'ระบุนามสกุล',
            'phone' => 'ระบุเบอร์โทรศัพท์', 'email' => 'example@domain.com',
            'username' => 'ตั้งชื่อผู้ใช้ของคุณ', 'password' => 'รหัสผ่าน 8 ตัวอักษรขึ้นไป',
            'confirm_password' => 'กรอกรหัสผ่านอีกครั้ง'
        ],
        'buttons' => ['next' => 'ถัดไป', 'back' => 'ย้อนกลับ', 'register' => 'ยืนยันการสมัคร'],
        'file_upload' => ['title' => 'คลิกเพื่อเลือกรูปภาพ', 'subtitle' => 'JPG, PNG, GIF (สูงสุด 2MB)'],
        'login_link' => 'มีบัญชีอยู่แล้ว?', 'login' => 'เข้าสู่ระบบที่นี่',
        'errors' => [
            'required' => 'กรุณากรอกข้อมูลให้ครบทุกช่อง',
            'password_mismatch' => 'รหัสผ่านไม่ตรงกัน',
            'password_weak' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            'email_invalid' => 'รูปแบบอีเมลไม่ถูกต้อง',
            'phone_invalid' => 'รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง',
            'image_invalid' => 'ไฟล์รูปภาพไม่ถูกต้อง',
            'user_exists' => 'ชื่อผู้ใช้หรืออีเมลนี้ถูกใช้งานแล้ว',
            'db_error' => 'เกิดข้อผิดพลาดที่ระบบฐานข้อมูล',
            'upload_error' => 'ไม่สามารถอัปโหลดรูปภาพได้',
            'success' => 'สมัครสมาชิกสำเร็จ! กำลังนำคุณไปหน้าเข้าสู่ระบบ...'
        ]
    ],
    'en' => [ /* (ละไว้เพื่อความกระชับ) */ ]
];
if (!isset($content['en'])) $content['en'] = $content['th']; // Fallback
$current_content = $content[$lang] ?? $content['th'];

// --- Helper Functions ---
function clean_input($data) { return htmlspecialchars(stripslashes(trim($data)), ENT_QUOTES, 'UTF-8'); }
function check_email($email) { return filter_var($email, FILTER_VALIDATE_EMAIL); }
function check_phone($phone) { return preg_match('/^[\d\s\+\-\(\)]{10,15}$/', $phone); }

// --- CSRF Token ---
if (empty($_SESSION['csrf_token'])) { $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); }

// --- Handle Form ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ปุ่มย้อนกลับ
    if (isset($_POST['back'])) {
        $step = 1;
    } 
    // ปุ่มถัดไป (Step 1 -> 2)
    elseif (isset($_POST['next'])) {
        $firstname = clean_input($_POST['firstname']);
        $lastname  = clean_input($_POST['lastname']);
        $phone     = clean_input($_POST['phone']);
        $email     = clean_input($_POST['email']);
        
        if ($firstname && $lastname && $phone && $email) {
            if (!check_email($email)) $error = $current_content['errors']['email_invalid'];
            elseif (!check_phone($phone)) $error = $current_content['errors']['phone_invalid'];
            else $step = 2;
        } else {
            $error = $current_content['errors']['required'];
        }
    } 
    // ปุ่มยืนยัน (Submit ลง DB)
    elseif (isset($_POST['register'])) {
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            $error = 'Invalid Security Token'; $step = 2;
        } else {
            // รับค่า
            $firstname = clean_input($_POST['firstname']);
            $lastname  = clean_input($_POST['lastname']);
            $phone     = clean_input($_POST['phone']);
            $email     = clean_input($_POST['email']);
            $username  = clean_input($_POST['username']);
            $password  = $_POST['password'];
            $c_password = $_POST['confirm_password'];
            
            if ($firstname && $lastname && $phone && $email && $username && $password) {
                if (strlen($password) < 8) { $error = $current_content['errors']['password_weak']; $step = 2; }
                elseif ($password !== $c_password) { $error = $current_content['errors']['password_mismatch']; $step = 2; }
                else {
                    // 1. จัดการรูปภาพ
                    $new_filename = ""; // ถ้าไม่ได้อัปโหลดจะเป็นค่าว่าง (หรือกำหนด default.png ก็ได้)
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                        $file_tmp = $_FILES['image']['tmp_name'];
                        $file_name = $_FILES['image']['name'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        
                        if (in_array($file_ext, $allowed)) {
                            $upload_dir = 'uploads/'; 
                            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                            
                            $new_filename = $username . '_' . time() . '.' . $file_ext;
                            
                            if (!move_uploaded_file($file_tmp, $upload_dir . $new_filename)) {
                                $error = $current_content['errors']['upload_error'];
                                $step = 2;
                            }
                        } else {
                            $error = $current_content['errors']['image_invalid'];
                            $step = 2;
                        }
                    }

                    // 2. Insert ลงฐานข้อมูล (แก้ไขตามคำขอ)
                    if (empty($error)) {
                        $password_hashed = password_hash($password, PASSWORD_DEFAULT);

                        // SQL ตามที่ต้องการ: 7 คอลัมน์
                        $sql = "INSERT INTO users (firstname, lastname, email, phone, username, password, image) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)";
                        
                        if ($stmt = $conn->prepare($sql)) {
                            // Bind 7 ตัวแปร (s=string ทั้งหมด 7 ตัว)
                            $stmt->bind_param("sssssss", 
                                $firstname, 
                                $lastname, 
                                $email, 
                                $phone, 
                                $username, 
                                $password_hashed, 
                                $new_filename
                            );
                            
                            try {
                                if ($stmt->execute()) {
                                    $success = $current_content['errors']['success'];
                                    $step = 1;
                                    $_POST = []; 
                                    echo "<meta http-equiv='refresh' content='2;url=index.php?page=login'>";
                                } else {
                                    throw new Exception($stmt->error);
                                }
                            } catch (Exception $e) {
                                if ($conn->errno == 1062) {
                                    $error = $current_content['errors']['user_exists'];
                                } else {
                                    $error = $current_content['errors']['db_error'] . ": " . $e->getMessage();
                                }
                                $step = 2;
                            }
                            $stmt->close();
                        } else {
                            $error = "Database prepare error: " . $conn->error;
                        }
                    }
                }
            } else {
                $error = $current_content['errors']['required']; $step = 2;
            }
        }
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
    /* Scoped Styles */
    .register-wrapper {
        font-family: 'Poppins', 'Kanit', sans-serif;
        display: flex; justify-content: center; align-items: center;
        min-height: 85vh; padding: 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
    }
    .register-container { width: 100%; max-width: 600px; position: relative; }
    .register-card {
        background: #ffffff; border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        overflow: hidden;
        animation: slideUp 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .rc-header { background: #fff; padding: 35px 30px 20px; text-align: center; }
    .rc-header h2 { color: #2b2d42; font-weight: 600; margin: 0 0 5px; font-size: 1.8rem; }
    .rc-header p { color: #8d99ae; font-size: 0.9rem; margin: 0; }
    .rc-steps { display: flex; justify-content: center; margin-bottom: 30px; position: relative; padding: 0 40px; }
    .rc-steps::before { content: ''; position: absolute; top: 15px; left: 25%; right: 25%; height: 2px; background: #e9ecef; z-index: 1; }
    .step-box { position: relative; z-index: 2; background: #fff; padding: 0 15px; text-align: center; }
    .step-num {
        width: 32px; height: 32px; border-radius: 50%; background: #f8f9fa; border: 2px solid #e9ecef;
        display: flex; align-items: center; justify-content: center; margin: 0 auto 5px; font-weight: 600; color: #adb5bd; transition: 0.3s;
    }
    .step-box.active .step-num { border-color: #4361ee; color: #4361ee; background: #fff; box-shadow: 0 0 0 4px rgba(67, 97, 238, 0.1); }
    .step-box.done .step-num { background: #4361ee; border-color: #4361ee; color: #fff; }
    .step-title { font-size: 0.8rem; color: #8d99ae; font-weight: 500; }
    .step-box.active .step-title { color: #4361ee; }
    .rc-body { padding: 0 40px 40px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    .span-2 { grid-column: span 2; }
    .input-group { margin-bottom: 15px; }
    .input-group label { display: block; font-size: 0.85rem; font-weight: 500; color: #2b2d42; margin-bottom: 8px; }
    .custom-input {
        width: 100%; padding: 12px 15px; border: 2px solid #f1f3f5; border-radius: 12px;
        background: #f8f9fa; color: #495057; font-family: inherit; font-size: 0.95rem; transition: 0.3s;
    }
    .custom-input:focus { outline: none; border-color: #4361ee; background: #fff; box-shadow: 0 4px 12px rgba(67, 97, 238, 0.1); }
    .btn-row { display: flex; gap: 15px; margin-top: 25px; }
    .btn-c {
        flex: 1; padding: 12px; border-radius: 10px; border: none; font-weight: 600; cursor: pointer;
        font-family: inherit; transition: 0.3s; display: flex; justify-content: center; align-items: center; gap: 8px;
    }
    .btn-primary { background: #4361ee; color: #fff; box-shadow: 0 4px 10px rgba(67,97,238,0.3); }
    .btn-primary:hover { background: #3a56d4; transform: translateY(-2px); }
    .btn-back { background: #e9ecef; color: #495057; }
    .btn-back:hover { background: #dee2e6; }
    .btn-success { background: #2ec4b6; color: #fff; box-shadow: 0 4px 10px rgba(46,196,182,0.3); }
    .btn-success:hover { background: #25a297; transform: translateY(-2px); }
    .upload-box {
        border: 2px dashed #ced4da; border-radius: 12px; height: 100px; display: flex; flex-direction: column;
        justify-content: center; align-items: center; cursor: pointer; position: relative; overflow: hidden; transition: 0.3s; background: #f8f9fa;
    }
    .upload-box:hover { border-color: #4361ee; background: #f0f4ff; }
    .upload-box input { position: absolute; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
    .preview-img { width: 60px; height: 60px; border-radius: 50%; object-fit: cover; margin-top: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .alert-msg { padding: 12px 15px; border-radius: 10px; margin-bottom: 20px; font-size: 0.9rem; display: flex; align-items: center; gap: 10px; }
    .alert-err { background: #fff5f5; color: #e63946; border-left: 4px solid #e63946; }
    .alert-ok { background: #f0fffa; color: #2a9d8f; border-left: 4px solid #2a9d8f; }
    @media (max-width: 600px) {
        .form-grid { grid-template-columns: 1fr; }
        .span-2 { grid-column: span 1; }
        .rc-body { padding: 0 20px 30px; }
        .rc-steps { padding: 0; }
        .rc-steps::before { left: 15%; right: 15%; }
    }
</style>

<div class="register-wrapper">
    <div class="register-container">
        <div class="register-card">
            
            <div class="rc-header">
                <h2><?= $current_content['title'] ?></h2>
                <p><?= $current_content['subtitle'] ?></p>
            </div>

            <div class="rc-steps">
                <div class="step-box <?= $step >= 1 ? ($step > 1 ? 'done' : 'active') : '' ?>">
                    <div class="step-num"><?= $step > 1 ? '<i class="fas fa-check"></i>' : '1' ?></div>
                    <div class="step-title"><?= $current_content['steps'][0] ?></div>
                </div>
                <div class="step-box <?= $step == 2 ? 'active' : '' ?>">
                    <div class="step-num">2</div>
                    <div class="step-title"><?= $current_content['steps'][1] ?></div>
                </div>
            </div>

            <div class="rc-body">
                
                <?php if ($error): ?>
                    <div class="alert-msg alert-err"><i class="fas fa-exclamation-circle"></i> <?= $error ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert-msg alert-ok"><i class="fas fa-check-circle"></i> <?= $success ?></div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data" id="regForm">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <?php if ($step == 1): ?>
                        <div class="form-grid">
                            <div class="input-group">
                                <label><?= $current_content['labels']['firstname'] ?></label>
                                <input type="text" name="firstname" class="custom-input" 
                                       value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" 
                                       placeholder="<?= $current_content['placeholders']['firstname'] ?>" required>
                            </div>
                            <div class="input-group">
                                <label><?= $current_content['labels']['lastname'] ?></label>
                                <input type="text" name="lastname" class="custom-input" 
                                       value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>"
                                       placeholder="<?= $current_content['placeholders']['lastname'] ?>" required>
                            </div>
                            <div class="input-group span-2">
                                <label><?= $current_content['labels']['phone'] ?></label>
                                <input type="tel" name="phone" class="custom-input" 
                                       value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                                       placeholder="<?= $current_content['placeholders']['phone'] ?>" required>
                            </div>
                            <div class="input-group span-2">
                                <label><?= $current_content['labels']['email'] ?></label>
                                <input type="email" name="email" class="custom-input" 
                                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                       placeholder="<?= $current_content['placeholders']['email'] ?>" required>
                            </div>
                        </div>
                        <div class="btn-row">
                            <button type="submit" name="next" class="btn-c btn-primary">
                                <?= $current_content['buttons']['next'] ?> <i class="fas fa-arrow-right"></i>
                            </button>
                        </div>
                    <?php endif; ?>

                    <?php if ($step == 2): ?>
                        <?php foreach(['firstname','lastname','phone','email'] as $f): ?>
                            <input type="hidden" name="<?= $f ?>" value="<?= htmlspecialchars($_POST[$f] ?? '') ?>">
                        <?php endforeach; ?>

                        <div class="form-grid">
                            <div class="input-group span-2">
                                <label><?= $current_content['labels']['username'] ?></label>
                                <input type="text" name="username" class="custom-input" 
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                       placeholder="<?= $current_content['placeholders']['username'] ?>" required>
                            </div>
                            <div class="input-group">
                                <label><?= $current_content['labels']['password'] ?></label>
                                <input type="password" name="password" class="custom-input" 
                                       placeholder="<?= $current_content['placeholders']['password'] ?>" required>
                            </div>
                            <div class="input-group">
                                <label><?= $current_content['labels']['confirm_password'] ?></label>
                                <input type="password" name="confirm_password" class="custom-input" 
                                       placeholder="<?= $current_content['placeholders']['confirm_password'] ?>" required>
                            </div>
                            <div class="input-group span-2">
                                <label><?= $current_content['labels']['image'] ?></label>
                                <div class="upload-box">
                                    <input type="file" name="image" accept="image/*" onchange="previewFile(this)">
                                    <div id="uploadText">
                                        <i class="fas fa-cloud-upload-alt" style="font-size: 1.5rem; color: #4361ee; margin-bottom:5px;"></i><br>
                                        <small><?= $current_content['file_upload']['title'] ?></small>
                                    </div>
                                    <img id="imgPreview" class="preview-img" style="display:none;">
                                </div>
                            </div>
                        </div>

                        <div class="btn-row">
                            <button type="submit" name="back" class="btn-c btn-back" formnovalidate>
                                <i class="fas fa-arrow-left"></i> <?= $current_content['buttons']['back'] ?>
                            </button>
                            <button type="submit" name="register" class="btn-c btn-success">
                                <i class="fas fa-check"></i> <?= $current_content['buttons']['register'] ?>
                            </button>
                        </div>
                    <?php endif; ?>

                </form>

                <div style="text-align: center; margin-top: 25px; font-size: 0.9rem; color: #8d99ae;">
                    <?= $current_content['login_link'] ?> 
                    <a href="?page=login" style="color: #4361ee; text-decoration: none; font-weight: 600;">
                        <?= $current_content['login'] ?>
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function previewFile(input) {
        const preview = document.getElementById('imgPreview');
        const text = document.getElementById('uploadText');
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                text.style.display = 'none';
            }
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
            text.style.display = 'block';
        }
    }
</script>