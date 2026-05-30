<?php 
include 'includes/db_config.php'; 
include 'includes/header.php'; 

try {
    $stmtUsers = $pdo->query("SELECT COUNT(*) FROM users");
    $displayUsers = $stmtUsers->fetchColumn();

    $stmtBookings = $pdo->query("SELECT COUNT(*) FROM bookings");
    $displayBookings = $stmtBookings->fetchColumn();
    
    $stmtTripsCount = $pdo->query("SELECT COUNT(*) FROM trips");
    $displayTrips = $stmtTripsCount->fetchColumn();

} catch (Exception $e) {
    $displayUsers = 0;
    $displayBookings = 0;
    $displayTrips = 0;
}
?>

<style>
    .grad-overlay {
        background: linear-gradient(transparent, rgba(0,0,0,0.9));
    }
    .trip-card {
        transition: transform 0.3s ease;
        overflow: hidden;
        min-height: 400px;
    }
    .trip-card:hover {
        transform: translateY(-10px);
    }
    .feature-box {
        padding: 30px;
        border-radius: 15px;
        transition: 0.3s;
        background: #151515;
        border: 1px solid #222;
        height: 100%;
    }
    .active-feature {
        background: rgba(255, 102, 0, 0.1);
        border: 1px solid #ff6600;
    }
    .about-img {
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        transition: 0.5s;
    }
    .about-img:hover {
        transform: scale(1.02);
    }
</style>

<div class="hero" style="position: relative; height: 80vh; background: url('https://images.unsplash.com/photo-1547234935-80c7145ec969?q=80&w=1474') center/cover;">
    <div class="hero-overlay" style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6);"></div>
    <div class="container hero-content text-center" style="position: relative; top: 50%; transform: translateY(-50%); z-index: 2;">
        <h1 class="display-1 fw-bold animate__animated animate__fadeInDown" style="color: #ff6600; letter-spacing: 10px;">Mesh</h1>
        <h2 class="display-4 fw-bold mb-4 text-white">Rider</h2>
        <p class="lead fs-3 mb-5 text-light">اكتشف عظمة الأردن بلمسة ذكية وعصرية</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="login.php" class="btn btn-warning btn-lg px-5 rounded-pill fw-bold">ابدأ رحلتك الآن</a>
            <a href="#about-jordan" class="btn btn-outline-light btn-lg rounded-pill px-5">لماذا الأردن؟</a>
        </div>
    </div>
</div>

<section class="py-5" style="background: #000; border-bottom: 1px solid #222;">
    <div class="container text-center">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-users fa-3x text-warning mb-3"></i>
                    <h2 class="display-4 fw-bold text-white counter" data-target="<?php echo $displayUsers; ?>">0</h2>
                    <p class="text-muted fw-bold">مكتشف مسجل</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card border-warning-side">
                    <i class="fas fa-map-marked-alt fa-3x text-warning mb-3"></i>
                    <h2 class="display-4 fw-bold text-white counter" data-target="<?php echo $displayTrips; ?>">0</h2>
                    <p class="text-muted fw-bold">وجهة سياحية متاحة</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <i class="fas fa-route fa-3x text-warning mb-3"></i>
                    <h2 class="display-4 fw-bold text-white counter" data-target="<?php echo $displayBookings; ?>">0</h2>
                    <p class="text-muted fw-bold">رحلة تم حجزها</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="featured" class="py-5 bg-dark">
    <div class="container">
        <h2 class="text-center mb-5 fw-bold display-5 text-white">أشهر الوجهات 🔥</h2>
        <div class="row g-4">
