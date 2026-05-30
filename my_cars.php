<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db"); 

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

include 'includes/header.php'; 

if(!isset($_SESSION['user_id'])){
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

$u_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css"> 
    <title>سياراتي | MeshRider</title>
    <style>
        body { background: #0a0a0a; }
        .data-section { background: #111; border: 1px solid #222; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; color: #fff; border-radius: 15px; overflow: hidden; }
        th { background-color: #e67e22; color: white; padding: 15px; text-align: center; }
        td { padding: 12px; border-bottom: 1px solid #333; text-align: center; }
        tr:hover { background-color: #1a1a1a; }
        .status-pill { padding: 5px 12px; border-radius: 50px; font-weight: bold; font-size: 0.9rem; }
        
        /* ستايل رسالة النجاح الفخمة */
        .success-box {
            background: rgba(46, 204, 113, 0.1);
            border: 1px dashed #2ecc71;
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container mt-5 pt-4">

    <!-- بداية رسالة النجاح: بتظهر بس إذا الحجز تم بنجاح -->
    <?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <div class="success-box animate__animated animate__fadeInDown">
        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
        <h3 class="text-white fw-bold">تم استلام طلب الحجز بنجاح!</h3>
        <p class="text-white-50">يمكنك الآن متابعة حالة طلبك من الجدول أدناه أو العودة للرئيسية.</p>
        <div class="mt-3">
            <a href="index.php" class="btn btn-outline-light rounded-pill px-4 me-2">العودة للرئيسية</a>
            <button class="btn btn-success rounded-pill px-4" onclick="this.parentElement.parentElement.style.display='none'">حسناً</button>
        </div>
    </div>
    <?php endif; ?>
    <!-- نهاية رسالة النجاح -->

    <div class="data-section shadow-lg p-4 rounded-4">
        <h4 class="text-center mb-4" style="color: #e67e22;">
            <i class="fas fa-car me-2"></i> سجل حجوزات السيارات الخاصة بي
        </h4>
        
        <table>
            <thead>
                <tr>
                    <th>رقم الحجز</th>
                    <th>نوع السيارة</th>
                    <th>تاريخ الاستلام</th>
                    <th>تاريخ التسليم</th>
                    <th>السعر الإجمالي</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query = "SELECT car_bookings.*, cars.car_type 
                          FROM car_bookings 
                          JOIN cars ON car_bookings.car_id = cars.id 
                          WHERE car_bookings.user_id = '$u_id' 
                          ORDER BY car_bookings.id DESC";
                
                $result = mysqli_query($conn, $query);
                
                if($result && mysqli_num_rows($result) > 0) {
                    while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold text-muted">#<?php echo $row['id']; ?></td>
                        <td style="color: #3498db; font-weight: bold;"><?php echo $row['car_type']; ?></td>
                        <td><?php echo $row['pickup_date']; ?></td>
                        <td><?php echo $row['return_date']; ?></td>
                        <td style="color: #2ecc71; font-weight: bold;"><?php echo $row['total_price']; ?> JOD</td>
                        <td>
                            <?php 
                            if($row['status'] == 'pending') 
                                echo '<span class="status-pill" style="background: rgba(241, 196, 15, 0.2); color: #f1c40f;">قيد الانتظار</span>';
                            elseif($row['status'] == 'approved' || $row['status'] == 'confirmed') 
                                echo '<span class="status-pill" style="background: rgba(46, 204, 113, 0.2); color: #2ecc71;">تم القبول</span>';
                            else 
                                echo '<span class="status-pill" style="background: rgba(231, 76, 60, 0.2); color: #e74c3c;">مرفوض</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endwhile; 
                } else {
                    echo "<tr><td colspan='6' style='padding:60px; color: #888;'>لا يوجد لديك أي حجوزات سيارات حالياً.</td></tr>";
                } ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>