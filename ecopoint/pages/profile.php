<?php
// profile.php
include_once 'includes/config.php';

// ตรวจสอบการล็อกอิน
if (!isset($_SESSION['uid'])) {
    header('Location: index.php?page=login');
    exit;
}

// ดึงข้อมูลผู้ใช้ล่าสุดจากฐานข้อมูล
$user_id = $_SESSION['uid'];
$stmt = $conn->prepare("SELECT * FROM users WHERE uid = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// อัปเดต session ด้วยข้อมูลล่าสุด
$_SESSION['firstname'] = $user['firstname'];
$_SESSION['lastname'] = $user['lastname'];
$_SESSION['username'] = $user['username'];
$_SESSION['email'] = $user['email'];
$_SESSION['phone'] = $user['phone'];
$_SESSION['image'] = $user['image'];
$_SESSION['role'] = $user['u_role'];

// ใช้คะแนนจากตาราง users (u_total_point) แทนที่จะ SUM จาก user_points
$total_points = $user['u_total_point'] ?? 0;

// ดึงกิจกรรมล่าสุด (ต้องตรวจสอบว่ามีตาราง user_points และ activities หรือไม่)
// ถ้ายังไม่มีตาราง ให้แสดงตัวอย่างกิจกรรมแทน
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
/* CSS เดิมทั้งหมดคงไว้... */
*{
    font-family: 'Noto Sans Thai', sans-serif;
}
/* เพิ่ม CSS สำหรับความสำเร็จ */
.achievement-item.locked {
    opacity: 0.5;
    filter: grayscale(100%);
}

.achievement-item.locked .achievement-icon {
    background: linear-gradient(135deg, #cccccc, #999999);
}

/* เพิ่ม badge สำหรับระดับสมาชิก */
.level-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: white;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    box-shadow: 0 4px 15px rgba(255, 165, 0, 0.3);
}

.level-badge.silver {
    background: linear-gradient(135deg, #C0C0C0, #808080);
    box-shadow: 0 4px 15px rgba(192, 192, 192, 0.3);
}

.level-badge.bronze {
    background: linear-gradient(135deg, #CD7F32, #A0522D);
    box-shadow: 0 4px 15px rgba(205, 127, 50, 0.3);
}

.level-badge.member {
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
}

/* ปรับปรุง info-item */
.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 12px;
    transition: all 0.3s ease;
    border-left: 4px solid #4CAF50;
}

.info-item:nth-child(2) {
    border-left-color: #2196F3;
}

.info-item:nth-child(3) {
    border-left-color: #9C27B0;
}

.info-item:nth-child(4) {
    border-left-color: #FF9800;
}

.info-item:nth-child(5) {
    border-left-color: #E91E63;
}

/* ===== RESET & BASE STYLES ===== */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
    min-height: 100vh;
    color: #333;
    line-height: 1.6;
}

/* ===== PROFILE CONTAINER ===== */
.profile-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
    animation: fadeIn 0.5s ease-out;
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

/* ===== PROFILE HEADER ===== */
.profile-header {
    background: linear-gradient(135deg, #4CAF50 0%, #2E7D32 100%);
    border-radius: 20px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(46, 125, 50, 0.2);
    color: white;
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: linear-gradient(90deg, #FFD700, #FFA500, #4CAF50);
}

.profile-info {
    display: flex;
    align-items: center;
    gap: 30px;
    position: relative;
    z-index: 1;
}

/* ===== PROFILE AVATAR ===== */
.profile-avatar {
    position: relative;
    flex-shrink: 0;
}

.avatar-image {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid rgba(255, 255, 255, 0.3);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.avatar-image:hover {
    transform: scale(1.05);
    border-color: rgba(255, 255, 255, 0.5);
}

.avatar-edit {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: white;
    color: #4CAF50;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
}

.avatar-edit:hover {
    background: #4CAF50;
    color: white;
    transform: scale(1.1);
}

/* ===== USER DETAILS ===== */
.user-details {
    flex: 1;
}

.user-details h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.user-details p {
    font-size: 1.1rem;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* ===== USER STATS ===== */
.user-stats {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
}

.stat-card {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 20px;
    border-radius: 15px;
    min-width: 180px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    transition: all 0.3s ease;
    flex: 1;
}

.stat-card:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: translateY(-5px);
}

.stat-value {
    font-size: 2.2rem;
    font-weight: bold;
    margin-bottom: 5px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* ===== LEVEL BADGES ===== */
.level-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 20px;
    border-radius: 20px;
    font-weight: bold;
    text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    font-size: 1rem;
    transition: all 0.3s ease;
}

.level-badge:hover {
    transform: scale(1.05);
}

.level-badge.gold {
    background: linear-gradient(135deg, #FFD700, #FFA500);
    color: #856404;
}

.level-badge.silver {
    background: linear-gradient(135deg, #C0C0C0, #808080);
    color: #333;
}

.level-badge.bronze {
    background: linear-gradient(135deg, #CD7F32, #A0522D);
    color: white;
}

.level-badge.member {
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    color: white;
}

/* ===== PROFILE CONTENT LAYOUT ===== */
.profile-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
    margin-bottom: 30px;
}

@media (max-width: 768px) {
    .profile-content {
        grid-template-columns: 1fr;
    }
}

/* ===== PROFILE SECTIONS ===== */
.profile-section {
    background: white;
    border-radius: 20px;
    padding: 30px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
}

.profile-section:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f0f0f0;
}

.section-header h2 {
    font-size: 1.5rem;
    color: #2E7D32;
    display: flex;
    align-items: center;
    gap: 10px;
}

.edit-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #4CAF50;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.edit-btn:hover {
    background: #388E3C;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
}

/* ===== INFO GRID ===== */
.info-grid {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    background: #f9f9f9;
    border-radius: 12px;
    transition: all 0.3s ease;
    border-left: 4px solid #4CAF50;
}

.info-item:hover {
    background: #f0f0f0;
    transform: translateX(5px);
}

.info-item:nth-child(2) {
    border-left-color: #2196F3;
}

.info-item:nth-child(3) {
    border-left-color: #9C27B0;
}

.info-item:nth-child(4) {
    border-left-color: #FF9800;
}

.info-item:nth-child(5) {
    border-left-color: #E91E63;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-content label {
    display: block;
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 5px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-content span {
    display: block;
    font-size: 1.1rem;
    font-weight: 500;
    color: #333;
}

/* ===== POINTS PROGRESS ===== */
.points-progress {
    margin-top: 30px;
    padding: 25px;
    background: linear-gradient(135deg, #f8fff8 0%, #e8f5e9 100%);
    border-radius: 15px;
    border: 1px solid #c8e6c9;
}

.points-progress h3 {
    color: #2E7D32;
    font-size: 1.3rem;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 20px;
}

.progress-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 0.95rem;
    color: #555;
}

.next-level {
    font-weight: 600;
    color: #4CAF50;
}

.progress-bar {
    height: 12px;
    background: #e0e0e0;
    border-radius: 6px;
    overflow: hidden;
    position: relative;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4CAF50, #8BC34A);
    border-radius: 6px;
    width: 0%;
    transition: width 1s ease-in-out;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.3) 50%, 
        transparent 100%);
    animation: shimmer 2s infinite;
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* ===== ACTIVITY LIST ===== */
.activity-list {
    max-height: 400px;
    overflow-y: auto;
    padding-right: 10px;
}

.activity-list::-webkit-scrollbar {
    width: 6px;
}

.activity-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.activity-list::-webkit-scrollbar-thumb {
    background: #c8e6c9;
    border-radius: 3px;
}

.activity-list::-webkit-scrollbar-thumb:hover {
    background: #4CAF50;
}

.activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    background: white;
    border-radius: 12px;
    margin-bottom: 15px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
}

.activity-item:hover {
    border-color: #4CAF50;
    box-shadow: 0 5px 15px rgba(76, 175, 80, 0.1);
    transform: translateY(-2px);
}

.activity-item:last-child {
    margin-bottom: 0;
}

.activity-info h4 {
    color: #333;
    margin-bottom: 5px;
    font-size: 1.1rem;
}

.activity-info p {
    color: #666;
    font-size: 0.9rem;
}

.activity-points {
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    color: white;
    padding: 8px 15px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 1.1rem;
    min-width: 60px;
    text-align: center;
}

/* ===== ACHIEVEMENTS ===== */
.achievement-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.achievement-item {
    background: white;
    border-radius: 15px;
    padding: 25px 20px;
    text-align: center;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.achievement-item:hover:not(.locked) {
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
    border-color: #4CAF50;
}

.achievement-item.locked {
    opacity: 0.6;
    filter: grayscale(100%);
}

.achievement-item.locked:hover {
    transform: none;
    box-shadow: none;
    border-color: #e0e0e0;
}

.achievement-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #4CAF50, #2E7D32);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    font-size: 1.8rem;
    color: white;
    transition: all 0.3s ease;
}

.achievement-item.locked .achievement-icon {
    background: linear-gradient(135deg, #cccccc, #999999);
}

.achievement-item:hover:not(.locked) .achievement-icon {
    transform: scale(1.1) rotate(5deg);
}

.achievement-name {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.achievement-desc {
    font-size: 0.9rem;
    color: #666;
    line-height: 1.5;
}

/* ===== EMPTY STATES ===== */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.3;
}

.empty-state p {
    font-size: 1.1rem;
    margin-bottom: 20px;
}

/* ===== RESPONSIVE DESIGN ===== */
@media (max-width: 1024px) {
    .profile-info {
        flex-direction: column;
        text-align: center;
    }
    
    .user-details h1 {
        justify-content: center;
    }
    
    .user-stats {
        justify-content: center;
    }
    
    .stat-card {
        min-width: 150px;
    }
}

@media (max-width: 768px) {
    .profile-header {
        padding: 20px;
    }
    
    .section-header {
        flex-direction: column;
        gap: 15px;
        align-items: flex-start;
    }
    
    .edit-btn {
        align-self: stretch;
        justify-content: center;
    }
    
    .achievement-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .profile-container {
        padding: 15px;
    }
    
    .user-stats {
        flex-direction: column;
    }
    
    .stat-card {
        width: 100%;
    }
    
    .info-item {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
    
    .activity-item {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
}

/* ===== ANIMATIONS ===== */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.pulse {
    animation: pulse 2s infinite;
}

/* ===== LOADING STATES ===== */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, 
        transparent 0%, 
        rgba(255, 255, 255, 0.4) 50%, 
        transparent 100%);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

/* ===== ACCESSIBILITY ===== */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}

/* ===== PRINT STYLES ===== */
@media print {
    .edit-btn,
    .avatar-edit {
        display: none;
    }
    
    .profile-header {
        background: white !important;
        color: black !important;
        box-shadow: none !important;
    }
    
    .profile-section {
        box-shadow: none !important;
        border: 1px solid #ddd;
    }
}
</style>

<link rel="stylesheet" href="style/profile.css">

<div class="profile-container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-info">
            <div class="profile-avatar">
                <?php 
                $profile_image = !empty($user['image']) && $user['image'] != 'no-profile.png' 
                    ? "uploads/users/" . htmlspecialchars($user['image']) 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($user['firstname'] . '+' . $user['lastname']) . '&background=4CAF50&color=fff&size=150';
                ?>
                <img src="<?php echo $profile_image; ?>" 
                     alt="Profile" class="avatar-image" 
                     onerror="this.src='https://ui-avatars.com/api/?name=<?php echo urlencode($user['firstname'] . '+' . $user['lastname']); ?>&background=4CAF50&color=fff&size=150'">
                <a href="?page=edit-profile" class="avatar-edit">
                    <i class="fas fa-camera"></i>
                </a>
            </div>
            
            <div class="user-details">
                <h1>
                    <?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?>
                    <span class="level-badge <?php echo strtolower($user['u_role']); ?>">
                        <i class="fas fa-crown"></i>
                        <?php echo htmlspecialchars($user['u_role']); ?>
                    </span>
                </h1>
                <p style="opacity: 0.9; margin-bottom: 20px;">
                    <i class="fas fa-user-circle"></i> @<?php echo htmlspecialchars($user['username']); ?>
                </p>
                
                <div class="user-stats">
                    <div class="stat-card">
                        <div class="stat-value" id="totalPoints"><?php echo number_format($total_points); ?></div>
                        <div class="stat-label">คะแนนสะสมทั้งหมด</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">
                            <?php 
                            if(isset($user['u_deta'])) {
                                echo date('d/m/Y', strtotime($user['u_deta']));
                            } else {
                                echo date('d/m/Y');
                            }
                            ?>
                        </div>
                        <div class="stat-label">วันที่สมัครสมาชิก</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value"><?php echo $user['u_total_point'] >= 500 ? '5+' : '0'; ?></div>
                        <div class="stat-label">กิจกรรมที่เข้าร่วม</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Content -->
    <div class="profile-content">
        <!-- Left Column - Personal Info -->
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-user-circle"></i> ข้อมูลส่วนตัว</h2>
                <a href="?page=edit-profile" class="edit-btn">
                    <i class="fas fa-edit"></i> แก้ไขข้อมูล
                </a>
            </div>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="info-content">
                        <label>ชื่อ-นามสกุล</label>
                        <span><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-at"></i>
                    </div>
                    <div class="info-content">
                        <label>ชื่อผู้ใช้</label>
                        <span>@<?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info-content">
                        <label>อีเมล</label>
                        <span><?php echo htmlspecialchars($user['email']); ?></span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <div class="info-content">
                        <label>เบอร์โทรศัพท์</label>
                        <span><?php echo !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'ยังไม่ได้กำหนด'; ?></span>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-icon">
                        <i class="fas fa-leaf"></i>
                    </div>
                    <div class="info-content">
                        <label>ระดับสมาชิก</label>
                        <span style="color: #4caf50; font-weight: bold;">
                            <?php echo htmlspecialchars($user['u_role']); ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar สำหรับระดับถัดไป -->
            <div class="points-progress">
                <h3 style="margin-bottom: 15px; color: #2E7D32;">
                    <i class="fas fa-chart-line"></i> ความก้าวหน้า
                </h3>
                <div class="progress-info">
                    <span>ระดับปัจจุบัน: <?php echo htmlspecialchars($user['u_role']); ?></span>
                    <span class="next-level">ระดับถัดไป: 
                        <?php 
                        if($user['u_role'] == 'member' && $total_points >= 1000) {
                            echo 'Bronze (ถึงแล้ว!)';
                        } elseif($user['u_role'] == 'bronze' && $total_points >= 2500) {
                            echo 'Silver (ถึงแล้ว!)';
                        } elseif($user['u_role'] == 'silver' && $total_points >= 5000) {
                            echo 'Gold (ถึงแล้ว!)';
                        } elseif($user['u_role'] == 'gold') {
                            echo 'Platinum (ต้องมี 10000 คะแนน)';
                        } else {
                            // คำนวณคะแนนที่เหลือ
                            $next_level_points = 0;
                            $next_level_name = '';
                            
                            if($user['u_role'] == 'member') {
                                $next_level_points = 1000;
                                $next_level_name = 'Bronze';
                            } elseif($user['u_role'] == 'bronze') {
                                $next_level_points = 2500;
                                $next_level_name = 'Silver';
                            } elseif($user['u_role'] == 'silver') {
                                $next_level_points = 5000;
                                $next_level_name = 'Gold';
                            } else {
                                $next_level_points = 10000;
                                $next_level_name = 'Platinum';
                            }
                            
                            $remaining = $next_level_points - $total_points;
                            if($remaining > 0) {
                                echo $next_level_name . ' (ต้องการอีก ' . number_format($remaining) . ' คะแนน)';
                            } else {
                                echo $next_level_name . ' (ถึงแล้ว!)';
                            }
                        }
                        ?>
                    </span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill" style="width: 0%"></div>
                </div>
            </div>
        </div>

        <!-- Right Column - Recent Activities -->
        <div class="profile-section">
            <div class="section-header">
                <h2><i class="fas fa-history"></i> กิจกรรมล่าสุด</h2>
                <a href="?page=activities" class="edit-btn">
                    <i class="fas fa-plus"></i> ดูกิจกรรมทั้งหมด
                </a>
            </div>
            
            <div class="activity-list">
                <!-- ตัวอย่างกิจกรรม (ถ้ายังไม่มีข้อมูลจริง) -->
                <?php if($total_points > 0): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4>ลงทะเบียนสมาชิกใหม่</h4>
                            <p><?php echo date('d/m/Y H:i'); ?></p>
                        </div>
                        <div class="activity-points">
                            +10
                        </div>
                    </div>
                    
                    <?php if($total_points >= 50): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4>กรอกข้อมูลโปรไฟล์ครบถ้วน</h4>
                            <p><?php echo date('d/m/Y', strtotime('-1 day')) . ' 10:30'; ?></p>
                        </div>
                        <div class="activity-points">
                            +50
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($total_points >= 100): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4>แชร์กิจกรรม Eco Point</h4>
                            <p><?php echo date('d/m/Y', strtotime('-2 days')) . ' 14:15'; ?></p>
                        </div>
                        <div class="activity-points">
                            +50
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($total_points >= 200): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4>เข้าร่วมโครงการลดขยะพลาสติก</h4>
                            <p><?php echo date('d/m/Y', strtotime('-3 days')) . ' 09:00'; ?></p>
                        </div>
                        <div class="activity-points">
                            +100
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if($total_points >= 500): ?>
                    <div class="activity-item">
                        <div class="activity-info">
                            <h4>กิจกรรมรีไซเคิลขวดพลาสติก</h4>
                            <p><?php echo date('d/m/Y', strtotime('-5 days')) . ' 13:45'; ?></p>
                        </div>
                        <div class="activity-points">
                            +300
                        </div>
                    </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 30px; color: #666;">
                        <i class="fas fa-inbox" style="font-size: 3rem; margin-bottom: 15px; opacity: 0.5;"></i>
                        <p>ยังไม่มีกิจกรรมที่เข้าร่วม</p>
                        <a href="?page=activities" class="edit-btn" style="margin-top: 15px; display: inline-block;">
                            <i class="fas fa-plus"></i> เข้าร่วมกิจกรรม
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Achievements Section -->
    <div class="profile-section" style="margin-top: 30px;">
        <div class="section-header">
            <h2><i class="fas fa-trophy"></i> ความสำเร็จ</h2>
            <span style="color: #666; font-size: 0.9rem;">
                ปลดล็อกแล้ว: 
                <span id="unlockedCount">0</span>/8
            </span>
        </div>
        
        <div class="achievement-grid">
            <?php 
            // กำหนดเงื่อนไขการปลดล็อก
            $achievements = [
                ['icon' => 'fa-seedling', 'name' => 'นักอนุรักษ์หน้าใหม่', 'desc' => 'เข้าร่วมกิจกรรมครั้งแรก', 'condition' => $total_points >= 50, 'points' => 50],
                ['icon' => 'fa-recycle', 'name' => 'ผู้รีไซเคิล', 'desc' => 'สะสม 500 คะแนน', 'condition' => $total_points >= 500, 'points' => 500],
                ['icon' => 'fa-tree', 'name' => 'ผู้พิทักษ์ป่า', 'desc' => 'เข้าร่วม 5 กิจกรรม', 'condition' => $total_points >= 1000, 'points' => 1000],
                ['icon' => 'fa-medal', 'name' => 'นักสะสมคะแนน', 'desc' => 'สะสม 2500 คะแนน', 'condition' => $total_points >= 2500, 'points' => 2500],
                ['icon' => 'fa-crown', 'name' => 'ราชาแห่งการอนุรักษ์', 'desc' => 'สะสม 5000 คะแนน', 'condition' => $total_points >= 5000, 'points' => 5000],
                ['icon' => 'fa-globe', 'name' => 'ฮีโร่โลกสีเขียว', 'desc' => 'สะสม 10000 คะแนน', 'condition' => $total_points >= 10000, 'points' => 10000],
                ['icon' => 'fa-fire', 'name' => 'นักกิจกรรมตัวยง', 'desc' => 'เข้าร่วม 10 กิจกรรม', 'condition' => $total_points >= 2000, 'points' => 2000],
                ['icon' => 'fa-star', 'name' => 'ดาราแห่ง Eco Point', 'desc' => 'ระดับสมาชิก Gold', 'condition' => $user['u_role'] == 'gold' || $total_points >= 5000, 'points' => 5000],
            ];
            
            foreach($achievements as $ach): 
                $isUnlocked = $ach['condition'];
            ?>
            <div class="achievement-item <?php echo !$isUnlocked ? 'locked' : ''; ?>" 
                 data-points="<?php echo $ach['points']; ?>">
                <div class="achievement-icon">
                    <i class="fas <?php echo $ach['icon']; ?>"></i>
                </div>
                <div class="achievement-name">
                    <?php echo $ach['name']; ?>
                    <?php if($isUnlocked): ?>
                        <i class="fas fa-check" style="color: #4CAF50; margin-left: 5px;"></i>
                    <?php endif; ?>
                </div>
                <div class="achievement-desc">
                    <?php echo $ach['desc']; ?>
                    <?php if(!$isUnlocked): ?>
                        <br><small style="color: #999;">(ต้องการ <?php echo number_format($ach['points']); ?> คะแนน)</small>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // คำนวณ progress bar
    const totalPoints = <?php echo $total_points; ?>;
    const userRole = "<?php echo $user['u_role']; ?>";
    
    let nextLevelPoints = 0;
    let currentLevelPoints = 0;
    
    // กำหนดระดับคะแนน
    switch(userRole.toLowerCase()) {
        case 'member':
            currentLevelPoints = 0;
            nextLevelPoints = 1000; // Bronze
            break;
        case 'bronze':
            currentLevelPoints = 1000;
            nextLevelPoints = 2500; // Silver
            break;
        case 'silver':
            currentLevelPoints = 2500;
            nextLevelPoints = 5000; // Gold
            break;
        case 'gold':
            currentLevelPoints = 5000;
            nextLevelPoints = 10000; // Platinum
            break;
        default:
            currentLevelPoints = 0;
            nextLevelPoints = 1000;
    }
    
    // คำนวณ percentage
    const progressPercentage = Math.min(((totalPoints - currentLevelPoints) / (nextLevelPoints - currentLevelPoints)) * 100, 100);
    
    // Animate progress bar
    const progressFill = document.getElementById('progressFill');
    if(progressFill) {
        setTimeout(() => {
            progressFill.style.width = progressPercentage + '%';
        }, 500);
    }
    
    // นับความสำเร็จที่ปลดล็อกแล้ว
    const unlockedCount = document.querySelectorAll('.achievement-item:not(.locked)').length;
    document.getElementById('unlockedCount').textContent = unlockedCount;
    
    // Animate stat numbers
    const totalPointsEl = document.getElementById('totalPoints');
    if(totalPointsEl && totalPoints > 0) {
        let current = 0;
        const target = totalPoints;
        const increment = target / 50;
        const timer = setInterval(() => {
            current += increment;
            if(current >= target) {
                current = target;
                clearInterval(timer);
            }
            totalPointsEl.textContent = Math.floor(current).toLocaleString();
        }, 30);
    }
    
    // Add hover effects to achievement items
    document.querySelectorAll('.achievement-item').forEach(item => {
        item.addEventListener('mouseenter', function() {
            if(!this.classList.contains('locked')) {
                this.style.transform = 'translateY(-10px)';
                this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.1)';
            }
        });
        
        item.addEventListener('mouseleave', function() {
            if(!this.classList.contains('locked')) {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = 'none';
            }
        });
    });
    
    // Show achievement tooltip
    document.querySelectorAll('.achievement-item').forEach(item => {
        item.addEventListener('click', function() {
            const points = this.getAttribute('data-points');
            const name = this.querySelector('.achievement-name').textContent;
            const desc = this.querySelector('.achievement-desc').textContent;
            const isLocked = this.classList.contains('locked');
            
            let message = `${name}\n${desc}`;
            if(isLocked) {
                message += `\n\nยังไม่ปลดล็อก\nต้องการ ${points} คะแนน`;
            } else {
                message += `\n\n✅ ปลดล็อกแล้ว!`;
            }
            
            alert(message);
        });
    });
});
</script>