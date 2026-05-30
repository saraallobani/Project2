<?php
session_start(); // البدء بالجلسة الحالية
session_unset(); // إزالة جميع متغيرات الجلسة
session_destroy(); // تدمير الجلسة بالكامل

// التوجيه إلى صفحة تسجيل دخول الأدمن أو الصفحة الرئيسية للموقع
header("Location: ../login.php"); 
exit();
?>