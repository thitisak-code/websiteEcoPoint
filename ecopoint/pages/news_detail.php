<?php
include_once 'includes/config.php';
if (!isset($_SESSION['username'])) {
    echo '
    <!DOCTYPE html>
    <html lang="th">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>กรุณาเข้าสู่ระบบ - Eco Point</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@300;400;600&display=swap" rel="stylesheet">
        <style>
            body {
                font-family: "Sarabun", sans-serif;
            }
            .auth-container {
                min-height: 100vh;
                background: linear-gradient(45deg, #134e5e, #71b280); /* โทนเขียวธรรมชาติ */
                display: flex;
                align-items: center;
                overflow: hidden;
            }

            /* เอฟเฟกต์กระจกฝ้า Glassmorphism */
            .auth-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(10px);
                border-radius: 24px;
                box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                border: 1px solid rgba(255, 255, 255, 0.3);
                transform: translateY(20px);
                opacity: 0;
                animation: slideUp 0.6s ease forwards;
            }

            @keyframes slideUp {
                to { transform: translateY(0); opacity: 1; }
            }

            .auth-icon {
                width: 90px;
                height: 90px;
                background: #198754;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                margin: -45px auto 20px; /* ย้ายไอคอนขึ้นไปกึ่งกลางขอบบน */
                box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
                color: white;
                font-size: 40px;
            }

            .btn-success {
                padding: 12px;
                border-radius: 12px;
                font-weight: 600;
                transition: all 0.3s;
            }

            .btn-success:hover {
                transform: translateY(-2px);
                box-shadow: 0 5px 15px rgba(25, 135, 84, 0.4);
            }

            /* แถบโหลดถอยหลัง */
            .redirect-progress {
                height: 4px;
                width: 100%;
                background: #eee;
                border-radius: 10px;
                margin-top: 20px;
                overflow: hidden;
            }
            .progress-bar-fill {
                height: 100%;
                background: #198754;
                width: 100%;
                animation: countdown 3s linear forwards;
            }

            @keyframes countdown {
                from { width: 100%; }
                to { width: 0%; }
            }
        </style>
    </head>
    <body>
        <div class="auth-container">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="card auth-card">
                            <div class="card-body p-4 p-md-5 text-center">
                                <div class="auth-icon">
                                    <i class="fas fa-shield-halved"></i>
                                </div>
                                <h3 class="fw-bold mb-2">เข้าถึงจำกัด</h3>
                                <p class="text-muted mb-4">
                                    กรุณาเข้าสู่ระบบเพื่อดำเนินการต่อ<br>
                                    <small>ระบบกำลังนำท่านไปหน้าล็อกอิน...</small>
                                </p>
                                
                                <div class="d-grid gap-2">
                                    <a href="index.php?page=login" class="btn btn-success">
                                        <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบทันที
                                    </a>
                                    <a href="index.php?page=home" class="btn btn-link text-decoration-none text-muted mt-2">
                                        <i class="fas fa-arrow-left me-1"></i> กลับหน้าหลัก
                                    </a>
                                </div>

                                <div class="redirect-progress">
                                    <div class="progress-bar-fill"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            setTimeout(() => {
                window.location.href = "index.php?page=login";
            }, 6000);
        </script>
    </body>
    </html>
    ';
    exit();
}

$news_id = $_GET['id'];

$sql_news = $conn->query("SELECT * FROM news WHERE news_id = '$news_id'");
$news = $sql_news->fetch_assoc();

if(isset($_SESSION['username'])){
    $conn->query("UPDATE news SET views = views + 1 WHERE news_id = '$news_id'");
}

$sum = $conn->query("SELECT COUNT(*) AS total_comment FROM comment WHERE news_id = '$news_id'");
$s1 = $sum->fetch_assoc();
$total_comment = $s1['total_comment'];

// ***********Edit************
$edit_comment = "";

