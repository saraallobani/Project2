<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// 1. الاتصال بالقاعدة
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
include 'includes/header.php'; 

$show_success = false; 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_now'])) {
    $user_id = $_SESSION['user_id'];
    $car_id  = mysqli_real_escape_string($conn, $_POST['car_id']);
    $pickup  = mysqli_real_escape_string($conn, $_POST['pickup']);
    $return  = mysqli_real_escape_string($conn, $_POST['return']);

    // جلب سعر السيارة من القاعدة
    $car_query = mysqli_query($conn, "SELECT price_per_day FROM cars WHERE id = '$car_id'");
    $car_data  = mysqli_fetch_assoc($car_query);
    $price_per_day = $car_data['price_per_day'] ?? 0;

    // حساب عدد الأيام (مع ضمان إن أقل مدة هي يوم واحد)
    $p_date = new DateTime($pickup);
    $r_date = new DateTime($return);
    $interval = $p_date->diff($r_date);
    $days = $interval->days;
    if ($days <= 0) { $days = 1; }

    $final_price = $days * $price_per_day;

    // إدخال الحجز
    $sql = "INSERT INTO car_bookings (user_id, car_id, pickup_date, return_date, total_price, status) 
            VALUES ('$user_id', '$car_id', '$pickup', '$return', '$final_price', 'pending')";

    if (mysqli_query($conn, $sql)) {
        $show_success = true; 
    } else {
        echo "<div class='container mt-5'><div class='alert alert-danger text-end'>خطأ في القاعدة: " . mysqli_error($conn) . "</div></div>";
    }
}
?>

<style>
    body { background: #0a0a0a; color: #fff; font-family: 'Cairo', sans-serif; }
    .success-card {
        background: #111;
        border: 1px solid #2ecc71;
        border-radius: 30px;
        padding: 50px 20px;
        margin: 100px auto;
        max-width: 600px;
        box-shadow: 0 15px 40px rgba(46, 204, 113, 0.1);
    }
</style>

<div class="container py-5 mt-5">
    <?php if ($show_success): ?>
        <div class="success-card text-center animate__animated animate__zoomIn">
            <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
            <h2 class="fw-bold mb-3">تم الحجز بنجاح!</h2>
            <p class="text-white-50 mb-4 fs-5">
                لقد سجلنا طلبك لسيارة <span class="text-warning"><?php echo $days; ?></span> أيام.<br>
                السعر الإجمالي المتوقع: <span class="text-success"><?php echo $final_price; ?> JOD</span>
            </p>
            <div class="d-flex justify-content-center gap-3">
                <a href="my_cars.php" class="btn btn-warning rounded-pill px-4 fw-bold shadow">شاهد حجوزاتي</a>
                <a href="index.php" class="btn btn-outline-light rounded-pill px-4">الداشبورد</a>
            </div>
        </div>
    <?php elseif (!isset($_POST['book_now'])): ?>
        <div class="text-center py-5">
            <h3 class="text-white-50">وصول غير مصرح به.. يرجى العودة للكتالوج.</h3>
            <a href="cars_catalog.php" class="btn btn-warning mt-3">تصفح السيارات</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>