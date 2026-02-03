<?php

include_once 'includes/config.php'; // ตรวจสอบให้แน่ใจว่าเชื่อมต่อฐานข้อมูลแล้ว

// Switch case สำหรับจัดการ operations
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            if (isset($_GET['id'])) {
                $ac_id = $_GET['id'];
                
                // ลบรูปภาพก่อน (ถ้ามี)
                $stmt_img = $conn->prepare("SELECT image FROM activity WHERE ac_id = ?");
                $stmt_img->bind_param("i", $ac_id);
                $stmt_img->execute();
                $result_img = $stmt_img->get_result();
                if ($row_img = $result_img->fetch_assoc()) {
                    if ($row_img['image'] && file_exists("uploads/activity/" . $row_img['image'])) {
                        unlink("uploads/activity/" . $row_img['image']);
                    }
                }
                
                $stmt = $conn->prepare("DELETE FROM activity WHERE ac_id = ?");
                $stmt->bind_param("i", $ac_id);
                if ($stmt->execute()) {
                    $_SESSION['success'] = 'ลบกิจกรรมสำเร็จ✅';
                    echo "<script>window.location='index.php?page=manage_activity';</script>";
                    exit();
                }
            }
            break;

        case 'approved':
            if (isset($_GET['ev_id'])) {
                $ev_id = $_GET['ev_id'];
                $uid = $_GET['uid'];
                $point = $_GET['point'];
                $title = $_GET['event'] ?? '';
                $data_title = "ได้รับจากกิจกรรม " . $title;
                $giver = $_SESSION['firstname'] ?? 'Admin';
                $data_giver = "เพิ่มคะแนนโดย : " . $giver;

                // เริ่ม Transaction
                $conn->begin_transaction();
                
                try {
                    // อัพเดทสถานะ record_ac
                    $stmt1 = $conn->prepare("UPDATE record_ac SET status = 'approved', ev_notify = 0 WHERE ev_id = ?");
                    $stmt1->bind_param("i", $ev_id);
                    $stmt1->execute();

                    // บันทึกรายการคะแนน
                    $stmt2 = $conn->prepare("INSERT INTO record_points(uid, p_other, p_total, p_giver) VALUES (?,?,?,?)");
                    $stmt2->bind_param("ssss", $uid, $data_title, $point, $data_giver);
                    $stmt2->execute();

                    // อัพเดทคะแนนรวมของผู้ใช้
                    $stmt3 = $conn->prepare("UPDATE users SET u_total_point = u_total_point + ? WHERE uid = ?");
                    $stmt3->bind_param("ii", $point, $uid);
                    $stmt3->execute();

                    $conn->commit();
                    $_SESSION['success'] = 'อนุมัติรายการสำเร็จ✅';
                } catch (Exception $e) {
                    $conn->rollback();
                    $_SESSION['error'] = 'เกิดข้อผิดพลาดในการอนุมัติ: ' . $e->getMessage();
                }
                
                echo "<script>window.location='index.php?page=manage_activity';</script>";
                exit();
            }
            break;
    }
}

