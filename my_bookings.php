<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

date_default_timezone_set('Asia/Amman');

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) {
    die("خطأ في الاتصال: " . mysqli_connect_error());
}

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

$now_time_str = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancel_booking_id'])) {

    $booking_id = intval($_POST['cancel_booking_id']);

    $check_trip = mysqli_query($conn, "SELECT trips.start_datetime FROM bookings LEFT JOIN trips ON bookings.trip_id = trips.id WHERE bookings.id = $booking_id");
    $trip_row = mysqli_fetch_assoc($check_trip);

    if ($trip_row && !empty($trip_row['start_datetime']) && $trip_row['start_datetime'] !== '0000-00-00 00:00:00' && $trip_row['start_datetime'] <= $now_time_str) {
        $_SESSION['error_msg'] = "لا يمكنك إلغاء حجز لرحلة بدأت أو انتهت بالفعل.";
    } else {
        $update_sql = "
            UPDATE bookings 
            SET status = 'cancelled' 
            WHERE id = $booking_id 
            AND status IN ('pending', 'confirmed', 'approved')
        ";

        if (mysqli_query($conn, $update_sql)) {
            $_SESSION['success_msg'] = "تم إلغاء الحجز بنجاح.";
        } else {
            $_SESSION['error_msg'] = "حدث خطأ أثناء محاولة إلغاء الحجز.";
        }
    }

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

include 'includes/header.php';

$sql = "
SELECT 
    bookings.*,
    trips.title,
    trips.location,
    trips.price,
    trips.duration,
    trips.image,
    trips.start_datetime,
    trips.end_datetime
FROM bookings
LEFT JOIN trips ON bookings.trip_id = trips.id
WHERE (
    bookings.user_id = $user_id
    OR bookings.user_id IS NULL
    OR bookings.user_id = 0
)
ORDER BY bookings.created_at DESC
";

$result = mysqli_query($conn, $sql);

$upcoming_bookings   = []; 
$ongoing_bookings    = []; 
$completed_bookings  = []; 
$rejected_bookings   = []; 
$cancelled_bookings  = []; 
$pending_bookings    = []; 
$all_bookings        = [];

while ($row = mysqli_fetch_assoc($result)) {

    $all_bookings[] = $row;

    if ($row['status'] === 'rejected') {
        $rejected_bookings[] = $row;
        continue;
    }

    if ($row['status'] === 'cancelled') {
        $cancelled_bookings[] = $row;
        continue;
    }

    if ($row['status'] === 'pending') {
        $pending_bookings[] = $row;
        continue;
    }

\    if ($row['status'] === 'confirmed' || $row['status'] === 'approved') {
        
        $start_str = (!empty($row['start_datetime']) && $row['start_datetime'] !== '0000-00-00 00:00:00') ? $row['start_datetime'] : '';
        $end_str   = (!empty($row['end_datetime']) && $row['end_datetime'] !== '0000-00-00 00:00:00') ? $row['end_datetime'] : '';

        if (!empty($end_str) && $now_time_str > $end_str) {
            $completed_bookings[] = $row;
            continue;
        }

        if (!empty($start_str) && !empty($end_str) && $now_time_str >= $start_str && $now_time_str <= $end_str) {
            $ongoing_bookings[] = $row;
            continue;
        }

        if (!empty($start_str) && empty($end_str) && $now_time_str >= $start_str) {
            $ongoing_bookings[] = $row;
            continue;
        }

        if (!empty($start_str) && $start_str > $now_time_str) {
            $upcoming_bookings[] = $row;
            continue;
        }
        
        $upcoming_bookings[] = $row;
    }
}

