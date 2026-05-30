<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال: " . mysqli_connect_error()); }

mysqli_set_charset($conn, 'utf8mb4');

if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);

    $res = mysqli_query($conn, "SELECT image, start_datetime, end_datetime FROM trips WHERE id = $id");
    $trip_data = mysqli_fetch_assoc($res);

    if ($trip_data) {
        $current_time = date('Y-m-d H:i:s');

        if (
            (!empty($trip_data['start_datetime']) && $trip_data['start_datetime'] <= $current_time) ||
            (!empty($trip_data['end_datetime']) && $trip_data['end_datetime'] <= $current_time)
        ) {
            header("Location: manage_trips.php?msg=cannot_delete");
            exit();
        }

        if(mysqli_query($conn, "DELETE FROM trips WHERE id = $id")){

            if(!empty($trip_data['image'])){
                @unlink("../uploads/" . $trip_data['image']);
            }

            header("Location: manage_trips.php?msg=deleted");
            exit();
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    $start_datetime = mysqli_real_escape_string($conn, $_POST['start_datetime']);
    $end_datetime = mysqli_real_escape_string($conn, $_POST['end_datetime']);
    
    $total_seats = intval($_POST['total_seats']);
    $available_seats = $total_seats; 
    
    $image_db_name = "";
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $image_db_name = time() . "_" . uniqid() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "../uploads/" . $image_db_name);
    }
    
    $insert_query = "INSERT INTO trips (title, price, duration, location, start_datetime, end_datetime, total_seats, available_seats, image) 
                     VALUES ('$title', '$price', '$duration', '$location', '$start_datetime', '$end_datetime', $total_seats, $available_seats, '$image_db_name')";
                     
    if (mysqli_query($conn, $insert_query)) {
        header("Location: manage_trips.php?msg=added");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meshrider | إدارة الرحلات</title>
    
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

        .main-content { 
            margin-right: 280px; 
            width: calc(100% - 280px); 
            padding: 40px; 
        }

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
        }

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
        }

        .btn-premium { 
            background: linear-gradient(135deg, var(--primary), #f39c12); 
            color: #000 !important; 
            padding: 14px 35px; 
            border-radius: 12px; 
            font-weight: 800; 
            border: none; 
            cursor: pointer; 
            margin-top: 25px;
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
        }

        td { 
            padding: 16px 20px; 
            border-bottom: 1px solid var(--border-color); 
            text-align: center; 
            color: var(--text-main); 
        }

        .btn-action-edit {
            color: var(--info);
            background: var(--info-glow);
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            margin-left: 8px;
        }

        .btn-action-delete {
            color: var(--danger);
            background: var(--danger-glow);
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
        }

        .btn-disabled {
            color: #55555c !important;
            background: rgba(255, 255, 255, 0.02) !important;
            padding: 6px 14px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 700;
            cursor: not-allowed;
            pointer-events: none; 
            margin-left: 8px;
        }

        @media (max-width: 992px) {
            .sidebar { display: none; }
            .main-content { margin-right: 0; width: 100%; padding: 20px; }
            .form-group { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>MESHRIDER</h2>
    </div>

    <ul>
        <li><a href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> الداشبورد</a></li>
        <li><a href="manage_trips.php" class="active"><i class="fa-solid fa-plane-departure"></i> إدارة الرحلات</a></li>
        <li><a href="manage_cars.php"><i class="fa-solid fa-car"></i> إدارة السيارات</a></li>
        <li><a href="manage_bookings.php"><i class="fa-solid fa-calendar-check"></i> الحجوزات</a></li>
        <li><a href="reports.php"><i class="fa-solid fa-wallet"></i> الأرباح</a></li>
        <li><a href="messages.php"><i class="fa-solid fa-envelope"></i> الرسائل</a></li>

        <li style="margin-top:auto;">
            <a href="../logout.php" style="color: var(--danger);">
                <i class="fa-solid fa-right-from-bracket"></i> تسجيل الخروج
            </a>
        </li>
    </ul>
</div>

<div class="main-content">

    <div class="data-section">
        <h4>إضافة رحلة سياحية جديدة</h4>

        <form action="manage_trips.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <input type="text" name="title" class="input-field" placeholder="اسم الرحلة" required>
                <input type="number" name="price" class="input-field" placeholder="السعر" required>

                <input type="text" name="duration" class="input-field" placeholder="المدة" required>
                <input type="text" name="location" class="input-field" placeholder="الموقع" required>

                <input type="datetime-local" name="start_datetime" class="input-field" required>
                <input type="datetime-local" name="end_datetime" class="input-field" required>

                <input type="number" name="total_seats" class="input-field" placeholder="عدد المقاعد الإجمالي" min="1" required>

                <input type="file" name="image" class="input-field" style="grid-column: span 1;">
            </div>

            <button type="submit" class="btn-premium">
                حفظ الرحلة
            </button>
        </form>
    </div>

    <div class="data-section">

        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">

            <h4>الرحلات الحالية</h4>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <span style="color: var(--danger); font-weight:bold;">
                    🗑️ تم حذف الرحلة بنجاح
                </span>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'added'): ?>
                <span style="color: var(--success); font-weight:bold;">
                    ✨ تم إضافة الرحلة بنجاح
                </span>
            <?php endif; ?>

            <?php if(isset($_GET['msg']) && $_GET['msg'] == 'cannot_delete'): ?>
                <span style="color: #f39c12; font-weight:bold;">
                    ⛔ لا يمكن حذف أو تعديل رحلة بدأت أو انتهت بالفعل
                </span>
            <?php endif; ?>

        </div>

        <div class="table-responsive">

            <table>

                <thead>
                    <tr>
                        <th>الصورة</th>
                        <th>الرحلة</th>
                        <th>السعر</th>
                        <th>المدة</th>
                        <th>المقاعد (المتبقية / الكلية)</th>
                        <th>التحكم</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $trips = mysqli_query($conn, "SELECT * FROM trips ORDER BY id DESC");
                    $current_time = date('Y-m-d H:i:s'); 

                    if(mysqli_num_rows($trips) > 0){

                        while($t = mysqli_fetch_assoc($trips)):
                            
                            $is_expired = false;
                            if (
                                (!empty($t['start_datetime']) && $t['start_datetime'] <= $current_time) ||
                                (!empty($t['end_datetime']) && $t['end_datetime'] <= $current_time)
                            ) {
                                $is_expired = true;
                            }
                    ?>

                    <tr>

                        <td>
                            <?php if(!empty($t['image'])): ?>

                                <img src="../uploads/<?php echo $t['image']; ?>"
                                     width="75"
                                     style="border-radius:10px; height:50px; object-fit:cover;">

                            <?php else: ?>

                                بدون صورة

                            <?php endif; ?>
                        </td>

                        <td><?php echo $t['title']; ?></td>

                        <td>
                            <span style="color: var(--primary); font-weight:700;">
                                <?php echo $t['price']; ?> JOD
                            </span>
                        </td>

                        <td><?php echo $t['duration']; ?></td>

                        <td>
                            <span style="color: var(--success); font-weight:700;">
                                <?php echo isset($t['available_seats']) ? $t['available_seats'] : '0'; ?>
                            </span> 
                            / 
                            <span style="color: var(--text-muted);">
                                <?php echo isset($t['total_seats']) ? $t['total_seats'] : '0'; ?>
                            </span>
                        </td>

                        <td>
                            <?php if($is_expired): ?>
                                <span class="btn-disabled" title="لا يمكن تعديل رحلة جارية أو منتهية">مغلقة</span>
                            <?php else: ?>
                                <a href="edit_trip.php?id=<?php echo $t['id']; ?>" class="btn-action-edit">
                                    تعديل
                                </a>

                                <a href="manage_trips.php?delete=<?php echo $t['id']; ?>"
                                   class="btn-action-delete"
                                   onclick="return confirm('هل أنت متأكد من حذف الرحلة؟')">
                                    حذف
                                </a>
                            <?php endif; ?>
                        </td>

                    </tr>

                    <?php
                        endwhile;

                    } else {

                        echo "<tr><td colspan='6'>لا توجد رحلات حالياً</td></tr>";
                    }
                    ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

</body>
</html>