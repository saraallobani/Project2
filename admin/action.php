<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("خطأ في الاتصال بالقاعدة: " . mysqli_connect_error());
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = intval($_GET['id']);
    
    // تحويل الحالة لكلمة approved أو rejected لتتوافق مع نظامك
    $status = ($_GET['status'] === 'approve') ? 'approved' : 'rejected';
    
    // استقبال نوع الحجز (إذا مش مبعوث، بنعتبره trip بشكل تلقائي عشان ما نخرب كودك القديم)
    $type = isset($_GET['type']) ? $_GET['type'] : 'trip';

    // تحديد الجدول بناءً على النوع
    if ($type === 'car') {
        $update = "UPDATE car_bookings SET status = '$status' WHERE id = $id";
    } else {
        $update = "UPDATE bookings SET status = '$status' WHERE id = $id";
    }
    
    if (mysqli_query($conn, $update)) {
        // تعديل التوجيه ليرجع على صفحة إدارة الحجوزات اللي فيها التبويبات مع رسالة النجاح
        header("Location: manage_bookings.php?msg=updated");
        exit();
    } else {
        echo "خطأ في التحديث: " . mysqli_error($conn);
    }
}
?>