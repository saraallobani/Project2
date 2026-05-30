<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
include 'includes/header.php'; 

// تحديد نوع الحجز لعرض الروابط الصحيحة
$type = $_GET['type'] ?? 'trip';
$target_link = ($type == 'car') ? 'my_car_bookings.php' : 'my_bookings.php';
$target_text = ($type == 'car') ? 'مشاهدة حجوزات السيارات' : 'مشاهدة حجوزات الرحلات';
?>

<style>
    body { background: #0a0a0a; color: #fff; font-family: 'Cairo', sans-serif; }
    .success-container {
        max-width: 600px;
        margin: 100px auto;
        background: #111;
        padding: 50px;
        border-radius: 30px;
        border: 1px solid #222;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }
    .check-icon {
        font-size: 80px;
        color: #2ecc71;
        margin-bottom: 20px;
        display: block;
    }
    .btn-custom {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: bold;
        transition: 0.3s;
        margin: 10px;
        text-decoration: none;
        display: inline-block;
    }
    .btn-view { background: #e67e22; color: #000; }
    .btn-view:hover { background: #d35400; transform: scale(1.05); }
    .btn-dash { background: transparent; color: #fff; border: 1px solid #444; }
    .btn-dash:hover { background: #222; color: #e67e22; }
</style>

<div class="container">
    <div class="success-container">
        <i class="fas fa-check-circle check-icon"></i>
        <h2 class="fw-bold mb-3">تم استلام طلبك بنجاح!</h2>
        <p class="text-muted mb-5">شكراً لاختيارك MeshRider. لقد تم تسجيل حجز السيارة وسنقوم بالتواصل معك لتأكيد التفاصيل النهائية.</p>
        
        <div class="d-flex flex-column flex-md-row justify-content-center">
            <a href="<?php echo $target_link; ?>" class="btn-custom btn-view">
                <i class="fas fa-list-ul me-2"></i> <?php echo $target_text; ?>
            </a>
            <a href="dashboard.php" class="btn-custom btn-dash">
                <i class="fas fa-home me-2"></i> العودة للرئيسية
            </a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>