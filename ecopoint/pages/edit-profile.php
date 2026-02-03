<?php
// edit-profile.php
// เพิ่ม output buffering ตั้งแต่ต้นไฟล์
ob_start();

// กำหนด path สำหรับ config.php
$config_path = dirname(__DIR__) . '/includes/config.php';
if (file_exists($config_path)) {
    include_once $config_path;
} else {
    // ลองหาในตำแหน่งอื่น
    $config_path = dirname(__DIR__) . '/../includes/config.php';
    if (file_exists($config_path)) {
        include_once $config_path;
    } else {
        // ถ้ายังไม่พบ ให้ใช้ relative path จากตำแหน่งปัจจุบัน
        $config_path = '../includes/config.php';
        if (file_exists($config_path)) {
            include_once $config_path;
        } else {
            // ใช้ absolute path จาก server root
            $config_path = $_SERVER['DOCUMENT_ROOT'] . '/ecopoint/includes/config.php';
            if (file_exists($config_path)) {
                include_once $config_path;
            } else {
                die("ไม่พบไฟล์การกำหนดค่า (config.php)");
            }
        }
    }
}

// ตรวจสอบว่ามีการล็อกอินหรือไม่ หลังจาก include config แล้ว
if (!isset($_SESSION['uid'])) {
    if (ob_get_length()) {
        ob_end_clean();
    }
    header('Location: index.php?page=login');
    exit();
}

$user_id = $_SESSION['uid'];
$error = '';
$success = '';

// ดึงข้อมูลผู้ใช้ปัจจุบัน
$user = null;
if (isset($conn)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE uid = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
    } else {
        $error = "เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล: " . $conn->error;
    }
} else {
    $error = "ไม่สามารถเชื่อมต่อฐานข้อมูลได้";
}

