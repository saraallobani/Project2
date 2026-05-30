<?php
/**
 * شريط ثابت يربط بصفحة الدعم الذكي — يُضمَّن في صفحات المسافر بعد header.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    return;
}
$meshrider_support_current = basename($_SERVER['PHP_SELF'] ?? '');
$meshrider_is_chat_page = ($meshrider_support_current === 'chat.php');
?>
<div class="meshrider-support-bar" dir="rtl" lang="ar">
    <div class="container py-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2 text-white">
            <span class="rounded-circle bg-warning bg-opacity-25 d-flex align-items-center justify-content-center meshrider-support-bar-icon">
                <i class="fas fa-robot text-warning"></i>
            </span>
            <div>
                <strong class="d-block small text-warning">MeshRider — الدعم الذكي</strong>
                <span class="text-white-50" style="font-size: 0.8rem;">أسئلة عن الحجز، الدفع، التجمع، والوجهات — ردود فورية</span>
            </div>
        </div>
        <?php if (!$meshrider_is_chat_page): ?>
            <a href="chat.php" class="btn btn-warning btn-sm fw-bold rounded-pill px-3 shadow-sm text-dark">
                <i class="fas fa-comments ms-1"></i> فتح الشات
            </a>
        <?php else: ?>
            <span class="badge bg-secondary border border-warning text-warning px-3 py-2 rounded-pill">
                <i class="fas fa-circle text-success small me-1"></i> أنت في الشات
            </span>
        <?php endif; ?>
    </div>
</div>
<style>
    .meshrider-support-bar {
        background: linear-gradient(105deg, #14100a 0%, #1a1512 45%, #121212 100%);
        border-bottom: 1px solid rgba(230, 126, 34, 0.45);
        box-shadow: 0 4px 20px rgba(0,0,0,0.35);
        position: relative;
        z-index: 1020;
    }
    .meshrider-support-bar-icon {
        width: 36px;
        height: 36px;
        min-width: 36px;
    }
</style>
