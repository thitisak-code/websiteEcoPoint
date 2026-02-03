<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Thai:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
* {
    font-family: 'Noto Sans Thai', sans-serif;
}

.news-card {
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.1);
    border: 1px solid rgba(0,0,0,0.08);
    height: 100%;
}

.news-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1), 0 0 0 1px rgba(40, 167, 69, 0.1);
}

/* รูปข่าว */
.news-image {
    position: relative;
    overflow: hidden;
}

.news-image img {
    height: 220px;
    width: 100%;
    object-fit: cover;
    transition: transform 0.6s ease;
}

.news-card:hover .news-image img {
    transform: scale(1.05);
}

.news-image::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: linear-gradient(to top, rgba(0,0,0,0.2), transparent);
}

/* ตัดเนื้อหา 3 บรรทัด */
.news-content {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.6;
    color: #555;
    font-size: 0.95rem;
}

/* หัวข้อข่าว */
.card-title {
    font-weight: 700;
    line-height: 1.4;
    margin-bottom: 12px;
    font-size: 1.1rem;
    color: #2c3e50;
    transition: color 0.3s ease;
}

.card-title:hover {
    color: #28a745;
}

/* Badge */
.badge-category {
    background: linear-gradient(135deg, #28a745, #20c997);
    border-radius: 20px;
    padding: 6px 14px;
    font-weight: 500;
    font-size: 0.85rem;
    box-shadow: 0 4px 6px rgba(40, 167, 69, 0.2);
    border: none;
}

/* Section Title */
.section-title {
    position: relative;
    padding-bottom: 15px;
    color: #2c3e50;
    font-weight: 700;
    font-size: 1.5rem;
    margin-top: 10px;
}

.section-title::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #28a745, #20c997);
    border-radius: 2px;
}

/* Card Header */
.card-header-custom {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 0 !important;
    padding: 1.2rem 2rem;
    border-bottom: 4px solid rgba(255,255,255,0.2);
}

/* Author & Views */
.author-views {
    font-size: 0.85rem;
    color: #6c757d;
    border-top: 1px solid rgba(0,0,0,0.08);
    padding-top: 12px;
    margin-top: 12px;
}

/* Date & Button Container */
.date-button-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 15px;
}

.date-text {
    color: #28a745;
    font-weight: 500;
    font-size: 0.9rem;
}

