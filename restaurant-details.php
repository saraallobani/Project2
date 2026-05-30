<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';

// جلب المعرف من الرابط وتأمين البيانات
$restaurant_id = isset($_GET['id']) ? (int) $_GET['id'] : null;

require_once 'includes/restaurant-data.php';

$restaurantDetails = $meshrider_restaurants;

// اختيار المطعم بناءً على الـ ID أو جلب أول مطعم كافتراضي
if ($restaurant_id !== null && isset($restaurantDetails[$restaurant_id])) {
    $restaurant = $restaurantDetails[$restaurant_id];
} else {
    $restaurant = reset($restaurantDetails); 
}
?>

<style>
    /* نظام الألوان والتصميم الحديث */
    :root {
        --primary: #e67e22; /* برتقالي دافئ */
        --primary-glow: rgba(230, 126, 34, 0.15);
        --bg-dark: #0c0c0e;
        --card-bg: #1a1a1f;
        --text-pure: #ffffff;
        --text-muted: #a1a1aa;
        --gold-accent: #f1c40f;
        --border-color: rgba(255, 255, 255, 0.08);
    }

    .restaurant-details-page {
        background-color: var(--bg-dark);
        color: var(--text-pure);
        font-family: 'Cairo', sans-serif;
        padding-bottom: 80px;
    }

    /* هيدر الصفحة - صورة الخلفية */
    .hotel-hero {
        background-position: center;
        background-size: cover;
        position: relative;
        overflow: hidden;
    }

    .hotel-hero::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 70%;
        background: linear-gradient(to top, var(--bg-dark), transparent);
    }

    /* حاوية التفاصيل الرئيسية */
    .hotel-details-container {
        max-width: 1200px;
        margin: -120px auto 0 auto;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }

    /* بطاقة المعلومات الأساسية */
    .hotel-header-info {
        background: rgba(26, 26, 31, 0.8);
        backdrop-filter: blur(15px);
        border: 1px solid var(--border-color);
        padding: 40px;
        border-radius: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 30px;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
    }

    .hotel-title-area h2 {
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 10px;
        background: linear-gradient(to left, #fff, #ccc);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .hotel-stars {
        color: var(--gold-accent);
        margin-bottom: 15px;
        font-size: 1.1rem;
    }

    .hotel-location {
        color: var(--text-muted);
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* الأقسام الجانبية */
    .hotel-section {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        padding: 40px;
        border-radius: 24px;
        margin-top: 40px;
        transition: transform 0.3s ease;
    }

    .hotel-section h3 {
        font-size: 1.6rem;
        font-weight: 700;
        margin-bottom: 25px;
        border-right: 4px solid var(--primary);
        padding-right: 15px;
    }

    /* شبكة المنيو والمرافق */
    .amenities-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
    }

    .amenity-item {
        background: rgba(255, 255, 255, 0.03);
        padding: 18px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        border: 1px solid transparent;
        transition: 0.3s;
    }

    .amenity-item:hover {
        background: var(--primary-glow);
        border-color: var(--primary);
        transform: translateY(-3px);
    }

    .amenity-item i {
        color: var(--primary);
        font-size: 1.3rem;
    }

    /* بطاقات المنيو */
    .rooms-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 30px;
    }

    .room-card {
        background: #212126;
        border-radius: 20px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        height: 100%;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .room-card:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
    }

    .room-img {
        height: 240px;
        width: 100%;
        object-fit: cover;
        transition: 0.5s;
    }

    .room-card:hover .room-img {
        filter: brightness(1.1);
    }

    .room-details {
        padding: 25px;
    }

    .price-tag {
        background: var(--primary);
        color: white;
        padding: 4px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
    }

    /* أزرار التواصل */
    .booking-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .btn-action {
        flex: 1;
        min-width: 200px;
        padding: 15px 25px;
        border-radius: 14px;
        font-weight: 700;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: 0.3s;
    }

    .btn-main { background: var(--primary); color: white; border: none; }
    .btn-main:hover { background: #d35400; box-shadow: 0 10px 20px var(--primary-glow); }

    .btn-whatsapp-alt { background: #25d366; color: white; }
    .btn-whatsapp-alt:hover { background: #1ebd5b; transform: translateY(-3px); }

    .btn-outline-custom { 
        background: transparent; 
        color: white; 
        border: 1px solid var(--border-color); 
    }
    .btn-outline-custom:hover { background: rgba(255,255,255,0.05); border-color: white; }

    @media (max-width: 768px) {
        .hotel-header-info { padding: 30px; flex-direction: column; text-align: center; }
        .hotel-title-area h2 { font-size: 1.8rem; }
        .hotel-section { padding: 25px; }
    }
</style>

<div class="restaurant-details-page">
    <section class="hotel-hero" style="background-image: url('<?php echo $restaurant['image']; ?>'); height: 60vh;">
    </section>

    <div class="hotel-details-container animate__animated animate__fadeInUp">
        
        <div class="hotel-header-info">
            <div class="hotel-title-area">
                <div class="hotel-stars">
                    <?php for ($i = 0; $i < $restaurant['stars']; $i++): ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <h2><?php echo htmlspecialchars($restaurant['name']); ?></h2>
                <div class="hotel-location">
                    <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i>
                    <?php echo htmlspecialchars($restaurant['location']); ?>
                </div>
                
                <div style="margin-top: 25px; display: flex; gap: 12px; flex-wrap: wrap;">
                    <span style="background: var(--primary-glow); color: var(--primary); padding: 8px 20px; border-radius: 12px; font-weight: 600; font-size: 0.9rem; border: 1px solid rgba(230,126,34,0.3);">
                        <?php echo htmlspecialchars($restaurant['cuisine']); ?>
                    </span>
                    <span style="background: rgba(255,255,255,0.05); color: var(--text-muted); padding: 8px 20px; border-radius: 12px; font-size: 0.9rem; border: 1px solid var(--border-color);">
                        <?php echo htmlspecialchars($restaurant['atmosphere'] ?? 'أجواء عائلية'); ?>
                    </span>
                </div>
            </div>
            
            <div style="text-align: center;">
                <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 5px;">مستوى الفخامة</p>
                <div style="font-size: 2.8rem; font-weight: 800; color: var(--gold-accent); line-height: 1;">
                    <?php echo $restaurant['price_level']; ?>
                </div>
                <div style="margin-top: 15px; color: #25d366; font-size: 0.9rem; font-weight: 600;">
                    <i class="fas fa-circle" style="font-size: 8px; vertical-align: middle;"></i> <?php echo $restaurant['status']; ?>
                </div>
            </div>
        </div>

        <div class="hotel-section">
            <h3>قصة المطعم</h3>
            <p style="color: var(--text-muted); font-size: 1.15rem; line-height: 1.9; margin: 0;">
                <?php echo htmlspecialchars($restaurant['desc']); ?>
            </p>
        </div>

        <div class="hotel-section">
            <h3>المرافق والخدمات</h3>
            <div class="amenities-grid">
                <?php foreach ($restaurant['amenities'] as $am): ?>
                    <div class="amenity-item">
                        <i class="fas <?php echo $am['icon']; ?>"></i>
                        <span style="font-weight: 600;"><?php echo $am['name']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="hotel-section">
            <h3>قائمة الطعام المختارة</h3>
            <div class="rooms-grid">
                <?php
                $menu = isset($restaurant['menu']) ? $restaurant['menu'] : [
                    ['title' => 'مقبلات اليوم', 'image' => 'https://images.unsplash.com/photo-1541518763669-27fef04b14ea?w=800', 'description' => 'تشكيلة طازجة من المقبلات المختارة.', 'price' => 'حسب الطلب', 'category' => 'Starters'],
                    ['title' => 'الطبق الرئيسي', 'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800', 'description' => 'طبقنا الأشهر المحضر بكل حب.', 'price' => 'سعر منافس', 'category' => 'Main Dish']
                ];

                foreach ($menu as $item):
                    $img_src = (filter_var($item['image'], FILTER_VALIDATE_URL)) ? $item['image'] : 'assets/images/menu/' . $item['image'];
                ?>
                    <div class="room-card">
                        <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo $item['title']; ?>" class="room-img">
                        <div class="room-details">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                <span style="font-size: 0.8rem; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; font-weight: 700;">
                                    <?php echo $item['category']; ?>
                                </span>
                                <span class="price-tag"><?php echo $item['price']; ?></span>
                            </div>
                            <h4 style="font-size: 1.3rem; margin-bottom: 10px; font-weight: 700;"><?php echo $item['title']; ?></h4>
                            <p style="color: var(--text-muted); font-size: 0.95rem; line-height: 1.6; margin: 0;">
                                <?php echo $item['description']; ?>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="contact-section" class="hotel-section" style="text-align: center; background: linear-gradient(145deg, #1a1a1f, #131317);">
            <h3 style="border: none; padding: 0;">هل تود الحجز أو الاستفسار؟</h3>
            <p style="color: var(--text-muted); max-width: 600px; margin: 0 auto 35px auto; font-size: 1.1rem;">
                فريقنا في <strong><?php echo htmlspecialchars($restaurant['name']); ?></strong> جاهز لاستقبالكم وتقديم أفضل تجربة طعام.
            </p>
            
            <div class="booking-actions">
                <a href="https://wa.me/<?php echo $restaurant['whatsapp'] ?? '962000000000'; ?>?text=ارغب بالحجز" class="btn-action btn-whatsapp-alt">
                    <i class="fab fa-whatsapp"></i> تواصل عبر واتساب
                </a>
                
                <a href="tel:<?php echo $restaurant['phone']; ?>" class="btn-action btn-main">
                    <i class="fas fa-phone-alt"></i> اتصل الآن
                </a>

                <a href="https://www.google.com/maps/search/<?php echo urlencode($restaurant['name'] . ' ' . $restaurant['location']); ?>" target="_blank" class="btn-action btn-outline-custom">
                    <i class="fas fa-directions"></i> الاتجاهات
                </a>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>