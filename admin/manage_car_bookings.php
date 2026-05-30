<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db_config.php';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $status = ($_GET['action'] == 'approve') ? 'accepted' : 'rejected';
    
    $update_query = "UPDATE car_bookings SET status = '$status' WHERE id = '$id'";
    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('تم تحديث حالة الطلب بنجاح'); window.location='manage_car_bookings.php';</script>";
        exit();
    }
}

$query = "SELECT cb.*, u.name as user_name, c.car_name 
          FROM car_bookings cb
          JOIN users u ON cb.user_id = u.id
          JOIN cars c ON cb.car_id = c.id
          ORDER BY cb.booking_date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة طلبات السيارات | Meshrider Admin</title>
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
            top: 0; bottom: 0; right: 0;
            height: 100vh; 
            border-left: 1px solid var(--border-color);
            padding: 40px 24px;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar h2 { 
            color: var(--primary); 
            font-weight: 900; 
            text-align: center; 
            margin-bottom: 40px; 
            font-size: 1.6rem;
            text-shadow: 0 0 20px var(--primary-glow);
        }

        .sidebar ul { 
            list-style: none; 
            display: flex;
            flex-direction: column;
            gap: 8px;
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

        .sidebar ul li a:hover { 
            background: rgba(255, 255, 255, 0.02); 
            color: #fff; 
        }

        .sidebar ul li a.active-menu {
            background: var(--primary-glow);
            color: var(--primary);
            border-right: 3px solid var(--primary);
            padding-right: 15px;
        }

        .main-content { 
            margin-right: 280px; 
            width: calc(100% - 280px); 
            padding: 40px; 
            transition: all 0.3s ease;
        }

        .data-section { 
            background: var(--card-bg); 
            border-radius: 24px; 
            padding: 35px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .data-header-container {
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 20px;
        }

        .data-header-container h2 { 
            color: #fff; 
            font-weight: 900;
            font-size: 1.4rem; 
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table { 
            width: 100%; 
            border-collapse: collapse; 
        }

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
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        .btn-action {
            padding: 6px 16px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 700; 
            font-size: 0.85rem; 
            transition: all 0.2s ease; 
            display: inline-flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
            border: none;
        }
        
        .btn-approve { 
            background: var(--success-glow); 
            color: var(--success); 
            border: 1px solid rgba(34, 197, 94, 0.25); 
        }
        
        .btn-approve:hover {
            background: var(--success);
            color: #fff;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-reject { 
            background: var(--danger-glow); 
            color: var(--danger); 
            border: 1px solid rgba(239, 68, 68, 0.25); 
            margin-right: 6px; 
        }

        .btn-reject:hover {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .badge { 
            padding: 6px 14px; 
            border-radius: 8px; 
            font-size: 0.8rem; 
            font-weight: 700; 
            display: inline-block;
        }
        
        .badge-pending { 
            background: var(--warning-glow); 
            color: var(--warning); 
            border: 1px solid rgba(245, 158, 11, 0.2);
        }
        
        .badge-approved { 
            background: var(--success-glow); 
            color: var(--success); 
            border: 1px solid rgba(34, 197, 94, 0.2);
        }
        
        .badge-rejected { 
            background: var(--danger-glow); 
            color: var(--danger); 
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>MESHRIDER</h2>
    <ul>
        <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> الداشبورد</a></li>
        <li><a href="manage_trips.php"><i class="fa-solid fa-plane"></i> إدارة الرحلات</a></li>
        <li><a href="manage_cars.php"><i class="fa-solid fa-car"></i> إدارة السيارات</a></li>
        <li><a href="manage_bookings.php" class="active-menu"><i class="fa-solid fa-calendar-check"></i> الحجوزات</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-wallet"></i> الأرباح</a></li>
        <li><a href="messages.php"><i class="fa-solid fa-envelope"></i> الرسائل</a></li>
        <li style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
            </a>
        </li>
    </ul>
</div>

<div class="main-content">
    <div class="data-section">
        <div class="data-header-container">
            <h2><i class="fas fa-car-side" style="color: var(--primary);"></i> إدارة طلبات استئجار السيارات</h2>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>اسم العميل</th>
                        <th>السيارة المطلوبة</th>
                        <th>تاريخ الاستلام</th>
                        <th>تاريخ الترجيع</th>
                        <th>حالة الطلب</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                            <tr>
                                <td style="font-weight: 600; color: #fff;"><?php echo $row['user_name']; ?></td>
                                <td>
                                    <span style="background: rgba(255,255,255,0.03); padding: 4px 12px; border-radius: 6px; font-size: 0.85rem; font-weight: 600;">
                                        <?php echo $row['car_name']; ?>
                                    </span>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo $row['pickup_date']; ?></td>
                                <td style="color: var(--text-muted); font-size: 0.9rem;"><?php echo $row['return_date']; ?></td>
                                <td>
                                    <?php 
                                    if ($row['status'] == 'pending') {
                                        echo '<span class="badge badge-pending">قيد الانتظار</span>';
                                    } elseif ($row['status'] == 'accepted' || $row['status'] == 'approved') {
                                        echo '<span class="badge badge-approved">مقبول</span>';
                                    } else {
                                        echo '<span class="badge badge-rejected">مرفوض</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($row['status'] == 'pending') { ?>
                                        <a href="manage_car_bookings.php?action=approve&id=<?php echo $row['id']; ?>" 
                                           class="btn-action btn-approve" onclick="return confirm('هل أنت متأكد من قبول طلب الاستئجار هذا؟')">
                                            <i class="fas fa-check"></i> قبول
                                        </a>
                                        <a href="manage_car_bookings.php?action=reject&id=<?php echo $row['id']; ?>" 
                                           class="btn-action btn-reject" onclick="return confirm('هل أنت متأكد من رفض طلب الاستئجار هذا؟')">
                                            <i class="fas fa-times"></i> رفض
                                        </a>
                                    <?php } else { ?>
                                        <span style="font-size: 0.85rem; opacity: 0.4; font-weight: 600;"><i class="fa-solid fa-lock" style="margin-left: 4px;"></i> تمت المعالجة</span>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="6" style="padding: 60px; color: var(--text-muted); font-weight: 600;">لا توجد طلبات استئجار سيارات حالياً في النظام</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>