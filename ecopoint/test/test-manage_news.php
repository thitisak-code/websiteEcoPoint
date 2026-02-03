<?php
include 'includes/config.php';

$search = $_GET['search'] ?? '';
$sql = "SELECT * FROM news 
        WHERE title LIKE '%$search%' 
        ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

    <head>
    <title>จัดการข่าวสาร</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

<div class="container mt-5">
    <div class="d-flex justify-content-between mb-3">
        <h3>จัดการข่าวสาร</h3>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addModal">
            + เพิ่มข่าว
        </button>
    </div>

    <!-- ค้นหา -->
    <form class="mb-3">
        <input type="hidden" name="page" value="manage_news">
        <input type="text" name="search" class="form-control"
               placeholder="ค้นหาข่าว..." value="<?= htmlspecialchars($search) ?>">
    </form>

    <!-- ตาราง -->
    <table class="table table-bordered table-hover">
        <thead class="table-success">
        <tr>
            <th>#</th>
            <th>หัวข้อ</th>
            <th>ผู้โพสต์</th>
            <th>สถานะ</th>
            <th>วันที่</th>
            <th width="180">จัดการ</th>
        </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['news_id'] ?></td>
                <td><?= $row['title'] ?></td>
                <td><?= $row['author'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editModal<?= $row['news_id'] ?>">
                        แก้ไข
                    </button>

                    <a href="?page=manage_news&delete=<?= $row['news_id'] ?>"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('ยืนยันลบข่าว?')">
                        ลบ
                    </a>
                </td>
            </tr>

            <!-- Modal แก้ไข -->
            <div class="modal fade" id="editModal<?= $row['news_id'] ?>">
                <div class="modal-dialog modal-lg">
                    <form method="post" action="news_update.php" class="modal-content">
                        <div class="modal-header">
                            <h5>แก้ไขข่าว</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            <input type="hidden" name="news_id" value="<?= $row['news_id'] ?>">

                            <div class="mb-3">
                                <label>หัวข้อข่าว</label>
                                <input type="text" name="title" class="form-control"
                                       value="<?= $row['title'] ?>" required>
                            </div>

                            <div class="mb-3">
                                <label>เนื้อหา</label>
                                <textarea name="content" class="form-control" rows="4"><?= $row['content'] ?></textarea>
                            </div>

                            <div class="mb-3">
                                <label>ผู้โพสต์</label>
                                <input type="text" name="author" class="form-control"
                                       value="<?= $row['author'] ?>">
                            </div>

                            <div class="mb-3">
                                <label>สถานะ</label>
                                <select name="status" class="form-select">
                                    <option value="publish" <?= $row['status']=='publish'?'selected':'' ?>>เผยแพร่</option>
                                    <option value="draft" <?= $row['status']=='draft'?'selected':'' ?>>ร่าง</option>
                                </select>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary">บันทึก</button>
                        </div>
                    </form>
                </div>
            </div>

        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal เพิ่ม -->
<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-lg">
        <form method="post"
              class="modal-content"
              enctype="multipart/form-data">

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

                <div class="mb-3">
                    <label>ผู้โพสต์</label>
                    <input type="text" name="author" class="form-control">
                </div>

                <div class="mb-3">
                    <label>สถานะ</label>
                    <select name="status" class="form-select">
                        <option value="publish">เผยแพร่</option>
                        <option value="draft">ร่าง</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>รูปข่าว</label>
                    <input type="file" name="image" class="form-control">
                </div>

            </div>

            <div class="modal-footer">
                <button class="btn btn-success" type="submit" name="add_news">
                    บันทึก
                </button>
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

    // โฟลเดอร์เก็บรูป
    $dir = "uploads/news/";
    $image = "";

    // ถ้ามีการอัปโหลดรูป
    if (!empty($_FILES['image']['name'])) {

        // ป้องกันชื่อซ้ำ
        $image = time() . "_" . basename($_FILES['image']['name']);
        $move  = $dir . $image;

        move_uploaded_file($_FILES['image']['tmp_name'], $move);
    }

    $stmt = $conn->prepare(
        "INSERT INTO news (title, content, author, status, image)
         VALUES (?, ?, ?, ?, ?)"
    );
    $stmt->bind_param("sssss", $title, $content, $author, $status, $image);

    if ($stmt->execute()) {
        echo "<script>
            alert('เพิ่มข่าวสำเร็จ ✅');
            window.location='?page=manage_news';
        </script>";
    } else {
        echo "<script>alert('เกิดข้อผิดพลาด ❌');</script>";
    }
}

//****************delete************************/
if(isset($_GET['delete'])){
    $id = $_GET['delete'];

    $sql = "DELETE FROM news WHERE news_id = '$id'";
    if($conn->query($sql)){
        echo "<script>window.location='?page=manage_news';</script>";
    }
}
?>
