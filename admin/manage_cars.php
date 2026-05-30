<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// قفل الحماية لضمان الأمان والخصوصية للوحة التحكم
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال بالقاعدة: " . mysqli_connect_error()); }

// عملية حذف سيارة من النظام
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    
    // جلب اسم الصورة قبل الحذف لمسحها من المجلد للحفاظ على مساحة السيرفر نظيفة
    $res = mysqli_query($conn, "SELECT image FROM cars WHERE id = $id");
    $img_data = mysqli_fetch_assoc($res);
    
    if(mysqli_query($conn, "DELETE FROM cars WHERE id = $id")){
        if(!empty($img_data['image'])){
            @unlink("../uploads/" . $img_data['image']);
        }
        header("Location: manage_cars.php?msg=deleted");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider | إدارة السيارات</title>
    
    <!-- الخطوط والأيقونات الرسمية الموحدة -->
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
            --info: #3b82f6;
            --info-glow: rgba(59, 130, 246, 0.15);
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

        /* القائمة الجانبية (Sidebar) الهندسية الثابتة والموحدة للداشبورد */
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

        /* منطقة المحتوى الرئيسي المستقرة بصرياً */
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

        /* كروت الأقسام الزجاجية الداكنة الفخمة */
        .data-section { 
            background: var(--card-bg); 
            border-radius: 24px; 
            padding: 35px; 
            border: 1px solid var(--border-color); 
            box-shadow: 0 20px 40px rgba(0,0,0,0.4);
            margin-bottom: 35px;
        }

        .data-section h4 { 
            color: #fff; 
            margin-bottom: 25px; 
            font-size: 1.25rem; 
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        /* شبكة حقول الإدخال المتناسقة */
        .form-group { 
            display: grid; 
            grid-template-columns: 1fr 1fr; 
            gap: 20px; 
        }

        .input-field { 
            background: var(--input-bg); 
            border: 1px solid var(--border-color); 
            padding: 15px 20px; 
            border-radius: 12px; 
            color: #fff; 
            outline: none; 
            font-family: 'Cairo';
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .input-field:focus { 
            border-color: var(--primary); 
            box-shadow: 0 0 12px rgba(230, 126, 34, 0.15);
            background: rgba(28, 28, 36, 0.8);
        }

        .input-field::placeholder {
            color: #52525b;
        }

        /* حقل رفع ملفات صور السيارات السلس */
        input[type="file"].input-field {
            padding: 11px 20px;
            cursor: pointer;
        }

        /* أزرار الإضافة الفاخرة بالتدرج البرتقالي الرائع */
        .btn-premium { 
            background: linear-gradient(135deg, var(--primary), #f39c12); 
            color: #000 !important; 
            padding: 14px 35px; 
            border-radius: 12px; 
            font-weight: 800; 
            font-size: 0.95rem;
            text-decoration: none; 
            border: none; 
            cursor: pointer; 
            margin-top: 25px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(230, 126, 34, 0.2);
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(230, 126, 34, 0.4);
        }

        /* الجداول الذكية المتجاوبة وعرض أسطول السيارات */
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
            padding: 16px 20px; 
            border-bottom: 1px solid var(--border-color); 
            text-align: center; 
            color: var(--text-main); 
            font-size: 0.95rem;
            vertical-align: middle;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.01);
        }

        /* زر الحذف ذو الحماية السيبرانية العالية والتصميم الأنيق */
        .btn-action-delete {
            color: var(--danger);
            background: var(--danger-glow);
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
        }

        .btn-action-delete:hover {
            background: var(--danger);
            color: #fff;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        /* تخصيص الـ Scrollbar الموحد لكافة عناصر اللوحة */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: rgba(230, 126, 34, 0.5); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

        /* شاشات الهواتف والأجهزة اللوحية الذكية */
        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
            .form-group { grid-template-columns: 1fr; }
            input[type="file"].input-field { grid-column: span 1 !important; }
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
        <li><a href="manage_cars.php" class="active"><i class="fa-solid fa-car"></i> <span>إدارة السيارات</span></a></li>
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
    
    <!-- فورم إضافة سيارة جديدة للأسطول -->
    <div class="data-section">
        <h4><i class="fa-solid fa-car-side" style="color: var(--primary);"></i> إضافة سيارة جديدة للأسطول</h4>
        <form action="insert_car.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="car_type" class="input-field" placeholder="نوع السيارة وموديلها (مثلاً: مرسيدس 2024)" required>
                <input type="number" name="price_per_day" class="input-field" placeholder="السعر باليوم (JOD)" required>
                <input type="text" name="transmission" class="input-field" placeholder="ناقل الحركة (أوتوماتيك / عادي)">
                <input type="file" name="image" class="input-field" style="grid-column: span 2;" required>
            </div>
            <button type="submit" name="add_car" class="btn-premium">
                <i class="fa-solid fa-square-plus"></i> تأكيد الإضافة في Meshrider
            </button>
        </form>
    </div>

    <!-- جدول عرض أسطول السيارات الحالي -->
    <div class="data-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h4><i class="fa-solid fa-list-check" style="color: var(--primary);"></i> السيارات المتوفرة حالياً</h4>
            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <span style="color: var(--danger); font-weight: bold; font-size: 0.9rem; background: var(--danger-glow); padding: 6px 16px; border-radius: 8px;">🗑️ تم حذف السيارة بنجاح</span>
            <?php endif; ?>
        </div>
        
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>الصورة المصغرة</th>
                        <th>الموديل والنوع</th>
                        <th>السعر / اليوم</th>
                        <th>الإجراءات والتحكم</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $cars = mysqli_query($conn, "SELECT * FROM cars ORDER BY id DESC");
                    if(mysqli_num_rows($cars) > 0) {
                        while($c = mysqli_fetch_assoc($cars)): ?>
                        <tr>
                            <td>
                                <?php if(!empty($c['image'])): ?>
                                    <img src="../uploads/<?php echo $c['image']; ?>" width="85" style="border-radius: 10px; height: 55px; object-fit: cover; border: 1px solid var(--border-color);">
                                <?php else: ?>
                                    <span style="font-size: 0.85rem; opacity: 0.4;">لا توجد صورة</span>
                                <?php endif; ?>
                            </td>
                            <td style="font-weight: 600; color: #fff;"><?php echo $c['car_type']; ?></td>
                            <td><span style="color: var(--primary); font-weight: 700;"><?php echo $c['price_per_day']; ?> JOD</span></td>
                            <td>
                                <a href="manage_cars.php?delete=<?php echo $c['id']; ?>" 
                                   class="btn-action-delete"
                                   onclick="return confirm('هل أنت متأكد تماماً من حذف هذه السيارة نهائياً من أسطول Meshrider؟')">
                                    <i class="fa-regular fa-trash-can"></i> حذف السيارة
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; 
                    } else {
                        echo "<tr><td colspan='4' style='padding: 50px; color: var(--text-muted); font-weight: 600;'>لا توجد سيارات مضافة في النظام حالياً</td></tr>";
                    } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>