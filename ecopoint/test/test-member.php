<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
<?php
$sum = $conn->query("SELECT COUNT(*) AS sum_member FROM users");
$ss = $sum->fetch_assoc();
$total_member = $ss['sum_member'];
?>
<div class="container my-4">

    <!-- การ์ดสรุป -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-people fs-1 text-success"></i>
                    <h5 class="mt-2">จำนวนผู้ใช้ในระบบ</h5>
                    <h2 class="fw-bold text-success"><?php echo $total_member;?></h2>
                </div>
            </div>
        </div>
    </div>

    <!-- ค้นหาผู้ใช้ -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form method="post" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="col-form-label fw-semibold">
                        <i class="bi bi-search"></i> ค้นหาผู้ใช้
                    </label>
                </div>
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control"
                        placeholder="ค้นหาจากชื่อ / username">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-search"></i> ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ตารางข้อมูล -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">
                <i class="bi bi-table"></i> รายการผู้ใช้
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-dark">
                        <tr>
                            <th><i class="bi bi-key"></i> ID</th>
                            <th><i class="bi bi-person"></i> Firstname</th>
                            <th>Lastname</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Management</th>
                        </tr>
                    </thead>
                    <?php
                    if(!empty($_POST['search'])){
                        $key = "%". $_POST['search']."%";
                        $stmt = $conn->prepare("SELECT * FROM users
                        WHERE uid LIKE ? OR
                        firstname LIKE ? OR
                        lastname LIKE ? OR
                        username LIKE ? OR
                        email LIKE ?");
                        $stmt->bind_param("sssss", $key, $key, $key, $key, $key);
                    }else{
                        $stmt=$conn->prepare("SELECT * FROM users");
                    }
                    $stmt->execute();
                    $result = $stmt->get_result();
                    if($result->num_rows > 0){
                        while($data=$result->fetch_assoc()){
                    ?>
                    <tbody>
                        <tr>
                            <td><?php echo $data['uid'];?></td>
                            <td><?php echo $data['firstname'];?></td>
                            <td><?php echo $data['lastname'];?></td>
                            <td><?php echo $data['username'];?></td>
                            <td><?php echo $data['email'];?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo $data['u_role'];?></span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <?php }
                    }
                    ?>
                </table>
            </div>
        </div>
    </div>

</div>
