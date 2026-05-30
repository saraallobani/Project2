<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<style>
    :root {
        --primary-gold: #c9a66b; 
        --bg-dark: #080808;
        --card-bg: #111114;
        --text-pure: #ffffff;
        --text-muted: #a1a1aa;
        --border-color: rgba(255, 255, 255, 0.05);
    }

    .hotels-page {
        background-color: var(--bg-dark);
        color: var(--text-pure);
        font-family: 'Cairo', sans-serif;
    }

    .hotel-hero {
        height: 65vh;
        background-position: center;
        background-size: cover;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hotel-hero::after {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0.2), var(--bg-dark));
    }

    .hotel-hero-content {
        position: relative;
        z-index: 10;
        text-align: center;
    }

    .hotel-hero-content h1 {
        font-size: 3.8rem;
        font-weight: 900;
        letter-spacing: -1px;
        margin-bottom: 15px;
    }

    .regions-container {
        max-width: 1300px;
        margin: -80px auto 100px auto;
        padding: 0 25px;
        position: relative;
        z-index: 20;
    }

    .section-title {
        font-size: 2rem;
        font-weight: 800;
        margin-bottom: 50px;
        border-right: 6px solid var(--primary-gold);
        padding-right: 20px;
    }

    .regions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 35px;
    }

    .region-card {
        background: var(--card-bg);
        border-radius: 30px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
        cursor: pointer;
    }

    .region-card:hover {
        transform: translateY(-15px);
        border-color: var(--primary-gold);
        box-shadow: 0 30px 60px rgba(201, 166, 107, 0.1);
    }

    .region-img-wrapper {
        height: 280px;
        overflow: hidden;
        position: relative;
    }

    .region-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.8s ease;
    }

    .region-card:hover .region-img {
        transform: scale(1.15);
    }

    .region-info {
        padding: 30px;
        text-align: right;
    }

    .region-info h3 {
        font-size: 1.7rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .btn-explore {
        display: inline-flex;
        align-items: center;
        gap: 12px;
        background: transparent;
        color: var(--primary-gold);
        padding: 12px 25px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: 700;
        border: 1px solid var(--primary-gold);
        transition: 0.3s;
    }

    .region-card:hover .btn-explore {
        background: var(--primary-gold);
        color: #000;
    }

    @media (max-width: 768px) {
        .hotel-hero-content h1 { font-size: 2.8rem; }
        .regions-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="hotels-page">
    <section class="hotel-hero" style="background-image: url('https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=1920');">
        <div class="hotel-hero-content animate__animated animate__zoomIn">
            <h1>فنادق الأردن</h1>
            <p style="font-size: 1.3rem; opacity: 0.9;">إقامة استثنائية تجمع بين الأصالة والفخامة العالمية</p>
        </div>
    </section>

    <section class="regions-container">
        <h2 class="section-title">وجهات الإقامة المختارة</h2>
        <div class="regions-grid">
            <?php
            $hotel_regions = [
                ['id' => 'amman', 'name' => 'عمّان', 'desc' => 'فنادق عالمية وخدمات راقية في قلب العاصمة النابضة بالحياة', 'img' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQLnjmxTx4ULz3fgFOBrlFTs8UBoeSArCDLTg&s'],
                ['id' => 'deadsea', 'name' => 'البحر الميت', 'desc' => 'منتجعات صحية عالمية في أخفض بقعة على وجه الأرض للاسترخاء التام', 'img' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTwPVCTf6Lu_3YH9AZNFsSS4baGJiPhyVbbUw&s'],
                ['id' => 'aqaba', 'name' => 'العقبة', 'desc' => 'إطلالات بحرية ساحرة ومنتجعات شاطئية للعائلات والأزواج', 'img' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRfFcpGcs7Qz1xBgj7x_fyarX878k14cyW4Nw&s']
            ];

            foreach ($hotel_regions as $r):
            ?>
            <div class="region-card" onclick="window.location.href='region-hotels.php?region=<?php echo $r['id']; ?>'">
                <div class="region-img-wrapper">
                    <img src="<?php echo $r['img']; ?>" alt="<?php echo $r['name']; ?>" class="region-img">
                </div>
                <div class="region-info">
                    <h3><?php echo $r['name']; ?></h3>
                    <p style="color: var(--text-muted); margin-bottom: 25px; line-height: 1.7;"><?php echo $r['desc']; ?></p>
                    <a href="region-hotels.php?region=<?php echo $r['id']; ?>" class="btn-explore">
                        استكشف الفنادق <i class="fas fa-chevron-left"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>