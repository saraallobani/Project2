<?php
require_once __DIR__ . '/includes/db_config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($conn)) {
    die("خطأ: متغير الاتصال غير معرف في ملف db_config.php");
}

if (!isset($_SESSION['user_id'])) {
    exit("يجب تسجيل الدخول أولاً");
}

$user_id = $_SESSION['user_id'];

$query = "SELECT * FROM chat_messages WHERE user_id = '$user_id' ORDER BY created_at ASC";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while($row = mysqli_fetch_assoc($result)) {
        $class = ($row['sender_type'] == 'user') ? 'msg-user' : 'msg-admin';
        $message = htmlspecialchars($row['message']);
        echo '<div class="'.$class.'">'.$message.'</div>';
    }
} else {
    echo '<div class="msg-admin">مرحباً بك في دعم MeshRider، كيف يمكننا مساعدتك اليوم؟</div>';
}
?>