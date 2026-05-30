<?php 
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال: " . mysqli_connect_error()); }
if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die('تعذر ضبط ترميز الاتصال utf8mb4');
}

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

// تعريف ID الرحلة من الرابط وتأمينه
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = intval($_SESSION['user_id']);

// 1. جلب بيانات الرحلة أولاً
$query = "SELECT * FROM trips WHERE id = $id";
$result = mysqli_query($conn, $query);
$trip = mysqli_fetch_assoc($result);

if (!$trip) { 
    include 'includes/header.php';
    echo "<div class='container mt-5 py-5 text-center text-white' style='min-height:50vh;'>
            <div class='p-5 bg-dark rounded-3 border border-secondary shadow-lg'>
                <i class='fas fa-exclamation-triangle text-warning display-3 mb-3'></i>
                <h2>الرحلة المطلوبة غير موجودة أو تم إلغاؤها!</h2>
                <a href='trips.php' class='btn btn-warning px-4 py-2 mt-3 fw-bold rounded-pill'>العودة لاستكشاف الرحلات</a>
            </div>
          </div>";
    include 'includes/footer.php';
    exit;
}

// 2. حساب المقاعد المتاحة والمحجوزة ديناميكياً
$total_seats = isset($trip['total_seats']) ? intval($trip['total_seats']) : 30;

