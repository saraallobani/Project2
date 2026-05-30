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

require_once 'includes/restaurant-data.php';
$restaurants = $meshrider_restaurants;



if ($region) {
    $restaurants = array_filter($restaurants, function ($r) use ($region) {
        return $r['region'] === $region;
    });
}
?>


<section class="hotel-hero"
    style="background-image: url('https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRHSWATW0i3Aph0vcDIeNhEoHLBBzkOSHNMQ&s'); height: 40vh; min-height: 300px;">
    <div class="hotel-hero-content text-center">
      
        <h1>مطاعم <?php echo htmlspecialchars($regionName); ?></h1>
      
        <p>استعرض أفضل الخيارات المتاحة لتجربة طعام استثنائية</p>
    </div>
</section>

<div class="hotel-details-container">

    <a href="restaurants.php" class="back-btn"><i class="fas fa-arrow-right"></i> عودة للمناطق</a>

   
    <div class="filters-bar">
    
        <input type="text" id="restaurantSearch" class="filter-input" placeholder="ابحث عن اسم المطعم...">
        
        <select id="cuisineFilter" class="filter-select">
            
            <option value="">جميع المطابخ</option>
           
            <option value="عربي">عربي / شرقي</option>
            
            <option value="إيطالي">إيطالي</option>
            <option value="بحري">مأكولات بحرية</option>
            <option value="بدوي">أكل بدوي</option>
            <option value="بوفيه">بوفيه مفتوح</option>
            
            <option value="شاورما">وجبات سريعة</option>
        </select>
        <select id="starFilter" class="filter-select">
            <option value="">جميع التقييمات</option>
            <option value="5">5 نجوم</option>
            <option value="4">4 نجوم</option>
            <option value="3">3 نجوم</option>
        </select>
    </div>

    <div class="hotels-container" style="padding: 0;">
        <?php if (empty($restaurants)): ?>
            <h3 class="text-center" style="width:100%; color: var(--text-muted);">لا توجد مطاعم متاحة حالياً في هذه المنطقة.
            </h3>
        <?php else: ?>
            <?php foreach ($restaurants as $restaurant): ?>
                <div class="hotel-card restaurant-card animate__animated animate__fadeInUp"
                    data-stars="<?php echo $restaurant['stars']; ?>"
                    data-cuisine="<?php echo htmlspecialchars($restaurant['cuisine']); ?>">
                    <img src="<?php echo $restaurant['image']; ?>" alt="<?php echo htmlspecialchars($restaurant['name']); ?>"
                        class="hotel-image" style="object-position: center;">
                    <div class="hotel-info">
                        <h3><?php echo htmlspecialchars($restaurant['name']); ?></h3>
                        <div class="hotel-stars mb-2" style="color: #f1c40f;">
                            <?php for ($i = 0; $i < $restaurant['stars']; $i++): ?>
                                <i class="fas fa-star"></i>
                            <?php endfor; ?>
                        </div>
                        <div style="margin-bottom: 10px;">
                            <span
                                style="background: rgba(230, 126, 34, 0.2); color: var(--primary); padding: 3px 10px; border-radius: 10px; font-size: 0.85rem;">
                                <?php echo htmlspecialchars($restaurant['cuisine']); ?>
                            </span>
                        </div>
                        <p style="color: var(--text-muted); font-size: 0.9rem; min-height: 40px;">
                            <?php echo htmlspecialchars($restaurant['short_desc']); ?>
                        </p>
                        <p style="color: var(--text-muted);"><i class="fas fa-map-marker-alt"
                                style="color: var(--primary);"></i> <?php echo htmlspecialchars($restaurant['location']); ?></p>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3"
                            style="border-top: 1px solid rgba(255,255,255,0.05);">
                            <?php
                            $avg_price = '10 - 15 دينار';
                            if ($restaurant['price_level'] == '$$$')
                                $avg_price = '20 - 35 دينار';
                            else if ($restaurant['price_level'] == '$')
                                $avg_price = '3 - 8 دينار';
                            ?>
                            <div class="d-flex flex-column gap-1">
                                <span class="price-tag d-inline-block text-center"
                                    style="letter-spacing: 2px; font-weight: bold; padding: 2px 10px;"><?php echo $restaurant['price_level']; ?></span>
                                <span style="font-size: 0.8rem; color: var(--gold-accent);">متوسط:
                                    <?php echo $avg_price; ?></span>
                            </div>
                            <a href="restaurant-details.php?id=<?php echo $restaurant['id']; ?>" class="btn-explore"
                                style="padding: 5px 15px; font-size: 0.9rem;">عرض التفاصيل</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>


<?php require_once 'includes/footer.php'; ?>

<script src="assets/js/restaurants.js"></script>
</body>

</html>