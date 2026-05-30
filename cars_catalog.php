<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }

include 'includes/header.php'; 

$final_conn = mysqli_connect("localhost", "root", "", "meshrider_db");

if (!$final_conn) {
    die("<div class='alert alert-danger text-center'>فشل الاتصال: " . mysqli_connect_error() . "</div>");
}

$sql = "SELECT id, car_type, price_per_day, image FROM cars";
$result = mysqli_query($final_conn, $sql);
?>

<style>
    :root {
        --accent: #e67e22;
        --card-bg: #141414;
    }

    body { background-color: #0a0a0a; color: #fff; font-family: 'Cairo', sans-serif; }

    .car-card {
        background: var(--card-bg);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 25px;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        position: relative;
    }

    .car-card:hover {
        transform: translateY(-10px);
        border-color: var(--accent);
        box-shadow: 0 20px 40px rgba(0,0,0,0.6);
    }

    .car-img-container {
        position: relative;
        overflow: hidden;
        height: 220px;
        background: #1a1a1a; 
    }

    .car-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s;
    }

    .car-card:hover .car-img { transform: scale(1.1); }

    .price-tag {
        position: absolute;
        top: 15px;
        left: 15px;
        background: var(--accent);
        color: #000;
        padding: 5px 15px;
        border-radius: 50px;
        font-weight: 900;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 2;
    }

    .custom-date {
        background: #1d1d1d !important;
        border: 1px solid #333 !important;
        color: #fff !important;
        border-radius: 12px !important;
        font-size: 0.8rem !important;
    }

    .btn-book {
        background: linear-gradient(45deg, #e67e22, #ff9d47);
        color: #000 !important;
        border: none;
        border-radius: 15px;
        font-weight: 900;
        transition: 0.3s;
    }

    .btn-book:hover {
        transform: scale(1.05);
        box-shadow: 0 0 20px rgba(230, 126, 34, 0.4);
    }
</style>

<div class="container py-5 mt-5 text-end" dir="rtl">
    <div class="text-center mb-5">
        <h1 class="fw-bold" style="color: var(--accent);">أسطول <span class="text-white">السيارات</span></h1>
        <p class="text-muted">اختر الرفيق المثالي لرحلتك القادمة في الأردن</p>
        <div style="width: 60px; height: 4px; background: var(--accent); margin: 0 auto; border-radius: 2px;"></div>
    </div>

    <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while($car = mysqli_fetch_assoc($result)): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card car-card h-100">
                        <div class="car-img-container">
                            <img src="uploads/<?php echo $car['image']; ?>" class="car-img" alt="<?php echo $car['car_type']; ?>">
                            <div class="price-tag">
                                <?php echo $car['price_per_day']; ?> <small>JOD/يوم</small>
                            </div>
                        </div>
                        
                        <div class="card-body p-4">
                            <h4 class="card-title fw-bold text-white mb-3"><?php echo $car['car_type']; ?></h4>
                            
                            <form action="process_car_booking.php" method="POST">
                                <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
                                
                                <div class="row g-2 mb-4">
                                    <div class="col-6 text-start">
                                        <label class="small text-secondary mb-1">📅 الاستلام</label>
                                        <input type="date" name="pickup" class="form-control custom-date" required>
                                    </div>
                                    <div class="col-6 text-start">
                                        <label class="small text-secondary mb-1">📅 التسليم</label>
                                        <input type="date" name="return" class="form-control custom-date" required>
                                    </div>
                                </div>
                                
                                <button type="submit" name="book_now" class="btn btn-book w-100 py-3">
                                    تأكيد الحجز <i class="fas fa-chevron-left ms-2"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fas fa-car-side fa-4x text-muted mb-3"></i>
                <h4 class="text-white-50">لا يوجد سيارات متاحة حالياً.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>