<?php 
$conn = mysqli_connect("localhost", "root", "", "meshrider_db");
if (!$conn) { die("خطأ في الاتصال: " . mysqli_connect_error()); }

if (session_status() === PHP_SESSION_NONE) { session_start(); }

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/header.php'; 

$trip_id = isset($_GET['trip_id']) ? intval($_GET['trip_id']) : 0;

$query = "SELECT * FROM trips WHERE id = $trip_id";
$result = mysqli_query($conn, $query);
$trip = mysqli_fetch_assoc($result);

if (!$trip) {
    die("<div class='container mt-5 py-5 text-center text-white'><h2>عذراً، الرحلة غير متوفرة حالياً.</h2><a href='trips.php' class='btn btn-warning mt-3'>العودة للرحلات</a></div>");
}

// جلب المقاعد المحجوزة مسبقاً لحساب المقاعد المتاحة بدقة
$booking_count_query = mysqli_query($conn, "
    SELECT SUM(seats_count) AS total_booked FROM bookings 
    WHERE trip_id = $trip_id AND status IN ('pending', 'confirmed', 'approved')
");
$booking_data = mysqli_fetch_assoc($booking_count_query);
$already_booked = intval($booking_data['total_booked'] ?? 0);
$total_seats = isset($trip['total_seats']) ? intval($trip['total_seats']) : 30;
$available_seats = $total_seats - $already_booked;

// تخزين سعر الرحلة الأساسي للاستخدام في الـ JavaScript
$base_price = floatval($trip['price']);
?>

<style>
    :root {
        --primary: #ff9d47; 
        --card-dark: #141414;
    }

    body { background-color: #0a0a0a; color: #fff; font-family: 'Cairo', sans-serif; text-align: right; }

    .checkout-container { max-width: 900px; margin: 60px auto; padding: 0 20px; }

    .glass-card {
        background: var(--card-dark);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 30px;
        padding: 40px;
        box-shadow: 0 25px 50px rgba(0,0,0,0.5);
    }

    .ticket-summary {
        background: linear-gradient(135deg, #1a1a1a 0%, #000 100%);
        border-radius: 20px;
        padding: 25px;
        border-right: 6px solid var(--primary);
        margin-bottom: 35px;
    }

    .ticket-price { font-size: 2rem; font-weight: 900; color: var(--primary); }

    .custom-input {
        background: #1d1d1d !important;
        border: 1px solid #333 !important;
        color: #fff !important;
        border-radius: 15px !important;
        padding: 15px !important;
        text-align: right;
    }
    .custom-input:focus { border-color: var(--primary) !important; box-shadow: 0 0 10px rgba(255, 157, 71, 0.2) !important; }

    .payment-options {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .payment-method-card {
        background: #1a1a1a;
        border: 2px solid #333;
        padding: 20px;
        border-radius: 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    .payment-method-card i { font-size: 2rem; margin-bottom: 10px; display: block; }

    .payment-method-card.active {
        border-color: var(--primary);
        background: rgba(255, 157, 71, 0.05);
        transform: translateY(-5px);
    }

    .payment-method-card.active i { color: var(--primary) !important; }

    .payment-method-card input { display: none; }

    .btn-confirm {
        background: linear-gradient(45deg, #e67e22, var(--primary));
        color: #000 !important;
        border: none;
        padding: 20px;
        border-radius: 50px;
        font-weight: 900;
        font-size: 1.3rem;
        width: 100%;
        margin-top: 30px;
        box-shadow: 0 10px 25px rgba(230, 126, 34, 0.3);
    }

    .btn-confirm:hover { transform: scale(1.02); }
</style>

<div class="checkout-container">
    <div class="glass-card">
        <div class="text-center mb-5">
            <h2 class="fw-900">تأكيد <span style="color: var(--primary);">الحجز</span></h2>
            <p class="text-secondary">أنت على بعد خطوة واحدة من رحلتك القادمة</p>
        </div>

        <div class="ticket-summary">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1"><?php echo $trip['title']; ?></h4>
                    <p class="text-secondary m-0"><i class="fas fa-location-dot me-2"></i> <?php echo $trip['location']; ?></p>
                    <span class="badge bg-dark border border-secondary mt-2 text-warning">المقاعد المتاحة: <?php echo $available_seats; ?></span>
                </div>
                <div class="col-md-4 text-md-start text-end mt-3 mt-md-0">
                    <div class="ticket-price"><span id="display_total_price"><?php echo $base_price; ?></span> <small style="font-size: 1rem;">JOD</small></div>
                </div>
            </div>
        </div>

        <?php if ($available_seats <= 0): ?>
            <div class="alert alert-danger text-center rounded-4">للأسف كابتن، جميع مقاعد هذه الرحلة ممتلئة بالكامل حالياً!</div>
        <?php else: ?>

        <form action="process_booking.php" method="POST" id="bookingForm">
            <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" name="trip_id" value="<?php echo $trip['id']; ?>">
            <input type="hidden" name="total_price" id="hidden_total_price" value="<?php echo $base_price; ?>">

            <div class="row">
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">الاسم بالكامل</label>
                    <input type="text" name="full_name" class="form-control custom-input" 
                           value="<?php echo $_SESSION['user_name'] ?? ''; ?>" required>
                </div>
                <div class="col-md-6 mb-4">
                    <label class="form-label fw-bold">رقم الهاتف</label>
                    <input type="tel" name="phone" class="form-control custom-input" 
                           placeholder="07XXXXXXXX" required>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 mb-4">
                    <label class="form-label fw-bold text-warning">عدد المقاعد المطلوبة</label>
                    <input type="number" name="seats_count" id="seats_count" class="form-control custom-input" 
                           value="1" min="1" max="<?php echo $available_seats; ?>" required>
                    <div class="form-text text-muted">يمكنك حجز أي عدد من المقاعد المتبقية والمتاحة حالياً لهذه الرحلة.</div>
                </div>
            </div>

            <div class="mb-5">
                <label class="form-label fw-bold d-block mb-3">اختر طريقة الدفع</label>
                <div class="payment-options">
                    
                    <div class="payment-method-card active" onclick="selectPayment(this, 'cash')">
                        <i class="fas fa-hand-holding-dollar text-muted"></i>
                        <span class="fw-bold">دفع نقدي</span>
                        <input type="radio" name="payment_method" value="cash" checked>
                    </div>
                    
                    <div class="payment-method-card" onclick="selectPayment(this, 'cliq')">
                        <i class="fas fa-mobile-screen text-muted"></i>
                        <span class="fw-bold">CliQ</span>
                        <input type="radio" name="payment_method" value="cliq">
                    </div>
                    
                    <div class="payment-method-card" onclick="selectPayment(this, 'card')">
                        <i class="fas fa-credit-card text-muted"></i>
                        <span class="fw-bold">بطاقة بنكية</span>
                        <input type="radio" name="payment_method" value="card">
                    </div>
                </div>
            </div>

            <div class="p-3 rounded-4 bg-black border border-secondary mb-4">
                <div class="form-check d-flex align-items-center gap-3 justify-content-end">
                    <label class="form-check-label small text-secondary" for="terms">
                        أوافق على <a href="#" class="text-warning">شروط وأحكام</a> MeshRider
                    </label>
                    <input class="form-check-input" type="checkbox" id="terms" required>
                </div>
            </div>

            <button type="submit" class="btn-confirm" id="submitBtn">
                تأكيد حجز (1) مقعد <i class="fas fa-chevron-left ms-2"></i>
            </button>
        </form>

        <?php endif; ?>
    </div>
</div>

<script>
const basePrice = <?php echo $base_price; ?>;

const seatsInput = document.getElementById('seats_count');
const displayPrice = document.getElementById('display_total_price');
const hiddenPrice = document.getElementById('hidden_total_price');
const submitBtn = document.getElementById('submitBtn');

if (seatsInput) {
    seatsInput.addEventListener('input', function() {
        let count = parseInt(this.value) || 1;
        let maxAvailable = parseInt(this.max);
        
        if (count > maxAvailable) {
            count = maxAvailable;
            this.value = maxAvailable;
        }
        if (count < 1) {
            count = 1;
            this.value = 1;
        }

        let total = basePrice * count;
        
        displayPrice.innerText = total.toFixed(2);
        hiddenPrice.value = total.toFixed(2);
        submitBtn.innerHTML = `تأكيد حجز (${count}) مقاعد <i class="fas fa-chevron-left ms-2"></i>`;
    });
}

function selectPayment(element, value) {
    document.querySelectorAll('.payment-method-card').forEach(card => {
        card.classList.remove('active');
    });
    element.classList.add('active');
    const radio = element.querySelector('input[type="radio"]');
    radio.checked = true;
}
</script>

<?php include 'includes/footer.php'; ?>