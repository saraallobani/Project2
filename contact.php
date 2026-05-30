<?php 
include 'includes/db_config.php'; 
include 'includes/header.php'; 

$message_sent = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $msg = htmlspecialchars($_POST['message']);

    try {

        $sql = "INSERT INTO contact_messages (name, email, subject, message)
                VALUES (?, ?, ?, ?)";

        $stmt = $pdo->prepare($sql);

        $stmt->execute([$name, $email, $subject, $msg]);

        $message_sent = true;

    } catch (Exception $e) {

        $error_msg = "حدث خطأ أثناء الإرسال";

    }
}
?>

<style>
    :root { --primary: #ff6600; --dark: #0b0b0b; }
    body { background-color: var(--dark); color: #fff; }
    
    .contact-card {
        background: #151515;
        border: 1px solid #333;
        border-radius: 20px;
        padding: 40px;
    }
    .form-control {
        background: #222;
        border: 1px solid #444;
        color: #fff;
        border-radius: 10px;
        padding: 12px;
    }
    .form-control:focus {
        background: #222;
        border-color: var(--primary);
        color: #fff;
        box-shadow: none;
    }
    .info-box {
        background: rgba(255, 102, 0, 0.1);
        border-right: 4px solid var(--primary);
        padding: 20px;
        border-radius: 10px;
        margin-bottom: 20px;
    }
    .btn-send {
        background: var(--primary);
        color: #fff;
        font-weight: bold;
        border-radius: 30px;
        padding: 12px 40px;
        border: none;
        transition: 0.3s;
    }
    .btn-send:hover { background: #fff; color: var(--primary); transform: scale(1.05); }
</style>

<div class="container py-5 mt-5">
    <div class="text-center mb-5">
        <h1 class="display-4 fw-bold text-warning">تواصل معنا</h1>
        <p class="text-muted">نحن هنا للإجابة على استفساراتك حول رحلتك القادمة في الأردن</p>
    </div>

    <div class="row g-5">
        <div class="col-lg-4">
            <div class="info-box">
                <h5 class="text-warning"><i class="fas fa-map-marker-alt me-2"></i> موقعنا</h5>
                <p class="mb-0">عمان، الأردن - مشروع تخرج MeshRider</p>
            </div>
            <div class="info-box">
                <h5 class="text-warning"><i class="fas fa-phone me-2"></i> الهاتف</h5>
                <p class="mb-0">+962 7X XXX XXXX</p>
            </div>
            <div class="info-box">
                <h5 class="text-warning"><i class="fas fa-envelope me-2"></i> البريد الإلكتروني</h5>
                <p class="mb-0">info@meshrider.com</p>
            </div>
            
            <div class="mt-4">
                <h5 class="mb-3 text-white">تابعنا على:</h5>
                <div class="d-flex gap-3">
                    <a href="#" class="text-warning fs-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-warning fs-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-warning fs-3"><i class="fab fa-twitter"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="contact-card">
                <?php if($message_sent): ?>
                    <div class="alert alert-success border-0 bg-success text-white py-3">
                        <i class="fas fa-check-circle me-2"></i> تم إرسال رسالتك بنجاح! سيقوم الأدمن بالرد عليك قريباً.
                    </div>
                <?php endif; ?>

                <form action="contact.php" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">الاسم الكامل</label>
                            <input type="text" name="name" class="form-control" placeholder="أدخل اسمك" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" placeholder="example@mail.com" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">الموضوع</label>
                            <input type="text" name="subject" class="form-control" placeholder="عنوان الرسالة" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">رسالتك</label>
                            <textarea name="message" class="form-control" rows="5" placeholder="اكتب استفسارك هنا..." required></textarea>
                        </div>
                        <div class="col-12 text-end">
                            <button type="submit" class="btn-send">إرسال الرسالة <i class="fas fa-paper-plane ms-2"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>