<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("خطأ في الاتصال: " . mysqli_connect_error());
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

$nameCol = isset($userData['full_name'])
    ? 'full_name'
    : (isset($userData['name']) ? 'name' : 'username');

$phoneCol = null;

if (isset($userData['phone'])) {
    $phoneCol = 'phone';
} elseif (isset($userData['phone_number'])) {
    $phoneCol = 'phone_number';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_update'])) {

    $in_name  = mysqli_real_escape_string($conn, trim($_POST['u_name']));
    $in_email = mysqli_real_escape_string($conn, trim($_POST['u_email']));
    $in_phone = mysqli_real_escape_string($conn, trim($_POST['u_phone']));

    $update_sql = "
        UPDATE users 
        SET 
            $nameCol = '$in_name',
            email = '$in_email'
    ";

    if ($phoneCol !== null) {
        $update_sql .= ", $phoneCol = '$in_phone'";
    }

    $update_sql .= " WHERE id = '$user_id'";

    if (mysqli_query($conn, $update_sql)) {

        $_SESSION['user_name'] = $in_name;
        $_SESSION['name'] = $in_name;
        $_SESSION['full_name'] = $in_name;

        echo "
        <script>
            alert('تم تحديث بياناتك بنجاح!');
            window.location.href='profile.php';
        </script>
        ";

        exit();

    } else {

        echo "
        <script>
            alert('حدث خطأ أثناء تحديث البيانات');
        </script>
        ";
    }
}

include 'includes/header.php';
?>

<style>
    :root {
        --pure-white: #ffffff;
        --bright-orange: #ff9d47;
        --deep-black: #000000;
        --input-bg: #1a1a1a;
    }

    body {
        background-color: var(--deep-black);
        font-family: 'Cairo', sans-serif;
    }

    .edit-profile-card {
        background: #111;
        border: 2px solid #333;
        border-radius: 25px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.7);
        margin-top: 50px;
    }

    .edit-title {
        color: var(--bright-orange);
        font-weight: 900;
        letter-spacing: 1px;
        text-shadow: 0 0 15px rgba(255, 157, 71, 0.3);
    }

    .custom-label {
        color: var(--pure-white) !important;
        font-weight: 700 !important;
        font-size: 1.1rem;
        margin-bottom: 10px;
        display: block;
        text-align: right;
    }

    .ultra-input {
        background-color: var(--input-bg) !important;
        border: 2px solid #444 !important;
        color: var(--pure-white) !important;
        border-radius: 15px !important;
        padding: 15px !important;
        font-weight: 700 !important;
        font-size: 1.1rem !important;
        text-align: right;
        transition: 0.3s;
    }

    .ultra-input:focus {
        border-color: var(--bright-orange) !important;
        background-color: #222 !important;
        box-shadow: 0 0 15px rgba(255, 157, 71, 0.2) !important;
    }

    .btn-save {
        background: linear-gradient(45deg, #e67e22, #ff9d47);
        color: #000 !important;
        font-weight: 900;
        font-size: 1.2rem;
        border: none;
        padding: 15px;
        border-radius: 50px;
        transition: 0.4s;
        margin-top: 20px;
    }

    .btn-save:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(230, 126, 34, 0.5);
    }

    .btn-cancel {
        color: #888 !important;
        font-weight: 600;
        text-decoration: none;
        display: block;
        text-align: center;
        margin-top: 15px;
        transition: 0.3s;
    }

    .btn-cancel:hover {
        color: #fff !important;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">

            <div class="edit-profile-card">

                <div class="text-center mb-5">
                    <i class="fas fa-user-edit fa-3x text-warning mb-3"></i>

                    <h2 class="edit-title">
                        تعديل بياناتي
                    </h2>

                    <p style="color: #888; font-weight: 600;">
                        تأكد من صحة بياناتك لضمان تواصل أفضل
                    </p>
                </div>

                <form method="POST" action="">

                    <div class="mb-4">
                        <label class="custom-label">
                            الاسم الكامل
                        </label>

                        <input 
                            type="text"
                            name="u_name"
                            class="form-control ultra-input"
                            value="<?php echo htmlspecialchars($userData[$nameCol]); ?>"
                            placeholder="أدخل اسمك الجديد"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="custom-label">
                            البريد الإلكتروني
                        </label>

                        <input 
                            type="email"
                            name="u_email"
                            class="form-control ultra-input"
                            value="<?php echo htmlspecialchars($userData['email']); ?>"
                            placeholder="name@example.com"
                            required
                        >
                    </div>

                    <div class="mb-4">
                        <label class="custom-label">
                            رقم الهاتف
                        </label>

                        <input 
                            type="text"
                            name="u_phone"
                            class="form-control ultra-input"
                            value="<?php echo ($phoneCol !== null && isset($userData[$phoneCol])) ? htmlspecialchars($userData[$phoneCol]) : ''; ?>"
                            placeholder="07XXXXXXXX"
                        >
                    </div>

                    <button 
                        type="submit"
                        name="submit_update"
                        class="btn btn-save w-100"
                    >
                        <i class="fas fa-save me-2"></i>
                        حفظ التغييرات الآن
                    </button>

                    <a href="profile.php" class="btn-cancel">
                        إلغاء والعودة للملف الشخصي
                    </a>

                </form>

            </div>

        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>