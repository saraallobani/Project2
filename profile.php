<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int) $_SESSION['user_id'];

$userQuery = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");

if (!$userQuery || mysqli_num_rows($userQuery) == 0) {
    die("المستخدم غير موجود");
}

$userData = mysqli_fetch_assoc($userQuery);

/* تحديد اسم العرض */
$displayName = "مستكشف مش رادر";

if (isset($userData['full_name']) && !empty(trim($userData['full_name']))) {

    $displayName = $userData['full_name'];

} elseif (isset($userData['name']) && !empty(trim($userData['name']))) {

    $displayName = $userData['name'];

} elseif (isset($userData['username']) && !empty(trim($userData['username']))) {

    $displayName = $userData['username'];
}

$points = 500;
$level = 2;
?>

<?php include 'includes/header.php'; ?>

<style>
    :root {
        --bright-orange: #ff9d47;
        --pure-white: #ffffff;
        --soft-white: #f0f0f0;
        --deep-black: #000000;
        --card-bg: #121212;
    }

    body {
        background-color: var(--deep-black);
        color: var(--soft-white);
        font-family: 'Cairo', sans-serif;
        text-align: right;
    }

    .profile-card {
        background: var(--card-bg);
        border: 2px solid #222;
        border-radius: 30px;
        padding: 40px;
        margin-top: 80px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.9);
    }

    .user-avatar-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 20px;
    }

    .user-avatar {
        font-size: 90px;
        color: var(--bright-orange);
        filter: drop-shadow(0 0 10px rgba(255, 157, 71, 0.4));
    }

    .user-name {
        color: var(--pure-white) !important;
        font-weight: 900 !important;
        font-size: 2rem;
        margin-bottom: 5px;
    }

    .user-email {
        color: #bbb !important;
        font-weight: 600;
        font-size: 1.1rem;
    }

    .stat-item {
        background: #1a1a1a;
        padding: 20px;
        border-radius: 20px;
        border: 1px solid #333;
        transition: 0.3s;
    }

    .stat-item:hover {
        border-color: var(--bright-orange);
        transform: translateY(-5px);
    }

    .stat-label {
        color: #888;
        font-weight: 700;
        font-size: 0.9rem;
        display: block;
        margin-bottom: 5px;
    }

    .stat-value {
        color: var(--bright-orange);
        font-weight: 900;
        font-size: 1.8rem;
        margin: 0;
    }

    .activity-log {
        background: #000;
        border-radius: 20px;
        padding: 20px;
        border: 1px dashed #444;
    }

    .activity-item {
        color: #ddd !important;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 1px solid #222;
        padding: 8px 0;
    }

    .activity-item:last-child {
        border: none;
    }

    .btn-edit-profile {
        background: var(--bright-orange);
        color: #000 !important;
        font-weight: 800;
        border-radius: 50px;
        padding: 12px 30px;
        border: none;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-edit-profile:hover {
        box-shadow: 0 10px 20px rgba(255, 157, 71, 0.4);
        transform: scale(1.05);
    }
</style>

<div class="container pb-5">

    <div class="profile-card">

        <div class="row align-items-center">

            <div class="col-md-5 text-center border-md-start" style="border-color: #333 !important;">

                <div class="user-avatar-wrapper">

                    <div class="user-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>

                    <span class="position-absolute bottom-0 start-0 badge rounded-pill bg-success border border-dark" style="font-size: 12px;">
                        متصل الآن
                    </span>

                </div>

                <h2 class="user-name">
                    <?php echo htmlspecialchars($displayName); ?>
                </h2>

                <p class="user-email">
                    <?php echo htmlspecialchars($userData['email'] ?? 'no-email@mesh.com'); ?>
                </p>

                <div class="mt-4 d-flex justify-content-center gap-2">

                    <a href="editprofile.php" class="btn-edit-profile">
                        <i class="fas fa-pen-to-square me-2"></i>
                        تعديل بياناتي
                    </a>

                </div>

                <div class="mt-3">
                    <span class="badge bg-dark border border-warning text-warning px-4 py-2 rounded-pill fw-bold">
                        مستكشف ذهبي ⭐
                    </span>
                </div>

            </div>

            <div class="col-md-7 mt-4 mt-md-0">

                <h4 class="fw-900 text-white mb-4">
                    <i class="fas fa-chart-line text-warning me-2"></i>
                    لوحة الإنجازات
                </h4>

                <div class="row g-3 text-center">

                    <div class="col-6">
                        <div class="stat-item">
                            <span class="stat-label">المستوى</span>
                            <h2 class="stat-value"><?php echo $level; ?></h2>
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="stat-item">
                            <span class="stat-label">مجموع النقاط</span>
                            <h2 class="stat-value"><?php echo $points; ?></h2>
                        </div>
                    </div>

                </div>

                <div class="activity-log mt-4 text-end">

                    <h6 class="text-warning fw-bold mb-3 small text-uppercase">
                        آخر التحركات
                    </h6>

                    <div class="activity-item">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        سجلت دخول للوحة التحكم
                    </div>

                    <div class="activity-item">
                        <i class="fas fa-clock text-info me-2"></i>
                        تصفحت رحلة وادي رم قبل قليل
                    </div>

                    <div class="activity-item">
                        <i class="fas fa-star text-warning me-2"></i>
                        حصلت على 50 نقطة ترحيبية
                    </div>

                </div>

                <div class="mt-4 d-flex gap-2">

                    <a href="dashboard.php" class="btn btn-dark border-secondary rounded-pill px-4 fw-bold">
                        الرئيسية
                    </a>

                    <a href="logout.php" class="btn btn-outline-danger rounded-pill px-4 fw-bold">
                        تسجيل خروج
                    </a>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>