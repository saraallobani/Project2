<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

include '../includes/db_config.php';

$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC");
$messages = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider | رسائل التواصل</title>

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
            --danger: #ef4444;
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
            transition: all 0.3s ease;
            animation: fadeIn 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        h2.page-title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .card {
            background: var(--card-bg);
            padding: 35px;
            border-radius: 24px;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
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
            color: var(--text-muted);
            text-align: right;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            font-weight: 700;
            font-size: 0.9rem;
            background: rgba(255, 255, 255, 0.01);
        }

        td {
            padding: 18px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-main);
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        .sender-name {
            font-weight: 700;
            color: #fff;
        }

        .sender-email {
            font-size: 0.85rem;
            color: var(--text-muted);
            display: block;
            margin-top: 2px;
        }

        .msg-subject {
            font-weight: 600;
            color: var(--primary);
        }

        .msg-body {
            background: rgba(255, 255, 255, 0.02);
            padding: 12px 16px;
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.02);
            color: #e4e4e7;
            max-width: 400px;
            line-height: 1.6;
            font-size: 0.9rem;
        }

        .msg-date {
            font-size: 0.85rem;
            color: var(--text-muted);
            white-space: nowrap;
        }

        .empty {
            text-align: center;
            color: var(--text-muted);
            padding: 50px;
            font-weight: 600;
            font-size: 1.1rem;
        }

        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(230, 126, 34, 0.5); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

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
        <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> <span>الحجوزات</span></a></li>
        <li><a href="reports.php"><i class="fa-solid fa-wallet"></i> <span>الأرباح</span></a></li>
        <li><a href="messages.php" class="active"><i class="fa-solid fa-envelope"></i> <span>الرسائل</span></a></li>

        <li style="margin-top: auto; padding-top: 20px;">
            <a href="../logout.php" style="color: var(--danger); background: rgba(239, 68, 68, 0.05);">
                <i class="fa-solid fa-right-from-bracket"></i> <span>تسجيل الخروج</span>
            </a>
        </li>
    </ul>
</div>

<div class="main-content">

    <h2 class="page-title"><i class="fa-solid fa-inbox" style="color: var(--primary);"></i> رسائل المستخدمين الواردة</h2>

    <div class="card">
        <?php if(count($messages) > 0): ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 20%;">المرسل</th>
                        <th style="width: 20%;">الموضوع</th>
                        <th style="width: 45%;">نص الرسالة</th>
                        <th style="width: 15%; text-align: center;">التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($messages as $msg): ?>
                    <tr>
                        <td>
                            <span class="sender-name"><?= htmlspecialchars($msg['name']); ?></span>
                            <span class="sender-email"><?= htmlspecialchars($msg['email']); ?></span>
                        </td>
                        <td>
                            <span class="msg-subject"><?= htmlspecialchars($msg['subject']); ?></span>
                        </td>
                        <td>
                            <div class="msg-body">
                                <?= nl2br(htmlspecialchars($msg['message'])); ?>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="msg-date">
                                <i class="fa-regular fa-clock" style="font-size: 0.8rem; margin-left: 4px;"></i>
                                <?= date("Y-m-d H:i", strtotime($msg['created_at'])); ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <div class="empty">
                <i class="fa-regular fa-folder-open" style="font-size: 2.5rem; color: var(--primary); display: block; margin-bottom: 15px; opacity: 0.6;"></i>
                صندوق الوارد فارغ، لا توجد رسائل حالياً.
            </div>
        <?php endif; ?>
    </div>

</div>

</body>
</html>