<?php
ob_start();
session_start();

ini_set('display_errors', 1);
error_reporting(E_ALL);

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) {
    die("فشل الاتصال بالقاعدة: " . mysqli_connect_error());
}

$error = "";


if (!empty($_GET['redirect'])) {
    $raw = (string) $_GET['redirect'];

    if (
        preg_match('/^(?:trips\.php|destinations\.php|trip_details\.php)(?:\?[^#]*)?$/u', $raw)
        && strpos($raw, '..') === false
    ) {
        $_SESSION['meshrider_after_login'] = $raw;
    }
}


if (isset($_POST['submit_login'])) {

    $user_input = mysqli_real_escape_string($conn, $_POST['username_or_email']);
    $password_input = $_POST['password'];
    $ui_role = $_POST['ui_role'] ?? 'user';

    $query = "SELECT * FROM users 
              WHERE email='$user_input' OR name='$user_input'
              LIMIT 1";

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) == 1) {

        $user_data = mysqli_fetch_assoc($result);

        if (password_verify($password_input, $user_data['password'])) {

            $real_role = $user_data['role'];

          
            if ($ui_role !== $real_role) {

                $error = "اختيار نوع الحساب غير صحيح!";

            } else {

                $_SESSION['user_id']   = $user_data['id'];
                $_SESSION['user_name'] = $user_data['name'];
                $_SESSION['user_role'] = $real_role;

              
                if ($real_role === 'admin') {

                    $_SESSION['admin_logged_in'] = true;
                    header("Location: admin/dashboard.php");
                    exit();

                } else {

                    $_SESSION['user_logged_in'] = true;

                    $next = "dashboard.php";

                    if (!empty($_SESSION['meshrider_after_login'])) {

                        $cand = $_SESSION['meshrider_after_login'];

                        if (
                            preg_match('/^(?:trips\.php|destinations\.php|trip_details\.php)(?:\?[^#]*)?$/u', $cand)
                            && strpos($cand, '..') === false
                        ) {
                            $next = $cand;
                        }

                        unset($_SESSION['meshrider_after_login']);
                    }

                    header("Location: $next");
                    exit();
                }
            }

        } else {
            $error = "كلمة المرور غير صحيحة!";
        }

    } else {
        $error = "المستخدم غير موجود!";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Meshrider | تسجيل الدخول</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary: #e67e22;
            --dark: #0b0b0b;
            --light-dark: #1a1a1a;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)),
                        url('https://images.unsplash.com/photo-1541752171745-4196eead662e?q=80&w=2070') no-repeat center/cover;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .login-card {
            background: var(--light-dark);
            padding: 45px;
            border-radius: 25px;
            width: 400px;
            text-align: center;
            border: 1px solid rgba(230, 126, 34, 0.3);
        }

        h2 {
            color: var(--primary);
            font-weight: 900;
        }

        .input-box {
            width: 100%;
            padding: 15px;
            margin: 10px 0;
            border-radius: 12px;
            border: none;
            background: var(--dark);
            color: white;
            box-sizing: border-box;
        }

        .btn-login {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 50px;
            background: linear-gradient(45deg, var(--primary), #f39c12);
            font-weight: 900;
            cursor: pointer;
        }

        .error-msg {
            color: #e74c3c;
            margin-bottom: 10px;
        }

        .role-box {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .role-box label {
            flex: 1;
            cursor: pointer;
            background: #111;
            padding: 10px;
            border-radius: 10px;
            color: white;
            font-size: 14px;
        }

        .register-link {
            margin-top: 15px;
            display: block;
            color: #ccc;
            text-decoration: none;
        }

        .register-link span {
            color: var(--primary);
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="login-card">

    <h2>MESHRIDER</h2>
    <p style="color:#888;">بوابة الدخول للمسافرين والإدارة</p>

    <?php if (!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

    <form method="POST">

        <div class="role-box">

            <label>
                <input type="radio" name="ui_role" value="user" checked>
                👤 مستخدم
            </label>

            <label>
                <input type="radio" name="ui_role" value="admin">
                👑 أدمن
            </label>

        </div>

        <input type="text" name="username_or_email" class="input-box" placeholder="الإيميل أو اسم المستخدم" required>
        <input type="password" name="password" class="input-box" placeholder="كلمة المرور" required>

        <button type="submit" name="submit_login" class="btn-login">تسجيل الدخول</button>
    </form>

    <a href="register.php" class="register-link">
        ليس لديك حساب؟ <span>سجل الآن</span>
    </a>

</div>

</body>
</html>

<?php ob_end_flush(); ?>