<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("فشل الاتصال: " . mysqli_connect_error()); }

if (isset($_GET['id'])) {
    $trip_id = mysqli_real_escape_string($conn, $_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM trips WHERE id = '$trip_id'");
    $trip = mysqli_fetch_assoc($result);

    if (!$trip) {
        die("<div style='color:white; text-align:center; margin-top:50px; font-family:Cairo;'>الرحلة غير موجودة!</div>");
    }
} else {
    header("Location: manage_trips.php");
    exit();
}

if (isset($_POST['update_trip'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $price = $_POST['price'];
    $departure = mysqli_real_escape_string($conn, $_POST['departure_point']);
    $return = mysqli_real_escape_string($conn, $_POST['return_point']);
    
    $image_path = $trip['image']; 
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        $target_dir = "../uploads/";
        $file_name = time() . "_" . basename($_FILES["new_image"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["new_image"]["tmp_name"], $target_file)) {
            $image_path = "uploads/" . $file_name; 
        }
    }

    $sql_update = "UPDATE trips SET title='$title', location='$location', price='$price', 
                   image='$image_path', departure_point='$departure', return_point='$return' 
                   WHERE id='$trip_id'";

    if (mysqli_query($conn, $sql_update)) {
        echo "<script>alert('تم التعديل بنجاح!'); window.location.href='manage_trips.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل رحلة | Meshrider Admin</title>
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
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .edit-container {
            width: 100%;
            max-width: 700px;
            animation: slideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .edit-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .header-box {
            text-align: center;
            margin-bottom: 35px;
        }

        .header-box h2 {
            font-weight: 900;
            color: var(--primary);
            font-size: 1.6rem;
            margin: 0;
            text-shadow: 0 0 20px var(--primary-glow);
        }

        .preview-wrapper {
            position: relative;
            width: 100%;
            height: 240px;
            margin-bottom: 30px;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--border-color);
        }

        .trip-img-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .preview-wrapper:hover .trip-img-preview {
            transform: scale(1.03);
        }

        .upload-overlay {
            position: absolute;
            bottom: 0; left: 0; right: 0;
            background: linear-gradient(transparent, rgba(0,0,0,0.85));
            padding: 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .btn-upload-trigger {
            background: rgba(230, 126, 34, 0.2);
            border: 1px solid var(--primary);
            color: #fff;
            padding: 8px 20px;
            border-radius: 10px;
            cursor: pointer;
            font-family: 'Cairo', sans-serif;
            font-weight: 600;
            font-size: 0.85rem;
            backdrop-filter: blur(4px);
            transition: all 0.3s ease;
        }

        .btn-upload-trigger:hover {
            background: var(--primary);
            box-shadow: 0 0 15px var(--primary-glow);
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
            color: #fff;
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

        .row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .btn-save {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 15px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 1rem;
            width: 100%;
            cursor: pointer;
            margin-top: 15px;
            box-shadow: 0 4px 15px var(--primary-glow);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px var(--primary-glow);
            background: #f39c12;
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: all 0.2s ease;
            padding: 8px;
            border-radius: 8px;
        }

        .btn-cancel:hover { 
            color: var(--danger); 
            background: rgba(239, 68, 68, 0.05);
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }

        @media (max-width: 576px) {
            .edit-card { padding: 25px 20px; }
            .row-grid { grid-template-columns: 1fr; gap: 0; }
        }
    </style>
</head>
<body>

<div class="edit-container">
    <div class="edit-card">
        <div class="header-box">
            <h2><i class="fa-solid fa-pen-to-square"></i> تعديل تفاصيل الرحلة</h2>
        </div>
        
        <form method="POST" enctype="multipart/form-data"> 
            
            <div class="preview-wrapper">
                <img src="../<?php echo $trip['image']; ?>" class="trip-img-preview" id="imgView">
                <div class="upload-overlay">
                    <input type="file" name="new_image" id="fileInput" accept="image/*" onchange="previewImage(event)" style="display:none;">
                    <button type="button" class="btn-upload-trigger" onclick="document.getElementById('fileInput').click()">
                        <i class="fa-solid fa-camera"></i> تغيير الصورة
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">اسم الوجهة / الموقع</label>
                <input type="text" name="title" class="form-control" value="<?php echo $trip['title']; ?>" placeholder="مثلاً: رحلة وادي رم" required>
            </div>

            <div class="row-grid">
                <div class="form-group">
                    <label class="form-label">المحافظة</label>
                    <input type="text" name="location" class="form-control" value="<?php echo $trip['location']; ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label">السعر (JOD)</label>
                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $trip['price']; ?>" required>
                </div>
            </div>

            <div class="row-grid">
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-location-dot" style="color: var(--primary); margin-left:5px;"></i> نقطة الانطلاق</label>
                    <input type="text" name="departure_point" class="form-control" value="<?php echo $trip['departure_point']; ?>">
                </div>
                <div class="form-group">
                    <label class="form-label"><i class="fa-solid fa-flag-checkered" style="color: var(--primary); margin-left:5px;"></i> نقطة العودة</label>
                    <input type="text" name="return_point" class="form-control" value="<?php echo $trip['return_point']; ?>">
                </div>
            </div>

            <button type="submit" name="update_trip" class="btn-save">
                <i class="fa-solid fa-floppy-disk"></i> حفظ التغييرات
            </button>
            
            <a href="manage_trips.php" class="btn-cancel">إلغاء التعديل والعودة</a>
        </form>
    </div>
</div>

<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById('imgView');
            output.src = reader.result;
            output.style.opacity = '0';
            setTimeout(() => { output.style.opacity = '1'; }, 100);
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>

</body>
</html>