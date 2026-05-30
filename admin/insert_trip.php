<?php
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $price = $_POST['price'];
    $duration = $_POST['duration'];
    $location = $_POST['location'];
    
    // معالجة رفع الصورة
    $image = $_FILES['image']['name'];
    $target = "../uploads/" . basename($image);
    
    $sql = "INSERT INTO trips (title, price, duration, location, image) 
            VALUES ('$title', '$price', '$duration', '$location', '$image')";

    if (mysqli_query($conn, $sql)) {
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        header("Location: manage_trips.php?status=success");
        exit();
    } else {
        echo "خطأ: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة رحلة جديدة | Meshrider Admin</title>
    
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
        
        /* ضبط المساحة للمحتوى بجانب الـ Sidebar الافتراضي */
        .main-wrapper {
            width: 100%;
            max-width: 650px;
            margin-right: 280px; /* متوافق تلقائياً مع حجم السايد بار */
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

        .form-group { margin-bottom: 22px; }

        .form-label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-muted);
            font-weight: 600;
            font-size: 0.9rem;
            padding-right: 2px;
        }

        .form-control {
            background: var(--input-bg);
            border: 1px solid var(--border-color);
            color: white !important;
            padding: 14px 18px;
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

        /* شبكة الحقول المتجاوبة */
        .row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* ستايل منطقة رفع الصور */
        .file-upload-wrapper {
            border: 2px dashed var(--border-color);
            padding: 25px 20px;
            border-radius: 14px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            background: rgba(255,255,255,0.01);
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
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
            font-size: 2rem;
            color: var(--primary);
            transition: all 0.3s ease;
        }
        
        .file-upload-wrapper:hover i {
            transform: translateY(-2px);
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

        /* الاستجابة للشاشات والأجهزة المختلفة */
        @media (max-width: 992px) {
            .main-wrapper { margin-right: 0; }
        }
        @media (max-width: 576px) {
            .custom-card { padding: 30px 20px; }
            .row-grid { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

<div class="main-wrapper">
    <div class="custom-card">
        <div class="header-title">
            <h2><i class="fa-solid fa-map-location-dot" style="margin-left: 8px;"></i> إضافة رحلة جديدة</h2>
            <p style="color: var(--text-muted); margin-top: 10px; font-size:0.9rem;">قم بإدخال تفاصيل الوجهة والرحلة الإرشادية لزوار موقعك</p>
        </div>

        <form method="POST" enctype="multipart/form-data">
            
            <div class="form-group">
                <label class="form-label">عنوان الرحلة / الوجهة</label>
                <input type="text" name="title" class="form-control" placeholder="مثلاً: مغامرة التخييم في وادي رم" required>
            </div>

            <div class="row-grid">
                <div class="form-group">
                    <label class="form-label">تكلفة الرحلة (JOD)</label>
                    <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                </div>
                <div class="form-group">
                    <label class="form-label">مدة الرحلة (مثال: يومين)</label>
                    <input type="text" name="duration" class="form-control" placeholder="مثلاً: 3 أيام / ليلتين" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">المحافظة / الموقع الجغرافي</label>
                <input type="text" name="location" class="form-control" placeholder="مثلاً: العقبة، الأردن" required>
            </div>

            <div class="form-group">
                <label class="form-label">الصورة التعبيرية للرحلة</label>
                <div class="file-upload-wrapper" id="uploadArea" onclick="document.getElementById('tripImage').click()">
                    <i class="fa-solid fa-cloud-arrow-up" id="uploadIcon"></i>
                    <p id="fileName" style="margin:0; font-size: 0.85rem; color: var(--text-muted); font-weight: 600;">اسحب غلاف الرحلة هنا أو اضغط للاختيار</p>
                    <input type="file" name="image" id="tripImage" hidden accept="image/*" required onchange="updateFileName(this)">
                </div>
            </div>

            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-plus-circle"></i> نشر وإضافة الرحلة الآن
            </button>

            <a href="manage_trips.php" class="btn-cancel">إلغاء والعودة للقائمة</a>

        </form>
    </div>
</div>

<script>
    // تحديث تفاعلي لمنطقة الرفع عند اختيار الصورة بنجاح
    function updateFileName(input) {
        if (input.files && input.files[0]) {
            const fileName = input.files[0].name;
            const uploadArea = document.getElementById('uploadArea');
            const uploadIcon = document.getElementById('uploadIcon');
            const fileNameTxt = document.getElementById('fileName');
            
            fileNameTxt.innerText = "تم اختيار غلاف: " + fileName;
            fileNameTxt.style.color = "var(--success)";
            uploadIcon.style.color = "var(--success)";
            uploadIcon.className = "fa-solid fa-circle-check";
            uploadArea.classList.add('file-selected');
        }
    }
</script>

</body>
</html>