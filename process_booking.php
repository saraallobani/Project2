<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) { session_start(); }

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("خطأ في الاتصال: " . mysqli_connect_error());
}

if (!mysqli_set_charset($conn, 'utf8mb4')) {
    die('تعذر ضبط ترميز الاتصال utf8mb4');
}

if (!isset($_SESSION['user_id'])) {
    die("وصول غير مسموح؛ الرجاء تسجيل الدخول أولاً.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $user_id        = intval($_SESSION['user_id']);
    $trip_id        = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;
    $full_name      = mysqli_real_escape_string($conn, trim($_POST['full_name']));
    $phone          = mysqli_real_escape_string($conn, trim($_POST['phone']));
    $payment_method = mysqli_real_escape_string($conn, trim($_POST['payment_method']));
    
    $seats_count    = isset($_POST['seats_count']) ? intval($_POST['seats_count']) : 1;
    if ($seats_count < 1) { $seats_count = 1; }

    $trip_query = mysqli_query($conn, "SELECT price, total_seats FROM trips WHERE id = $trip_id LIMIT 1");
    $trip = mysqli_fetch_assoc($trip_query);

    if (!$trip) {
        die("خطأ: الرحلة المطلوبة غير موجودة في النظام.");
    }

    $trip_price  = floatval($trip['price']);
    $total_seats = isset($trip['total_seats']) ? intval($trip['total_seats']) : 30; 

    $booking_count_query = mysqli_query($conn, "
        SELECT SUM(seats_count) AS total_booked FROM bookings 
        WHERE trip_id = $trip_id AND status IN ('pending', 'confirmed', 'approved')
    ");
    $booking_data = mysqli_fetch_assoc($booking_count_query);
    $already_booked = intval($booking_data['total_booked'] ?? 0);
    $available_seats = $total_seats - $already_booked;

    if ($seats_count > $available_seats) {
        die("عذراً كابتن! المقاعد المتاحة المتبقية لهذه الرحلة هي ({$available_seats}) مقاعد فقط، ولا يمكنك حجز {$seats_count} مقاعد.");
    }

    $secure_total_price = $trip_price * $seats_count;
    $created_at = date('Y-m-d H:i:s');

    $sql = "INSERT INTO bookings (user_id, trip_id, full_name, phone, payment_method, seats_count, total_price, status, created_at) 
            VALUES ('$user_id', '$trip_id', '$full_name', '$phone', '$payment_method', '$seats_count', '$secure_total_price', 'pending', '$created_at')";

    if (mysqli_query($conn, $sql)) {
        header("Location: success.php");
        exit(); 
    } else {
        echo "خطأ في قاعدة البيانات: " . mysqli_error($conn);
    }
} else {
    echo "وصول غير مسموح";
}
?>