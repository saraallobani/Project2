<?php
session_start();
require_once 'includes/header.php';

$region = isset($_GET['region']) ? $_GET['region'] : '';
$regionName = '';

switch ($region) {
    case 'amman':
        $regionName = 'عمّان';
        break;
    case 'aqaba':
        $regionName = 'العقبة';
        break;
    case 'wadirum':
        $regionName = 'وادي رم';
        break;
    case 'jerash':
        $regionName = 'جرش';
        break;
    case 'irbid':
        $regionName = 'إربد';
        break;
    case 'deadsea':
        $regionName = 'البحر الميت';
        break;
    default:
        $regionName = 'كل المناطق';
        break;
}

require_once 'includes/hotel-data.php';
$hotels = $meshrider_hotels;

if ($region) {
    $hotels = array_filter($hotels, function ($h) use ($region) {
        return $h['region'] === $region;
    });
}
?>

<section class="hotel-hero"
    style="background-image: url('https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1920&q=80'); height: 40vh; min-height: 300px;">
    <div class="hotel-hero-content text-center">
        <h1>فنادق <?php echo htmlspecialchars($regionName); ?></h1>
        <p>استعرض أفضل الخيارات المتاحة لإقامتك</p>
    </div>
</section>

<div class="hotel-details-container">
    <a href="hotels.php" class="back-btn"><i class="fas fa-arrow-right"></i> عودة للمناطق</a>

  
    <div class="filters-bar">
        <input type="text" id="hotelSearch" class="filter-input" placeholder="ابحث عن اسم الفندق...">
        <select id="starFilter" class="filter-select">
            <option value="">جميع التقييمات</option>
            <option value="5">5 نجوم</option>
            <option value="4">4 نجوم</option>
            <option value="3">3 نجوم</option>
        </select>
    </div>

    <div class="hotels-container" style="padding: 0;">
        <?php if (empty($hotels)): ?>
            <h3 class="text-center" style="width:100%; color: var(--text-muted);">لا توجد فنادق متاحة حالياً في هذه المنطقة.
            </h3>
        <?php else: ?>
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card animate__animated animate__fadeInUp" data-stars="<?php echo $hotel['stars']; ?>">
                    <img src="<?php echo $hotel['image']; ?>" alt="<?php echo htmlspecialchars($hotel['name']); ?>"
                        class="hotel-image">
                    <div class="hotel-info">
                        <h3><?php echo htmlspecialchars($hotel['name']); ?></h3>
                        <div class="hotel-stars mb-2" style="color: #f1c40f;">
                            <?php for ($i = 0; $i < $hotel['stars']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <p style="color: var(--text-muted);"><i class="fas fa-map-marker-alt"
                                style="color: var(--primary);"></i> <?php echo htmlspecialchars($hotel['location']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <span class="price-tag"><?php echo $hotel['price']; ?> / ليلة</span>
                            <a href="hotel-details.php?id=<?php echo $hotel['id']; ?>" class="btn-explore"
                                style="padding: 5px 15px; font-size: 0.9rem;">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
<script src="assets/js/hotels.js"></script>
</body>

</html>