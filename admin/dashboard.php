<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال: " . mysqli_connect_error()); }

mysqli_set_charset($conn, 'utf8mb4');

$u_count_res = mysqli_query($conn, "SELECT COUNT(id) as total_users FROM users");
$u_row = mysqli_fetch_assoc($u_count_res);
$u_count = $u_row['total_users'] ?? 0;

$b_count_res = mysqli_query($conn, "SELECT COUNT(id) as total_bookings FROM bookings WHERE status IN ('pending', 'approved', 'accepted')");
$b_row = mysqli_fetch_assoc($b_count_res);
$b_count = $b_row['total_bookings'] ?? 0;
 
$income = 0;
$result = mysqli_query($conn, "SELECT SUM(total_price) as total_profit FROM bookings WHERE status='approved' OR status='accepted'");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    $income = $row['total_profit'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider Admin | لوحة التحكم</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #e67e22;
            --primary-glow: rgba(230, 126, 34, 0.2);
            --bg-dark: #0b0b0e;
            --card-bg: #141419;
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --border-color: rgba(255, 255, 255, 0.05);
            
            --accent: #f39c12;
            --success: #22c55e;
            --success-glow: rgba(34, 197, 94, 0.15);
            --danger: #ef4444;
            --danger-hover: #dc2626;
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
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            background: var(--card-bg);
            height: 100vh;
            position: fixed;
            right: 0;
            top: 0;
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

        .sidebar-header h2 {
            color: var(--primary);
            font-size: 1.6rem;
            font-weight: 900;
            margin: 0;
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
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 0.95rem;
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
            padding: 40px;
            width: calc(100% - 280px);
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .stat-box {
            background: var(--card-bg);
            padding: 25px;
            border-radius: 20px;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .stat-box:hover {
            transform: translateY(-5px);
            border-color: rgba(230, 126, 34, 0.25);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
        }

        .stat-box h3 { 
            color: var(--text-muted); 
            font-size: 0.9rem; 
            margin: 0; 
            font-weight: 600;
        }
        
        .stat-box p { 
            color: #fff; 
            font-size: 2.4rem; 
            font-weight: 800; 
            margin: 10px 0 0; 
            line-height: 1.2;
        }
        
        .stat-box i { 
            position: absolute; 
            left: 20px; 
            top: 50%;
            transform: translateY(-50%);
            font-size: 2.8rem; 
            opacity: 0.08; 
            color: var(--primary); 
            transition: all 0.3s ease;
        }
        
        .stat-box:hover i {
            opacity: 0.15;
            transform: translateY(-50%) scale(1.1);
        }

        .data-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 30px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .card-header h3 {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
            text-align: right;
        }
        
        th { 
            color: var(--text-main); 
            padding: 16px; 
            border-bottom: 2px solid var(--border-color); 
            font-weight: 700; 
            font-size: 0.9rem;
        }
        
        td { 
            padding: 16px; 
            border-bottom: 1px solid rgba(255,255,255,0.02); 
            color: var(--text-muted); 
            font-size: 0.9rem;
            vertical-align: middle;
        }
        
        tr:hover td { 
            background: rgba(255, 255, 255, 0.01); 
            color: #fff; 
        }

        .actions-wrapper {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .btn-action {
            background: var(--primary);
            color: #fff !important;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 700;
            text-decoration: none;
            font-size: 0.8rem;
            transition: all 0.2s ease;
            border: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .btn-action:hover {
            background: var(--accent);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px var(--primary-glow);
        }

        .btn-cancel {
            color: var(--danger) !important;
            text-decoration: none;
            font-size: 0.8rem;
            font-weight: 700;
            transition: all 0.2s ease;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .btn-cancel:hover {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger-hover) !important;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 0.75rem;
            font-weight: 600;
            background: rgba(243, 156, 18, 0.1);
            color: var(--accent);
            border: 1px solid rgba(243, 156, 18, 0.15);
            display: inline-block;
        }

        .small-details {
            display: block;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-top: 4px;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(230, 126, 34, 0.5); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
        }

        @media (max-width: 576px) {
            .stats-grid { grid-template-columns: 1fr; }
            .card-header { flex-direction: column; align-items: flex-start; gap: 10px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>MESHRIDER</h2>
    </div>
    <ul>
        <li><a href="dashboard.php" class="active"><i class="fa-solid fa-chart-pie"></i> <span>الداشبورد</span></a></li>
        <li><a href="manage_trips.php"><i class="fa-solid fa-plane-departure"></i> <span>إدارة الرحلات</span></a></li>
        <li><a href="manage_cars.php"><i class="fa-solid fa-car"></i> <span>إدارة السيارات</span></a></li>
        <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>الحجوزات</span></a></li>
        <li><a href="reports.php"><i class="fa-solid fa-wallet"></i> <span>الأرباح</span></a></li>
        <li><a href="messages.php"><i class="fa-solid fa-envelope"></i> <span>الرسائل</span></a></li>
        
        <li style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                <i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="stats-grid">
        <div class="stat-box">
            <h3>إجمالي المستخدمين</h3>
            <p><?php echo number_format($u_count); ?></p>
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-box">
            <h3>الحجوزات النشطة</h3>
            <p><?php echo number_format($b_count); ?></p>
            <i class="fa-solid fa-ticket"></i>
        </div>
        <div class="stat-box" style="border-color: rgba(34, 197, 94, 0.15);">
            <h3>صافي الأرباح الحقيقي</h3>
            <p style="color: var(--success); text-shadow: 0 0 15px var(--success-glow);"><?php echo number_format($income, 2); ?> <small style="font-size: 1rem; font-weight:600;">JOD</small></p>
            <i class="fa-solid fa-money-bill-trend-up" style="color: var(--success);"></i>
        </div>
    </div>

    <div class="data-card">
        <div class="card-header">
            <h3 style="margin:0;">📦 طلبات الحجز المعلقة وتفاصيل المسار</h3>
            <span style="color: var(--text-muted); font-size: 0.85rem;">آخر 10 طلبات بحاجة لإجراء</span>
        </div>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>المستكشف (العميل)</th>
                        <th>الوجهة والرحلة (لوين)</th>
                        <th>المقاعد المطلوبة</th>
                        <th>الكلفة الإجمالية</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query_bookings = "
                        SELECT b.*, u.name AS user_real_name, u.email AS user_email, t.title AS trip_title, t.location AS trip_location, t.price AS unit_price
                        FROM bookings b
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN trips t ON b.trip_id = t.id
                        WHERE b.status = 'pending' 
                        ORDER BY b.id DESC 
                        LIMIT 10
                    ";
                    
                    $res = mysqli_query($conn, $query_bookings);
                    
                    if (mysqli_num_rows($res) === 0): ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 30px; color: var(--text-muted);">
                                <i class="fa-solid fa-circle-check text-success mb-2" style="font-size: 2rem; display: block;"></i>
                                لا توجد طلبات حجز معلقة حالياً. الكل تمام!
                            </td>
                        </tr>
                    <?php 
                    endif;

                    while($row = mysqli_fetch_assoc($res)): 
                        $total_order_cost = intval($row['seats_count'] ?? 1) * floatval($row['unit_price'] ?? 0);
                    ?>
                    <tr>
                        <td>
                            <div style="font-weight: 600; color: #fff;">
                                <?php echo htmlspecialchars($row['user_real_name'] ?? $row['full_name'] ?? 'مستخدم غير معروف'); ?>
                            </div>
                            <span class="small-details"><i class="fa-regular fa-envelope"></i> <?php echo htmlspecialchars($row['user_email'] ?? 'بلا إيميل'); ?></span>
                        </td>
                        
                        <td>
                            <div style="font-weight: 600; color: var(--primary);">
                                <?php echo htmlspecialchars($row['trip_title'] ?? 'رحلة عامة'); ?>
                            </div>
                            <span class="small-details">
                                <i class="fa-solid fa-map-location-dot"></i> الوجهة: <strong><?php echo htmlspecialchars($row['trip_location'] ?? 'غير محددة'); ?></strong>
                            </span>
                        </td>
                        
                        <td style="text-align: center;">
                            <span style="background: rgba(255,255,255,0.05); padding: 4px 12px; border-radius: 6px; color: #fff; font-weight: 700;">
                                <?php echo intval($row['seats_count'] ?? 1); ?>
                            </span>
                        </td>

                        <td style="font-weight: 700; color: #fff;">
                            <?php echo number_format($total_order_cost, 2); ?> <span style="font-size: 0.75rem; color: var(--accent);">JOD</span>
                        </td>
                        
                        <td><span class="status-badge">قيد الانتظار</span></td>
                        
                        <td>
                            <div class="actions-wrapper">
                                <a href="action.php?id=<?php echo $row['id']; ?>&status=approve" class="btn-action">قبول الاعتماد</a>
                                <a href="action.php?id=<?php echo $row['id']; ?>&status=reject" class="btn-cancel" onclick="return confirm('هل أنت متأكد من رفض وإلغاء هذا الحجز؟')">إلغاء</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>