// ตรวจสอบการเพิ่ม/แก้ไขกิจกรรม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['add-activity'])) {
        // เพิ่มกิจกรรมใหม่
        $title = $_POST['title'] ?? '';
        $point = $_POST['point'] ?? 0;
        $limit = $_POST['limit'] ?? 0;
        
        // ตรวจสอบข้อมูล
        if (empty($title) || $point <= 0 || $limit <= 0) {
            $_SESSION['error'] = 'กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง';
            echo "<script>window.location='index.php?page=manage_activity';</script>";
            exit();
        }

        $dir = "uploads/activity/";
        $image = '';
        
        // สร้างโฟลเดอร์ถ้ายังไม่มี
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        // จัดการไฟล์รูปภาพ
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $move = $dir . $new_filename;
            
            // ตรวจสอบประเภทไฟล์
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $move)) {
                    $image = $new_filename;
                }
            }
        }

        $stmt = $conn->prepare("INSERT INTO activity(title, image, point, ac_limit) VALUES(?,?,?,?)");
        $stmt->bind_param("ssii", $title, $image, $point, $limit);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'บันทึกกิจกรรมสำเร็จ✅';
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
        }
        
        echo "<script>window.location='index.php?page=manage_activity';</script>";
        exit();
        
    } elseif (isset($_POST['edit-activity'])) {
        // แก้ไขกิจกรรม
        $ac_id = $_POST['ac_id'] ?? 0;
        $title = $_POST['title'] ?? '';
        $point = $_POST['point'] ?? 0;
        $limit = $_POST['limit'] ?? 0;
        
        if ($ac_id <= 0) {
            $_SESSION['error'] = 'ไม่พบกิจกรรมที่ต้องการแก้ไข';
            echo "<script>window.location='index.php?page=manage_activity';</script>";
            exit();
        }

        // ดึงข้อมูลรูปภาพเดิม
        $current_image = '';
        $stmt_current = $conn->prepare("SELECT image FROM activity WHERE ac_id = ?");
        $stmt_current->bind_param("i", $ac_id);
        $stmt_current->execute();
        $stmt_current->bind_result($current_image);
        $stmt_current->fetch();
        $stmt_current->close();

        $image = $current_image;
        $dir = "uploads/activity/";

        // ถ้ามีการอัปโหลดรูปใหม่
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            // ลบรูปเดิมถ้ามี
            if ($current_image && file_exists($dir . $current_image)) {
                unlink($dir . $current_image);
            }

            $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $new_filename = uniqid() . '.' . $file_extension;
            $move = $dir . $new_filename;
            
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array(strtolower($file_extension), $allowed_types)) {
                if (move_uploaded_file($_FILES['image']['tmp_name'], $move)) {
                    $image = $new_filename;
                }
            }
        }

        $stmt = $conn->prepare("UPDATE activity SET title = ?, image = ?, point = ?, ac_limit = ? WHERE ac_id = ?");
        $stmt->bind_param("ssiii", $title, $image, $point, $limit, $ac_id);
        if ($stmt->execute()) {
            $_SESSION['success'] = 'แก้ไขกิจกรรมสำเร็จ✅';
        } else {
            $_SESSION['error'] = 'เกิดข้อผิดพลาดในการแก้ไขข้อมูล';
        }
        
        echo "<script>window.location='index.php?page=manage_activity'</script>";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการกิจกรรม</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .activity-card {
            font-size: 14px;
            transition: transform 0.3s;
        }
        .activity-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .activity-img {
            height: 120px;
            object-fit: cover;
            border-top-left-radius: calc(0.25rem - 1px);
            border-top-right-radius: calc(0.25rem - 1px);
        }
        .activity-card .card-body {
            padding: 10px;
        }
        .activity-card .card-footer {
            padding: 6px;
            background-color: rgba(0,0,0,0.03);
        }
        .badge {
            font-size: 0.75em;
        }
        .table th {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-3">
        <!-- แสดงข้อความแจ้งเตือน -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="mb-0">
                <i class="bi bi-calendar-event me-2"></i>จัดการกิจกรรม
            </h3>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#activityModal">
                <i class="bi bi-plus-lg me-1"></i> เพิ่มกิจกรรม
            </button>
        </div>

        <!-- ส่วนแสดงการ์ดกิจกรรม -->
        <div class="row g-3 mb-5">
            <?php
            $ac1 = $conn->query("SELECT * FROM activity ORDER BY ac_id DESC");
            if ($ac1->num_rows > 0) {
                while ($row = $ac1->fetch_assoc()) {
                    $image = !empty($row['image']) && file_exists("uploads/activity/" . $row['image']) 
                            ? $row['image'] 
                            : 'no-image.png';
                    ?>
                    <div class="col-6 col-md-4 col-lg-3 col-xl-2">
                        <div class="card activity-card shadow-sm h-100">
                            <img src="uploads/activity/<?php echo htmlspecialchars($image); ?>"
                                 class="card-img-top activity-img"
                                 alt="<?php echo htmlspecialchars($row['title']); ?>"
                                 onerror="this.src='uploads/activity/no-image.png'">
                            
                            <div class="card-body">
                                <h6 class="card-title fw-bold text-truncate mb-2">
                                    <?php echo htmlspecialchars($row['title']); ?>
                                </h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-warning">
                                        <i class="bi bi-people me-1"></i><?php echo $row['ac_limit']; ?> คน
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="bi bi-star me-1"></i>+<?php echo $row['point']; ?>
                                    </span>
                                </div>
                            </div>
                            
                            <div class="card-footer text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" 
                                        onclick="editActivity(<?php echo $row['ac_id']; ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="?page=manage_activity&action=delete&id=<?php echo $row['ac_id']; ?>"
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('⚠️ คุณแน่ใจหรือไม่ว่าต้องการลบกิจกรรมนี้?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="col-12 text-center py-5">
                        <i class="bi bi-calendar-x display-1 text-muted"></i>
                        <p class="text-muted mt-3">ยังไม่มีกิจกรรม</p>
                      </div>';
            }
            ?>
        </div>

        <!-- ส่วนตารางข้อมูลการเข้าร่วมกิจกรรม -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-list-check me-2"></i>ประวัติการเข้าร่วมกิจกรรม
                </h5>
                
                <form method="post" class="d-flex gap-2">
                    <select name="search" class="form-select form-select-sm" style="width: 200px;">
                        <option value="">แสดงทั้งหมด</option>
                        <option value="panding" <?php echo (isset($_POST['search']) && $_POST['search'] == 'panding') ? 'selected' : ''; ?>>รอการอนุมัติ</option>
                        <option value="approved" <?php echo (isset($_POST['search']) && $_POST['search'] == 'approved') ? 'selected' : ''; ?>>อนุมัติแล้ว</option>
                    </select>
                    <button class="btn btn-sm btn-primary">
                        <i class="bi bi-funnel"></i> ค้นหา
                    </button>
                </form>
            </div>
            
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="text-center" width="80">#</th>
                                <th>ผู้ใช้งาน</th>
                                <th>กิจกรรม</th>
                                <th width="100">คะแนน</th>
                                <th class="text-center" width="120">สถานะ</th>
                                <th class="text-center" width="100">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $search_condition = "";
                            $params = [];
                            $types = "";
                            
                            if (!empty($_POST['search'])) {
                                $status = $_POST['search'];
                                $search_condition = "WHERE record_ac.status = ?";
                                $params[] = $status;
                                $types = "s";
                            }
                            
                            $sql = "
                                SELECT users.uid, users.firstname, users.lastname, 
                                       activity.title, activity.ac_id,
                                       record_ac.ev_id, record_ac.status, record_ac.ac_point,
                                       record_ac.created_at
                                FROM record_ac
                                INNER JOIN users ON users.uid = record_ac.uid
                                LEFT JOIN activity ON activity.ac_id = record_ac.ac_id
                                $search_condition
                                ORDER BY record_ac.created_at DESC
                                LIMIT 50
                            ";
                            
                            if ($types) {
                                $stmt = $conn->prepare($sql);
                                $stmt->bind_param($types, ...$params);
                                $stmt->execute();
                                $result = $stmt->get_result();
                            } else {
                                $result = $conn->query($sql);
                            }
                            
                            if ($result && $result->num_rows > 0) {
                                $counter = 1;
                                while ($data = $result->fetch_assoc()) {
                                    ?>
                                    <tr>
                                        <td class="text-center"><?php echo $counter++; ?></td>
                                        <td>
                                            <div><?php echo htmlspecialchars($data['firstname'] . " " . $data['lastname']); ?></div>
                                            <small class="text-muted">ID: <?php echo $data['uid']; ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($data['title'] ?? '-'); ?></td>
                                        <td class="fw-bold"><?php echo number_format($data['ac_point']); ?></td>
                                        <td class="text-center">
                                            <?php if ($data['status'] == "approved"): ?>
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>อนุมัติแล้ว
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-warning">
                                                    <i class="bi bi-clock me-1"></i>รอการอนุมัติ
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($data['status'] != "approved"): ?>
                                                <a href="?page=manage_activity&action=approved&ev_id=<?php echo $data['ev_id']; ?>&uid=<?php echo $data['uid']; ?>&point=<?php echo $data['ac_point']; ?>&event=<?php echo urlencode($data['title'] ?? ''); ?>"
                                                   class="btn btn-sm btn-success"
                                                   onclick="return confirm('ยืนยันการอนุมัติกิจกรรมนี้?')">
                                                    <i class="bi bi-check-lg"></i>
                                                </a>
                                            <?php else: ?>
                                                <button class="btn btn-sm btn-secondary" disabled>
                                                    <i class="bi bi-check-all"></i>
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                echo '<tr><td colspan="6" class="text-center py-4 text-muted">ไม่พบข้อมูล</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal เพิ่มกิจกรรม -->
    <div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <form method="post" enctype="multipart/form-data">
                    <div class="modal-header bg-primary text-light">
                        <h5 class="modal-title">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่มกิจกรรมใหม่
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อกิจกรรม</label>
                            <input type="text" class="form-control" name="title" required 
                                   placeholder="เช่น กิจกรรมเก็บขยะโรงเรียน">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">รูปภาพกิจกรรม</label>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">รองรับไฟล์ JPG, PNG, GIF (แนะนำขนาด 400x300 px)</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">คะแนนที่ได้รับ</label>
                                <div class="input-group">
                                    <span class="input-group-text">+</span>
                                    <input type="number" class="form-control" name="point" min="1" required>
                                    <span class="input-group-text">คะแนน</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">จำนวนผู้เข้าร่วมสูงสุด</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" name="limit" min="1" required>
                                    <span class="input-group-text">คน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-primary" name="add-activity">
                            <i class="bi bi-save me-1"></i>บันทึกกิจกรรม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal แก้ไขกิจกรรม -->
    <div class="modal fade" id="editActivityModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content shadow-lg">
                <form method="post" enctype="multipart/form-data">
                    <input type="hidden" name="ac_id" id="edit_ac_id">
                    
                    <div class="modal-header bg-warning text-dark">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square me-2"></i>แก้ไขกิจกรรม
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold">ชื่อกิจกรรม</label>
                            <input type="text" class="form-control" id="edit_title" name="title" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">รูปภาพกิจกรรม</label>
                            <div class="mb-2" id="current_image_container">
                                <img id="current_image" src="" class="img-thumbnail" style="max-height: 100px; display: none;">
                            </div>
                            <input type="file" class="form-control" name="image" accept="image/*">
                            <small class="text-muted">เว้นว่างหากไม่ต้องการเปลี่ยนรูป</small>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">คะแนนที่ได้รับ</label>
                                <div class="input-group">
                                    <span class="input-group-text">+</span>
                                    <input type="number" class="form-control" id="edit_point" name="point" min="1" required>
                                    <span class="input-group-text">คะแนน</span>
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">จำนวนผู้เข้าร่วมสูงสุด</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="edit_limit" name="limit" min="1" required>
                                    <span class="input-group-text">คน</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg me-1"></i>ยกเลิก
                        </button>
                        <button type="submit" class="btn btn-warning" name="edit-activity">
                            <i class="bi bi-check-lg me-1"></i>อัพเดทกิจกรรม
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // ฟังก์ชันสำหรับเปิด Modal แก้ไขกิจกรรม
        function editActivity(ac_id) {
            // ดึงข้อมูลกิจกรรมผ่าน AJAX
            fetch(`ajax/get_activity.php?ac_id=${ac_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // เติมข้อมูลในฟอร์ม
                        document.getElementById('edit_ac_id').value = data.ac_id;
                        document.getElementById('edit_title').value = data.title;
                        document.getElementById('edit_point').value = data.point;
                        document.getElementById('edit_limit').value = data.ac_limit;
                        
                        // แสดงรูปภาพปัจจุบัน
                        const imgElement = document.getElementById('current_image');
                        if (data.image && data.image !== '') {
                            imgElement.src = `uploads/activity/${data.image}`;
                            imgElement.style.display = 'block';
                            imgElement.onerror = function() {
                                this.src = 'uploads/activity/no-image.png';
                            };
                        } else {
                            imgElement.style.display = 'none';
                        }
                        
                        // แสดง Modal
                        const editModal = new bootstrap.Modal(document.getElementById('editActivityModal'));
                        editModal.show();
                    } else {
                        alert('ไม่พบข้อมูลกิจกรรม');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('เกิดข้อผิดพลาดในการโหลดข้อมูล');
                });
        }
        
        // ฟังก์ชันยืนยันการลบ
        function confirmDelete(event) {
            if (!confirm('⚠️ คุณแน่ใจหรือไม่ว่าต้องการลบกิจกรรมนี้?\nข้อมูลที่เกี่ยวข้องทั้งหมดจะถูกลบออก')) {
                event.preventDefault();
                return false;
            }
            return true;
        }
        
        // อัพเดทปีใน footer
        document.addEventListener('DOMContentLoaded', function() {
            const yearSpan = document.getElementById('currentYear');
            if (yearSpan) {
                yearSpan.textContent = new Date().getFullYear();
            }
        });
    </script>
</body>
</html>