$count_bookings_query = mysqli_query($conn, "
    SELECT SUM(seats_count) AS total_booked FROM bookings 
    WHERE trip_id = $id AND status IN ('pending', 'confirmed', 'approved')
");
$bookings_data = mysqli_fetch_assoc($count_bookings_query);
$booked_seats = intval($bookings_data['total_booked'] ?? 0);

$available_seats = $total_seats - $booked_seats;
if ($available_seats < 0) { $available_seats = 0; }

// 3. التحقق من الحجز الخاص بالمستخدم الحالي لهذه الرحلة
$booking_query = mysqli_query($conn, "
    SELECT * FROM bookings 
    WHERE trip_id = $id
    AND (user_id = $user_id OR user_id IS NULL OR user_id = 0)
    AND status IN ('pending', 'confirmed', 'approved')
    LIMIT 1
");
$my_booking = mysqli_fetch_assoc($booking_query);

include 'includes/header.php';
include 'includes/meshrider_support_bar.php';

// تجهيز النصوص والبيانات الافتراضية
$departure_label = trim((string)($trip['departure_point'] ?? ''));
if ($departure_label === '') { $departure_label = 'عمان — يُحدَّد عند التأكيد'; }

$return_label = trim((string)($trip['return_point'] ?? ''));
if ($return_label === '') { $return_label = 'نقطة العودة كما في المسار على الخريطة'; }

$duration_label = trim((string)($trip['duration'] ?? ''));
if ($duration_label === '') { $duration_label = 'غير محدد'; } 
elseif (preg_match('/^\d+$/u', $duration_label)) { $duration_label .= ' أيام'; }

$location_label = trim((string)($trip['location'] ?? ''));

// الإحداثيات الجغرافية للخريطة
$defaultStartLat = '31.9539'; $defaultStartLng = '35.9106';
$destLat   = (!empty($trip['latitude']))  ? $trip['latitude']  : '31.9454';
$destLng   = (!empty($trip['longitude'])) ? $trip['longitude'] : '35.9284';
$startLat  = (!empty($trip['start_lat'])) ? $trip['start_lat'] : $defaultStartLat;
$startLng  = (!empty($trip['start_lng'])) ? $trip['start_lng'] : $defaultStartLng;
$returnLat = (!empty($trip['return_lat']))? $trip['return_lat']: $startLat;
$returnLng = (!empty($trip['return_lng']))? $trip['return_lng']: $startLng;

$trip_title_json = json_encode($trip['title'] ?? '', JSON_HEX_TAG | JSON_HEX_APOS | JSON_UNESCAPED_UNICODE);
if ($trip_title_json === false) { $trip_title_json = '""'; }

$start_dt = !empty($trip['start_datetime']) ? $trip['start_datetime'] : null;
$end_dt = !empty($trip['end_datetime']) ? $trip['end_datetime'] : null;

// بناء رابط مسار جوجل ماب الرسمي والمحسن
if (!empty($trip['departure_point']) && !empty($trip['location'])) {
    $origin_encoded = urlencode($trip['departure_point']);
    $destination_encoded = urlencode($trip['return_point'] ? $trip['return_point'] : $trip['location']);
    $waypoint_encoded = urlencode($trip['location']);
    $google_maps_route_url = "https://www.google.com/maps/dir/?api=1&origin={$origin_encoded}&destination={$destination_encoded}&waypoints={$waypoint_encoded}&travelmode=driving";
} else {
    $fallback_location = !empty($trip['location']) ? urlencode($trip['location']) : urlencode($trip['title']);
    $google_maps_route_url = "https://www.google.com/maps/search/?api=1&query={$fallback_location}";
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

<style>
    :root {
        --primary: #ff9d47; 
        --primary-glow: rgba(255, 157, 71, 0.25);
        --glass: rgba(15, 15, 15, 0.85);
        --glass-border: rgba(255, 255, 255, 0.1);
        --card-bg: #121212;
    }

    body { background-color: #000; color: #fff; font-family: 'Cairo', sans-serif; overflow-x: hidden; direction: rtl; text-align: right; }

    .details-wrapper {
        margin-top: 25px;
        background: var(--glass);
        border: 1px solid var(--glass-border);
        border-radius: 32px;
        overflow: hidden;
        box-shadow: 0 30px 70px rgba(0,0,0,0.9);
        backdrop-filter: blur(10px);
    }

    .trip-hero-img {
        height: 100%;
        min-height: 700px;
        object-fit: cover;
        mask-image: linear-gradient(to left, black 80%, transparent 100%);
        transition: transform 0.7s ease;
    }
    .details-wrapper:hover .trip-hero-img {
        transform: scale(1.02);
    }

    .info-card {
        background: var(--card-bg);
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #222;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .info-card:hover {
        border-color: var(--primary);
        background: #181818;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.4);
    }
    .info-card i { color: var(--primary); font-size: 1.6rem; margin-bottom: 10px; display: block; }
    .info-card h6 { color: #888; font-weight: 600; margin-bottom: 6px; font-size: 0.85rem; }
    .info-card p { color: #fff; font-weight: 700; font-size: 1.05rem; margin: 0; }

    .seats-counter-box {
        background: linear-gradient(135deg, #161616 0%, #0d0d0d 100%);
        border: 1px solid #262626;
        border-radius: 20px;
        padding: 15px 20px;
    }
    .seats-progressbar {
        height: 6px;
        background-color: #222;
        border-radius: 10px;
        overflow: hidden;
    }
    .seats-progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #ff9d47, #ff6b00);
        border-radius: 10px;
        transition: width 1s ease-in-out;
    }

    .booking-section {
        background: linear-gradient(145deg, rgba(255,157,71,0.07) 0%, rgba(10,10,10,0.5) 100%);
        padding: 30px;
        border-radius: 24px;
        border: 1px solid rgba(255,157,71,0.15);
    }
    .trip-price-big { font-size: 3rem; font-weight: 900; color: var(--primary); letter-spacing: -1px; }

    .leaflet-map-wrap {
        direction: ltr;
        unicode-bidi: isolate;
        height: 450px;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #222;
        box-shadow: 0 15px 40px rgba(0,0,0,0.5);
        margin-top: 15px;
        background: #111;
    }
    #map { height: 100%; width: 100%; z-index: 1; }

    .btn-grad {
        background: linear-gradient(45deg, #ff8000, #ffb366);
        color: #000 !important;
        font-weight: 800;
        border-radius: 50px;
        padding: 16px;
        font-size: 1.2rem;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 10px 25px rgba(255, 128, 0, 0.35);
    }
    .btn-grad:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(255, 128, 0, 0.5);
        background: linear-gradient(45deg, #ff9d47, #ffd9b3);
    }

    .btn-google-maps {
        background: rgba(255, 157, 71, 0.05);
        color: var(--primary) !important;
        font-weight: 700;
        border-radius: 50px;
        padding: 12px;
        font-size: 0.95rem;
        border: 1px dashed rgba(255, 157, 71, 0.3);
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.25s ease;
    }
    .btn-google-maps:hover {
        background: var(--primary);
        color: #000 !important;
        border-style: solid;
        transform: translateY(-2px);
    }

    .route-panel {
        background: #0d0d0f;
        border: 1px solid rgba(255,157,71,0.12);
        border-radius: 20px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
    }
    .route-panel h3 { font-size: 1rem; font-weight: 800; color: var(--primary); margin-bottom: 1rem; }
    .route-steps { display: flex; flex-direction: column; }
    .route-step {
        display: grid;
        grid-template-columns: auto 1fr;
        gap: 1rem;
        align-items: start;
        padding: 0.85rem 0;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }
    .route-step:last-child { border-bottom: none; padding-bottom: 0; }
    .route-step:first-child { padding-top: 0; }
    
    .route-icon {
        width: 38px; height: 38px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1rem;
    }
    .route-icon.start { background: rgba(46, 204, 113, 0.1); color: #2ecc71; }
    .route-icon.mid { background: rgba(255, 157, 71, 0.1); color: var(--primary); }
    .route-icon.end { background: rgba(231, 76, 60, 0.1); color: #e74c3c; }
    
    .route-step h5 { font-size: 0.8rem; color: #777; margin: 0 0 2px; font-weight: 700; }
    .route-step p { margin: 0; color: #fff; font-weight: 700; font-size: 0.95rem; }
    
    .time-badge {
        display: inline-block;
        background: rgba(255, 157, 71, 0.08);
        color: var(--primary);
        font-size: 0.75rem;
        padding: 2px 8px;
        border-radius: 6px;
        margin-top: 4px;
        border: 1px solid rgba(255, 157, 71, 0.15);
    }

    .extras-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; }
    .extra-pill {
        background: #0f0f0f;
        border: 1px solid #222;
        border-radius: 14px;
        padding: 10px 14px;
    }
    .extra-pill small { display: block; color: #666; font-size: 0.7rem; margin-bottom: 2px; }
    .extra-pill strong { color: #ddd; font-size: 0.82rem; font-weight: 700; }
</style>

<div class="container py-4">

    <?php if ($my_booking): ?>
        <div class="alert alert-success text-center border-0 shadow mb-4" style="border-radius:16px; background: rgba(46, 204, 113, 0.15); color: #2ecc71;">
            <i class="fas fa-check-circle class me-2"></i> <strong>أهلاً بك كابتن!</strong> تم تأكيد وقبول مقعدك في هذه الرحلة. نحن بانتظارك بحماس!
            <br><small class="text-white-50">تاريخ تسجيل الحجز: <?php echo date('Y-m-d H:i', strtotime($my_booking['created_at'])); ?></small>
        </div>
    <?php endif; ?>

    <div class="details-wrapper">
        <div class="row g-0">
            <div class="col-lg-6 d-none d-lg-block overflow-hidden">
                <img src="<?php echo htmlspecialchars($trip['image'] ?? 'assets/img/default-trip.jpg', ENT_QUOTES, 'UTF-8'); ?>" class="trip-hero-img w-100" alt="صورة الرحلة">
            </div>

            <div class="col-lg-6 p-md-5 p-4 text-end d-flex flex-column justify-content-between">
                <div>
                    <div class="mb-3">
                        <span class="badge bg-warning text-dark fw-bold px-3 py-2 rounded-pill">
                            <i class="fas fa-motorcycle me-1"></i> رحلة مغامرة مميزة
                        </span>
                    </div>
                    
                    <h1 class="display-6 fw-bold mb-3 text-white"><?php echo htmlspecialchars($trip['title']); ?></h1>
                    <p class="text-secondary fs-6 mb-4">
                        <i class="fas fa-map-pin text-warning ms-2"></i>
                        الوجهة الأساسية: <span class="text-white fw-bold"><?php echo htmlspecialchars($location_label); ?></span>
                    </p>

                    <div class="route-panel">
                        <h3><i class="fas fa-route ms-2"></i> مخطط مسار الحركة والجدول الزمني</h3>
                        <div class="route-steps">
                            <div class="route-step">
                                <div class="route-icon start"><i class="fas fa-play"></i></div>
                                <div>
                                    <h5>نقطة التجمع والانطلاق</h5>
                                    <p><?php echo htmlspecialchars($departure_label); ?></p>
                                    <?php if ($start_dt): ?>
                                        <div class="time-badge">
                                            <i class="far fa-clock ms-1"></i> المغادرة: <?php echo date('Y-m-d | h:i A', strtotime($start_dt)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="route-step">
                                <div class="route-icon mid"><i class="fas fa-map-marker-alt"></i></div>
                                <div>
                                    <h5>المحطة السياحية المستهدفة</h5>
                                    <p><?php echo htmlspecialchars($location_label); ?></p>
                                </div>
                            </div>
                            <div class="route-step">
                                <div class="route-icon end"><i class="fas fa-flag-checkered"></i></div>
                                <div>
                                    <h5>نقطة النهاية والعودة</h5>
                                    <p><?php echo htmlspecialchars($return_label); ?></p>
                                    <?php if ($end_dt): ?>
                                        <div class="time-badge">
                                            <i class="far fa-clock ms-1"></i> العودة المتوقعة: <?php echo date('Y-m-d | h:i A', strtotime($end_dt)); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 mb-4 text-center">
                        <div class="col-6">
                            <div class="info-card">
                                <i class="fas fa-hourglass-half"></i>
                                <h6>مدة الرحلة</h6>
                                <p><?php echo htmlspecialchars($duration_label); ?></p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-card">
                                <i class="fas fa-shield-alt"></i>
                                <h6>الدعم والحماية</h6>
                                <p>مرافقة كاملة للمسار</p>
                            </div>
                        </div>
                    </div>

                    <div class="seats-counter-box mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-secondary small fw-bold">حالة توفر المقاعد بالرحلة:</span>
                            <?php if ($available_seats > 0): ?>
                                <span class="badge bg-success rounded-pill px-2">متاح حالياً</span>
                            <?php else: ?>
                                <span class="badge bg-danger rounded-pill px-2">ممتلئة بالكامل</span>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex justify-content-between align-items-baseline mb-2">
                            <div>
                                <span class="text-white fs-4 fw-bold"><?php echo $available_seats; ?></span>
                                <span class="text-muted small"> / <?php echo $total_seats; ?> مقعد متبقي</span>
                            </div>
                            <small class="text-warning fw-bold"><?php echo $booked_seats; ?> محجوزة</small>
                        </div>
                        <div class="seats-progressbar">
                            <?php 
                                $fill_percentage = ($total_seats > 0) ? ($booked_seats / $total_seats) * 100 : 100; 
                            ?>
                            <div class="seats-progress-fill" style="width: <?php echo $fill_percentage; ?>%;"></div>
                        </div>
                    </div>

                    <div class="extras-grid mb-4">
                        <div class="extra-pill">
                            <small>نوع التأكيد</small>
                            <strong>فوري بعد الدفع</strong>
                        </div>
                        <div class="extra-pill">
                            <small>المساعد الذكي</small>
                            <strong>شات دعم مباشر متوفر</strong>
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <p class="small text-secondary m-0">كلفة الاشتراك المقررة للفرد</p>
                        <div>
                            <span class="trip-price-big"><?php echo number_format($trip['price'], 2); ?></span>
                            <span class="text-warning fw-bold ms-1">JOD</span>
                        </div>
                    </div>

                    <div id="countdown-box" class="p-3 mb-3 rounded-3 text-center" style="background: rgba(0,0,0,0.4); border: 1px solid #222;">
                        <span class="text-white-50 small d-block mb-1" id="status-label"><i class="fas fa-hourglass-start text-warning ms-1"></i> جارٍ احتساب الوقت...</span>
                        <div id="countdown-timer" class="text-warning fw-bold fs-5">...</div>
                    </div>

                    <div class="mb-3">
                        <a href="<?php echo $google_maps_route_url; ?>" target="_blank" class="btn-google-maps">
                            <i class="fas fa-map-marked-alt"></i> تتبع الخارطة عبر تطبيق Google Maps
                        </a>
                    </div>
                    
                    <?php 
                    $current_time = date('Y-m-d H:i:s');
                    $is_trip_started = ($start_dt && $start_dt <= $current_time);

                    if ($my_booking): ?>
                        <div class="alert alert-success text-center m-0 py-3 rounded-pill fw-bold" style="background: rgba(46,204,113,0.1); border:1px solid #2ecc71; color:#2ecc71;">
                            <i class="fas fa-check"></i> تم تسجيلك بنجاح في الرحلة
                        </div>
                    <?php elseif ($is_trip_started): ?>
                        <div class="alert alert-danger text-center m-0 py-3 rounded-pill fw-bold" style="background: rgba(231,76,60,0.1); border:1px solid #e74c3c; color:#e74c3c;">
                            <i class="fas fa-ban"></i> عذراً، بدأت الرحلة بالفعل وأغلق باب الحجز.
                        </div>
                    <?php elseif ($available_seats <= 0): ?>
                        <div class="alert alert-warning text-center m-0 py-3 rounded-pill fw-bold" style="background: rgba(241,196,15,0.1); border:1px solid #f1c40f; color:#f1c40f;">
                            <i class="fas fa-user-slash"></i> عذراً، نفدت كافة المقاعد المتاحة لهذه الرحلة.
                        </div>
                    <?php else: ?>
                        <a href="checkout.php?trip_id=<?php echo $trip['id']; ?>" class="btn btn-grad w-100 py-3">
                            تأكيد المقعد وحجز مكانك الآن <i class="fas fa-chevron-left mr-2"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="p-lg-5 p-4" style="background: #080808; border-top: 1px solid #1a1a1a;">
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h3 class="fw-bold m-0 text-white"><i class="fas fa-map-marked-alt text-warning ms-2"></i> مسار الرحلة الجغرافي التفاعلي</h3>
                <div class="d-flex gap-3 bg-dark p-2 px-3 rounded-pill border border-secondary text-white-50 small">
                    <span><i class="fas fa-circle text-success ms-1"></i> التجمع</span>
                    <span><i class="fas fa-circle text-warning ms-1"></i> المحطة</span>
                    <span><i class="fas fa-circle text-danger ms-1"></i> العودة</span>
                </div>
            </div>
            <p class="text-secondary small mb-3">يمكنك تحريك، تقريب، واستكشاف نقاط التوقف الكاملة المخططة لخط سير الرحلة على الخريطة أدناه.</p>
            <div class="leaflet-map-wrap">
                <div id="map" aria-label="خريطة مسار الرحلة"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

<script>
(function() {
    const startDateTime = new Date("<?php echo $start_dt; ?>").getTime();
    const endDateTime = new Date("<?php echo $end_dt; ?>").getTime();

    function updateCountdown() {
        const now = new Date().getTime();
        const statusLabel = document.getElementById("status-label");
        const timerDisplay = document.getElementById("countdown-timer");
        if(!timerDisplay) return;

        let distance;
        if (now < startDateTime) {
            distance = startDateTime - now;
            statusLabel.innerHTML = "<i class='fas fa-hourglass-start text-warning ms-1'></i> الوقت المتبقي لانطلاق التجمع الخاص بالرحلة:";
        } else if (now >= startDateTime && now < endDateTime) {
            distance = endDateTime - now;
            statusLabel.innerHTML = "<i class='fas fa-motorcycle text-success ms-1'></i> الرحلة جارية حالياً على المسار - متبقي على العودة:";
        } else {
            statusLabel.innerHTML = "<i class='fas fa-calendar-check text-secondary ms-1'></i> حالة الجدول الزمني:";
            timerDisplay.innerHTML = "هذه الرحلة انتهت بسلامة الله وعادت لعمان.";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        timerDisplay.innerHTML = days + " يوم و " + hours + " ساعة و " + minutes + " دقيقة و " + seconds + " ثانية ";
    }
    setInterval(updateCountdown, 1000);
    updateCountdown();
})();

(function () {
    const start = [<?php echo (float)$startLat; ?>, <?php echo (float)$startLng; ?>];
    const dest   = [<?php echo (float)$destLat; ?>, <?php echo (float)$destLng; ?>];
    const back   = [<?php echo (float)$returnLat; ?>, <?php echo (float)$returnLng; ?>];
    const tripTitle = <?php echo $trip_title_json; ?>;

    function initTripMap() {
        var el = document.getElementById('map');
        // الـ شرط الآن سيمر بنجاح لأن المكتبة متوفرة
        if (!el || typeof L === 'undefined') return;

        var map = L.map('map', { scrollWheelZoom: true, zoomControl: true }).setView(dest, 9);

        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap © CARTO',
            maxZoom: 19
        }).addTo(map);

        function createIcon(color) {
            return new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-' + color + '.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
            });
        }

        L.marker(start, { icon: createIcon('green') }).addTo(map).bindPopup('<b dir="rtl">نقطة التجمع والانطلاق المقررة</b>');
        L.marker(dest, { icon: createIcon('gold') }).addTo(map).bindPopup('<b dir="rtl">الوجهة السياحية: ' + String(tripTitle) + '</b>').openPopup();
        L.marker(back, { icon: createIcon('red') }).addTo(map).bindPopup('<b dir="rtl">نقطة الوصول والعودة النهائية</b>');

        var routeLine = L.polyline([start, dest, back], {
            color: '#ff9d47', weight: 5, opacity: 0.85, dashArray: '10, 12', lineJoin: 'round'
        }).addTo(map);

        map.fitBounds(routeLine.getBounds(), { padding: [50, 50], maxZoom: 12 });
        
        function fixSize() {
            map.invalidateSize(true);
            map.fitBounds(routeLine.getBounds(), { padding: [50, 50], maxZoom: 12 });
        }
        setTimeout(fixSize, 150);
        window.addEventListener('resize', function () { map.invalidateSize(true); });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initTripMap);
    } else {
        initTripMap();
    }
})();
</script>

<?php include 'includes/footer.php'; ?>