function getStatusBadgeCustom($status, $panel_type)
{
    if ($panel_type === 'completed') {
        return '<span class="status-badge status-expired"><i class="fas fa-check-double"></i> رحلة مكتملة </span>';
    }

    if ($panel_type === 'ongoing') {
        return '<span class="status-badge status-confirmed"><i class="fas fa-bus"></i> الرحلة جارية الآن </span>';
    }

    $statuses = [
        'pending' => [
            'label' => 'بانتظار موافقة الأدمن',
            'class' => 'status-pending',
            'icon'  => 'hourglass-half'
        ],
        'confirmed' => [
            'label' => 'الحجز مؤكد وجاهز',
            'class' => 'status-confirmed',
            'icon'  => 'check-circle'
        ],
        'approved' => [
            'label' => 'مقبول من الإدارة',
            'class' => 'status-confirmed',
            'icon'  => 'check-circle'
        ],
        'rejected' => [
            'label' => 'مرفوض من الإدارة',
            'class' => 'status-rejected',
            'icon'  => 'times-circle'
        ],
        'cancelled' => [
            'label' => 'تم إلغاء الحجز',
            'class' => 'status-cancelled',
            'icon'  => 'ban'
        ]
    ];

    $statusData = isset($statuses[$status]) ? $statuses[$status] : [
        'label' => $status,
        'class' => 'status-default',
        'icon'  => 'question'
    ];

    return '<span class="status-badge ' . $statusData['class'] . '"><i class="fas fa-' . $statusData['icon'] . '"></i> ' . $statusData['label'] . ' </span>';
}
?>

<style>
.bookings-page {
    background: #080808;
    color: #fff;
    font-family: 'Cairo', sans-serif;
    padding-bottom: 50px;
}

.page-header {
    text-align: center;
    margin-bottom: 40px;
    padding-top: 20px;
}

