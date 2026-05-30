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

$result = mysqli_query($conn, "SELECT SUM(total_price) as total_profit FROM bookings WHERE status='approved' OR status='accepted'");
$data = mysqli_fetch_assoc($result);
$total = $data['total_profit'] ?? 0;

$approved = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings WHERE status='approved' OR status='accepted'"));
$pending  = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings WHERE status='pending'"));
$cancelled = mysqli_num_rows(mysqli_query($conn, "SELECT id FROM bookings WHERE status='cancelled' OR status='rejected'"));

$total_bookings = $approved + $pending + $cancelled;

$acceptance_rate = $total_bookings > 0 ? round(($approved / $total_bookings) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider | التقارير والأرباح</title>

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
            
            --success: #22c55e;
            --success-glow: rgba(34, 197, 94, 0.1);
            --warning: #f59e0b;
            --warning-glow: rgba(245, 158, 11, 0.1);
            --danger: #ef4444;
            --danger-glow: rgba(239, 68, 68, 0.1);
            --info: #3b82f6;
            --info-glow: rgba(59, 130, 246, 0.1);
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
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .profit-card {
            background: linear-gradient(135deg, #141419, #1c1c24);
            padding: 45px;
            border-radius: 24px;
            text-align: center;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            position: relative;
            overflow: hidden;
            margin-bottom: 35px;
        }

        .profit-card::after {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 200px; height: 200px;
            background: var(--primary-glow);
            filter: blur(80px);
            border-radius: 50%;
            pointer-events: none;
        }

        .profit-card h3 {
            color: var(--text-muted);
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .profit-card h1 {
            font-size: 3.2rem;
            font-weight: 900;
            color: #fff;
            letter-spacing: -1px;
            text-shadow: 0 4px 20px rgba(0,0,0,0.2);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 30px 25px;
            border-radius: 20px;
            text-align: right;
            border: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: relative;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0,0,0,0.3);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .stat-card h4 {
            color: var(--text-muted);
            font-size: 0.95rem;
            font-weight: 600;
        }

        .stat-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .stat-card p {
            font-size: 2.2rem;
            font-weight: 900;
            line-height: 1;
        }

        .green-zone { color: var(--success); }
        .green-zone .stat-icon { background: var(--success-glow); color: var(--success); }

        /* تم تعديل العلامة هنا من $ إلى . لتصحيح الـ CSS */
        .orange-zone { color: var(--warning); }
        .orange-zone .stat-icon { background: var(--warning-glow); color: var(--warning); }

        .red-zone { color: var(--danger); }
        .red-zone .stat-icon { background: var(--danger-glow); color: var(--danger); }

        .info-zone { color: var(--info); }
        .info-zone .stat-icon { background: var(--info-glow); color: var(--info); }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(230, 126, 34, 0.5); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
            .profit-card h1 { font-size: 2.5rem; }
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
        <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>الحجوزات</span></a></li>
        <li><a href="reports.php" class="active"><i class="fa-solid fa-wallet"></i> <span>الأرباح</span></a></li>
        <li><a href="messages.php"><i class="fa-solid fa-envelope"></i> <span>الرسائل</span></a></li>
        
        <li style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                <i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">

    <div class="profit-card">
        <h3><i class="fa-solid fa-vault" style="color: var(--primary);"></i> صافي الأرباح المعتمدة في النظام</h3>
        <h1><?php echo number_format($total, 2); ?> <span style="font-size: 1.5rem; font-weight: 700; color: var(--primary);">JOD</span></h1>
    </div>

    <div class="stats">

        <div class="stat-card green-zone">
            <div class="stat-header">
                <h4>الحجوزات المقبولة</h4>
                <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            </div>
            <p><?php echo $approved; ?></p>
        </div>

        <div class="stat-card orange-zone">
            <div class="stat-header">
                <h4>الحجوزات المعلقة</h4>
                <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            </div>
            <p><?php echo $pending; ?></p>
        </div>

        <div class="stat-card red-zone">
            <div class="stat-header">
                <h4>الحجوزات الملغية</h4>
                <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
            </div>
            <p><?php echo $cancelled; ?></p>
        </div>

        <div class="stat-card info-zone">
            <div class="stat-header">
                <h4>نسبة القبول العامة</h4>
                <div class="stat-icon"><i class="fa-solid fa-chart-line"></i></div>
            </div>
            <p><?php echo $acceptance_rate; ?><span style="font-size: 1.2rem; font-weight: 700;">%</span></p>
        </div>

    </div>

</div>

</body>
</html>