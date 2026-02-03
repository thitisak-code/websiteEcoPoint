<?php
include 'includes/config.php';

$allow_roles = ['Super admin'];

if (
    !isset($_SESSION['username'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], $allow_roles)
) {
    echo "<script>window.location='index.php?page=home';</script>";
    exit();
}

$search = $_GET['search'] ?? '';
// เพิ่มการเลือกรูปภาพ (image) มาด้วยใน SQL
$sql = "SELECT * FROM news 
        WHERE title LIKE '%$search%' 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<head>
    <title>จัดการข่าวสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ปรับ CSS เพิ่มเติมเพื่อให้ Card สูงเท่ากันและรูปไม่เพี้ยน */
        * {
           font-family: 'Noto Sans Thai', sans-serif;
        }

        .card-img-top {
            height: 120px;
            object-fit: cover;
        }
        .news-card {
            transition: transform 0.2s;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
    </style>
</head>

<div class="container-fluid mt-4"> <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>จัดการข่าวสาร (Grid View - แก้ไขรูปได้)</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
            + เพิ่มข่าว
        </button>
    </div>

    <form class="mb-4">
        <input type="hidden" name="page" value="manage_news">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" class="form-control"
                   placeholder="ค้นหาข่าว..." value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary" type="submit">ค้นหา</button>
        </div>
    </form>

    <div class="row g-3"> <?php while($row = $result->fetch_assoc()): ?>
            
            <div class="col-6 col-md-4 col-lg-2">
                <div class="card h-100 news-card">
                    
                    <?php if(!empty($row['image']) && file_exists("uploads/news/".$row['image'])): ?>
                        <img src="uploads/news/<?= $row['image'] ?>" class="card-img-top" alt="News Image">
                    <?php else: ?>
                        <div class="bg-secondary text-white d-flex align-items-center justify-content-center" style="height: 120px;">
                            <small>ไม่มีรูปภาพ</small>
                        </div>
                    <?php endif; ?>

                    <div class="card-body p-2 d-flex flex-column">
                        <h6 class="card-title text-truncate" title="<?= $row['title'] ?>"><?= $row['title'] ?></h6>
                        
                        <div class="small text-muted mb-2">
                            <i class="bi bi-person"></i> <?= $row['author'] ?> <br>
                            <span class="badge <?= $row['status']=='publish'?'text-bg-success':'text-bg-secondary' ?>">
                                <?= $row['status'] ?>
                            </span>
                            <span style="font-size: 0.8rem;"><?= date('d/m/y', strtotime($row['created_at'])) ?></span>
                        </div>

                        <div class="mt-auto d-flex gap-1 justify-content-center">
                            <button class="btn btn-warning btn-sm flex-fill"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal<?= $row['news_id'] ?>">
                                แก้ไข
                            </button>
                            <a href="?page=manage_news&delete=<?= $row['news_id'] ?>"
                               class="btn btn-danger btn-sm flex-fill"
                               onclick="return confirm('ยืนยันลบข่าว?')">
                                ลบ
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade" id="editModal<?= $row['news_id'] ?>">
                <div class="modal-dialog modal-lg">
                    <form method="post" action="" class="modal-content" enctype="multipart/form-data">
                        <div class="modal-header">
                            <h5>แก้ไขข่าว (#<?= $row['news_id'] ?>)</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="news_id" value="<?= $row['news_id'] ?>">

                            <div class="mb-3">
                                <label>หัวข้อข่าว</label>
                                <input type="text" name="title" class="form-control" value="<?= $row['title'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>เนื้อหา</label>
                                <textarea name="content" class="form-control" rows="4"><?= $row['content'] ?></textarea>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label>ผู้โพสต์</label>
                                    <input type="text" name="author" class="form-control" value="<?= $row['author'] ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label>สถานะ</label>
                                    <select name="status" class="form-select">
                                        <option value="publish" <?= $row['status']=='publish'?'selected':'' ?>>เผยแพร่</option>
                                        <option value="draft" <?= $row['status']=='draft'?'selected':'' ?>>ร่าง</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label>รูปข่าวปัจจุบัน</label>
                                <?php if(!empty($row['image']) && file_exists("uploads/news/".$row['image'])): ?>
                                    <img src="uploads/news/<?= $row['image'] ?>" alt="Current Image" style="max-height: 100px; display: block;" class="mb-2">
                                <?php else: ?>
                                    <p>ไม่มีรูปภาพ</p>
                                <?php endif; ?>
                                <label>เปลี่ยนรูปภาพ (ถ้าต้องการ)</label>
                                <input type="file" name="image" class="form-control">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-primary" type="submit" name="update_news">บันทึกการแก้ไข</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endwhile; ?>
    </div>
</div>

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <form method="post" class="modal-content" enctype="multipart/form-data">
            <div class="modal-header">
                <h5>เพิ่มข่าวใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>หัวข้อข่าว</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>เนื้อหา</label>
                    <textarea name="content" class="form-control" rows="4"></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>ผู้โพสต์</label>
                        <input type="text" name="author" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>สถานะ</label>
                        <select name="status" class="form-select">
                            <option value="publish">เผยแพร่</option>
                            <option value="draft">ร่าง</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label>รูปข่าว</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit" name="add_news">บันทึก</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<?php
//************Add-news*******************/
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_news'])) {
    $title   = $_POST['title'];
    $content = $_POST['content'];
    $author  = $_POST['author'];
    $status  = $_POST['status'];

    $dir = "uploads/news/";
    // สร้างโฟลเดอร์ถ้ายังไม่มี
    if (!file_exists($dir)) { mkdir($dir, 0777, true); }
    
    $image = "";
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        $move  = $dir . $image;
        move_uploaded_file($_FILES['image']['tmp_name'], $move);
    }

    $stmt = $conn->prepare("INSERT INTO news (title, content, author, status, image) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $title, $content, $author, $status, $image);

    if ($stmt->execute()) {
        echo "<script>alert('เพิ่มข่าวสำเร็จ ✅'); window.location='?page=manage_news';</script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด ❌');</script>";
    }
    $stmt->close();
}

//************Update-news (ส่วนที่เพิ่มใหม่สำหรับแก้ไขรูปภาพ)*******************/
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['update_news'])) {

    $news_id = $_POST['news_id'];
    $title   = $_POST['title'];
    $content = $_POST['content'];
    $author  = $_POST['author'];
    $status  = $_POST['status'];

    // โฟลเดอร์เก็บรูป
    $dir = "uploads/news/";
    
    // 1. ดึงข้อมูลรูปเดิมจากฐานข้อมูลเพื่อใช้จัดการไฟล์
    $sql_old_img = "SELECT image FROM news WHERE news_id = '$news_id'";
    $res_old_img = $conn->query($sql_old_img);
    $row_old_img = $res_old_img->fetch_assoc();
    $old_image = $row_old_img['image'];
    $image_to_db = $old_image; // เริ่มต้นโดยตั้งสมมติฐานว่าใช้รูปเดิม

    // 2. ตรวจสอบว่ามีการอัปโหลดรูปใหม่มาหรือไม่
    if (!empty($_FILES['image']['name'])) {
        // มีการอัปโหลดรูปใหม่ -> จัดการไฟล์
        $new_image_name = time() . "_" . basename($_FILES['image']['name']); // สร้างชื่อไฟล์ใหม่กันซ้ำ
        $move_path = $dir . $new_image_name;

        // ย้ายไฟล์ที่อัปโหลดไปยังโฟลเดอร์เป้าหมาย
        if(move_uploaded_file($_FILES['image']['tmp_name'], $move_path)) {
            $image_to_db = $new_image_name; // เตรียมชื่อไฟล์ใหม่เพื่อบันทึกลง DB

            // ลบรูปเก่าออกจากเซิร์ฟเวอร์ถ้ามีอยู่จริง เพื่อประหยัดพื้นที่
            if(!empty($old_image) && file_exists($dir . $old_image)){
                unlink($dir . $old_image);
            }
        } else {
            echo "<script>alert('ไม่สามารถอัปโหลดรูปภาพใหม่ได้ มีข้อผิดพลาดเกิดขึ้น ❌');</script>";
            // คุณอาจจะเลือกหยุดการทำงานหรือดำเนินต่อโดยใช้รูปเดิมก็ได้ ขึ้นอยู่กับการออกแบบ
        }
    }

    // 3. อัปเดตข้อมูลลงฐานข้อมูลโดยใช้ prepared statement เพื่อความปลอดภัย
    $stmt = $conn->prepare(
        "UPDATE news SET title=?, content=?, author=?, status=?, image=? WHERE news_id=?"
    );
    // binding พารามิเตอร์ (s=string, i=integer) -> title(s), content(s), author(s), status(s), image(s), news_id(i)
    $stmt->bind_param("sssssi", $title, $content, $author, $status, $image_to_db, $news_id);

    if ($stmt->execute()) {
        echo "<script>
            alert('แก้ไขข่าวสำเร็จเรียบร้อย ✅');
            window.location='?page=manage_news';
        </script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาดในการอัปเดตข้อมูล ❌');</script>";
    }
    $stmt->close(); // ปิด statement
}

//****************delete************************/
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    
    // ควรลบรูปภาพออกจากเซิร์ฟเวอร์ด้วยเมื่อลบข้อมูล (แนะนำ)
    $check_img = $conn->query("SELECT image FROM news WHERE news_id = '$id'");
    if($check_img->num_rows > 0){
        $img_row = $check_img->fetch_assoc();
        if(!empty($img_row['image']) && file_exists("uploads/news/".$img_row['image'])){ 
            unlink("uploads/news/".$img_row['image']); // ลบไฟล์รูป
        }
    }

    $sql = "DELETE FROM news WHERE news_id = '$id'";
    if($conn->query($sql)){
        echo "<script>window.location='?page=manage_news';</script>";
    }
}
?>