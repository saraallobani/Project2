<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/includes/db_config.php';

$is_logged = isset($_SESSION['user_id']);

$jordan_provinces = [
    ['q' => 'إربد', 'icon' => 'fa-map-marked-alt', 'label' => 'محافظة إربد', 'region' => 'إقليم الشمال'],
    ['q' => 'جرش', 'icon' => 'fa-landmark', 'label' => 'محافظة جرش', 'region' => 'إقليم الشمال'],
    ['q' => 'عجلون', 'icon' => 'fa-tree', 'label' => 'محافظة عجلون', 'region' => 'إقليم الشمال'],
    ['q' => 'المفرق', 'icon' => 'fa-archway', 'label' => 'محافظة المفرق', 'region' => 'إقليم الشمال'],
    
    ['q' => 'عمان', 'icon' => 'fa-city', 'label' => 'العاصمة عمان', 'region' => 'إقليم الوسط'],
    ['q' => 'البلقاء', 'icon' => 'fa-gopuram', 'label' => 'محافظة البلقاء', 'region' => 'إقليم الوسط'],
    ['q' => 'الزرقاء', 'icon' => 'fa-industry', 'label' => 'محافظة الزرقاء', 'region' => 'إقليم الوسط'],
    ['q' => 'مادبا', 'icon' => 'fa-scroll', 'label' => 'محافظة مادبا', 'region' => 'إقليم الوسط'],
    
    ['q' => 'الكرك', 'icon' => 'fa-fort-awesome', 'label' => 'محافظة الكرك', 'region' => 'إقليم الجنوب'],
    ['q' => 'الطفيلة', 'icon' => 'fa-mountain', 'label' => 'محافظة الطفيلة', 'region' => 'إقليم الجنوب'],
    ['q' => 'معان', 'icon' => 'fa-sun', 'label' => 'محافظة معان', 'region' => 'إقليم الجنوب'],
    ['q' => 'العقبة', 'icon' => 'fa-water', 'label' => 'محافظة العقبة', 'region' => 'إقليم الجنوب'],
];

include __DIR__ . '/includes/header.php';
?>

<style>
    .dest-page { 
        direction: rtl; 
        text-align: right; 
        background: #080808;
        color: #fff;
        font-family: 'Cairo', sans-serif;
    }
    
    .dest-hero {
        padding: 4rem 0 3.5rem;
        background: linear-gradient(165deg, rgba(24, 15, 8, 0.98), #0c0c0c);
        border-bottom: 1px solid rgba(230, 126, 34, 0.15);
        text-align: center;
    }
    
    .dest-hero h1 { 
        font-size: 2.8rem;
        font-weight: 900; 
        color: #fff; 
        margin-bottom: 0.75rem; 
        letter-spacing: -0.5px;
    }
    
    .dest-hero .lead { 
        color: #b0b0b0; 
        max-width: 700px; 
        margin: 0 auto;
        font-size: 1.15rem;
        line-height: 1.6;
    }

    .header-divider {
        width: 80px;
        height: 4px;
        background: linear-gradient(90deg, #e67e22, #f39c12);
        margin: 1.25rem auto;
        border-radius: 2px;
    }

    .provinces-section {
        padding: 40px 0 80px;
    }

    .region-title-badge {
        display: inline-block;
        background: rgba(230, 126, 34, 0.1);
        color: #e67e22;
        padding: 6px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid rgba(230, 126, 34, 0.25);
        margin-bottom: 1.5rem;
        margin-top: 2rem;
    }

    .dest-province-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 1.5rem;
    }

    .province-card {
        background: linear-gradient(145deg, #121212, #181818);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 20px;
        padding: 2rem 1.5rem;
        text-align: center;
        text-decoration: none;
        color: #e0e0e0 !important;
        transition: all 0.35s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }

    .province-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, rgba(230, 126, 34, 0.05), transparent);
        opacity: 0;
        transition: opacity 0.35s ease;
    }

    .province-card:hover {
        border-color: #e67e22;
        transform: translateY(-6px);
        color: #fff !important;
        box-shadow: 0 15px 40px rgba(230, 126, 34, 0.15);
    }

    .province-card:hover::before {
        opacity: 1;
    }

    .province-icon-wrapper {
        width: 70px;
        height: 70px;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.25rem;
        transition: all 0.35s ease;
    }

    .province-card:hover .province-icon-wrapper {
        background: rgba(230, 126, 34, 0.1);
        border-color: rgba(230, 126, 34, 0.4);
        transform: scale(1.05);
    }

    .province-card i { 
        font-size: 1.8rem; 
        color: #e67e22; 
        transition: transform 0.35s ease;
    }

    .province-card:hover i {
        transform: rotate(-10deg);
    }

    .province-card h3 {
        font-size: 1.2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        z-index: 1;
    }

    .province-action-text {
        font-size: 0.82rem;
        color: #666;
        font-weight: 600;
        transition: color 0.35s ease;
        z-index: 1;
    }

    .province-card:hover .province-action-text {
        color: #f39c12;
    }

    .info-footer-banner {
        background: rgba(20, 20, 20, 0.6);
        border: 1px solid rgba(255, 255, 255, 0.03);
        padding: 20px;
        border-radius: 15px;
        text-align: center;
        max-width: 600px;
        margin: 3rem auto 0;
    }
</style>

<main class="dest-page">
    <section class="dest-hero">
        <div class="container">
            <h1><i class="fas fa-map-marked-alt ms-2 text-warning"></i> محافظات المملكة الأردنية الهاشمية</h1>
            <div class="header-divider"></div>
            <p class="lead">انطلق في رحلتك الاستكشافية القادمة. اختر إحدى المحافظات لاستعراض كافة الرحلات والأنشطة السياحية المنظمة والـمتاحة بها الآن.</p>
        </div>
    </section>

    <div class="container provinces-section">
        
        <?php
        $regions = ['إقليم الشمال', 'إقليم الوسط', 'إقليم الجنوب'];
        
        foreach ($regions as $current_region):
        ?>
            <div class="text-center">
                <span class="region-title-badge"><i class="fas fa-layer-group ms-1"></i> <?php echo $current_region; ?></span>
            </div>
            
            <div class="dest-province-grid mb-5">
                <?php 
                foreach ($jordan_provinces as $province): 
                    if ($province['region'] !== $current_region) continue;

                    $trip_url = 'trips.php?query=' . urlencode($province['q']);
                    $final_href = $is_logged ? $trip_url : ('login.php?redirect=' . urlencode($trip_url));
                ?>
                    <a class="province-card" href="<?php echo htmlspecialchars($final_href, ENT_QUOTES, 'UTF-8'); ?>">
                        <div class="province-icon-wrapper">
                            <i class="fas <?php echo htmlspecialchars($province['icon'], ENT_QUOTES, 'UTF-8'); ?>"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($province['label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <span class="province-action-text">استكشف الرحلات الآن <i class="fas fa-chevron-left me-1 small"></i></span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

        <?php if (!$is_logged): ?>
            <div class="info-footer-banner">
                <p class="text-muted small mb-0"><i class="fas fa-info-circle ms-1 text-warning"></i> يرجى العلم أنه عند اختيارك للمحافظة، سيطلب منك النظام تسجيل الدخول لعرض تفاصيل الخرائط وحجز المقاعد المتاحة فوراً.</p>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>