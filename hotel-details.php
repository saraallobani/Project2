<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';

// تأمين جلب المعرف
$hotel_id = isset($_GET['id']) ? (int) $_GET['id'] : null;
require_once 'includes/hotel-data.php';

// اختيار الفندق أو الافتراضي
if ($hotel_id !== null && isset($meshrider_hotels[$hotel_id])) {
    $hotel = $meshrider_hotels[$hotel_id];
} else {
    $hotel = reset($meshrider_hotels); 
}
?>

<style>
    :root {
        --gold-gradient: linear-gradient(135deg, #c9a66b 0%, #a47e44 100%);
        --dark-bg: #050505;
        --card-bg: #0f0f12;
        --text-gold: #d4af37;
    }

    body { background-color: var(--dark-bg); color: #fff; font-family: 'Cairo', sans-serif; }

    /* هيدر سينمائي */
    .hero-cinematic {
        height: 70vh;
        background: url('<?php echo $hotel['image']; ?>') center/cover no-repeat fixed;
        position: relative;
        display: flex;
        align-items: flex-end;
    }

    .hero-cinematic::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, var(--dark-bg) 5%, transparent 60%);
    }

    /* كرت المعلومات العائم */
    .floating-info-card {
        max-width: 1200px;
        margin: -100px auto 50px;
        position: relative;
        z-index: 20;
        padding: 0 20px;
    }

    .glass-base {
        background: rgba(15, 15, 18, 0.7);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 40px;
        padding: 50px;
        box-shadow: 0 30px 60px rgba(0,0,0,0.8);
    }

    .hotel-badge {
        display: inline-block;
        background: var(--gold-gradient);
        color: #000;
        padding: 5px 20px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 0.8rem;
        margin-bottom: 20px;
        text-transform: uppercase;
    }

    .hotel-main-title {
        font-size: 3.5rem;
        font-weight: 900;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }

    /* أزرار التواصل (الدليل الذكي) */
    .directory-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .action-link {
        padding: 20px;
        border-radius: 20px;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255,255,255,0.1);
    }

    .btn-wa { background: #25d366; color: #fff; border: none; }
    .btn-map { background: #fff; color: #000; }
    .btn-call { background: transparent; color: #fff; border: 1px solid var(--text-gold); }

    .action-link:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.4);
    }

    /* أقسام المحتوى */
    .content-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 40px;
        margin-top: 50px;
    }

    .desc-box {
        background: var(--card-bg);
        padding: 40px;
        border-radius: 30px;
        border: 1px solid rgba(255,255,255,0.03);
    }

    .amenity-tag {
        background: rgba(255,255,255,0.05);
        padding: 15px 25px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
        border: 1px solid transparent;
        transition: 0.3s;
    }

    .amenity-tag:hover {
        border-color: var(--text-gold);
        background: rgba(212, 175, 55, 0.05);
    }

    .amenity-tag i { color: var(--text-gold); font-size: 1.2rem; }

    @media (max-width: 992px) {
        .content-grid { grid-template-columns: 1fr; }
        .hotel-main-title { font-size: 2.5rem; }
    }
</style>

<div class="hotel-details-page">
    <section class="hero-cinematic"></section>

    <div class="floating-info-card animate__animated animate__fadeInUp">
        <div class="glass-base">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="hotel-badge">وجهة مختارة بعناية</span>
                    <h1 class="hotel-main-title"><?php echo htmlspecialchars($hotel['name']); ?></h1>
                    
                    <div style="display: flex; gap: 20px; align-items: center; color: var(--text-gold);">
                        <div class="stars">
                            <?php for($i=0; $i<$hotel['stars']; $i++) echo '<i class="fas fa-star"></i>'; ?>
                        </div>
                        <span style="color: #fff; opacity: 0.6;">|</span>
                        <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($hotel['location']); ?></span>
                    </div>
                </div>
                <div class="col-lg-4 text-lg-start mt-4 mt-lg-0">
                    <div style="background: rgba(255,255,255,0.05); padding: 20px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.1);">
                        <p style="margin: 0; opacity: 0.6; font-size: 0.9rem;">متوسط سعر الليلة</p>
                        <h2 style="margin: 0; color: var(--text-gold); font-weight: 800;"><?php echo $hotel['price']; ?></h2>
                    </div>
                </div>
            </div>

            <div class="directory-actions">
                <a href="https://wa.me/<?php echo $hotel['whatsapp'] ?? ''; ?>?text=مرحباً، أريد الاستفسار عن تفاصيل الإقامة" target="_blank" class="action-link btn-wa">
                    <i class="fab fa-whatsapp" style="font-size: 1.5rem;"></i>
                    تواصل واتساب مباشر
                </a>

                <a href="tel:<?php echo $hotel['phone']; ?>" class="action-link btn-call">
                    <i class="fas fa-phone-alt"></i>
                    اتصال بالاستقبال
                </a>

                <a href="https://www.google.com/maps/search/<?php echo urlencode($hotel['name'] . ' ' . $hotel['location']); ?>" target="_blank" class="action-link btn-map">
                    <i class="fas fa-location-arrow"></i>
                    فتح الخريطة
                </a>
            </div>
        </div>

        <div class="content-grid">
            <div class="desc-box">
                <h3 style="font-weight: 800; margin-bottom: 25px; color: var(--text-gold);">عن هذه الوجهة</h3>
                <p style="font-size: 1.2rem; line-height: 2.2; color: #d1d1d1;">
                    <?php echo htmlspecialchars($hotel['desc']); ?>
                </p>
                <hr style="border-color: rgba(255,255,255,0.1); margin: 40px 0;">
                <h4 style="margin-bottom: 20px;">لماذا ننصح بهذا المكان؟</h4>
                <p style="color: #aaa;"><?php echo htmlspecialchars($hotel['short_desc']); ?></p>
            </div>

            <div class="desc-box">
                <h3 style="font-weight: 800; margin-bottom: 25px; color: var(--text-gold);">المميزات</h3>
                <?php foreach ($hotel['amenities'] as $am): ?>
                    <div class="amenity-tag">
                        <i class="fas <?php echo $am['icon']; ?>"></i>
                        <span style="font-weight: 600;"><?php echo $am['name']; ?></span>
                    </div>
                <?php endforeach; ?>
                
                <div style="margin-top: 30px; padding: 20px; background: rgba(212, 175, 55, 0.1); border-radius: 20px; border: 1px dashed var(--text-gold);">
                    <p style="margin: 0; font-size: 0.9rem; color: var(--text-gold); text-align: center;">
                        <i class="fas fa-info-circle"></i> يتم التوجيه لمزود الخدمة مباشرة
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>