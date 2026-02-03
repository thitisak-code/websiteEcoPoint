<?php 
include 'includes/config.php';
//ตรวจสอบ Session
$allow_roles = ['Super admin'];

if (
    !isset($_SESSION['username'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], $allow_roles)
) {
    echo "<script>window.location='index.php?page=home';</script>";
    exit();
}

$firstname = $_SESSION['firstname'];

$result = "";
$searchResults = [];

if(isset($_GET['search']) && !empty($_GET['search'])){
    $key = "%".$_GET['search']."%";
    $stmt = $conn->prepare("SELECT * FROM users 
    WHERE uid LIKE ? OR
    firstname LIKE ? OR
    lastname LIKE ? OR
    username LIKE ?");
    $stmt->bind_param("ssss", $key, $key, $key, $key);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // เก็บผลลัพธ์ใน array
    while($row = $result->fetch_assoc()){
        $searchResults[] = $row;
    }
}
?>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{
    font-family: 'Noto Sans Thai', sans-serif;
}

/* Custom CSS Styles */
:root {
    --primary-color: #198754;
    --primary-light: #e8f5e9;
    --primary-dark: #157347;
    --secondary-color: #20c997;
    --accent-color: #ffc107;
    --light-bg: #f8f9fa;
    --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    --hover-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
}

.addpoint-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
    padding: 20px;
}

.search-header-card {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: 20px;
    border: none;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    margin-bottom: 30px;
    transition: var(--transition);
}

.search-header-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--hover-shadow);
}

.results-card {
    background: white;
    border-radius: 20px;
    border: none;
    box-shadow: var(--card-shadow);
    overflow: hidden;
    transition: var(--transition);
}

.results-card:hover {
    box-shadow: var(--hover-shadow);
}

.user-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.5rem;
    box-shadow: 0 4px 15px rgba(25, 135, 84, 0.3);
    transition: var(--transition);
}

.user-avatar:hover {
    transform: scale(1.1);
    box-shadow: 0 6px 20px rgba(25, 135, 84, 0.4);
}

.points-badge {
    background: linear-gradient(135deg, var(--primary-light) 0%, #d1f7e5 100%);
    color: var(--primary-dark);
    font-weight: 700;
    padding: 8px 16px;
    border-radius: 50px;
    border: 2px solid rgba(25, 135, 84, 0.1);
    box-shadow: 0 4px 12px rgba(25, 135, 84, 0.1);
}

.input-group-custom {
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid #e9ecef;
    transition: var(--transition);
}

.input-group-custom:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.input-group-custom .input-group-text {
    background-color: white;
    border: none;
    padding: 0.75rem 1rem;
}

.input-group-custom .form-control {
    border: none;
    padding: 0.75rem 1rem;
    box-shadow: none;
}

.btn-success-custom {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border: none;
    border-radius: 12px;
    padding: 0.75rem 2rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.btn-success-custom:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(25, 135, 84, 0.4);
}

.btn-success-custom:active {
    transform: translateY(-1px);
}

.btn-success-custom::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: 0.5s;
}

.btn-success-custom:hover::after {
    left: 100%;
}

.table-custom {
    border-collapse: separate;
    border-spacing: 0;
}

.table-custom thead th {
    background: linear-gradient(to bottom, #f8f9fa, #e9ecef);
    color: var(--primary-dark);
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
    padding: 1rem 1.25rem;
    position: sticky;
    top: 0;
    z-index: 10;
}

.table-custom tbody tr {
    transition: var(--transition);
}

.table-custom tbody tr:hover {
    background-color: rgba(25, 135, 84, 0.03);
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
}

.table-custom tbody td {
    padding: 1.25rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    vertical-align: middle;
}

.total-points-display {
    font-size: 1.1rem;
    font-weight: 700;
    padding: 6px 12px;
    border-radius: 8px;
    background: rgba(25, 135, 84, 0.1);
    transition: var(--transition);
}

.total-points-display.active {
    background: rgba(25, 135, 84, 0.2);
    color: var(--primary-dark);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
    70% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
    100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0); }
}

.empty-state {
    padding: 60px 20px;
    text-align: center;
}

.empty-state-icon {
    font-size: 5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    width: 120px;
    height: 120px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 30px;
    color: #adb5bd;
}

.search-highlight {
    background-color: #fff3cd;
    padding: 2px 4px;
    border-radius: 4px;
    font-weight: 600;
}