.read-more-btn {
    background: linear-gradient(135deg, #28a745, #20c997);
    border: none;
    padding: 8px 20px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    text-decoration: none;
    display: inline-flex;
    align-items: center;
}

.read-more-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
    background: linear-gradient(135deg, #218838, #1ba87e);
    color: white;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px dashed #dee2e6;
}

.empty-state i {
    font-size: 4rem;
    color: #6c757d;
    margin-bottom: 20px;
    opacity: 0.7;
}

/* Popular Badge */
.popular-badge {
    position: absolute;
    top: 15px;
    left: 15px;
    background: linear-gradient(135deg, #dc3545, #fd7e14);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
    z-index: 1;
}

/* Responsive Design */
@media (max-width: 768px) {
    .news-image img {
        height: 180px;
    }
    
    .card-title {
        font-size: 1rem;
    }
    
    .section-title {
        font-size: 1.3rem;
    }
    
    .card-header-custom h3 {
        font-size: 1.3rem;
    }
    
    .date-button-container {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .read-more-btn {
        align-self: flex-end;
        margin-top: 5px;
    }
}

/* Animation for cards */
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

.news-item {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

/* Custom container */
.custom-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}

/* Load more button */
.load-more-btn {
    background: linear-gradient(135deg, #6c757d, #495057);
    border: none;
    padding: 10px 30px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.load-more-btn:hover {
    background: linear-gradient(135deg, #545b62, #3d4348);
    transform: translateY(-2px);
}

/* Stats badge */
.stats-badge {
    background: rgba(255,255,255,0.2);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.3);
    font-size: 0.9rem;
}

/* Search and filter */
.search-filter-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 30px;
    border: 1px solid #e9ecef;
}

/* New badge สำหรับข่าวใหม่ */
.new-badge {
    position: absolute;
    top: 15px;
    right: 15px;
    background: linear-gradient(135deg, #007bff, #6610f2);
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
    z-index: 1;
}

/* View detail page styles */
.view-detail-container {
    max-width: 800px;
    margin: 0 auto;
}

.news-detail-image {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 25px;
}

.news-detail-image img {
    width: 100%;
    max-height: 400px;
    object-fit: cover;
}

.news-meta-info {
    background: #f8f9fa;
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 25px;
}

.news-content-full {
    line-height: 1.8;
    font-size: 1.05rem;
    color: #333;
}

.back-button {
    background: linear-gradient(135deg, #6c757d, #495057);
    border: none;
    padding: 10px 25px;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-button:hover {
    background: linear-gradient(135deg, #545b62, #3d4348);
    transform: translateY(-2px);
}
</style>

<?php
// เรียกใช้ config จาก includes/config.php
require_once 'includes/config.php';

// ตรวจสอบว่ามีการเชื่อมต่อฐานข้อมูลหรือไม่
if (!isset($conn) || !$conn) {
    die("<div class='alert alert-danger m-3'>ไม่สามารถเชื่อมต่อฐานข้อมูลได้ กรุณาตรวจสอบการตั้งค่าใน includes/config.php</div>");
}

// ตรวจสอบว่ามีการดูรายละเอียดข่าวหรือไม่
if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $news_id = intval($_GET['view']);
    
    // ดึงข่าวที่ต้องการดู
    $news_query = "SELECT * FROM news WHERE news_id = ? AND status='publish'";
    $stmt = $conn->prepare($news_query);
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $news_result = $stmt->get_result();
    
    if ($news_result->num_rows > 0) {
        $news_detail = $news_result->fetch_assoc();
        
        // อัปเดตจำนวนผู้ดู
        $update_view = "UPDATE news SET views = views + 1 WHERE news_id = ?";
        $stmt2 = $conn->prepare($update_view);
        $stmt2->bind_param("i", $news_id);
        $stmt2->execute();
        ?>
        
        <div class="custom-container view-detail-container">
            <div class="mb-4">
                <a href="?page=news" class="btn back-button text-white">
                    <i class="bi bi-arrow-left me-2"></i>กลับสู่หน้าหลัก
                </a>
            </div>
            
            <div class="card shadow-lg border-0">
                <div class="card-body p-4 p-md-5">
                    <!-- หัวข้อข่าว -->
                    <h1 class="mb-3 fw-bold" style="color: #2c3e50;">
                        <?php echo htmlspecialchars($news_detail['title']); ?>
                    </h1>
                    
                    <!-- ข้อมูลเมตา -->
                    <div class="news-meta-info">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-person-circle me-2"></i>
                                    <?php echo htmlspecialchars($news_detail['author']); ?>
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-eye me-2"></i>
                                    <?php echo number_format($news_detail['views'] + 1); ?> ครั้ง
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-calendar3 me-2"></i>
                                    <?php 
                                    $date = date("d M Y", strtotime($news_detail['created_at']));
                                    $thai_months = [
                                        'Jan' => 'ม.ค.', 'Feb' => 'ก.พ.', 'Mar' => 'มี.ค.',
                                        'Apr' => 'เม.ย.', 'May' => 'พ.ค.', 'Jun' => 'มิ.ย.',
                                        'Jul' => 'ก.ค.', 'Aug' => 'ส.ค.', 'Sep' => 'ก.ย.',
                                        'Oct' => 'ต.ค.', 'Nov' => 'พ.ย.', 'Dec' => 'ธ.ค.'
                                    ];
                                    $date = str_replace(array_keys($thai_months), array_values($thai_months), $date);
                                    echo $date;
                                    ?>
                                </span>
                            </div>
                            <div class="col-md-6 mb-2">
                                <span class="badge badge-category">
                                    <i class="bi bi-tag me-1"></i>
                                    <?php echo htmlspecialchars($news_detail['category'] ?? 'ข่าวทั่วไป'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- รูปข่าว -->
                    <?php if (!empty($news_detail['image'])) { ?>
                    <div class="news-detail-image">
                        <?php 
                        $image_path = "uploads/news/" . htmlspecialchars($news_detail['image']);
                        $image_url = file_exists($image_path) ? $image_path : 'https://via.placeholder.com/800x400/28a745/ffffff?text=NEWS';
                        ?>
                        <img src="<?php echo $image_url; ?>" 
                             alt="<?php echo htmlspecialchars($news_detail['title']); ?>"
                             class="img-fluid"
                             onerror="this.src='https://via.placeholder.com/800x400/28a745/ffffff?text=NEWS'">
                    </div>
                    <?php } ?>
                    
                    <!-- เนื้อหาเต็ม -->
                    <div class="news-content-full mb-4">
                        <?php echo nl2br(htmlspecialchars($news_detail['content'])); ?>
                    </div>
                    
                    <!-- ปุ่มกลับ -->
                    <div class="text-center mt-5">
                        <a href="?page=news" class="btn back-button text-white">
                            <i class="bi bi-arrow-left me-2"></i>กลับสู่หน้าหลัก
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <?php
        exit();
    }
}

// ถ้าไม่ใช่การดูรายละเอียด ขอแสดงหน้าหลัก
// กำหนดการเรียงลำดับจาก URL parameter
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'views';
$order_by = ($sort == 'latest') ? "ORDER BY created_at DESC" : "ORDER BY views DESC";

// ดึงข้อมูลข่าว
$news_query = "SELECT * FROM news WHERE status='publish' $order_by LIMIT 9";
$news = $conn->query($news_query);

// นับจำนวนข่าวทั้งหมด
$count_query = "SELECT COUNT(*) as total FROM news WHERE status='publish'";
$count_result = $conn->query($count_query);
$total_news = $count_result->fetch_assoc()['total'];
?>

<div class="custom-container">
    <div class="card shadow-lg mb-5 border-0">
        <div class="card-header-custom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <h3 class="text-light mb-3 mb-md-0">
                    <i class="bi bi-newspaper me-2"></i>ข่าวสารทั้งหมด
                    <span class="badge stats-badge ms-2">
                        <?php echo number_format($total_news); ?> ข่าว
                    </span>
                </h3>
                
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-funnel me-1"></i>เรียงตาม
                        </button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item <?php echo $sort == 'views' ? 'active' : ''; ?>" 
                                   href="?page=news&sort=views">
                                    <i class="bi bi-eye me-2"></i>ยอดนิยม
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item <?php echo $sort == 'latest' ? 'active' : ''; ?>" 
                                   href="?page=news&sort=latest">
                                    <i class="bi bi-clock me-2"></i>ล่าสุด
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            <h4 class="section-title mb-4">
                <i class="bi bi-lightning-charge-fill text-warning me-2"></i>
                <?php echo $sort == 'latest' ? 'ข่าวล่าสุด' : 'ข่าวยอดนิยม'; ?>
            </h4>

            <?php if ($news && $news->num_rows > 0) { ?>
            <div class="row">
                <?php 
                $counter = 0;
                while ($nws = $news->fetch_assoc()) {
                    $counter++;
                    $animation_delay = $counter * 0.1;
                    
                    // ตรวจสอบว่าเป็นข่าวใหม่หรือไม่ (ไม่เกิน 7 วัน)
                    $is_new = (time() - strtotime($nws['created_at'])) < (7 * 24 * 60 * 60);
                ?>
                <div class="col-lg-4 col-md-6 mb-4 news-item" style="animation-delay: <?php echo $animation_delay; ?>s">
                    <div class="card news-card h-100 shadow-sm">
                        <!-- รูปข่าว -->
                        <div class="news-image position-relative">
                            <?php 
                            $image_path = "uploads/news/" . htmlspecialchars($nws['image']);
                            $image_url = file_exists($image_path) ? $image_path : 'https://via.placeholder.com/800x400/28a745/ffffff?text=NEWS';
                            ?>
                            <img src="<?php echo $image_url; ?>"
                                 alt="<?php echo htmlspecialchars($nws['title']); ?>"
                                 class="img-fluid"
                                 onerror="this.src='https://via.placeholder.com/800x400/28a745/ffffff?text=NEWS'">
                            
                            <!-- Badge สำหรับข่าวยอดนิยม -->
                            <?php if ($nws['views'] > 100) { ?>
                            <div class="popular-badge">
                                <i class="bi bi-fire me-1"></i>ยอดนิยม
                            </div>
                            <?php } ?>
                            
                            <!-- Badge สำหรับข่าวใหม่ -->
                            <?php if ($is_new && $nws['views'] <= 100) { ?>
                            <div class="new-badge">
                                <i class="bi bi-star me-1"></i>ใหม่
                            </div>
                            <?php } ?>
                        </div>

                        <div class="card-body d-flex flex-column p-4">
                            <!-- หมวดข่าว -->
                            <span class="badge badge-category mb-3 align-self-start">
                                <i class="bi bi-tag me-1"></i> 
                                <?php echo htmlspecialchars($nws['category'] ?? 'ข่าวทั่วไป'); ?>
                            </span>

                            <!-- หัวข้อข่าว -->
                            <h5 class="card-title mb-3">
                                <a href="?page=news&view=<?php echo $nws['news_id']; ?>" 
                                   class="text-decoration-none text-dark">
                                    <?php echo htmlspecialchars($nws['title']); ?>
                                </a>
                            </h5>

                            <!-- เนื้อหาแบบย่อ -->
                            <p class="card-text text-muted news-content mb-4">
                                <?php 
                                $content = strip_tags($nws['content']);
                                if (mb_strlen($content) > 120) {
                                    echo mb_substr($content, 0, 120, 'UTF-8') . '...';
                                } else {
                                    echo $content;
                                }
                                ?>
                            </p>

                            <!-- ข้อมูลเพิ่มเติม -->
                            <div class="mt-auto">
                                <div class="d-flex justify-content-between text-muted small mb-3 author-views">
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-person-circle me-2"></i>
                                        <?php echo htmlspecialchars($nws['author']); ?>
                                    </span>
                                    <span class="d-flex align-items-center">
                                        <i class="bi bi-eye me-2"></i>
                                        <?php echo number_format($nws['views']); ?>
                                    </span>
                                </div>

                                <div class="date-button-container">
                                    <small class="date-text">
                                        <i class="bi bi-calendar3 me-1"></i>
                                        <?php 
                                        $date = date("d M Y", strtotime($nws['created_at']));
                                        $thai_months = [
                                            'Jan' => 'ม.ค.', 'Feb' => 'ก.พ.', 'Mar' => 'มี.ค.',
                                            'Apr' => 'เม.ย.', 'May' => 'พ.ค.', 'Jun' => 'มิ.ย.',
                                            'Jul' => 'ก.ค.', 'Aug' => 'ส.ค.', 'Sep' => 'ก.ย.',
                                            'Oct' => 'ต.ค.', 'Nov' => 'พ.ย.', 'Dec' => 'ธ.ค.'
                                        ];
                                        $date = str_replace(array_keys($thai_months), array_values($thai_months), $date);
                                        echo $date;
                                        ?>
                                    </small>

                                    <a href="?page=comment&id=<?php echo number_format($nws['news_id']); ?>"
                                       class="btn btn-success btn-sm read-more-btn">
                                        <i class="bi bi-arrow-right me-1"></i>อ่านเพิ่มเติม
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            
            <!-- แสดงปุ่มโหลดเพิ่มถ้ามีข่าวมากกว่า 9 ข่าว -->
            <?php if ($total_news > 9) { ?>
            <div class="text-center mt-5">
                <button class="btn load-more-btn text-white" onclick="loadMoreNews()">
                    <i class="bi bi-plus-circle me-2"></i>โหลดข่าวเพิ่มเติม
                </button>
            </div>
            <?php } ?>
            
            <?php } else { ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="bi bi-newspaper"></i>
                <h4 class="text-muted mt-3">ยังไม่มีข่าวสารในขณะนี้</h4>
                <p class="text-muted">กรุณาตรวจสอบอีกครั้งในภายหลัง</p>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
// ฟังก์ชันสำหรับโหลดข่าวเพิ่มเติม
function loadMoreNews() {
    const loadMoreBtn = document.querySelector('.load-more-btn');
    const currentCount = document.querySelectorAll('.news-item').length;
    
    loadMoreBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>กำลังโหลด...';
    loadMoreBtn.disabled = true;
    
    // ส่ง request ไปยังเซิร์ฟเวอร์
    fetch(`load_more_news.php?offset=${currentCount}&sort=<?php echo $sort; ?>`)
        .then(response => response.text())
        .then(data => {
            if (data.trim() !== '') {
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = data;
                
                // เพิ่ม animation delay สำหรับข่าวใหม่
                const newItems = tempDiv.querySelectorAll('.news-item');
                newItems.forEach((item, index) => {
                    item.style.animationDelay = `${(currentCount + index) * 0.1}s`;
                });
                
                // เพิ่มข่าวใหม่เข้าไปใน container
                document.querySelector('.row').insertAdjacentHTML('beforeend', data);
                
                // อัพเดทปุ่มหรือซ่อนปุ่มถ้าโหลดครบแล้ว
                const totalItems = document.querySelectorAll('.news-item').length;
                if (totalItems >= <?php echo $total_news; ?>) {
                    loadMoreBtn.style.display = 'none';
                } else {
                    loadMoreBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>โหลดข่าวเพิ่มเติม';
                    loadMoreBtn.disabled = false;
                }
            } else {
                loadMoreBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>โหลดครบทั้งหมดแล้ว';
                loadMoreBtn.disabled = true;
                setTimeout(() => {
                    loadMoreBtn.style.display = 'none';
                }, 2000);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            loadMoreBtn.innerHTML = '<i class="bi bi-exclamation-triangle me-2"></i>เกิดข้อผิดพลาด';
            setTimeout(() => {
                loadMoreBtn.innerHTML = '<i class="bi bi-plus-circle me-2"></i>โหลดข่าวเพิ่มเติม';
                loadMoreBtn.disabled = false;
            }, 2000);
        });
}

// ฟังก์ชันค้นหาข่าว
function searchNews() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm.length > 0) {
        window.location.href = `?page=news&search=${encodeURIComponent(searchTerm)}`;
    }
}

// เพิ่ม event listener สำหรับการกด Enter ในช่องค้นหา
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchNews();
            }
        });
    }
});
</script>