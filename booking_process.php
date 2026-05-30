<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include 'includes/db_config.php'; 

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id        = intval($_SESSION['user_id']);
$trip_id        = isset($_POST['trip_id']) ? intval($_POST['trip_id']) : 0;
$full_name      = trim($_POST['full_name'] ?? '');
$phone          = trim($_POST['phone'] ?? '');
$payment_method = trim($_POST['payment_method'] ?? '');

$seats_count    = isset($_POST['seats_count']) ? intval($_POST['seats_count']) : 1;
if ($seats_count < 1) { $seats_count = 1; }

$status         = 'approved'; 
$created_at     = date('Y-m-d H:i:s');

try {
    $trip_stmt = $pdo->prepare("SELECT price, total_seats FROM trips WHERE id = ?");
    $trip_stmt->execute([$trip_id]);
    $trip = $trip_stmt->fetch();

    if (!$trip) {
        die("خطأ: هذه الرحلة غير موجودة في النظام.");
    }

    $trip_price  = floatval($trip['price']);
    $total_seats = isset($trip['total_seats']) ? intval($trip['total_seats']) : 30; 

    $booking_count_stmt = $pdo->prepare("
        SELECT SUM(seats_count) AS total_booked FROM bookings 
        WHERE trip_id = ? AND status IN ('pending', 'confirmed', 'approved')
    ");
    $booking_count_stmt->execute([$trip_id]);
    $booking_data = $booking_count_stmt->fetch();
    
    $already_booked = intval($booking_data['total_booked'] ?? 0);
    $available_seats = $total_seats - $already_booked;

    if ($seats_count > $available_seats) {
        die("عذراً كابتن! المقاعد المتاحة المتبقية لهذه الرحلة هي ({$available_seats}) مقاعد فقط، ولا يمكنك حجز {$seats_count} مقاعد.");
    }

    $total_price = $trip_price * $seats_count;


    $sql = "INSERT INTO bookings (user_id, trip_id, full_name, phone, payment_method, seats_count, total_price, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $pdo->prepare($sql);
    
    $success = $stmt->execute([
        $user_id, 
        $trip_id, 
        $full_name, 
        $phone, 
        $payment_method, 
        $seats_count, 
        $total_price, 
        $status, 
        $created_at
    ]);

    if ($success) {
        header("Location: profile.php?booked=success");
        exit();
    }

} catch (PDOException $e) {
    echo "خطأ في عملية الحجز: " . $e->getMessage();
}
?>