.form-select-custom {
    border-radius: 10px;
    border: 2px solid #e9ecef;
    padding: 0.5rem 1rem;
    transition: var(--transition);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23198754' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}

.form-select-custom:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.25rem rgba(25, 135, 84, 0.25);
}

.quick-stats {
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    border-radius: 15px;
    padding: 15px;
    margin-bottom: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.05);
}

.stats-item {
    text-align: center;
    padding: 10px;
}

.stats-value {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--primary-color);
    margin-bottom: 5px;
}

.stats-label {
    font-size: 0.85rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    display: none;
}

.spinner-custom {
    width: 60px;
    height: 60px;
    border: 5px solid rgba(25, 135, 84, 0.1);
    border-radius: 50%;
    border-top-color: var(--primary-color);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

.toast-notification {
    position: fixed;
    bottom: 20px;
    right: 20px;
    min-width: 300px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    z-index: 9999;
    transform: translateY(100px);
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.toast-notification.show {
    transform: translateY(0);
    opacity: 1;
}

.toast-success {
    border-left: 5px solid var(--primary-color);
}

.toast-error {
    border-left: 5px solid #dc3545;
}

.success-checkmark {
    width: 40px;
    height: 40px;
    background-color: var(--primary-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .addpoint-container {
        padding: 10px;
    }
    
    .table-custom tbody td {
        padding: 0.75rem;
    }
    
    .user-avatar {
        width: 45px;
        height: 45px;
        font-size: 1.2rem;
    }
}
</style>

<div class="addpoint-container">
    <!-- Header Section -->
    <div class="search-header-card">
        <div class="card-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="text-white fw-bold mb-3">
                        <i class="fas fa-search me-3"></i>ค้นหาผู้ใช้งาน
                    </h1>
                    <p class="text-white-50 mb-4">
                        ค้นหาผู้ใช้งานด้วย ID, ชื่อ, นามสกุล หรือชื่อผู้ใช้ เพื่อเพิ่มคะแนนสะสม
                    </p>
                    
                    <form action="" method="GET" id="searchForm">
                        <div class="row g-2">
                            <div class="col-md-9">
                                <div class="input-group input-group-custom">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="search" 
                                           class="form-control form-control-lg" 
                                           placeholder="ค้นหาโดย ID, ชื่อ, นามสกุล, หรือชื่อผู้ใช้..." 
                                           name="search"
                                           id="searchInput"
                                           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                                           autocomplete="off">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <input type="hidden" name="page" value="addpoint">
                                <button type="submit" class="btn btn-success-custom w-100 btn-lg">
                                    <i class="fas fa-search me-2"></i>ค้นหา
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <div class="position-relative">
                        <div class="user-avatar mx-auto mb-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="text-white">
                            <h5 class="mb-1"><?php echo htmlspecialchars($firstname); ?></h5>
                            <small class="opacity-75">ผู้จัดการระบบ</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <?php if(!empty($searchResults)): ?>
    <div class="row quick-stats">
        <div class="col-md-3 stats-item">
            <div class="stats-value"><?php echo count($searchResults); ?></div>
            <div class="stats-label">ผู้ใช้งานทั้งหมด</div>
        </div>
        <div class="col-md-3 stats-item">
            <div class="stats-value">
                <?php 
                $totalPoints = 0;
                foreach($searchResults as $data) {
                    $totalPoints += isset($data['u_total_point']) ? $data['u_total_point'] : 0;
                }
                echo number_format($totalPoints);
                ?>
            </div>
            <div class="stats-label">คะแนนรวม</div>
        </div>
        <div class="col-md-3 stats-item">
            <div class="stats-value">
                <?php 
                $avgPoints = count($searchResults) > 0 ? $totalPoints / count($searchResults) : 0;
                echo number_format($avgPoints, 1);
                ?>
            </div>
            <div class="stats-label">คะแนนเฉลี่ย</div>
        </div>
        <div class="col-md-3 stats-item">
            <div class="stats-value">
                <?php 
                $activeUsers = 0;
                foreach($searchResults as $data) {
                    if(isset($data['u_total_point']) && $data['u_total_point'] > 0) {
                        $activeUsers++;
                    }
                }
                echo $activeUsers;
                ?>
            </div>
            <div class="stats-label">ผู้ใช้งานที่ใช้งาน</div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Results Section -->
    <?php if(!empty($searchResults)): ?>
    <div class="results-card mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-primary mb-1">
                        <i class="fas fa-users me-3"></i>ผลการค้นหา
                    </h3>
                    <p class="text-muted mb-0">
                        พบผู้ใช้งาน <span class="fw-bold text-success"><?php echo count($searchResults); ?></span> รายการ
                        <?php if(isset($_GET['search'])): ?>
                        สำหรับคำค้นหา: <span class="search-highlight">"<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex align-items-center">
                    <button class="btn btn-outline-success me-3" onclick="resetAllForms()">
                        <i class="fas fa-redo me-2"></i>รีเซ็ตทั้งหมด
                    </button>
                    <div class="text-end">
                        <div class="text-muted small">อัปเดตล่าสุด</div>
                        <div class="fw-bold"><?php echo date('H:i'); ?> น.</div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th width="25%" class="ps-4">ผู้ใช้งาน</th>
                            <th width="15%">คะแนนปัจจุบัน</th>
                            <th width="15%">แก้ว</th>
                            <th width="15%">ขวดพลาสติก</th>
                            <th width="15%">กิจกรรมอื่นๆ</th>
                            <th width="15%" class="pe-4">ดำเนินการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($searchResults as $index => $data): 
                            $currentPoints = isset($data['u_total_point']) ? $data['u_total_point'] : 0;
                        ?>
                        <tr>
                            <!-- User Info Column -->
                            <td class="ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="user-avatar me-3">
                                        <?php echo strtoupper(substr(htmlspecialchars($data['firstname']), 0, 1)); ?>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            <?php echo htmlspecialchars($data['firstname'] . ' ' . $data['lastname']); ?>
                                        </h6>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-id-card me-1"></i><?php echo htmlspecialchars($data['uid']); ?>
                                            </span>
                                            <?php if(isset($data['username']) && !empty($data['username'])): ?>
                                            <span class="badge bg-light text-dark border">
                                                <i class="fas fa-at me-1"></i><?php echo htmlspecialchars($data['username']); ?>
                                            </span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Current Points Column -->
                            <td>
                                <div class="points-badge d-inline-block text-center">
                                    <div class="fw-bold fs-4"><?php echo number_format($currentPoints); ?></div>
                                    <small class="text-muted">คะแนน</small>
                                </div>
                            </td>
                            
                            <!-- Add Points Form -->
                            <form method="POST" id="form-<?php echo $data['uid']; ?>" class="add-point-form">
                            <!-- Cup Input -->
                            <td>
                                <div class="input-group input-group-custom" style="width: 140px;">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-glass-whiskey text-success"></i>
                                    </span>
                                    <input type="number" 
                                           class="form-control" 
                                           name="cup" 
                                           placeholder="0"
                                           min="0"
                                           max="999"
                                           oninput="calculateTotal('<?php echo $data['uid']; ?>', <?php echo $currentPoints; ?>)"
                                           id="cup-<?php echo $data['uid']; ?>">
                                    <span class="input-group-text bg-white px-2">
                                        <small class="text-muted">x1</small>
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-1">1 คะแนน/ชิ้น</small>
                            </td>
                            
                            <!-- Bottle Input -->
                            <td>
                                <div class="input-group input-group-custom" style="width: 140px;">
                                    <span class="input-group-text bg-white">
                                        <i class="fas fa-wine-bottle text-success"></i>
                                    </span>
                                    <input type="number" 
                                           class="form-control" 
                                           name="bot" 
                                           placeholder="0"
                                           min="0"
                                           max="999"
                                           oninput="calculateTotal('<?php echo $data['uid']; ?>', <?php echo $currentPoints; ?>)"
                                           id="bot-<?php echo $data['uid']; ?>">
                                    <span class="input-group-text bg-white px-2">
                                        <small class="text-muted">x2</small>
                                    </span>
                                </div>
                                <small class="text-muted d-block mt-1">2 คะแนน/ชิ้น</small>
                            </td>
                            
                            <!-- Other Activities -->
                            <td>
                                <div style="width: 180px;">
                                    <select name="other" 
                                            class="form-select form-select-custom w-100"
                                            onchange="calculateTotal('<?php echo $data['uid']; ?>', <?php echo $currentPoints; ?>)"
                                            id="other-<?php echo $data['uid']; ?>">
                                        <option value="0">เลือกกิจกรรมอื่นๆ</option>
                                        <option value="5">ทำความสะอาดร้าน (+5)</option>
                                        <option value="10">แยกขยะ (+10)</option>
                                        <option value="15">เติมสต็อคช่วยร้าน (+15)</option>
                                    </select>
                                    <div class="mt-3">
                                        <div class="total-points-display" id="total-<?php echo $data['uid']; ?>">
                                            รวม: 0 คะแนน
                                        </div>
                                        <div class="small text-muted mt-1">
                                            รวมทั้งหมด: <span id="new-total-<?php echo $data['uid']; ?>" class="fw-bold"><?php echo number_format($currentPoints); ?></span> คะแนน
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Action Buttons -->
                            <td class="pe-4">
                                <input type="hidden" name="uid" value="<?php echo $data['uid']; ?>">
                                <input type="hidden" name="current_points" value="<?php echo $currentPoints; ?>">
                                
                                <button type="submit" 
                                        name="add-point" 
                                        class="btn btn-success-custom w-100 mb-2"
                                        data-user-name="<?php echo htmlspecialchars($data['firstname'] . ' ' . $data['lastname']); ?>">
                                    <i class="fas fa-plus-circle me-2"></i>เพิ่มคะแนน
                                </button>
                                
                                <div class="text-center">
                                    <small class="text-muted">
                                        <i class="fas fa-user me-1"></i>
                                        <?php echo htmlspecialchars($firstname); ?>
                                    </small>
                                </div>
                            </td>
                            </form>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card-footer bg-white border-0 py-3 px-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="text-muted">
                        <i class="fas fa-info-circle me-2"></i>
                        <span class="fw-bold">หมายเหตุ:</span> คะแนนแก้ว = 1 คะแนน/ชิ้น, คะแนนขวด = 2 คะแนน/ชิ้น
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <button class="btn btn-outline-success" onclick="exportToExcel()">
                            <i class="fas fa-file-excel me-2"></i>Export
                        </button>
                        <button class="btn btn-outline-primary" onclick="window.print()">
                            <i class="fas fa-print me-2"></i>Print
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php elseif(isset($_GET['search']) && empty($searchResults)): ?>
    <!-- No Results State -->
    <div class="results-card">
        <div class="card-body p-5">
            <div class="empty-state">
                <div class="empty-state-icon text-warning">
                    <i class="fas fa-search-minus"></i>
                </div>
                <h3 class="fw-bold text-muted mb-3">ไม่พบข้อมูลผู้ใช้ที่ค้นหา</h3>
                <p class="text-muted mb-4">
                    ไม่พบข้อมูลสำหรับคำค้นหา: <span class="search-highlight">"<?php echo htmlspecialchars($_GET['search']); ?>"</span>
                </p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="?page=addpoint" class="btn btn-success-custom px-4">
                        <i class="fas fa-redo me-2"></i>ค้นหาใหม่
                    </a>
                    <button class="btn btn-outline-success px-4" onclick="document.getElementById('searchInput').focus()">
                        <i class="fas fa-edit me-2"></i>แก้ไขคำค้นหา
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Initial State -->
    <div class="results-card">
        <div class="card-body p-5">
            <div class="empty-state">
                <div class="empty-state-icon text-success">
                    <i class="fas fa-search"></i>
                </div>
                <h3 class="fw-bold text-muted mb-3">ค้นหาผู้ใช้งานเพื่อเพิ่มคะแนน</h3>
                <p class="text-muted mb-4">
                    กรอกข้อมูลในช่องค้นหาด้านบนเพื่อค้นหาผู้ใช้งานและเพิ่มคะแนนสะสม
                </p>
                <div class="row mt-5">
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3 border rounded-3">
                            <i class="fas fa-glass-whiskey text-success fs-1 mb-3"></i>
                            <h6 class="fw-bold">เพิ่มคะแนนแก้ว</h6>
                            <small class="text-muted">1 คะแนนต่อชิ้น</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3 border rounded-3">
                            <i class="fas fa-wine-bottle text-success fs-1 mb-3"></i>
                            <h6 class="fw-bold">เพิ่มคะแนนขวด</h6>
                            <small class="text-muted">2 คะแนนต่อชิ้น</small>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="text-center p-3 border rounded-3">
                            <i class="fas fa-tasks text-success fs-1 mb-3"></i>
                            <h6 class="fw-bold">กิจกรรมอื่นๆ</h6>
                            <small class="text-muted">5-15 คะแนน</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="text-center">
        <div class="spinner-custom mb-3"></div>
        <h4 class="text-primary fw-bold">กำลังประมวลผล...</h4>
        <p class="text-muted">กรุณารอสักครู่</p>
    </div>
</div>

<!-- Toast Notification -->
<div class="toast-notification toast-success" id="successToast">
    <div class="toast-body p-3">
        <div class="d-flex align-items-center">
            <div class="success-checkmark me-3">
                <i class="fas fa-check"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">เพิ่มคะแนนสำเร็จ!</h6>
                <p class="mb-0 text-muted" id="toastMessage">คะแนนถูกเพิ่มให้กับผู้ใช้งานเรียบร้อยแล้ว</p>
            </div>
            <button type="button" class="btn-close" onclick="hideToast()"></button>
        </div>
    </div>
</div>

<div class="toast-notification toast-error" id="errorToast" style="display: none;">
    <div class="toast-body p-3">
        <div class="d-flex align-items-center">
            <div class="success-checkmark me-3 bg-danger">
                <i class="fas fa-times"></i>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">เกิดข้อผิดพลาด!</h6>
                <p class="mb-0 text-muted" id="errorToastMessage">ไม่สามารถเพิ่มคะแนนได้ กรุณาลองใหม่อีกครั้ง</p>
            </div>
            <button type="button" class="btn-close" onclick="hideToast()"></button>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันคำนวณคะแนนรวม
function calculateTotal(userId, currentPoints) {
    const cup = parseInt(document.getElementById('cup-' + userId).value) || 0;
    const bot = parseInt(document.getElementById('bot-' + userId).value) || 0;
    const other = parseInt(document.getElementById('other-' + userId).value) || 0;
    
    const cupPoints = cup * 1;
    const botPoints = bot * 2;
    const addedTotal = cupPoints + botPoints + other;
    const newTotal = currentPoints + addedTotal;
    
    const totalElement = document.getElementById('total-' + userId);
    const newTotalElement = document.getElementById('new-total-' + userId);
    
    totalElement.textContent = `รวม: ${addedTotal.toLocaleString()} คะแนน`;
    newTotalElement.textContent = newTotal.toLocaleString();
    
    if (addedTotal > 0) {
        totalElement.classList.add('active');
        totalElement.style.backgroundColor = 'rgba(25, 135, 84, 0.2)';
        totalElement.style.color = '#157347';
    } else {
        totalElement.classList.remove('active');
        totalElement.style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
        totalElement.style.color = '#495057';
    }
}

// ฟังก์ชันรีเซ็ตฟอร์มทั้งหมด
function resetAllForms() {
    document.querySelectorAll('.add-point-form').forEach(form => {
        form.reset();
        const userId = form.id.replace('form-', '');
        const currentPoints = parseInt(form.querySelector('[name="current_points"]').value) || 0;
        
        document.getElementById('total-' + userId).textContent = 'รวม: 0 คะแนน';
        document.getElementById('total-' + userId).classList.remove('active');
        document.getElementById('total-' + userId).style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
        document.getElementById('total-' + userId).style.color = '#495057';
        document.getElementById('new-total-' + userId).textContent = currentPoints.toLocaleString();
    });
    
    showToast('รีเซ็ตฟอร์มทั้งหมดเรียบร้อยแล้ว', 'success');
}

// ฟังก์ชันแสดง Toast Notification
function showToast(message, type = 'success') {
    if (type === 'success') {
        document.getElementById('toastMessage').textContent = message;
        document.getElementById('successToast').style.display = 'block';
        setTimeout(() => {
            document.getElementById('successToast').classList.add('show');
        }, 100);
        
        setTimeout(() => {
            hideToast();
        }, 5000);
    } else {
        document.getElementById('errorToastMessage').textContent = message;
        document.getElementById('errorToast').style.display = 'block';
        setTimeout(() => {
            document.getElementById('errorToast').classList.add('show');
        }, 100);
        
        setTimeout(() => {
            hideToast();
        }, 5000);
    }
}

function hideToast() {
    document.querySelectorAll('.toast-notification').forEach(toast => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.style.display = 'none';
        }, 400);
    });
}

