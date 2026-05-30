<?php 
include 'includes/db_config.php'; 
include 'includes/header.php'; 
?>

<style>
    :root { --primary-color: #ff6600; }
    body { background-color: #0b0b0b; color: #ffffff; }
    
    .about-header {
        background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                    url('https://images.unsplash.com/photo-1547234935-80c7145ec969?q=80&w=1200') center/cover;
        height: 40vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .card-about {
        background: #1a1a1a;
        border: 1px solid #333;
        border-radius: 15px;
        padding: 30px;
        height: 100%;
        transition: 0.3s;
    }

    .card-about:hover {
        border-color: var(--primary-color);
        transform: translateY(-5px);
    }

    .icon-box {
        width: 60px;
        height: 60px;
        background: rgba(255, 102, 0, 0.1);
        color: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
        font-size: 24px;
    }
</style>

<div class="about-header">
    <div class="container">
        <h1 class="display-3 fw-bold text-warning">عن MeshRider</h1>
        <p class="lead">بوابتك الذكية لاستكشاف المملكة الأردنية الهاشمية</p>
    </div>
</div>

<div class="container py-5">
    <div class="row g-5 align-items-center mb-5">
        <div class="col-lg-6">
            <h2 class="text-warning mb-4">رؤيتنا التقنية</h2>
            <p style="text-align: justify; line-height: 1.8;">
                بدأ مشروع **MeshRider** كفكرة طموحة ضمن مشروع تخرج في تخصص هندسة البرمجيات. هدفنا ليس مجرد إنشاء موقع سياحي تقليدي، بل بناء منصة ذكية تدمج بين تكنولوجيا الويب وتطوير تجربة المستخدم لتسهيل الوصول إلى المواقع السياحية الأردنية.
            </p>
            <p style="text-align: justify; line-height: 1.8;">
                نحن نؤمن بأن التكنولوجيا هي المفتاح لترويج السياحة في الأردن بشكل عصري، من خلال أنظمة حجز دقيقة وإدارة ذكية للرحلات تحت إشراف طاقم إداري متمكن.
            </p>
        </div>
        <div class="col-lg-6 text-center">
            <img src="https://images.unsplash.com/photo-1595859132107-7429188e658f?q=80&w=600" class="img-fluid rounded-4 shadow-lg" alt="Jordan Tourism">
        </div>
    </div>

    <div class="row g-4 text-center mt-5">
        <div class="col-md-4">
            <div class="card-about">
                <div class="icon-box mx-auto"><i class="fas fa-code"></i></div>
                <h4 class="text-white">تطوير مستمر</h4>
                <p class="text-muted small">استخدام أحدث تقنيات الويب PHP و MySQL لضمان سرعة وأمان البيانات.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-about">
                <div class="icon-box mx-auto"><i class="fas fa-map-marked-alt"></i></div>
                <h4 class="text-white">دعم السياحة</h4>
                <p class="text-muted small">تسليط الضوء على المعالم المخفية في الأردن وتوفير معلومات دقيقة عنها.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-about">
                <div class="icon-box mx-auto"><i class="fas fa-user-shield"></i></div>
                <h4 class="text-white">إدارة احترافية</h4>
                <p class="text-muted small">نظام إداري يسمح للأدمن بمتابعة الحجوزات وضمان جودة الخدمة.</p>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>