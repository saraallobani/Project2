<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/header.php';
?>

<style>
    :root {
        --primary: #e67e22;
        --bg-dark: #080808;
        --card-bg: #141419;
        --text-pure: #ffffff;
        --text-muted: #a1a1aa;
        --border-color: rgba(255, 255, 255, 0.05);
    }

    .restaurants-page {
        background-color: var(--bg-dark);
        color: var(--text-pure);
        font-family: 'Cairo', sans-serif;
    }

    .hotel-hero {
        height: 60vh;
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
        top: 0; left: 0; width: 100%; height: 100%;
        background: linear-gradient(to bottom, rgba(0,0,0,0.3), var(--bg-dark));
    }

    .hotel-hero-content {
        position: relative;
        z-index: 10;
        max-width: 800px;
    }

    .hotel-hero-content h1 {
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 20px;
        text-shadow: 0 4px 15px rgba(0,0,0,0.5);
    }

    .regions-container {
        max-width: 1200px;
        margin: -50px auto 80px auto;
        padding: 0 20px;
        position: relative;
        z-index: 20;
    }

    .section-title {
        text-align: right;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 40px;
        color: var(--text-pure);
        border-right: 5px solid var(--primary);
        padding-right: 15px;
    }

    .regions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
    }

    .region-card {
        background: var(--card-bg);
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    .region-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary);
        box-shadow: 0 20px 40px rgba(230, 126, 34, 0.15);
    }

    .region-img-wrapper {
        height: 250px;
        overflow: hidden;
        position: relative;
    }

    .region-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s ease;
    }

    .region-card:hover .region-img {
        transform: scale(1.1);
    }

    .region-img-wrapper::after {
        content: '';
        position: absolute;
        bottom: 0; left: 0; width: 100%; height: 50%;
        background: linear-gradient(to top, var(--card-bg), transparent);
    }

    .region-info {
        padding: 25px;
        text-align: right;
    }

    .region-info h3 {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 12px;
        color: var(--text-pure);
    }

    .region-info p {
        color: var(--text-muted);
        font-size: 0.95rem;
        line-height: 1.6;
        margin-bottom: 20px;
        min-height: 45px;
    }

    .btn-explore {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(230, 126, 34, 0.1);
        color: var(--primary);
        padding: 10px 25px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 700;
        font-size: 0.9rem;
        border: 1px solid rgba(230, 126, 34, 0.2);
        transition: all 0.3s ease;
    }

    .region-card:hover .btn-explore {
        background: var(--primary);
        color: #fff;
    }

    @media (max-width: 768px) {
        .hotel-hero-content h1 { font-size: 2.5rem; }
        .regions-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="restaurants-page">
    <section class="hotel-hero"
        style="background-image: url('https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1920&q=80');">
        <div class="hotel-hero-content text-center animate__animated animate__fadeIn">
            <h1>مطاعم الأردن</h1>
            <p style="font-size: 1.2rem; color: #ddd;">اكتشف أشهى المأكولات وأفضل تجارب الطعام في مختلف مناطق الأردن</p>
        </div>
    </section>

    <section class="regions-container">
        <h2 class="section-title">اختر المنطقة للاستكشاف</h2>
        
        <div class="regions-grid">
            <?php
            $regions = [
                ['id' => 'amman', 'name' => 'عمّان', 'desc' => 'مزيج رائع من المطاعم الفاخرة والمأكولات الشعبية العريقة', 'img' => 'https://www.aljazeera.net/wp-content/uploads/2014/11/1c6a6470-ee8c-4af5-8c4e-ba0519c86295.jpeg?resize=686%2C513&quality=80'],
                ['id' => 'aqaba', 'name' => 'العقبة', 'desc' => 'أشهى المأكولات البحرية الطازجة على سواحل البحر الأحمر الساحرة', 'img' => 'https://aseza.jo/EBV4.0/Root_Storage/AR/%D8%AA%D8%B7%D9%88%D9%8A%D8%B1_%D8%A7%D9%84%D8%B9%D9%82%D8%A8%D8%A9.jpg'],
                ['id' => 'wadirum', 'name' => 'وادي رم', 'desc' => 'تجربة الزرب البدوي الأصيل تحت سماء الصحراء المرصعة بالنجوم', 'img' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQnDrR2ubRH-UxuonTaRUngHwy89VnSZajPWQ&s'],
                ['id' => 'jerash', 'name' => 'جرش', 'desc' => 'تناول طعامك وسط أجواء التاريخ العريق وجمال الطبيعة الخضراء', 'img' => 'https://iresizer.devops.arabiaweather.com/resize?url=https://adminassets.devops.arabiaweather.com/sites/default/files/field/image/jarash.jpeg&size=650x450'],
                ['id' => 'irbid', 'name' => 'إربد', 'desc' => 'أفضل الوجهات العائلية والمطاعم الريفية ذات الإطلالات الخلابة', 'img' => 'https://www.just.edu.jo/Units_and_offices/Offices/IRO/PublishingImages/Pages/6b737254f37de82f9ee30fa9782a22cf.jpg'],
                ['id' => 'deadsea', 'name' => 'البحر الميت', 'desc' => 'تجارب طعام عالمية وفاخرة في أخفض بقعة على وجه الأرض', 'img' => 'https://modo3.com/thumbs/fit630x300/84392/1584879035/%D8%A3%D9%87%D9%85%D9%8A%D8%A9_%D8%A7%D9%84%D8%A8%D8%AD%D8%B1_%D8%A7%D9%84%D9%85%D9%8A%D8%AA.jpg']
            ];

            foreach ($regions as $r):
            ?>
            <div class="region-card" onclick="window.location.href='region-restaurants.php?region=<?php echo $r['id']; ?>'">
                <div class="region-img-wrapper">
                    <img src="<?php echo $r['img']; ?>" alt="<?php echo $r['name']; ?>" class="region-img">
                </div>
                <div class="region-info">
                    <h3><?php echo $r['name']; ?></h3>
                    <p><?php echo $r['desc']; ?></p>
                    <a href="region-restaurants.php?region=<?php echo $r['id']; ?>" class="btn-explore">
                        استكشف الآن <i class="fas fa-arrow-left"></i>
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>

<script src="assets/js/restaurants.js"></script>
</body>
</html>