// ฟังก์ชัน Export ข้อมูล
function exportToExcel() {
    const table = document.querySelector('.table-custom');
    const rows = table.querySelectorAll('tr');
    let csv = [];
    
    rows.forEach(row => {
        const rowData = [];
        const cells = row.querySelectorAll('th, td');
        
        cells.forEach(cell => {
            // ไม่รวม input fields และ buttons
            if (!cell.querySelector('input') && !cell.querySelector('button') && !cell.querySelector('select')) {
                rowData.push(cell.innerText.replace(/\n/g, ' ').trim());
            }
        });
        
        if (rowData.length > 0) {
            csv.push(rowData.join(','));
        }
    });
    
    const csvContent = "data:text/csv;charset=utf-8," + csv.join('\n');
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "user_points_<?php echo date('Y-m-d'); ?>.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    showToast('ส่งออกข้อมูลเรียบร้อยแล้ว', 'success');
}

// ป้องกันฟอร์มส่งข้อมูลซ้ำ
document.querySelectorAll('.add-point-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('[name="add-point"]');
        const cup = parseInt(this.querySelector('[name="cup"]').value) || 0;
        const bot = parseInt(this.querySelector('[name="bot"]').value) || 0;
        const other = parseInt(this.querySelector('[name="other"]').value) || 0;
        const total = (cup * 1) + (bot * 2) + other;
        
        if (total === 0) {
            e.preventDefault();
            showToast('กรุณากรอกจำนวนแก้ว, ขวด หรือเลือกกิจกรรม', 'error');
            return false;
        }
        
        // แสดง loading overlay
        document.getElementById('loadingOverlay').style.display = 'flex';
        
        // ปิดการคลิกปุ่มซ้ำ
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>กำลังบันทึก...';
        
        // ตั้งเวลาให้ปุ่มกลับมาใช้งานได้ถ้าไม่สำเร็จ
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>เพิ่มคะแนน';
            document.getElementById('loadingOverlay').style.display = 'none';
        }, 5000);
    });
});

