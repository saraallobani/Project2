<?php
// الاتصال بقاعدة البيانات بنفس الطريقة المستخدمة في مشروعك
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (isset($_POST['cancel_now'])) {
    // جلب رقم الحجز من النموذج
    $id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    
    // تنفيذ الحذف بناءً على id الحجز فقط لتجنب خطأ Column not found
    $sql = "DELETE FROM bookings WHERE id = '$id'";
    
    if (mysqli_query($conn, $sql)) {
        // العودة لصفحة الحجوزات بعد النجاح
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        echo "خطأ في تنفيذ العملية: " . mysqli_error($conn);
    }
}
?>