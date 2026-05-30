<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['clear_session'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

$is_logged_in = isset($_SESSION['user_id']);
$home_url = $is_logged_in ? "dashboard.php" : "index.php";

/* 🔥 التصحيح هون */
$is_admin = (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MeshRider | تجربة الأردن</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Icons + Fonts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    <!-- Your CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
</head>

<body class="has-fixed-nav">

<nav class="navbar navbar-expand-lg navbar-dark fixed-top shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold fs-3" href="<?php echo $home_url; ?>">
        <span style="color: #e67e22;">Mesh</span>Rider 🐪
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto fw-bold align-items-center">
        
        <li class="nav-item"><a class="nav-link px-3" href="<?php echo $home_url; ?>">الرئيسية</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="destinations.php"><i class="fas fa-map-pin ms-1"></i> الوجهات</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="hotels.php">الفنادق</a></li>
        <li class="nav-item"><a class="nav-link px-3" href="restaurants.php">المطاعم</a></li>

        <?php if($is_logged_in): ?>

            <li class="nav-item">
                <a class="nav-link" href="cars_catalog.php">تأجير السيارات</a>
            </li>

            <li class="nav-item"><a class="nav-link px-3" href="trips.php">الرحلات</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="generator.php">البحث الذكي</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="my_bookings.php">حجوزاتي</a></li>

            <li class="nav-item">
                <a class="nav-link px-3 text-warning" href="my_cars.php">
                    <i class="fas fa-car-side"></i> سياراتي
                </a>
            </li>
            
            <!-- 🔥 Dropdown -->
            <li class="nav-item dropdown ms-lg-3">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 bg-dark rounded-pill px-3 py-1 border border-warning shadow-sm" 
                   href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    
                    <span class="text-warning small">
<?php
echo htmlspecialchars(
    $_SESSION['user_name']
    ?? $_SESSION['full_name']
    ?? $_SESSION['name']
    ?? 'مستخدم'
);
?>                    </span>

                    <i class="fas fa-user-circle fa-lg text-warning"></i>
                </a>

                <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end shadow" aria-labelledby="userDropdown">

                    <?php if($is_admin): ?>
                        <li>
                            <a class="dropdown-item text-end text-warning" href="admin_dashboard.php">
                                <i class="fas fa-user-shield me-2"></i> لوحة الإدارة
                            </a>
                        </li>
                        <li><hr class="dropdown-divider border-secondary"></li>
                    <?php endif; ?>

                    <li>
                        <a class="dropdown-item text-end" href="profile.php">
                            <i class="fas fa-id-card me-2"></i> ملفي الشخصي
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item text-end" href="dashboard.php">
                            <i class="fas fa-th-large me-2"></i> لوحة التحكم
                        </a>
                    </li>

                    <li><hr class="dropdown-divider border-secondary"></li>

                    <li>
                        <a class="dropdown-item text-end text-danger" href="logout.php">
                            <i class="fas fa-sign-out-alt me-2"></i> تسجيل الخروج
                        </a>
                    </li>
                </ul>
            </li>

        <?php else: ?>

            <li class="nav-item"><a class="nav-link" href="about.php">من نحن</a></li>
            <li class="nav-item"><a class="nav-link px-3" href="index.php#jordan">عن الأردن</a></li>
            <li class="nav-item"><a class="nav-link" href="contact.php">تواصل معنا</a></li>

            <li class="nav-item">
                <a class="nav-link px-3 btn btn-warning text-dark ms-lg-3 rounded-pill fw-bold" href="login.php">
                    تسجيل الدخول
                </a>
            </li>

        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- 🔥🔥🔥 هذا أهم سطر (حل المشكلة) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>