// Auto focus search input
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput && !searchInput.value) {
        searchInput.focus();
    }
    
    // เพิ่ม animation ให้กับผลลัพธ์
    document.querySelectorAll('.table-custom tbody tr').forEach((row, index) => {
        row.style.animationDelay = `${index * 0.05}s`;
        row.style.animation = 'fadeInUp 0.5s ease-out forwards';
        row.style.opacity = '0';
    });
});

// เพิ่ม CSS animation
const style = document.createElement('style');
style.textContent = `
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
`;
document.head.appendChild(style);
</script>

<?php 
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add-point'])){
    // รับค่า
    $uid = $_POST['uid'] ?: 0;
    $cup = $_POST['cup'] ?: 0;
    $bot = $_POST['bot'] ?: 0;
    $other = $_POST['other'] ?: 0;
    $current_points = $_POST['current_points'] ?: 0;

    // คำนวณค่า
    $bot1 = $bot * 2;
    $cup1 = $cup * 1;
    $total = $cup1 + $bot1 + $other;
    $new_total = $current_points + $total;

    // ผู้ให้คะแนน
    $giver = "เพิ่มคะแนนโดย : " . $firstname;

    // เริ่ม transaction
    $conn->begin_transaction();
    
    try {
        // บันทึกประวัติ
        $stmt1 = $conn->prepare("INSERT INTO record_points(uid, p_cup, p_bottle, p_other, p_total, p_giver) VALUES(?,?,?,?,?,?)");
        $stmt1->bind_param("ssssss", $uid, $cup, $bot, $other, $total, $giver);
        $stmt1->execute();
        
        // อัพเดทคะแนนรวม
        $stmt2 = $conn->prepare("UPDATE users SET u_total_point = u_total_point + ? WHERE uid = ?");
        $stmt2->bind_param("ss", $total, $uid);
        $stmt2->execute();
        
        // Commit transaction
        $conn->commit();
        
        // ดึงชื่อผู้ใช้เพื่อแสดงในข้อความ
        $stmt3 = $conn->prepare("SELECT firstname, lastname FROM users WHERE uid = ?");
        $stmt3->bind_param("s", $uid);
        $stmt3->execute();
        $result = $stmt3->get_result();
        $user = $result->fetch_assoc();
        $user_name = $user ? $user['firstname'] . ' ' . $user['lastname'] : 'ผู้ใช้งาน';
        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('เพิ่มคะแนน " . addslashes($total) . " คะแนนให้กับ " . addslashes($user_name) . " สำเร็จ (รวมใหม่: " . addslashes($new_total) . " คะแนน)', 'success');
            
            // รีเฟรชหน้าเว็บหลังจากแสดงข้อความสำเร็จ
            setTimeout(function() {
                window.location.href = 'index.php?page=addpoint&search=" . urlencode($_GET['search'] ?? '') . "';
            }, 2000);
        });
        </script>";
        
    } catch (Exception $e) {
        // Rollback transaction ถ้าเกิดข้อผิดพลาด
        $conn->rollback();
        
        echo "<script>
        document.addEventListener('DOMContentLoaded', function() {
            showToast('เกิดข้อผิดพลาด: " . addslashes($e->getMessage()) . "', 'error');
            document.getElementById('loadingOverlay').style.display = 'none';
        });
        </script>";
    }
}
?>