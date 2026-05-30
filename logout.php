<?php
session_start(); // تفعيل الجلسة للتمكن من حذفها
session_unset(); // إفراغ جميع متغيرات السيشن
session_destroy(); // تدمير السيشن بالكامل من السيرفر

// التوجيه إلى صفحة اللاندنج بيج
header("Location: index.php"); 
exit();
?>