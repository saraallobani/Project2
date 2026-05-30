<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال بالقاعدة: " . mysqli_connect_error()); }

mysqli_set_charset($conn, 'utf8mb4');

$msg = isset($_GET['msg']) ? $_GET['msg'] : '';
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider | إدارة الحجوزات الشاملة</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #e67e22;
            --primary-glow: rgba(230, 126, 34, 0.2);
            --bg-dark: #0b0b0e;
            --card-bg: #141419;
            --input-bg: #1c1c24;
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --border-color: rgba(255, 255, 255, 0.05);

            --success: #22c55e;
            --success-glow: rgba(34, 197, 94, 0.15);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.15);
            --warning: #f59e0b;
            --warning-glow: rgba(245, 158, 11, 0.15);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--bg-dark);
            color: var(--text-main);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            background: var(--card-bg);
            position: fixed;
            top: 0;
            bottom: 0;
            right: 0;
            height: 100vh;
            border-left: 1px solid var(--border-color);
            padding: 40px 24px;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }

        .sidebar-header {
            margin-bottom: 40px;
            text-align: center;
        }

        .sidebar h2 {
            color: var(--primary);
            font-weight: 900;
            margin: 0;
            font-size: 1.6rem;
            text-shadow: 0 0 20px var(--primary-glow);
        }

        .sidebar ul {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-grow: 1;
        }

        .sidebar ul li a {
            color: var(--text-muted);
            text-decoration: none;
            padding: 14px 18px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .sidebar ul li a i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
        }

        .sidebar ul li a:hover {
            background: rgba(255, 255, 255, 0.02);
            color: #fff;
        }

        .sidebar ul li a.active {
            background: var(--primary-glow);
            color: var(--primary);
            border-right: 3px solid var(--primary);
            padding-right: 15px;
        }

        .main-content {
            margin-right: 280px;
            width: calc(100% - 280px);
            padding: 40px;
        }

        .bookings-switcher {
            display: flex;
            gap: 12px;
            margin-bottom: 25px;
            background: rgba(0,0,0,0.2);
            padding: 6px;
            border-radius: 14px;
            width: fit-content;
            border: 1px solid var(--border-color);
        }

        .switch-btn {
            background: transparent;
            border: none;
            color: var(--text-muted);
            padding: 10px 24px;
            font-size: 0.95rem;
            font-weight: 700;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .switch-btn:hover { color: #fff; }

        .switch-btn.active {
            background: var(--primary);
            color: #000 !important;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .data-section {
            background: var(--card-bg);
            border-radius: 24px;
            padding: 35px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
        }

        .data-header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }

        .data-section h4 {
            color: #fff;
            font-weight: 700;
            font-size: 1.3rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        /* تعديل جوهري لضمان الإخفاء والإظهار الفوري والدقيق */
        .booking-tab-content { 
            display: none !important; 
        }
        .booking-tab-content.active { 
            display: block !important; 
        }

        table { width: 100%; border-collapse: collapse; }

        th {
            padding: 16px 20px;
            color: var(--text-muted);
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            font-weight: 700;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.01);
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            text-align: center;
            color: var(--text-main);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:hover td { background: rgba(255, 255, 255, 0.01); }

        .btn-action {
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }

        .btn-approve {
            background: var(--success-glow);
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.2);
        }

        .btn-approve:hover { background: var(--success); color: #fff; }

        .btn-reject {
            background: var(--danger-glow);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
            margin-right: 8px;
        }

        .btn-reject:hover { background: var(--danger); color: #fff; }

        .badge {
            padding: 6px 14px;
            border-radius: 8px;
            font-size: 0.8rem;
            font-weight: 700;
            display: inline-block;
        }

        .badge-pending { background: var(--warning-glow); color: var(--warning); }
        .badge-approved { background: var(--success-glow); color: var(--success); }
        .badge-rejected { background: var(--danger-glow); color: var(--danger); }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>MESHRIDER</h2>
    </div>

    <ul>
        <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> <span>الداشبورد</span></a></li>
        <li><a href="manage_trips.php"><i class="fa-solid fa-plane-departure"></i> <span>إدارة الرحلات</span></a></li>
        <li><a href="manage_cars.php"><i class="fa-solid fa-car"></i> <span>إدارة السيارات</span></a></li>
        <li><a href="manage_bookings.php" class="active"><i class="fa-solid fa-calendar-check"></i> <span>الحجوزات</span></a></li>
        <li><a href="reports.php"><i class="fa-solid fa-wallet"></i> <span>الأرباح</span></a></li>
        <li><a href="messages.php"><i class="fa-solid fa-envelope"></i> <span>الرسائل</span></a></li>

        <li style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>تسجيل الخروج</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">

    <div class="bookings-switcher">
        <button class="switch-btn active" onclick="switchBookingType('trips-panel', this)">
            <i class="fa-solid fa-suitcase-rolling"></i> حجوزات الرحلات
        </button>
        <button class="switch-btn" onclick="switchBookingType('cars-panel', this)">
            <i class="fa-solid fa-car-side"></i> حجوزات السيارات
        </button>
    </div>

    <div class="data-section">

        <div class="data-header-container">
            <h4 id="panel-title-text">
                <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i>
                إدارة طلبات الحجز (قائمة المسافرين)
            </h4>

            <?php if($msg == 'updated'): ?>
                <span style="color: var(--success); font-weight: bold; font-size: 0.95rem; background: var(--success-glow); padding: 6px 16px; border-radius: 8px;">
                    ✅ تم تحديث الحالة بنجاح
                </span>
            <?php endif; ?>
        </div>

        <!-- ==================== أولاً: لوحة حجوزات الرحلات ==================== -->
        <div id="trips-panel" class="booking-tab-content active">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>رقم الحجز</th>
                            <th>اسم العميل</th>
                            <th>الرحلة</th>
                            <th>المقاعد المحجوزة</th> 
                            <th>رقم الهاتف</th>
                            <th>الدفع</th>
                            <th>التكلفة الإجمالية</th>
                            <th>تاريخ الحجز</th>
                            <th>الحالة</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $bookings = mysqli_query($conn, "
                        SELECT bookings.*, trips.title AS trip_title
                        FROM bookings
                        INNER JOIN trips ON bookings.trip_id = trips.id
                        ORDER BY
                        CASE WHEN bookings.status = 'pending' THEN 0 ELSE 1 END,
                        bookings.id DESC
                    ");

                    if(mysqli_num_rows($bookings) > 0) {
                        while($row = mysqli_fetch_assoc($bookings)):
                            $status_class = 'badge-pending';
                            $status_text = 'قيد الانتظار';

                            if(in_array($row['status'], ['approved', 'accepted', 'confirmed'])) {
                                $status_class = 'badge-approved';
                                $status_text = 'مقبول';
                            }
                            if(in_array($row['status'], ['rejected', 'cancelled'])) {
                                $status_class = 'badge-rejected';
                                $status_text = 'مرفوض';
                            }

                            $client_name = !empty($row['full_name']) ? $row['full_name'] : "مستخدم رقم (" . $row['user_id'] . ")";
                            $is_trip_ended = false;

                            if (!empty($row['trip_id'])) {
                                $trip_check = mysqli_query($conn, "SELECT end_datetime FROM trips WHERE id = {$row['trip_id']} LIMIT 1");
                                if ($trip_data = mysqli_fetch_assoc($trip_check)) {
                                    if (!empty($trip_data['end_datetime']) && strtotime($trip_data['end_datetime']) < time()) {
                                        $is_trip_ended = true;
                                    }
                                }
                            }
                    ?>
                        <tr>
                            <td style="font-weight: bold; color: #fff;">#<?php echo $row['id']; ?></td>
                            <td style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($client_name); ?></td>
                            <td>
                                <span style="background: rgba(255,255,255,0.03); padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; border: 1px solid var(--border-color);">
                                    <?php echo htmlspecialchars($row['trip_title']); ?>
                                </span>
                            </td>
                            <td style="font-weight: 600;">
                                <span style="color: #fff; background: rgba(230, 126, 34, 0.1); padding: 4px 10px; border-radius: 6px; border: 1px solid rgba(230, 126, 34, 0.2);">
                                    <?php echo isset($row['seats_count']) ? $row['seats_count'] : '1'; ?> مقاعد
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row['phone']); ?></td>
                            <td><?php echo $row['payment_method'] == 'card' ? '💳 بطاقة' : '💵 كاش'; ?></td>
                            <td style="color: var(--primary); font-weight: 700;"><?php echo $row['total_price']; ?> JOD</td>
                            <td><?php echo date('Y-m-d h:i A', strtotime($row['created_at'])); ?></td>
                            <td><span class="badge <?php echo $status_class; ?>"><?php echo $status_text; ?></span></td>
                            <td>
                                <?php if(($row['status'] == 'pending' || $row['status'] == '') && !$is_trip_ended): ?>
                                    <a href="action.php?id=<?php echo $row['id']; ?>&status=approve&type=trip" class="btn-action btn-approve"><i class="fa-solid fa-check"></i> قبول</a>
                                    <a href="action.php?id=<?php echo $row['id']; ?>&status=reject&type=trip" class="btn-action btn-reject"><i class="fa-solid fa-xmark"></i> رفض</a>
                                <?php elseif($is_trip_ended): ?>
                                    <span style="font-size: 0.85rem; opacity: 0.5; font-weight: 600;"><i class="fa-solid fa-clock"></i> الرحلة انتهت</span>
                                <?php else: ?>
                                    <span style="font-size: 0.85rem; opacity: 0.4; font-weight: 600;"><i class="fa-solid fa-lock"></i> تمت المعالجة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='10' style='padding: 60px; color: var(--text-muted); font-weight: 600;'>لا توجد حجوزات رحلات مسجلة حالياً</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- ==================== ثانياً: لوحة حجوزات السيارات ==================== -->
        <div id="cars-panel" class="booking-tab-content">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>رقم الحجز</th>
                            <th>اسم العميل</th>
                            <th>السيارة المطلوبة</th>
                            <th>تاريخ الاستلام</th>
                            <th>تاريخ التسليم</th>
                            <th>رقم الهاتف</th>
                            <th>التكلفة الإجمالية</th>
                            <th>حالة الطلب</th>
                            <th>الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                 $car_bookings = mysqli_query($conn, "
    SELECT
        car_bookings.*,
        cars.car_type,
        cars.car_model,
        users.name AS user_real_name
    FROM car_bookings
    LEFT JOIN cars ON car_bookings.car_id = cars.id
    LEFT JOIN users ON car_bookings.user_id = users.id
    ORDER BY
        CASE
            WHEN car_bookings.status='pending' OR car_bookings.status='' THEN 0
            ELSE 1
        END,
        car_bookings.id DESC
");

if (!$car_bookings) {
    die(mysqli_error($conn));
}

                    if(mysqli_num_rows($car_bookings) > 0) {
                        while($crow = mysqli_fetch_assoc($car_bookings)):
                            $c_status_class = 'badge-pending';
                            $c_status_text = 'قيد الانتظار';

                            if(in_array($crow['status'], ['approved', 'accepted', 'confirmed'])) {
                                $c_status_class = 'badge-approved';
                                $c_status_text = 'مقبول';
                            }
                            if(in_array($crow['status'], ['rejected', 'cancelled'])) {
                                $c_status_class = 'badge-rejected';
                                $c_status_text = 'مرفوض';
                            }

                            $car_name = (!empty($crow['car_type']))
    ? htmlspecialchars($crow['car_type'] . ' ' . $crow['car_model'])
    : "سيارة غير معرفة (رقم ID: ".$crow['car_id'].")";
                                
                            $c_client = !empty($crow['user_real_name']) ? $crow['user_real_name'] : "مستخدم رقم (" . $crow['user_id'] . ")";
$c_phone = "--";                    ?>
                        <tr>
                            <td style="font-weight: bold; color: #fff;">#<?php echo $crow['id']; ?></td>
                            <td style="font-weight: 600; color: #fff;"><?php echo htmlspecialchars($c_client); ?></td>
                            <td>
                                <span style="background: rgba(230, 126, 34, 0.05); padding: 5px 12px; border-radius: 6px; font-size: 0.85rem; border: 1px solid rgba(230, 126, 34, 0.2); color: #fff;">
                                    <i class="fa-solid fa-car ms-1" style="color: var(--primary);"></i> <?php echo $car_name; ?>
                                </span>
                            </td>
                            <td style="color: var(--success); font-weight: 600;"><?php echo htmlspecialchars($crow['pickup_date']); ?></td>
                            <td style="color: var(--danger); font-weight: 600;"><?php echo htmlspecialchars($crow['return_date']); ?></td>
                            <td><?php echo htmlspecialchars($c_phone); ?></td>
                            <td style="color: var(--primary); font-weight: 700;"><?php echo $crow['total_price']; ?> JOD</td>
                            <td><span class="badge <?php echo $c_status_class; ?>"><?php echo $c_status_text; ?></span></td>
                            <td>
                                <?php if($crow['status'] == 'pending' || $crow['status'] == ''): ?>
                                    <a href="action.php?id=<?php echo $crow['id']; ?>&status=approve&type=car" class="btn-action btn-approve"><i class="fa-solid fa-check"></i> قبول</a>
                                    <a href="action.php?id=<?php echo $crow['id']; ?>&status=reject&type=car" class="btn-action btn-reject"><i class="fa-solid fa-xmark"></i> رفض</a>
                                <?php else: ?>
                                    <span style="font-size: 0.85rem; opacity: 0.4; font-weight: 600;"><i class="fa-solid fa-lock"></i> تمت المعالجة</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php 
                        endwhile;
                    } else {
                        echo "<tr><td colspan='9' style='padding: 60px; color: var(--text-muted); font-weight: 600;'>لا توجد حجوزات سيارات مسجلة حالياً في النظام</td></tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script>
function switchBookingType(panelId, btnElement) {
    // إخفاء كل التابات وإزالة كلاس النشاط من الأزرار بشكل حاسم
    const panels = document.querySelectorAll('.booking-tab-content');
    panels.forEach(panel => {
        panel.classList.remove('active');
    });

    const buttons = document.querySelectorAll('.switch-btn');
    buttons.forEach(btn => {
        btn.classList.remove('active');
    });

    // إظهار العنصر المطلوب وتنشيط الزر
    const targetPanel = document.getElementById(panelId);
    if(targetPanel) {
        targetPanel.classList.add('active');
    }
    
    btnElement.classList.add('active');

    // تغيير عنوان الهيدر بناءً على التاب
    const headerTitle = document.getElementById('panel-title-text');
    if(panelId === 'cars-panel') {
        headerTitle.innerHTML = '<i class="fa-solid fa-car" style="color: var(--primary);"></i> إدارة طلبات تأجير السيارات (طلبات الكابتن)';
    } else {
        headerTitle.innerHTML = '<i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i> إدارة طلبات الحجز (قائمة المسافرين)';
    }
}
</script>

</body>
</html>