// ประมวลผลฟอร์มเมื่อส่งข้อมูล
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $user) {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // ตรวจสอบข้อมูลที่จำเป็น
    if (empty($firstname) || empty($lastname) || empty($email)) {
        $error = "กรุณากรอกข้อมูลให้ครบถ้วน";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "รูปแบบอีเมลไม่ถูกต้อง";
    } else {
        // ตรวจสอบอีเมลซ้ำ (ยกเว้นอีเมลตัวเอง)
        $checkEmail = $conn->prepare("SELECT uid FROM users WHERE email = ? AND uid != ?");
        if ($checkEmail) {
            $checkEmail->bind_param("si", $email, $user_id);
            $checkEmail->execute();
            $checkEmail->store_result();
            
            if ($checkEmail->num_rows > 0) {
                $error = "อีเมลนี้มีการใช้งานแล้ว";
            } else {
                // อัพเดทรูปโปรไฟล์
                $image = $user['image'] ?? '';
                if (!empty($_FILES['image']['name'])) {
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    $fileType = $_FILES['image']['type'];
                    
                    if (in_array($fileType, $allowedTypes)) {
                        $dir = "uploads/users/";
                        if (!is_dir($dir)) {
                            mkdir($dir, 0777, true);
                        }
                        
                        // ลบรูปเก่าถ้ามี
                        if (!empty($image) && file_exists($dir . $image) && $image != 'no-profile.png') {
                            unlink($dir . $image);
                        }
                        
                        // สร้างชื่อไฟล์ใหม่
                        $timestamp = time();
                        $filename = preg_replace('/[^a-zA-Z0-9\._-]/', '', $_FILES['image']['name']);
                        $image = $timestamp . "_" . $filename;
                        $move_path = $dir . $image;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $move_path)) {
                            // สำเร็จ
                        } else {
                            $error = "ไม่สามารถอัพโหลดไฟล์ได้";
                            $image = $user['image']; // ใช้รูปเดิม
                        }
                    } else {
                        $error = "กรุณาอัพโหลดไฟล์ภาพเท่านั้น (JPG, PNG, GIF, WebP)";
                    }
                }
                
                if (empty($error)) {
                    // อัพเดทข้อมูล
                    $updateStmt = $conn->prepare("
                        UPDATE users 
                        SET firstname = ?, lastname = ?, phone = ?, email = ?, image = ? 
                        WHERE uid = ?
                    ");
                    if ($updateStmt) {
                        $updateStmt->bind_param("sssssi", $firstname, $lastname, $phone, $email, $image, $user_id);
                        
                        if ($updateStmt->execute()) {
                            // อัพเดท session
                            $_SESSION['firstname'] = $firstname;
                            $_SESSION['lastname'] = $lastname;
                            $_SESSION['email'] = $email;
                            $_SESSION['phone'] = $phone;
                            if (!empty($image)) {
                                $_SESSION['image'] = $image;
                            }
                            
                            $success = "อัพเดทโปรไฟล์สำเร็จแล้ว!";
                            // รีเฟรชหน้า profile หลังจาก 2 วินาที
                            echo '<meta http-equiv="refresh" content="2;url=index.php?page=profile">';
                        } else {
                            $error = "เกิดข้อผิดพลาดในการอัพเดทข้อมูล: " . $conn->error;
                        }
                        $updateStmt->close();
                    } else {
                        $error = "เกิดข้อผิดพลาดในการเตรียมคำสั่ง SQL: " . $conn->error;
                    }
                }
            }
            $checkEmail->close();
        } else {
            $error = "ไม่สามารถตรวจสอบอีเมลได้: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขโปรไฟล์ - Eco Point</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        /* CSS เดิมทั้งหมด... */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Noto Sans Thai', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            color: #333;
        }
        
        .edit-profile-container {
            max-width: 800px;
            margin: 20px auto;
            animation: fadeIn 0.5s ease-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .edit-profile-header {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            border-radius: 20px;
            padding: 30px;
            color: white;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(33, 150, 243, 0.2);
            position: relative;
            overflow: hidden;
        }
        
        .edit-profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #FFD700, #FFA500, #2196F3);
        }
        
        .edit-profile-header h1 {
            margin: 0 0 10px 0;
            font-size: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
        }
        
        .edit-profile-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .edit-profile-form {
            background: white;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }
        
        .alert {
            padding: 15px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            animation: slideDown 0.3s ease-out;
        }
        
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .alert-danger {
            background: linear-gradient(135deg, #ffeaea, #ffcccc);
            border: 1px solid #ff9999;
            color: #cc0000;
        }
        
        .alert-success {
            background: linear-gradient(135deg, #d4ffd4, #aaffaa);
            border: 1px solid #88cc88;
            color: #006600;
        }
        
        .alert i {
            font-size: 1.2rem;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #444;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #ddd;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            background: #f9f9f9;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2196F3;
            background: white;
            box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 14px 30px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            text-align: center;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #2196F3, #1976D2);
            color: white;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #1976D2, #1565C0);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(33, 150, 243, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #5a6268);
            color: white;
        }
        
        .btn-secondary:hover {
            background: linear-gradient(135deg, #5a6268, #4a5056);
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(108, 117, 125, 0.3);
        }
        
        .form-row {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .form-col {
            flex: 1;
        }
        
        .profile-picture-section {
            text-align: center;
            margin-bottom: 40px;
            padding: 20px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 15px;
            border: 2px dashed #dee2e6;
        }
        
        .current-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: all 0.3s;
        }
        
        .current-avatar:hover {
            transform: scale(1.05);
        }
        
        .avatar-placeholder {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2196F3, #1976D2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            margin: 0 auto 20px;
            border: 5px solid white;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .file-input-container {
            position: relative;
            display: inline-block;
            margin: 15px 0;
        }
        
        .file-input {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }
        
        .file-input-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 25px;
            background: white;
            border: 2px solid #2196F3;
            border-radius: 10px;
            color: #2196F3;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: 600;
        }
        
        .file-input-label:hover {
            background: #2196F3;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }
        
        .text-muted {
            display: block;
            margin-top: 10px;
            color: #666;
            font-size: 0.9rem;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 1px solid #eee;
        }
        
        .image-preview {
            margin-top: 20px;
            text-align: center;
        }
        
        .image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 10px;
            border: 3px solid #4CAF50;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.2);
        }
        
        @media (max-width: 768px) {
            .edit-profile-container {
                margin: 10px auto;
            }
            
            .edit-profile-header {
                padding: 20px;
                margin-bottom: 20px;
            }
            
            .edit-profile-header h1 {
                font-size: 1.5rem;
            }
            
            .edit-profile-form {
                padding: 25px;
            }
            
            .form-row {
                flex-direction: column;
                gap: 0;
            }
            
            .form-actions {
                flex-direction: column;
                gap: 15px;
            }
            
            .form-actions > div {
                width: 100%;
            }
            
            .form-actions .btn {
                width: 100%;
                justify-content: center;
                margin: 5px 0;
            }
        }
        
        /* เพิ่มสไตล์สำหรับ loading */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
            margin-right: 10px;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="edit-profile-container">
        <div class="edit-profile-header">
            <h1><i class="fas fa-user-edit"></i> แก้ไขโปรไฟล์</h1>
            <p>อัพเดทข้อมูลส่วนตัวของคุณ</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo htmlspecialchars($success); ?></span>
                <div class="loading" style="display: inline-block;"></div>
                <span>กำลังกลับไปหน้าโปรไฟล์...</span>
            </div>
        <?php endif; ?>
        
        <div class="edit-profile-form">
            <form method="POST" enctype="multipart/form-data" id="editProfileForm" onsubmit="return validateForm()">
                <!-- ข้อมูลรูปโปรไฟล์ -->
                <div class="profile-picture-section">
                    <?php 
                    $avatar_src = '';
                    if(!empty($user['image']) && $user['image'] != 'no-profile.png') {
                        $avatar_src = 'uploads/users/' . htmlspecialchars($user['image']);
                    } elseif(!empty($user['firstname']) && !empty($user['lastname'])) {
                        $initials = substr($user['firstname'], 0, 1) . substr($user['lastname'], 0, 1);
                        $avatar_src = 'https://ui-avatars.com/api/?name=' . urlencode($user['firstname'] . ' ' . $user['lastname']) . '&background=2196F3&color=fff&size=150';
                    } else {
                        $avatar_src = '';
                    }
                    ?>
                    
                    <?php if($avatar_src): ?>
                        <img src="<?php echo $avatar_src; ?>" 
                             alt="รูปโปรไฟล์" 
                             class="current-avatar"
                             id="currentAvatar"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name=User&background=2196F3&color=fff&size=150'">
                    <?php else: ?>
                        <div class="avatar-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="file-input-container">
                        <input type="file" name="image" id="image" class="file-input" accept="image/*">
                        <label for="image" class="file-input-label">
                            <i class="fas fa-camera"></i> เปลี่ยนรูปโปรไฟล์
                        </label>
                    </div>
                    <small class="text-muted">รองรับไฟล์ JPG, PNG, GIF, WebP (ขนาดไม่เกิน 2MB)</small>
                    
                    <!-- แสดงตัวอย่างรูปภาพ -->
                    <div class="image-preview" id="imagePreview"></div>
                </div>
                
                <!-- ข้อมูลส่วนตัว -->
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="firstname" class="form-label">
                                <i class="fas fa-user"></i> ชื่อจริง
                            </label>
                            <input type="text" 
                                   id="firstname" 
                                   name="firstname" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($user['firstname'] ?? ''); ?>"
                                   required
                                   oninput="validateName(this)">
                            <small class="text-muted" style="display: none;" id="firstnameError"></small>
                        </div>
                    </div>
                    
                    <div class="form-col">
                        <div class="form-group">
                            <label for="lastname" class="form-label">
                                <i class="fas fa-user"></i> นามสกุล
                            </label>
                            <input type="text" 
                                   id="lastname" 
                                   name="lastname" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($user['lastname'] ?? ''); ?>"
                                   required
                                   oninput="validateName(this)">
                            <small class="text-muted" style="display: none;" id="lastnameError"></small>
                        </div>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-col">
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> อีเมล
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                                   required
                                   oninput="validateEmail(this)">
                            <small class="text-muted" style="display: none;" id="emailError"></small>
                        </div>
                    </div>
                    
                    <div class="form-col">
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> โทรศัพท์
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   class="form-control" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   placeholder="เช่น 0812345678"
                                   oninput="formatPhoneNumber(this)">
                            <small class="text-muted" style="display: none;" id="phoneError"></small>
                        </div>
                    </div>
                </div>
                
                <!-- ปุ่มดำเนินการ -->
                <div class="form-actions">
                    <div>
                        <a href="index.php?page=profile" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> กลับไปโปรไฟล์
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i> บันทึกการเปลี่ยนแปลง
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript สำหรับแสดงตัวอย่างรูปภาพก่อนอัพโหลด
        document.addEventListener('DOMContentLoaded', function() {
            const imageInput = document.getElementById('image');
            const imagePreview = document.getElementById('imagePreview');
            const currentAvatar = document.getElementById('currentAvatar');
            const submitBtn = document.getElementById('submitBtn');
            
            if (imageInput) {
                imageInput.addEventListener('change', function() {
                    const file = this.files[0];
                    if (file) {
                        // ตรวจสอบขนาดไฟล์ (ไม่เกิน 2MB)
                        if (file.size > 2 * 1024 * 1024) {
                            alert('ไฟล์ภาพต้องมีขนาดไม่เกิน 2MB');
                            this.value = '';
                            return;
                        }
                        
                        const reader = new FileReader();
                        
                        reader.addEventListener('load', function() {
                            // ลบรูปเก่าที่อาจจะแสดงอยู่
                            imagePreview.innerHTML = '';
                            
                            // สร้างรูปภาพใหม่
                            const img = document.createElement('img');
                            img.src = reader.result;
                            img.alt = 'ตัวอย่างรูปภาพ';
                            
                            // แสดงข้อความ
                            const text = document.createElement('p');
                            text.textContent = 'ตัวอย่างรูปภาพใหม่:';
                            text.style.fontWeight = 'bold';
                            text.style.color = '#4CAF50';
                            text.style.marginBottom = '10px';
                            
                            imagePreview.appendChild(text);
                            imagePreview.appendChild(img);
                        });
                        
                        reader.readAsDataURL(file);
                    }
                });
            }
            
            // ป้องกันการปิดฟอร์มโดยไม่ได้ตั้งใจ
            const form = document.getElementById('editProfileForm');
            let formChanged = false;
            
            // ตรวจสอบการเปลี่ยนแปลงในฟอร์ม
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                const initialValue = input.value;
                input.addEventListener('input', function() {
                    formChanged = (this.value !== initialValue);
                });
            });
            
            // แจ้งเตือนเมื่อพยายามออกจากหน้า
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'คุณมีข้อมูลที่ยังไม่ได้บันทึก ต้องการออกจากหน้านี้หรือไม่?';
                    return e.returnValue;
                }
            });
            
            // รีเซ็ตสถานะเมื่อบันทึกสำเร็จ
            form.addEventListener('submit', function() {
                formChanged = false;
                // แสดง loading
                if (submitBtn) {
                    submitBtn.innerHTML = '<div class="loading"></div> กำลังบันทึก...';
                    submitBtn.disabled = true;
                }
            });
        });
        
        // ฟังก์ชันตรวจสอบความถูกต้องของฟอร์ม
        function validateForm() {
            let isValid = true;
            
            // ตรวจสอบชื่อ
            if (!validateName(document.getElementById('firstname'))) {
                isValid = false;
            }
            
            // ตรวจสบนามสกุล
            if (!validateName(document.getElementById('lastname'))) {
                isValid = false;
            }
            
            // ตรวจสอบอีเมล
            if (!validateEmail(document.getElementById('email'))) {
                isValid = false;
            }
            
            // ตรวจสอบเบอร์โทรศัพท์
            const phone = document.getElementById('phone').value;
            if (phone && !/^[0-9]{10}$/.test(phone.replace(/\D/g, ''))) {
                showError('phoneError', 'กรุณากรอกหมายเลขโทรศัพท์ให้ถูกต้อง (10 หลัก)');
                isValid = false;
            } else {
                hideError('phoneError');
            }
            
            return isValid;
        }
        
        function validateName(input) {
            const name = input.value.trim();
            const errorId = input.id + 'Error';
            
            if (name.length < 2) {
                showError(errorId, 'กรุณากรอกอย่างน้อย 2 ตัวอักษร');
                return false;
            } else if (!/^[ก-๙a-zA-Z\s]+$/.test(name)) {
                showError(errorId, 'กรุณากรอกเฉพาะตัวอักษร');
                return false;
            } else {
                hideError(errorId);
                return true;
            }
        }
        
        function validateEmail(input) {
            const email = input.value.trim();
            const errorId = input.id + 'Error';
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                showError(errorId, 'กรุณากรอกอีเมลให้ถูกต้อง');
                return false;
            } else {
                hideError(errorId);
                return true;
            }
        }
        
        function formatPhoneNumber(input) {
            // ลบอักขระที่ไม่ใช่ตัวเลข
            let phone = input.value.replace(/\D/g, '');
            
            // จัดรูปแบบใหม่
            if (phone.length > 6) {
                phone = phone.substring(0, 3) + '-' + phone.substring(3, 6) + '-' + phone.substring(6, 10);
            } else if (phone.length > 3) {
                phone = phone.substring(0, 3) + '-' + phone.substring(3);
            }
            
            input.value = phone;
            
            // ตรวจสอบความถูกต้อง
            const digits = phone.replace(/\D/g, '');
            const errorId = 'phoneError';
            
            if (digits && digits.length !== 10) {
                showError(errorId, 'หมายเลขโทรศัพท์ต้องมี 10 หลัก');
                return false;
            } else {
                hideError(errorId);
                return true;
            }
        }
        
        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.color = '#ff4444';
                errorElement.style.display = 'block';
                errorElement.style.marginTop = '5px';
                errorElement.style.fontSize = '0.85rem';
            }
        }
        
        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php
// ปิด output buffering
ob_end_flush();
?>