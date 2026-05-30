<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. الاتصال بالقاعدة meshrider_db
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$conn) { die("خطأ في الاتصال: " . mysqli_connect_error()); }

// 2. معالجة الإضافة
if(isset($_POST['add_car'])) {
    $car_type = mysqli_real_escape_string($conn, $_POST['car_type']); 
    $price = mysqli_real_escape_string($conn, $_POST['price_per_day']);
    
    // إدارة رفع الصورة (اختياري)
    $image_name = ""; // قيمة افتراضية في حال لم يتم رفع صورة
    $upload_success = true;

    if(isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $image_name = time() . '_' . $_FILES['image']['name']; 
        $target = "../uploads/" . basename($image_name);
        
        if(!move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $upload_success = false;
            $error = "حدث خطأ أثناء حفظ الصورة على الخادم.";
        }
    }

    if ($upload_success) {
        $sql = "INSERT INTO cars (car_type, price_per_day, image) VALUES ('$car_type', '$price', '$image_name')";

        if(mysqli_query($conn, $sql)) {
            echo "<script>alert('✅ تمت إضافة السيارة بنجاح!'); window.location.href='manage_cars.php';</script>";
            exit(); 
        } else {
            $error = "حدث خطأ أثناء الإضافة: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة سيارة | Meshrider Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary: #e67e22;
            --primary-glow: rgba(230, 126, 34, 0.2);
            --bg-dark: #0b0b0e;
            --card-bg: #141419;
            --input-bg: #1c1c24;
            --text-main: #f4f4f5;
            --text-muted: #a1a1aa;
            --border-color: rgba(255, 255, 255, 0.05);
            --success: #22c55e;
            --success-glow: rgba(34, 197, 94, 0.1);
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body { 
            background-color: var(--bg-dark); 
            color: var(--text-main); 
            font-family: 'Cairo', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-x: hidden;
            padding: 20px;
        }
        
        .main-wrapper {
            width: 100%;
            max-width: 600px;
            margin-right: 280px; 
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            transition: all 0.3s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .custom-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header-title {
            text-align: center;
            margin-bottom: 35px;
        }

        .header-title h2 {
            color: var(--primary);
            font-weight: 900;
            font-size: 1.6rem;
            margin: 0;
            text-shadow: 0 0 20px var(--primary-glow);
        }

        .form-group { margin-bottom: 24px; }

        .form-label {
            display: block;
            margin-bottom: 10px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            padding-right: 2px;
        }

        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: white !important;
            padding: 15px 20px;
            border-radius: 12px;
            width: 100%;
            font-family: 'Cairo', sans-serif;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: #21212a;
            box-shadow: 0 0 0 4px var(--primary-glow);
        }

        .file-upload-wrapper {
            border: 2px dashed var(--border-color);
            padding: 30px 20px;
            border-radius: 14px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: rgba(255,255,255,0.01);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary);
            background: rgba(230, 126, 34, 0.04);
        }
        
        .file-upload-wrapper.file-selected {
            border-color: var(--success);
            background: rgba(34, 197, 94, 0.03);
        }

        .file-upload-wrapper i {
            font-size: 2.2rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .file-upload-wrapper:hover i {
            transform: translateY(-3px);
        }

        .btn-submit {
            background: var(--primary);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            width: 100%;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            box-shadow: 0 4px 15px var(--primary-glow);
        }

        .btn-submit:hover {
            background: #f39c12;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 600;
            transition: all 0.2s ease;
            padding: 8px;
            border-radius: 8px;
        }

        .btn-cancel:hover { 
            color: var(--danger); 
            background: rgba(239, 68, 68, 0.05);
        }

        @media (max-width: 992px) {
            .main-wrapper { margin-right: 0; }
        }
        @media (max-width: 576px) {
            .custom-card { padding: 30px 20px; }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="custom-card">
        <div class="header-title">
            <h2><i class="fa-solid fa-car-side" style="margin-left: 8px;"></i> إضافة سيارة جديدة</h2>
            <p style="color: var(--text-muted); margin-top: 10px; font-size:0.9rem;">قم بإدخال بيانات السيارة لتظهر في أسطولك المتاح</p>
        </div>

        <?php if(isset($error)): ?>
            <p style="color: var(--danger); text-align: center; margin-bottom: 15px;"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label class="form-label">نوع وموديل السيارة</label>
                <input type="text" name="car_type" class="form-control" placeholder="مثلاً: Range Rover Sport 2024" required>
            </div>

            <div class="form-group">
                <label class="form-label">سعر الإيجار اليومي (JOD)</label>
                <input type="number" name="price_per_day" class="form-control" placeholder="0.00" required>
            </div>

            <div class="form-group">
                <label class="form-label">صورة السيارة الاحترافية (اختياري)</label>
                <div class="file-upload-wrapper" id="uploadArea" onclick="document.getElementById('carImage').click()">
                    <i class="fa-solid fa-cloud-arrow-up" id="uploadIcon"></i>
                    <p id="fileName" style="margin:0; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">اسحب الصورة هنا أو اضغط للاختيار</p>
                    <!-- تم إزالة required من هنا -->
                    <input type="file" name="image" id="carImage" hidden accept="image/*" onchange="updateFileName(this)">
                </div>
            </div>

            <button type="submit" name="add_car" class="btn-submit">
                <i class="fa-solid fa-plus-circle"></i> تأكيد الإضافة والاحتفاظ
            </button>

            <a href="manage_cars.php" class="btn-cancel">إلغاء العملية والعودة</a>

        </form>
    </div>
</div>

<script>
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const uploadArea = document.getElementById('uploadArea');
            const uploadIcon = document.getElementById('uploadIcon');
            const fileNameTxt = document.getElementById('fileName');
            
            fileNameTxt.innerText = "تم اختيار: " + fileName;
            fileNameTxt.style.color = "var(--success)";
            uploadIcon.style.color = "var(--success)";
            uploadIcon.className = "fa-solid fa-circle-check";
            uploadArea.classList.add('file-selected');
        }
    }
</script>

</body>
</html>