<?php 
try {
    $stmt = $pdo->query("SELECT * FROM trips LIMIT 3");
    while ($trip = $stmt->fetch()) {

        // ✅ التعديل الوحيد
        $img = !empty($trip['image']) ? $trip['image'] : 'https://images.unsplash.com/photo-1547234935-80c7145ec969?q=80&w=800';
        $tripName = $trip['title'] ?? 'رحلة سياحية';
        $tripPrice = $trip['price'] ?? '0.00';
?>
<div class="col-md-4">
    <div class="card trip-card border-0 h-100 shadow">
        <img src="<?php echo $img; ?>" class="card-img h-100" alt="<?php echo htmlspecialchars($tripName); ?>" style="object-fit: cover;">
        <div class="card-img-overlay d-flex flex-column justify-content-end grad-overlay p-4">
            <h3 class="fw-bold text-white"><?php echo htmlspecialchars($tripName); ?></h3>
            <p class="text-warning fw-bold fs-4"><?php echo $tripPrice; ?> JOD</p>
            
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="trip_details.php?id=<?php echo $trip['id'] ?? '#'; ?>" class="btn btn-warning w-100 fw-bold rounded-pill">عرض التفاصيل والحجز</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-outline-warning w-100 fw-bold rounded-pill text-white">سجل دخول للحجز</a>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php 
    } 
} catch (Exception $e) {
    echo "<p class='text-white text-center'>نعمل حالياً على تحديث البيانات...</p>";
}
?>
        </div>
    </div>
</section>

<section id="about-jordan" class="py-5 bg-black">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <h2 class="display-5 fw-bold text-warning mb-4">الأردن: حيث يلتقي التاريخ بالحداثة</h2>
                <p class="lead text-light mb-4" style="text-align: justify; line-height: 1.8;">
                    يعتبر الأردن متحفاً مفتوحاً يضم بين جنباته حضارات تعاقبت لآلاف السنين. من المدينة الوردية المنحوتة في الصخر، إلى أخفض بقعة في العالم.
                </p>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>تنوع مناخي وبيئي فريد</span>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="d-flex align-items-center text-white">
                            <i class="fas fa-check-circle text-warning me-2"></i>
                            <span>كرم ضيافة أصيل</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row g-2">
                    <div class="col-6"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSXElbRnDm3S_a13IXgIZSQaQtjqj2zy9ZyFQ&s" class="img-fluid about-img" alt="Petra"></div>
                    <div class="col-6 mt-4"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS3GxQt8dpnphX_jzh_eUW4375a0aQzN23gVQ&s" class="img-fluid about-img" alt="Wadi Rum"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5 bg-dark">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold display-6 text-warning">لماذا تختار منصتنا؟</h2>
            <p class="text-muted">نسعى لتغيير مفهوم السياحة التقليدية في المملكة</p>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-magic fa-3x text-warning mb-3"></i>
                    <h4 class="fw-bold text-white">مولد رحلات ذكي</h4>
                    <p class="text-muted small">نظام متطور يقترح عليك الرحلات بناءً على ميزانيتك ووقتك المتاح.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box active-feature shadow">
                    <i class="fas fa-shield-alt fa-3x text-warning mb-3"></i>
                    <h4 class="fw-bold text-white">أمان ومصداقية</h4>
                    <p class="text-muted small">حجوزاتك مؤكدة ومحمية، مع دعم فني متواصل خلال رحلتك.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="fas fa-map-marked-alt fa-3x text-warning mb-3"></i>
                    <h4 class="fw-bold text-white">تغطية شاملة</h4>
                    <p class="text-muted small">من شمال الأردن إلى جنوبه، نغطي كافة المعالم السياحية والخدمات.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
const counters = document.querySelectorAll('.counter');
counters.forEach(counter => {
    counter.innerText = '0';
    const updateCounter = () => {
        const target = +counter.getAttribute('data-target');
        const c = +counter.innerText;
        const increment = target / 100;
        if(c < target) {
            counter.innerText = `${Math.ceil(c + increment)}`;
            setTimeout(updateCounter, 20);
        } else {
            counter.innerText = target;
        }
    };
    updateCounter();
});
</script>

<?php include 'includes/footer.php'; ?>