.page-header h1 {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.header-divider {
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, #e67e22, #f39c12);
    margin: 15px auto;
    border-radius: 2px;
}

.custom-tabs-nav {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
    background: rgba(20,20,20,0.6);
    padding: 8px;
    border-radius: 15px;
    border: 1px solid rgba(255,255,255,0.05);
    flex-wrap: wrap;
}

.tab-trigger {
    background: transparent;
    border: none;
    color: #888;
    padding: 12px 25px;
    font-size: 1rem;
    font-weight: 700;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 10px;
}

.tab-trigger:hover {
    color: #fff;
    background: rgba(255,255,255,0.03);
}

.tab-trigger.active {
    background: linear-gradient(135deg, #e67e22, #f39c12);
    color: #fff;
    box-shadow: 0 5px 15px rgba(230,126,34,0.25);
}

.tab-count {
    background: rgba(0,0,0,0.2);
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 0.8rem;
}

.tab-content-panel {
    display: none;
}

.tab-content-panel.active {
    display: block;
    animation: fadeIn 0.4s ease forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.bookings-container {
    background: rgba(20,20,20,0.8);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.05);
    border-radius: 20px;
    padding: 40px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}

.bookings-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0 15px;
}

.bookings-table thead th {
    color: #f39c12;
    padding: 18px 20px;
    text-align: right;
    font-weight: 700;
    font-size: 0.95rem;
    border-bottom: 2px solid rgba(230,126,34,0.3);
}

.bookings-table tbody tr {
    background: rgba(30,30,30,0.7);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.bookings-table tbody tr:hover {
    background: rgba(40,40,40,0.9);
    transform: translateY(-3px);
}

.bookings-table td {
    padding: 22px 20px;
    vertical-align: middle;
    text-align: right;
}

.booking-id {
    color: #b0b0b0;
    font-weight: 600;
}

.trip-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.trip-title {
    color: #fff;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 8px;
}

.trip-dates {
    color: #a0a0a0;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    gap: 8px;
}

.price-badge {
    background: linear-gradient(135deg, #e67e22, #f39c12);
    color: #fff;
    font-weight: 900;
    padding: 10px 16px;
    border-radius: 8px;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    border: 1.5px solid;
    white-space: nowrap;
}

.status-pending {
    background: rgba(0,195,255,0.08);
    color: #00c3ff;
    border-color: rgba(0,195,255,0.4);
}

.status-confirmed {
    background: rgba(46,204,113,0.08);
    color: #2ecc71;
    border-color: rgba(46,204,113,0.4);
}

.status-rejected {
    background: rgba(231,76,60,0.08);
    color: #e74c3c;
    border-color: rgba(231,76,60,0.4);
}

.status-cancelled {
    background: rgba(189,195,199,0.08);
    color: #bdc3c7;
    border-color: rgba(189,195,199,0.4);
}

.status-expired {
    background: rgba(155,89,182,0.1);
    color: #9b59b6;
    border-color: rgba(155,89,182,0.4);
}

.btn-cancel-booking {
    background: rgba(231,76,60,0.1);
    color: #e74c3c;
    border: 1px solid rgba(231,76,60,0.3);
    padding: 8px 14px;
    border-radius: 8px;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-cancel-booking:hover {
    background: #e74c3c;
    color: #fff;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #666;
}
</style>

<?php if(isset($_SESSION['success_msg'])): ?>
    <div style="background:rgba(46,204,113,0.2); color:#2ecc71; padding:15px; border-radius:10px; text-align:center; margin:20px auto; max-width:1200px; font-weight:bold;">
        <?php echo $_SESSION['success_msg']; unset($_SESSION['success_msg']); ?>
    </div>
<?php endif; ?>

<?php if(isset($_SESSION['error_msg'])): ?>
    <div style="background:rgba(231,76,60,0.2); color:#e74c3c; padding:15px; border-radius:10px; text-align:center; margin:20px auto; max-width:1200px; font-weight:bold;">
        <?php echo $_SESSION['error_msg']; unset($_SESSION['error_msg']); ?>
    </div>
<?php endif; ?>

<div class="bookings-page">
    <div class="container">

        <div class="page-header">
            <h1>حجوزاتي الشخصية</h1>
            <div class="header-divider"></div>
        </div>

        <div class="custom-tabs-nav">
            <button class="tab-trigger active" onclick="switchTab('upcoming-panel', this)">
                <i class="fas fa-calendar-check"></i> الرحلات القادمة
                <span class="tab-count"><?php echo count($upcoming_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('ongoing-panel', this)">
                <i class="fas fa-bus"></i> الرحلات الجارية
                <span class="tab-count"><?php echo count($ongoing_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('pending-panel', this)">
                <i class="fas fa-hourglass-half"></i> بانتظار الموافقة
                <span class="tab-count"><?php echo count($pending_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('completed-panel', this)">
                <i class="fas fa-check-double"></i> الرحلات المكتملة
                <span class="tab-count"><?php echo count($completed_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('rejected-panel', this)">
                <i class="fas fa-times-circle"></i> المرفوضة
                <span class="tab-count"><?php echo count($rejected_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('cancelled-panel', this)">
                <i class="fas fa-ban"></i> الملغية
                <span class="tab-count"><?php echo count($cancelled_bookings); ?></span>
            </button>

            <button class="tab-trigger" onclick="switchTab('all-panel', this)">
                <i class="fas fa-list"></i> كل السجل
                <span class="tab-count"><?php echo count($all_bookings); ?></span>
            </button>
        </div>

        <div class="bookings-container">

<?php
function renderBookingsTable($bookingsList, $panel_type)
{
    if (empty($bookingsList)) {
        echo '
        <div class="empty-state">
            <i class="fas fa-folder-open" style="font-size:3rem; margin-bottom:15px;"></i>
            <br>لا توجد حجوزات في هذا القسم حالياً
        </div>';
        return;
    }

    echo '
    <div class="table-responsive">
        <table class="bookings-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>تفاصيل الرحلة</th>
                    <th>المسافر</th>
                    <th>طريقة الدفع</th>
                    <th>السعر</th>
                    <th style="text-align:center;">الحالة</th>
                    <th style="text-align:center;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>';

    foreach ($bookingsList as $row) {
        $has_date = !empty($row['start_datetime']) && $row['start_datetime'] !== '0000-00-00 00:00:00';
        
        $current_panel = $panel_type;
        if($current_panel === 'all') {
            $now_str = date('Y-m-d H:i:s');
            $st = ($has_date) ? $row['start_datetime'] : '';
            $et = (!empty($row['end_datetime']) && $row['end_datetime'] !== '0000-00-00 00:00:00') ? $row['end_datetime'] : '';
            
            if($row['status'] === 'rejected') { $current_panel = 'rejected'; }
            elseif($row['status'] === 'cancelled') { $current_panel = 'cancelled'; }
            elseif($row['status'] === 'pending') { $current_panel = 'pending'; }
            elseif(!empty($et) && $now_str > $et) { $current_panel = 'completed'; }
            elseif(!empty($st) && !empty($et) && $now_str >= $st && $now_str <= $et) { $current_panel = 'ongoing'; }
            elseif(!empty($st) && empty($et) && $now_str >= $st) { $current_panel = 'ongoing'; }
            else { $current_panel = 'upcoming'; }
        }

        echo '
        <tr>
            <td class="booking-id">#' . $row['id'] . '</td>
            <td>
                <div class="trip-info">
                    <div class="trip-title">
                        <i class="fas fa-map-marker-alt" style="color:#e67e22;"></i>
                        ' . (!empty($row['title']) ? htmlspecialchars($row['title']) : 'رحلة غير معروفة') . '
                    </div>
                    <div class="trip-dates">
                        <i class="fas fa-calendar-alt"></i> الانطلاق: ' . ($has_date ? htmlspecialchars($row['start_datetime']) : 'غير محدد') . '
                    </div>
                </div>
            </td>
            <td>' . htmlspecialchars($row['full_name']) . '</td>
            <td><span style="color:#f39c12;">' . htmlspecialchars(ucfirst($row['payment_method'])) . '</span></td>
            <td><span class="price-badge">' . htmlspecialchars($row['total_price']) . ' JOD</span></td>
            
            <td style="text-align:center;">
                ' . getStatusBadgeCustom($row['status'], $current_panel) . '
            </td>

            <td style="text-align:center;">';

        if (in_array($current_panel, ['upcoming', 'pending']) && in_array($row['status'], ['pending', 'confirmed', 'approved'])) {
            echo '
            <form method="POST" onsubmit="return confirm(\'هل أنت متأكد من إلغاء الحجز؟\');" style="margin-bottom:8px;">
                <input type="hidden" name="cancel_booking_id" value="' . $row['id'] . '">
                <button type="submit" class="btn-cancel-booking">
                    <i class="fas fa-trash-alt"></i> إلغاء الحجز
                </button>
            </form>';
        }

        if ($current_panel === 'completed') {
            echo '
            <span style="display:block; background:rgba(46,204,113,0.1); color:#2ecc71; border:1px solid rgba(46,204,113,0.3); padding:10px; border-radius:8px; font-weight:bold; font-size:0.82rem;">
                <i class="fas fa-check-circle"></i> رحلة مكتملة
            </span>';
        } 
        elseif ($current_panel === 'ongoing') {
            echo '
            <a href="trip_details.php?id=' . $row['trip_id'] . '" style="display:block; background:#3498db; color:#fff; padding:10px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:0.82rem;">
                <i class="fas fa-location-arrow"></i> رحلة جارية الآن
            </a>';
        } 
        elseif ($current_panel === 'upcoming') {
            echo '
            <a href="trip_details.php?id=' . $row['trip_id'] . '" style="display:block; background:#ff9d47; color:#000; padding:10px; border-radius:8px; text-decoration:none; font-weight:bold; font-size:0.82rem;">
                <i class="fas fa-info-circle"></i> تفاصيل الرحلة
            </a>';
        } 
        else {
            echo '
            <span style="color:#555; font-size:0.85rem;">
                <i class="fas fa-lock"></i> مغلق
            </span>';
        }

        echo '
            </td>
        </tr>';
    }

    echo '
            </tbody>
        </table>
    </div>';
}
?>

            <div id="upcoming-panel" class="tab-content-panel active">
                <?php renderBookingsTable($upcoming_bookings, 'upcoming'); ?>
            </div>

            <div id="ongoing-panel" class="tab-content-panel">
                <?php renderBookingsTable($ongoing_bookings, 'ongoing'); ?>
            </div>

            <div id="pending-panel" class="tab-content-panel">
                <?php renderBookingsTable($pending_bookings, 'pending'); ?>
            </div>

            <div id="completed-panel" class="tab-content-panel">
                <?php renderBookingsTable($completed_bookings, 'completed'); ?>
            </div>

            <div id="rejected-panel" class="tab-content-panel">
                <?php renderBookingsTable($rejected_bookings, 'rejected'); ?>
            </div>

            <div id="cancelled-panel" class="tab-content-panel">
                <?php renderBookingsTable($cancelled_bookings, 'cancelled'); ?>
            </div>

            <div id="all-panel" class="tab-content-panel">
                <?php renderBookingsTable($all_bookings, 'all'); ?>
            </div>

        </div>
    </div>
</div>

<script>
function switchTab(panelId, element) {
    document.querySelectorAll('.tab-content-panel').forEach(panel => panel.classList.remove('active'));
    document.querySelectorAll('.tab-trigger').forEach(btn => btn.classList.remove('active'));

    document.getElementById(panelId).classList.add('active');
    element.classList.add('active');
}
</script>

<?php include 'includes/footer.php'; ?>