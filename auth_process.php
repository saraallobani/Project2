<?php
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

$db_name = "meshrider_db"; 
$conn = mysqli_connect("localhost", "root", "", $db_name); 

if (!$conn) {
    die("فشل الاتصال: " . mysqli_connect_error());
}

if (isset($_POST['login'])) {

    $user_input_email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $password = $_POST['password']; 

    $query = "SELECT * FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($conn, $query);
    
    if ($stmt) {

        mysqli_stmt_bind_param($stmt, "s", $user_input_email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['user_name'] = isset($user['name']) ? $user['name'] : $user['email']; 

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: dashboard.php"); 
                }
                exit();

            } else {
                header("Location: login.php?error=wrong_password");
                exit();
            }

        } else {
            header("Location: login.php?error=user_not_found");
            exit();
        }

        mysqli_stmt_close($stmt);

    } else {
        die("خطأ في الاستعلام: " . mysqli_error($conn));
    }
}
?>