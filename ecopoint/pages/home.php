<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eco Point - ศูนย์รวมสิ่งแวดล้อม</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style/home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.5.0/font/bootstrap-icons.min.css">
</head>
<style>
    *{
         font-family: 'Noto Sans Thai', sans-serif;
    }
    .reward-card img {
        height: 180px;
        object-fit: cover;
    }
    .reward-card:hover {
        transform: translateY(-4px);
        transition: 0.3s;
    }

</style>
<body>
    <!-- Header -->
    <header class="eco-header">
        <div class="container">
            <div class="eco-logo">
                <i class="fas fa-leaf"></i>
                Eco Point
            </div>
            <p class="text-light text-center">
                ศูนย์รวมข่าวสารและกิจกรรมเพื่อสิ่งแวดล้อม ร่วมเป็นส่วนหนึ่งในการรักษ์โลกไปกับเรา
            </p>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container">
        <!-- Carousel -->
        <div class="carousel-container">
            <div id="ecoCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                        <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="กิจกรรมปลูกป่า">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>กิจกรรมปลูกป่าชายเลน</h5>
                            <p>ร่วมกันฟื้นฟูระบบนิเวศชายฝั่ง วันที่ 25 มิถุนายน 2566</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1582719508461-905c673771fd?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="รีไซเคิลขยะ">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>โครงการรีไซเคิลเพื่อชุมชน</h5>
                            <p>นำขยะรีไซเคิลมาแลกของรางวัลได้ทุกวันเสาร์</p>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="https://images.unsplash.com/photo-1550147760-44c9966d6bc7?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1170&q=80" class="d-block w-100" alt="พลังงานสะอาด">
                        <div class="carousel-caption d-none d-md-block">
                            <h5>พลังงานสะอาดเพื่ออนาคต</h5>
                            <p>เรียนรู้การติดตั้งโซลาร์เซลล์ใช้เองในครัวเรือน</p>
                        </div>
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#ecoCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#ecoCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
        
        <!-- Features -->
        <div class="eco-features">
            <div class="row text-center">
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-recycle"></i>
                    </div>
                    <h4>รีไซเคิลขยะ</h4>
                    <p>เรียนรู้วิธีการแยกขยะและรีไซเคิลอย่างถูกวิธี</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-tree"></i>
                    </div>
                    <h4>ปลูกป่าฟื้นฟู</h4>
                    <p>ร่วมกิจกรรมปลูกต้นไม้ฟื้นฟูระบบนิเวศ</p>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-icon">
                        <i class="fas fa-sun"></i>
                    </div>
                    <h4>พลังงานสะอาด</h4>
                    <p>ส่งเสริมการใช้พลังงานทดแทนในชุมชน</p>
                </div>
            </div>
        </div>
        
        <!-- News Section -->
        <div class="card shadow-sm mb-5">
            <div class="card-header bg-success">
                <h3 class="text-center text-light mb-0">
                    <i class="fas fa-newspaper me-2"></i>ข่าวสารสิ่งแวดล้อม
                </h3>
            </div>

            <div class="card-body p-4">
                <h4 class="section-title mb-4">ข่าวสารล่าสุด</h4>
                <div class="row">
                    <!-- News Item -->
                    <?php
                    $news = $conn->query("SELECT * FROM news WHERE status = 'publish' ORDER BY views DESC LIMIT 6 ");
                    while($nws = $news->fetch_assoc()){?>
                    <div class="col-md-4 mb-4">
                        <div class="card news-card h-100 shadow-sm">

                            <!-- รูปข่าว -->
                            <img src="uploads/news/<?php echo $nws['image'];?>"
                                class="card-img-top"
                                alt="ข่าวสิ่งแวดล้อม">

                            <div class="card-body d-flex flex-column">

                                <!-- หมวดข่าว -->
                                <span class="badge bg-success mb-2">สิ่งแวดล้อม</span>

                                <!-- หัวข้อ -->
                                <h5 class="card-title">
                                    <?php echo $nws['title'];?>
                                </h5>

                                <!-- เนื้อหาแบบย่อ -->
                                <p class="card-text text-muted">
                                    <?php echo $nws['content'];?>
                                </p>

                                <!-- ข้อมูลเพิ่มเติม -->
                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between text-muted small mb-2">
                                        <span>
                                            <i class="fas fa-user me-1"></i><?php echo $nws['author'];?>
                                        </span>
                                        <span>
                                            <i class="fas fa-eye me-1"></i><?php echo $nws['views'];?>
                                        </span>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?php echo $nws['created_at'];?>
                                        </small>

                                        <a href="?page=comment&id=<?php echo $nws['news_id'];?>"
                                        class="btn btn-success btn-sm">
                                            อ่านเพิ่มเติม
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php } ?>
                    <!-- End News Item -->
                     <a href="index.php?page=news" class="text-end"><i class="bi bi-book mx-2"></i>  ข่าวสารทั้งหมด </a>
                </div>
            </div>
        </div>

        <!-- Rewards Selection -->
        <div class="card shadow-sm">
            <div class="card-header card-header-custom">
                <h3 class="text-center text-light mb-0">
                    <i class="bi bi-gift-fill me-2"></i>ของรางวัล
                </h3>
            </div>
        <div class="card-body p-4">
            <h4 class="section-title">รายการของรางวัลในเว็บไซต์</h4>
            <div class="row">
                <!-- PHP -->
                 <?php 
                 $rewards = $conn->query("SELECT * FROM rewards LIMIT 3");
                 while($rew = $rewards->fetch_assoc()){?>
                <div class="col-md-4 mb-4">
                        <div class="card reward-card shadow-sm h-100">
                            <img src="uploads/rewards/<?php echo $rew['rw_image'];?>"
                                class="card-img-top"
                                alt="<?php echo $rew['rw_name'];?>">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $rew['rw_name'];?></h5>

                                <div class="mb-2">
                                    <span class="badge bg-primary me-2">
                                        <i class="bi bi-star-fill me-1"></i> <?php echo $rew['rw_price'];?> คะแนน
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="bi bi-box-seam me-1"></i> คงเหลือ <?php echo $rew['rw_stock'];?> ชิ้น
                                    </span>
                                </div>

                                <button class="btn btn-success btn-sm mt-auto"
                                        onclick="window.location.href='index.php?page=reward'">
                                        <i class="bi bi-arrow-repeat me-1"></i> แลกของรางวัล
                                </button>

                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div> 
        </div>
        
        <!-- Activity Selection -->
        <div class="card shadow-sm mt-5">
            <div class="card-header card-header-custom">
                <h3 class="text-center text-light mb-0">
                    <i class="bi bi-gift-fill me-2"></i>กิจกรรม
                </h3>
            </div>

            <div class="card-body p-4">
                <h4 class="section-title">กิจกรรมในเว็บไซต์</h4>
                <div class="row">
                    <?php
                    $activity = $conn->prepare("SELECT * FROM activity");
                    $activity->execute();
                    $res_ac = $activity->get_result();
                    if($res_ac->num_rows > 0){
                        while($act = $res_ac->fetch_assoc()){?>
                    <!-- Card 1 -->
                    <div class="col-md-4 mb-4">
                        <div class="card reward-card shadow-sm h-100">
                            <img src="uploads/activity/<?php echo $act['image'];?>"
                                class="card-img-top"
                                alt="<?php echo $act['title'];?>">

                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo $act['title'];?></h5>

                                <div class="mb-2">
                                    <span class="badge bg-primary me-2">
                                        <i class="bi bi-star-fill me-1"></i> <?php echo $act['point'];?> คะแนน
                                    </span>
                                    <span class="badge bg-success">
                                        <i class="fas fa-people-carry me-1"></i>รับผู้ใช้งานจำนวน <?php echo $act['ac_limit'];?>
                                    </span>
                                </div>
                                <?php if($act['ac_limit'] > 0){?>
                                <button class="btn btn-success btn-sm mt-auto"
                                        onclick="window.location.href='function/event.php?ac_id=<?php echo $act['ac_id'];?>&point=<?php echo $act['point'];?>'">
                                    <i class="bi bi-arrow-repeat me-1"></i> เข้าร่วมกิจกรรม
                                </button>
                                <?php }else{?>
                                <button class="btn btn-danger btn-sm mt-auto">
                                    <i class="bi bi-arrow-repeat me-1"></i> ผู้เข้าร่วมครบแล้ว
                                </button>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <?php } 
                    } ?>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="footer-eco">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4><i class="fas fa-leaf me-2"></i>Eco Point</h4>
                    <p class="text-light">ศูนย์รวมข้อมูลข่าวสารและกิจกรรมเพื่อสิ่งแวดล้อม เรามุ่งมั่นส่งเสริมการมีส่วนร่วมของชุมชนในการรักษ์โลก</p>
                    <div class="social-icons mt-3">
                        <a href="#" class="text-light me-3"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-light me-3"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-light"><i class="fab fa-youtube fa-lg"></i></a>
                        <p class="text-light" style="margin-top: 10px;">#DevBy Mak Thitisak & Poom Aphisit</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>ลิงก์ด่วน</h5>
                    <div class="footer-links">
                        <a href="?page=news"><i class="fas fa-chevron-right me-2"></i>ข่าวสารล่าสุด</a>
                        <a href="?page=home"><i class="fas fa-chevron-right me-2"></i>กิจกรรมใกล้เคียง</a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>ติดต่อเรา</h5>
                    <p class="text-light"><i class="fas fa-map-marker-alt me-2"></i>ที่อยู่ : 579 ถ.นครสวรรค์ ต.ตลาด อ.เมือง จ.มหาสารคาม 44000</p>
                    <p class="text-light"><i class="fas fa-phone me-2"></i>06-057-4758</p>
                    <p class="text-light"><i class="fas fa-envelope me-2"></i>67319100005@mvc.ac.th</p>
                    <p class="text-light"><i class="fas fa-clock me-2"></i>จันทร์ - ศุกร์: 8:00 น. - 17:00 น.</p>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center pt-3">
                <p class="mb-0 text-light">&copy; 2026 Eco Point.</p>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>