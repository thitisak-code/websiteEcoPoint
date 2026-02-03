<?php
include_once 'includes/config.php';

$allow_roles = ['Super admin'];

if (
    !isset($_SESSION['username'], $_SESSION['role']) ||
    !in_array($_SESSION['role'], $allow_roles)
) {
    echo "<script>window.location='index.php?page=home';</script>";
    exit();
}

// Process approval
if(isset($_GET['approved'])){
    $req_id = $_GET['approved'];
    $page = $_GET['page'];

    $stmt1 = $conn->prepare("UPDATE request SET req_status = 'approved' WHERE req_id = ?");
    $stmt1->bind_param("i", $req_id);
    if($stmt1->execute()){
        echo "
        <script>
        window.location='index.php?page={$page}';
        </script>
        ";
    }
}
?>

<!-- ********************************************* -->
<!-- *****************content********************* -->
<!-- ********************************************* -->

<div class="container mt-4">

    <!-- Header -->
    <div class="card shadow-sm mb-4">
        <div class="card-body bg-success text-center text-white rounded">
            <h4 class="mb-0">
                <i class="bi bi-gift-fill me-2"></i>
                รายการคำขอแลกของรางวัล
            </h4>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-center">
                <input type="hidden" name="page" value="request">

                <div class="col-md-4">
                    <select name="search" class="form-select">
                        <option value="">📋 รายการทั้งหมด</option>
                        <option value="approved" <?php echo (!empty($_GET['search']) && $_GET['search'] == 'approved') ? 'selected' : ''; ?>>✅ อนุมัติแล้ว</option>
                        <option value="panding" <?php echo (!empty($_GET['search']) && $_GET['search'] == 'panding') ? 'selected' : ''; ?>>⏳ รอการอนุมัติ</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <button type="submit" class="btn btn-warning w-100">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>ชื่อผู้ใช้</th>
                        <th>รายการสินค้า</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if(!empty($_GET['search'])){
                    $key = "%".$_GET['search']."%";
                    $stmt = $conn->prepare("SELECT users.uid, users.firstname, users.lastname, rewards.rw_name,
                    request.req_status, request.req_id
                    FROM request 
                    INNER JOIN users ON users.uid = request.uid
                    LEFT JOIN rewards ON rewards.rw_id = request.rw_id
                    WHERE request.req_status LIKE ?
                    ORDER BY request.req_date DESC");
                    $stmt->bind_param("s", $key);
                }else{
                    $stmt = $conn->prepare("SELECT users.uid, users.firstname, users.lastname, rewards.rw_name,
                    request.req_status, request.req_id
                    FROM request 
                    INNER JOIN users ON users.uid = request.uid
                    LEFT JOIN rewards ON rewards.rw_id = request.rw_id
                    ORDER BY request.req_date DESC");
                }

                $stmt->execute();
                $result = $stmt->get_result();
                if($result->num_rows > 0){
                    while($data = $result->fetch_assoc()){
                ?>
                    <tr>
                        <td><?php echo $data['uid'];?></td>
                        <td><?php echo $data['firstname']. " ". $data['lastname'];?></td>
                        <td><?php echo $data['rw_name'];?></td>
                        <td>
                            <?php if($data['req_status'] == 'approved'): ?>
                                <span class="badge bg-success">✅ อนุมัติแล้ว</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">⏳ รอการอนุมัติ</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($data['req_status'] == "panding"): ?>
                                <a href="?approved=<?php echo $data['req_id'];?>&page=request"
                                onclick="return confirm('ยืนยันการอนุมัติรายการนี้?')" 
                                class="btn btn-warning btn-sm">
                                    👆 อนุมัติ
                                </a>
                            <?php else: ?>
                                <span class="text-success">✅ อนุมัติแล้ว</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php
                    }
                } else {
                ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">
                            <i class="bi bi-inbox fs-1 text-muted"></i>
                            <p class="mt-2 mb-0">ไม่พบข้อมูลคำขอแลกของรางวัล</p>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>