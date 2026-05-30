<?php 
include 'includes/header.php'; 
?>

<style>
    :root {
        --success-green: #2ecc71;
        --bright-orange: #ff9d47;
        --pure-white: #ffffff;
        --bg-black: #000000;
        --card-bg: #111111;
    }

    body { 
        background-color: var(--bg-black); 
        color: var(--pure-white); 
        font-family: 'Cairo', sans-serif; 
    }

    /* حاوية النجاح المضيئة */
    .success-container {
        margin-top: 80px; 
        padding: 80px 40px; 
        background: var(--card-bg);
        border: 2px solid #222; 
        border-radius: 40px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.9);
    }

    /* حركة الإضاءة حول الحاوية */
    .success-container::before {
        content: "";
        position: absolute;
        top: -2px; left: -2px; right: -2px;
        height: 5px;
        background: linear-gradient(90deg, transparent, var(--bright-orange), transparent);
    }

    /* أيقونة الصح المتحركة */
    .check-icon {
        font-size: 100px; 
        color: var(--success-green);
        filter: drop-shadow(0 0 20px rgba(46, 204, 113, 0.4));
        animation: scaleIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    @keyframes scaleIn {
        from { transform: scale(0); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    /* النصوص بوضوح عالي */
    .success-title {
        color: var(--bright-orange) !important;
        font-weight: 900 !important;
        font-size: 3rem;
        margin-bottom: 20px;
        text-shadow: 0 0 20px rgba(230, 126, 34, 0.2);
    }

    .success-msg {
        color: #f0f0f0 !important; /* أبيض ساطع بدل الرمادي */
        font-weight: 600 !important;
        font-size: 1.25rem;
        max-width: 600px;
        margin: 0 auto 40px;
        line-height: 1.8;
    }

    /* تحسين الأزرار */
    .btn-action {
        border-radius: 50px; 
        padding: 18px 40px; 
        font-weight: 800;
        font-size: 1.1rem;
        transition: 0.4s; 
        text-decoration: none; 
        display: inline-block;
        min-width: 220px;
    }

    .btn-main { 
        background: linear-gradient(45deg, #e67e22, var(--bright-orange)); 
        color: #000 !important; 
        border: none; 
        box-shadow: 0 10px 25px rgba(230, 126, 34, 0.3);
    }
    .btn-main:hover { 
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(230, 126, 34, 0.5);
    }

    .btn-secondary-outline { 
        background: transparent; 
        color: var(--pure-white) !important; 
        border: 2px solid #444; 
    }
    .btn-secondary-outline:hover { 
        border-color: var(--bright-orange);
        color: var(--bright-orange) !important;
        background: rgba(255, 157, 71, 0.05);
    }

    /* أيقونات صغيرة بجانب النص */
    .info-badge {
        display: inline-block;
        background: #1a1a1a;
        padding: 8px 20px;
        border-radius: 50px;
        border: 1px solid #333;
        margin-bottom: 20px;
        color: var(--success-green);
        font-weight: 700;
    }
</style>

<div class="container text-center">
    <div class="success-container">
        
        <!-- بادج صغير يعطي ثقة -->
        <div class="info-badge">
            <i class="fas fa-shield-alt me-2"></i> تأكيد حجز آمن 100%
        </div>

        <div class="mb-4">
            <i class="fas fa-check-circle check-icon"></i>
        </div>

        <h1 class="success-title">تم الحجز بنجاح!</h1>
        
        <p class="success-msg">
            شكراً لثقتك بـ <span class="text-warning">MeshRider</span>. <br> 
            لقد استلمنا طلبك، يمكنك الآن الاسترخاء وسنقوم بالتواصل معك لتأكيد التفاصيل النهائية.
        </p>
        
        <div class="d-flex gap-3 justify-content-center flex-wrap">
            <a href="my_bookings.php" class="btn-action btn-main">
                <i class="fas fa-ticket-alt me-2"></i> مشاهدة حجوزاتي
            </a>
            <a href="trips.php" class="btn-action btn-secondary-outline">
                <i class="fas fa-search-location me-2"></i> استكشاف المزيد
            </a>
        </div>

        <div class="mt-5">
            <p class="text-muted small">هل لديك سؤال؟ <a href="contact.php" class="text-warning">تواصل معنا الآن</a></p>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>