<?php 
date_default_timezone_set('Asia/Amman');

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("خطأ في الاتصال: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';
include 'includes/meshrider_support_bar.php';

$search = isset($_GET['query']) ? trim($_GET['query']) : '';
$search_esc = $search !== '' ? mysqli_real_escape_string($conn, $search) : '';

$sql = "SELECT * FROM trips";
if ($search_esc !== '') {
    $sql .= " WHERE title LIKE '%$search_esc%' OR location LIKE '%$search_esc%' OR IFNULL(description,'') LIKE '%$search_esc%'";
}
$sql .= " ORDER BY is_featured DESC, id DESC";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('خطأ في جلب الرحلات: ' . htmlspecialchars(mysqli_error($conn)));
}
$total_shown = mysqli_num_rows($result);
?>

<style>
    :root {
        --primary: #e67e22;
        --primary-gradient: linear-gradient(135deg, #e67e22, #f39c12);
        --dark-bg: #0a0a0a;
        --dark-card: #141414;
        --border-color: rgba(255, 255, 255, 0.06);
        --text-muted: #999;
        --success: #2ecc71;
        --info: #3498db;
        --danger: #e74c3c;
    }

    .trips-page {
        background-color: var(--dark-bg);
        color: #fff;
        font-family: 'Cairo', sans-serif;
        direction: rtl;
        text-align: right;
    }

    .trips-hero {
        padding: 50px 0 80px;
        background: linear-gradient(rgba(0,0,0,0.85), rgba(10,10,10,1)),
                    url('https://images.unsplash.com/photo-1541752171745-4196eead662e?q=80&w=1500') center/cover;
        text-align: center;
        border-bottom: 1px solid rgba(230, 126, 34, 0.15);
    }

    .search-panel {
        max-width: 850px;
        margin: -45px auto 45px;
        position: relative;
        z-index: 10;
        padding: 0 15px;
    }

    .search-panel-inner {
        background: #181818;
        border: 1px solid var(--border-color);
        border-radius: 24px;
        padding: 1.25rem;
        box-shadow: 0 25px 60px rgba(0,0,0,0.65);
    }

    .search-form-row {
        display: flex;
        flex-direction: row;
        align-items: center;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .search-input-wrap {
        flex: 1 1 280px;
        position: relative;
    }

    .search-input-wrap i {
        position: absolute;
        right: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary);
        font-size: 1.1rem;
        pointer-events: none;
    }

    .search-bar {
        width: 100%;
        background: #0f0f0f;
        border: 1px solid #2a2a2a;
        color: #fff;
        padding: 15px 48px 15px 16px;
        border-radius: 16px;
        outline: none !important;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    
    .search-bar:focus {
        border-color: var(--primary);
        background: #121212;
        box-shadow: 0 0 0 3px rgba(230, 126, 34, 0.15);
    }
    .search-bar::placeholder { color: #555; }

    .btn-search {
        background: var(--primary-gradient);
        color: #000 !important;
        border: none;
        border-radius: 16px;
        padding: 15px 32px;
        font-weight: 800;
        white-space: nowrap;
        box-shadow: 0 4px 15px rgba(230, 126, 34, 0.2);
        transition: all 0.3s ease;
    }
    .btn-search:hover { 
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(230, 126, 34, 0.35);
    }

    .btn-search-clear {
        background: #222;
        border: 1px solid #333;
        color: #aaa;
        border-radius: 16px;
        padding: 15px 24px;
        font-weight: 700;
        transition: all 0.3s ease;
    }
    .btn-search-clear:hover { 
        background: #2a2a2a;
        color: #fff;
        border-color: #444;
    }

    .trips-meta-line {
        font-size: 0.88rem;
        color: #777;
        margin-top: 1rem;
        text-align: center;
    }

    .trip-card {
        background: var(--dark-card) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 24px;
        overflow: hidden;
        transition: transform 0.4s cubic-bezier(0.165, 0.84, 0.44, 1), border-color 0.4s ease, box-shadow 0.4s ease;
        height: 100%;
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .trip-card:hover:not(.opacity-50) {
        transform: translateY(-10px);
        border-color: rgba(230, 126, 34, 0.4) !important;
        box-shadow: 0 25px 50px rgba(0,0,0,0.8), 0 10px 30px rgba(230, 126, 34, 0.08);
    }

    .image-container {
        position: relative;
        overflow: hidden;
        height: 240px;
        background-color: #222;
    }

    .trip-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .trip-card:hover:not(.opacity-50) .trip-image { 
        transform: scale(1.05); 
    }

    .price-tag {
        position: absolute;
        bottom: 16px;
        left: 16px;
        background: rgba(10, 10, 10, 0.8);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        color: #fff;
        padding: 6px 16px;
        border-radius: 12px;
        font-weight: 800;
        border: 1px solid rgba(255, 255, 255, 0.1);
        font-size: 1rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }
    
    .price-tag span {
        color: var(--primary);
        font-size: 1.1rem;
    }

    .card-body-content {
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .trip-meta-strip {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem 1rem;
        margin-bottom: 1rem;
        font-size: 0.82rem;
        color: var(--text-muted);
        align-items: center;
    }
    .trip-meta-strip span {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: rgba(255,255,255,0.03);
        padding: 4px 10px;
        border-radius: 8px;
        border: 1px solid rgba(255,255,255,0.02);
    }
    .trip-meta-strip i { color: var(--primary); }
    
    .trip-meta-strip .seats-count {
        background: rgba(46, 204, 113, 0.07);
        border: 1px solid rgba(46, 204, 113, 0.15);
        color: #2ecc71;
    }
    .trip-meta-strip .seats-count i { color: #2ecc71; }
    .trip-meta-strip .seats-count.full {
        background: rgba(231, 76, 60, 0.07);
        border: 1px solid rgba(231, 76, 60, 0.15);
        color: #e74c3c;
    }
    .trip-meta-strip .seats-count.full i { color: #e74c3c; }

    .trip-desc {
        color: #b0b0b0;
        font-size: 0.9rem;
        line-height: 1.6;
        margin-bottom: 1.25rem;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        min-height: 4.3rem;
    }

    .trip-timeline {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.04);
        border-radius: 16px;
        padding: 12px 14px;
        margin-bottom: 1.25rem;
        margin-top: auto;
    }
    .timeline-title {
        font-size: 0.78rem;
        color: #777;
        margin-bottom: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .timeline-track {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }
    .timeline-track::before {
        content: '';
        position: absolute;
        top: 14px;
        left: 15%;
        right: 15%;
        height: 2px;
        background: linear-gradient(90deg, var(--primary), #f39c12);
        opacity: 0.25;
        z-index: 1;
    }
    .timeline-node {
        position: relative;
        z-index: 2;
        text-align: center;
        flex: 1;
    }
    .timeline-dot {
        width: 10px;
        height: 10px;
        background: var(--primary);
        border-radius: 50%;
        margin: 0 auto 6px;
        box-shadow: 0 0 10px var(--primary);
        transition: all 0.3s ease;
    }
    .timeline-node.end .timeline-dot {
        background: #f39c12;
        box-shadow: 0 0 10px #f39c12;
    }
    
    .trip-card.status-ongoing .timeline-track::before {
        background: var(--info);
        opacity: 0.6;
    }
    .trip-card.status-ongoing .timeline-dot {
        background: var(--info) !important;
        box-shadow: 0 0 10px var(--info) !important;
    }

    .timeline-label {
        font-size: 0.75rem;
        color: #ccc;
        font-weight: 600;
        display: block;
        margin-bottom: 2px;
    }
    .timeline-date {
        font-size: 0.7rem;
        color: #666;
        display: block;
        font-family: monospace, sans-serif;
    }

    .btn-book {
        background: var(--primary-gradient);
        color: #000 !important;
        border: none;
        border-radius: 14px;
        padding: 14px;
        font-weight: 800;
        width: 100%;
        transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
        text-decoration: none;
        display: block;
        text-align: center;
        box-shadow: 0 4px 15px rgba(230, 126, 34, 0.15);
    }
    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(230, 126, 34, 0.35);
    }

    .btn-status-lock {
        width: 100%; 
        border-radius: 14px; 
        padding: 14px; 
        font-weight: 700;
        cursor: not-allowed;
        border: 1px solid transparent !important;
    }
    .btn-status-lock.expired {
        background: #1c1c1c !important; 
        border-color: #262626 !important;
        color: #555 !important;
    }
    .btn-status-lock.ongoing {
        background: rgba(52, 152, 219, 0.1) !important;
        border-color: rgba(52, 152, 219, 0.2) !important;
        color: var(--info) !important;
    }
    .btn-status-lock.full {
        background: rgba(231, 76, 60, 0.1) !important;
        border-color: rgba(231, 76, 60, 0.2) !important;
        color: var(--danger) !important;
    }

    .empty-state {
        padding: 100px 16px;
        text-align: center;
    }

    .featured-ribbon {
        position: absolute;
        top: 14px;
        right: 14px;
        background: var(--primary-gradient);
        color: #000;
        font-size: 0.72rem;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 10px;
        z-index: 2;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
    }
    
    .opacity-50 {
        opacity: 0.55;
    }
    .status-full {
        border-color: rgba(231, 76, 60, 0.2) !important;
    }
</style>

<div class="trips-page">
    <header class="trips-hero">
        <div class="container">
            <h1 class="display-5 fw-bold text-white mb-2">وجهات <span style="color: var(--primary);">MeshRider</span></h1>
            <p class="lead mb-0" style="color: #aaa;">رحلات منظّمة في الأردن — بحث سريع، تفاصيل، وخريطة لكل مسار</p>
        </div>
    </header>

    <div class="container pb-5">
        <div class="search-panel">
            <div class="search-panel-inner">
                <form action="trips.php" method="GET" class="search-form-row" dir="rtl">
                    <div class="search-input-wrap">
                        <i class="fas fa-search"></i>
                        <input type="text" name="query" class="search-bar" dir="rtl"
                               placeholder="ابحث باسم الرحلة، المدينة، المعلم، أو كلمة من الوصف…"
                               value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"
                               autocomplete="off">
                    </div>
                    <button type="submit" class="btn-search"><i class="fas fa-search ms-1"></i> بحث</button>
                    <?php if ($search !== ''): ?>
                        <a href="trips.php" class="btn-search-clear text-decoration-none d-inline-flex align-items-center justify-content-center">مسح</a>
                    <?php endif; ?>
                </form>
                <p class="trips-meta-line mb-0">
                    <?php if ($search !== ''): ?>
                         نتائج البحث عن «<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>»: <strong class="text-warning"><?php echo (int) $total_shown; ?></strong> رحلة
                    <?php else: ?>
                        عرض جميع الرحلات المتاحة: <strong class="text-warning"><?php echo (int) $total_shown; ?></strong> رحلة جاهزة للمغامرة
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="row g-4">
            <?php 
            if ($total_shown > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $title_safe = htmlspecialchars($row['title'] ?? '', ENT_QUOTES, 'UTF-8');
                    $loc_safe = htmlspecialchars($row['location'] ?? '', ENT_QUOTES, 'UTF-8');
                    $desc_raw = trim((string)($row['description'] ?? ''));
                    $desc_safe = $desc_raw !== '' ? htmlspecialchars(mb_substr($desc_raw, 0, 160, 'UTF-8'), ENT_QUOTES, 'UTF-8') . (mb_strlen($desc_raw, 'UTF-8') > 160 ? '…' : '') : '';
                    $dep = isset($row['departure_point']) ? trim((string) $row['departure_point']) : '';
                    $dep_safe = $dep !== '' ? htmlspecialchars($dep, ENT_QUOTES, 'UTF-8') : '';
                    $dur = isset($row['duration']) ? trim((string) $row['duration']) : '';
                    if ($dur === '' && isset($row['duration_days'])) {
                        $dur = (string) (int) $row['duration_days'];
                    }
                    $dur_safe = $dur !== '' ? htmlspecialchars($dur, ENT_QUOTES, 'UTF-8') : '';
                    $img_src = htmlspecialchars($row['image'] ?? '', ENT_QUOTES, 'UTF-8');
                    $price_val = $row['price'] ?? $row['price_per_person'] ?? 0;
                    $is_feat = !empty($row['is_featured']);
                    
                    $available_seats = isset($row['available_seats']) ? (int)$row['available_seats'] : 10; 

                    $start_dt = !empty($row['start_datetime']) ? $row['start_datetime'] : null;
                    $end_dt = !empty($row['end_datetime']) ? $row['end_datetime'] : null;
                    
                    $current_time_ts = time(); 
                    $start_dt_ts = $start_dt ? strtotime($start_dt) : 0;
                    $end_dt_ts = $end_dt ? strtotime($end_dt) : 0;

                    $is_expired = ($end_dt_ts !== 0 && $end_dt_ts < $current_time_ts); 
                    $is_ongoing = (!$is_expired && $start_dt_ts !== 0 && $end_dt_ts !== 0 && $current_time_ts >= $start_dt_ts && $current_time_ts <= $end_dt_ts); 
                    $is_full = ($available_seats <= 0 && !$is_expired && !$is_ongoing); 

                    $card_status_class = '';
                    if ($is_expired) {
                        $card_status_class = 'opacity-50 status-expired';
                    } elseif ($is_ongoing) {
                        $card_status_class = 'status-ongoing';
                    } elseif ($is_full) {
                        $card_status_class = 'status-full';
                    }
            ?>
                <div class="col-lg-4 col-md-6">
                    <div class="card trip-card <?php echo $card_status_class; ?>">
                        <?php if ($is_feat): ?>
                            <span class="featured-ribbon">مميّزة</span>
                        <?php endif; ?>
                        <div class="image-container">
                            <img src="<?php echo $img_src; ?>" class="trip-image" alt="<?php echo $title_safe; ?>" loading="lazy">
                            <div class="price-tag"><span><?php echo htmlspecialchars((string) $price_val, ENT_QUOTES, 'UTF-8'); ?></span> د.أ</div>
                        </div>
                        <div class="card-body-content text-end">
                            <h4 class="text-white fw-bold mb-2 h5" style="letter-spacing: -0.3px; line-height: 1.4;"><?php echo $title_safe; ?></h4>
                            
                            <div class="trip-meta-strip">
                                <?php if ($loc_safe !== ''): ?>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo $loc_safe; ?></span>
                                <?php endif; ?>
                                <?php if ($dur_safe !== ''): ?>
                                    <span><i class="fas fa-clock"></i> <?php echo $dur_safe; ?><?php echo preg_match('/^\d+$/u', $dur_safe) ? ' يوم' : ''; ?></span>
                                <?php endif; ?>
                                
                                <?php if ($is_expired): ?>
                                    <span class="seats-count full"><i class="fas fa-hourglass-end"></i> منتهية</span>
                                <?php elseif ($is_ongoing): ?>
                                    <span class="seats-count full" style="background: rgba(52, 152, 219, 0.07); border-color: rgba(52, 152, 219, 0.15); color: var(--info);"><i class="fas fa-running"></i> جارية الآن</span>
                                <?php elseif ($is_full): ?>
                                    <span class="seats-count full"><i class="fas fa-ban"></i> ممتلئة بالكامل</span>
                                <?php else: ?>
                                    <span class="seats-count"><i class="fas fa-users"></i> متبقي <?php echo $available_seats; ?> مقاعد</span>
                                <?php endif; ?>
                            </div>

                            <?php if ($desc_safe !== ''): ?>
                                <p class="trip-desc"><?php echo $desc_safe; ?></p>
                            <?php else: ?>
                                <p class="trip-desc">رحلة منظّمة — التفاصيل والمسار التفاعلي من الصفحة الداخلية.</p>
                            <?php endif; ?>

                            <?php if ($start_dt && $end_dt): ?>
                                <div class="trip-timeline text-end" dir="rtl">
                                    <div class="timeline-title">
                                        <i class="fas fa-calendar-alt text-warning"></i> الجدول الزمني للرحلة:
                                    </div>
                                    <div class="timeline-track">
                                        <div class="timeline-node start">
                                            <div class="timeline-dot"></div>
                                            <span class="timeline-label">الانطلاق</span>
                                            <span class="timeline-date"><?php echo date('m-d H:i', strtotime($start_dt)); ?></span>
                                        </div>
                                        <div class="timeline-node end">
                                            <div class="timeline-dot"></div>
                                            <span class="timeline-label">العودة</span>
                                            <span class="timeline-date"><?php echo date('m-d H:i', strtotime($end_dt)); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <div class="mt-auto">
                                <?php if ($is_expired): ?>
                                    <button class="btn-status-lock expired d-flex align-items-center justify-content-center gap-2" disabled>
                                        <i class="fas fa-hourglass-end"></i> انتهت الرحلة ⌛
                                    </button>
                                <?php elseif ($is_ongoing): ?>
                                    <button class="btn-status-lock ongoing d-flex align-items-center justify-content-center gap-2" disabled>
                                        <i class="fas fa-running"></i> الرحلة جارية الآن 🏃‍♂️
                                    </button>
                                <?php elseif ($is_full): ?>
                                    <button class="btn-status-lock full d-flex align-items-center justify-content-center gap-2" disabled>
                                        <i class="fas fa-user-slash"></i> الحجز مغلق (ممتلئة) 🚫
                                    </button>
                                <?php else: ?>
                                    <a href="trip_details.php?id=<?php echo (int) $row['id']; ?>" class="btn-book">
                                        التفاصيل والحجز <i class="fas fa-arrow-left me-1" style="font-size: 0.85rem;"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
            ?>
                <div class="col-12 empty-state">
                    <i class="fas fa-map-signs fa-3x mb-3" style="color: var(--primary); opacity: 0.6;"></i>
                    <h3 class="text-white">لا توجد نتائج مطابقة لبحثك</h3>
                    <p class="text-secondary">جرّب كلمات أخرى مثل «البتراء»، «وادي رم»، «عقبة»، أو قم بمسح الفلتر الحالي.</p>
                    <a href="trips.php" class="btn btn-warning fw-bold rounded-pill px-4 py-2 mt-2" style="background: var(--primary-gradient); border: none; color: #000;">عرض كل الرحلات</a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php 
if (isset($result)) {
    mysqli_free_result($result);
}
mysqli_close($conn);

include 'includes/footer.php'; 
?>