if(isset($_GET['edit'])){
    $cm_id = $_GET['edit'];
    $news_id = $_GET['id'];

    $stmt1 = $conn->prepare("SELECT * FROM comment WHERE cm_id = ?");
    $stmt1->bind_param("i", $cm_id);
    $stmt1->execute();
    $edit_comment = $stmt1->get_result()->fetch_assoc();
}
?>


<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดข่าว (2 Columns)</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<style>
    .dropdown-menu a:hover {
    background-color: #cfcfcfff;
}
/* กล่องคอมเมนต์ */
.comment-box {
    max-width: 100%;
    overflow: hidden;
}

/* ตัวข้อความคอมเมนต์ */
.comment-text {
    word-wrap: break-word;
    overflow-wrap: break-word;
    white-space: pre-wrap; /* รองรับ enter */
}

</style>
<div class="container my-5">

    <div class="row">

        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm h-100">
                <img src="uploads/news/<?php echo $news['image'];?>"
                     class="card-img-top" alt="ข่าวสิ่งแวดล้อม"
                     style="max-height: 400px; object-fit: cover;"> <div class="card-body">
                    <span class="badge bg-success mb-2">ข่าวสาร</span>

                    <h3 class="card-title">
                        <?php echo $news['title'];?>
                    </h3>

                    <div class="text-muted mb-3">
                        <i class="fas fa-user me-1"></i><?php echo $news['author'];?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-calendar me-1"></i><?php echo $news['updated_at'];?>
                        <span class="mx-2">|</span>
                        <i class="fas fa-eye me-1"></i><?php echo $news['views'];?> ครั้ง
                    </div>

                    <p class="card-text">
                        <?php echo $news['content'];?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-lg-4">

            <!-- form-comment & Edit -->
            <?php if($edit_comment == ""){?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-comments me-2"></i>แสดงความคิดเห็น
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">ความคิดเห็น</label>
                            <textarea class="form-control" rows="4" placeholder="พิมพ์ความคิดเห็น..." name="comment" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-success w-100" name="add-comment">
                            ส่งความคิดเห็น
                        </button>
                    </form>
                </div>
            </div>
            <?php }else{ ?>
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-comments me-2"></i>แก้ไขความคิดเห็น
                </div>
                <div class="card-body">
                    <form method="post">
                        <input type="hidden" name="cm_id" value="<?php echo $edit_comment['cm_id'];?>">
                        <input type="hidden" name="news_id" value="<?php echo $news_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">ความคิดเห็น</label>
                            <textarea class="form-control" rows="4" placeholder="พิมพ์ความคิดเห็น..." name="comment" required><?php echo $edit_comment['comment'];?></textarea>
                        </div>

                        <button type="submit" class="btn btn-warning w-100" name="edit-comment">
                            แก้ไขความคิดเห็น
                        </button>
                    </form>
                </div>
            </div>
            <?php } ?>

            <div class="card shadow-sm">
                <div class="card-header">
                    <i class="fas fa-comment-dots me-2"></i>
                    ความคิดเห็นทั้งหมด (<?php echo $total_comment;?>)
                </div>
                <?php 
                $cm = $conn->prepare("SELECT users.uid, users.firstname, users.lastname, comment.comment, users.image,
                comment.updated_at, comment.cm_id
                FROM comment 
                INNER JOIN users ON comment.uid = users.uid 
                WHERE news_id = ?
                ORDER BY comment.created_at DESC");
                $cm->bind_param("i" , $news_id);
                $cm->execute();
                $res1 = $cm->get_result();
                if($res1->num_rows > 0 ){
                    while($row = $res1->fetch_assoc()){?>
                <div class="card-body">
                    <div class="d-flex mb-3 border-bottom pb-3 position-relative">
                        <!-- ปุ่มแก้ไข / ลบ -->
                        <?php
                        $isOwner = $_SESSION['uid'] == $row['uid'];
                        $isMember = $_SESSION['role'] === 'member';
                        $isSuperAdmin = $_SESSION['role'] === 'Super admin';

                        if ($isOwner || $isSuperAdmin) {
                        ?>
                        <div class="position-absolute top-0 end-0">
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end shadow">

                                    <!-- ปุ่มแก้ไข -->
                                    <?php if ($isOwner || $isSuperAdmin) { ?>
                                    <li>
                                        <a class="dropdown-item text-primary"
                                        href="?page=comment&edit=<?php echo $row['cm_id']; ?>&id=<?php echo $news_id; ?>">
                                            <i class="bi bi-pencil-square me-2"></i>แก้ไข
                                        </a>
                                    </li>
                                    <?php } ?>

                                    <!-- ปุ่มลบ (เฉพาะ Super admin เท่านั้น) -->
                                    <?php if ($isOwner || $isSuperAdmin) { ?>
                                    <li>
                                        <a class="dropdown-item text-danger"
                                        href="?page=comment&delete=<?php echo $row['cm_id']; ?>&id=<?php echo $news_id; ?>"
                                        onclick="return confirm('คุณต้องการลบความคิดเห็นนี้หรือไม่?')">
                                            <i class="bi bi-trash me-2"></i>ลบ
                                        </a>
                                    </li>
                                    <?php } ?>

                                </ul>
                            </div>
                        </div>
                        <?php } ?>

                        <!-- รูปโปรไฟล์ -->
                        <div class="flex-shrink-0">
                            <img src="uploads/users/<?php echo $row['image'];?>"
                                class="rounded-circle"
                                style="width:40px;height:40px;">
                        </div>

                        <!-- เนื้อหา -->
                        <div class="ms-3">
                            <h6 class="mb-1">
                                <?php echo $row['firstname']." ".$row['lastname'];?>
                            </h6>

                            <p class="mb-1 text-muted small">
                                <?php echo $row['comment'];?>
                            </p>

                            <small class="text-muted" style="font-size: 0.75rem;">
                                <i class="bi bi-clock me-1"></i>
                                <?php echo date("d M Y H:i", strtotime($row['updated_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
                <?php }
                }else{?>
                     <div class="text-center mt-4 text-secondary">
                            <h2>ยังไม่มีความคิดเห็น</h2>
                        </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// ***************************Add Comment *************************
if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['add-comment'])) {

    $page    = $_POST['news_id'];
    $comment = $_POST['comment'];
    $uid     = $_SESSION['uid'];

    $stmt = $conn->prepare(
        "INSERT INTO `comment` (news_id, uid, comment, updated_at)
         VALUES (?, ?, ?, NOW())"
    );

    $stmt->bind_param("iis", $page, $uid, $comment);

    if ($stmt->execute()) {
       echo "
       <script>
       window.location='?page=comment&id={$page}';
       </script>
       ";
        exit;
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
}

// ******************Delete**********************
if(isset($_GET['delete'])){
    $news_id = $_GET['id'];
    $cm_id = $_GET['delete'];

    $stmt = $conn->prepare("DELETE FROM comment WHERE cm_id = ?");
    $stmt->bind_param("i", $cm_id);
    if($stmt->execute()){
        echo "<script>window.location='?page=comment&id={$news_id}';</script>";
        exit();
    }
}

// **************************Edit**********************
if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['edit-comment'])){
    $cm_id = $_POST['cm_id'];
    $news_id = $_POST['news_id'];
    $comment = $_POST['comment'];

    $stmt = $conn->prepare("UPDATE comment SET comment = ?, updated_at = now() WHERE cm_id = ?");
    $stmt->bind_param("si",$comment, $cm_id);
    if($stmt->execute()){
        echo "
        <script>
        alert('แก้ใข้ข้อมูลสำเร็จ✅');
        window.location='?page=comment&id={$news_id}';
        </script>
        ";
        exit();
    }
} 
?>
