<?php 
ob_start();
session_start();

include 'includes/db_config.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
    $name = trim($_POST['name']); 
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = 'user'; 

    try {
        $sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$name, $email, $password, $role]);
        
        echo "<script>alert('تم التسجيل بنجاح!'); window.location='login.php';</script>";
    } catch (Exception $e) {
        $error = "حدث خطأ أثناء التسجيل";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>Meshrider | إنشاء حساب</title>

    <!-- نفس ستايل login -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700;900&display=swap" rel="stylesheet">

    <style>
        :root { --primary: #e67e22; --dark: #0b0b0b; --light-dark: #1a1a1a; }

        body {
            font-family: 'Cairo', sans-serif;
            background-color: var(--dark);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('https://images.unsplash.com/photo-1541752171745-4196eead662e?q=80&w=2070') no-repeat center center/cover;
        }

        .card-box {
            background: var(--light-dark);
            padding: 45px;
            border-radius: 25px;
            border: 1px solid rgba(230, 126, 34, 0.3);
            width: 400px;
            text-align: center;
            box-shadow: 0 20px 40px rgba(0,0,0,0.5);
        }

        .card-box h2 {
            color: var(--primary);
            font-weight: 900;
            margin-bottom: 10px;
        }

        .input-box {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            background: var(--dark);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px;
            color: white;
            outline: none;
        }

        .btn-main {
            width: 100%;
            padding: 15px;
            background: linear-gradient(45deg, var(--primary), #f39c12);
            border: none;
            border-radius: 50px;
            color: #000;
            font-weight: 900;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .error-msg { color: #e74c3c; margin-bottom: 15px; }

        .login-link {
            margin-top: 20px;
            display: block;
            color: #ccc;
            text-decoration: none;
        }

        .login-link span {
            color: var(--primary);
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="card-box">
    <h2>إنشاء حساب</h2>
    <p style="color:#888; margin-bottom:25px;">انضم الآن إلى MeshRider</p>

    <?php if(!empty($error)) echo "<p class='error-msg'>$error</p>"; ?>

    <form method="POST">
        <input type="text" name="name" class="input-box" placeholder="الاسم" required>
        <input type="email" name="email" class="input-box" placeholder="البريد الإلكتروني" required>
        <input type="password" name="password" class="input-box" placeholder="كلمة المرور" required>
        <button type="submit" class="btn-main">إنشاء حساب</button>
    </form>

    <a href="login.php" class="login-link">
        لديك حساب؟ <span>تسجيل الدخول</span>
    </a>
</div>

</body>
</html>

<?php ob_end_flush(); ?>