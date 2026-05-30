<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

include 'includes/header.php';
include 'includes/meshrider_support_bar.php';

$thread = isset($_SESSION['meshrider_support_chat']) && is_array($_SESSION['meshrider_support_chat'])
    ? $_SESSION['meshrider_support_chat']
    : [];

$display_name = 'مستكشف';
if (!empty($_SESSION['user_name'])) {
    $display_name = $_SESSION['user_name'];
} elseif (!empty($_SESSION['user_id'])) {
    $display_name = 'مسافر #' . (int) $_SESSION['user_id'];
}
?>

<style>
    :root { --chat-primary: #ff9d47; --chat-bg: #0b0b0b; --chat-card: #121212; }
    body { background: var(--chat-bg); direction: rtl; text-align: right; }
    .support-wrap { margin-top: 0.5rem; }
    .support-hero {
        text-align: center;
        padding: 1.5rem 0 0.5rem;
    }
    .support-hero h1 { color: #fff; font-weight: 900; font-size: 1.75rem; }
    .support-hero p { color: #888; max-width: 520px; margin: 0.5rem auto 0; font-size: 0.95rem; }
    .chat-shell {
        background: var(--chat-card);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 24px 48px rgba(0,0,0,0.55);
    }
    .chat-head {
        background: linear-gradient(90deg, #1a1208, #121212);
        border-bottom: 1px solid rgba(255,157,71,0.25);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    .chat-head-title { color: var(--chat-primary); font-weight: 800; font-size: 1.05rem; }
    .chat-head-badge {
        font-size: 0.7rem;
        background: rgba(255,157,71,0.15);
        color: #ffc078;
        padding: 0.25rem 0.65rem;
        border-radius: 999px;
        border: 1px solid rgba(255,157,71,0.35);
    }
    #chatBox {
        background: #080808;
        height: min(52vh, 420px);
        overflow-y: auto;
        padding: 1.25rem;
    }
    .bubble-row { display: flex; margin-bottom: 1rem; }
    .bubble-row.user { justify-content: flex-end; }
    .bubble-row.bot { justify-content: flex-start; }
    .bubble {
        max-width: 88%;
        padding: 0.85rem 1.1rem;
        border-radius: 18px;
        line-height: 1.55;
        font-size: 0.95rem;
    }
    .bubble.user {
        background: linear-gradient(135deg, #e67e22, #ff9d47);
        color: #111;
        border-bottom-right-radius: 4px;
    }
    .bubble.bot {
        background: #1e1e22;
        color: #eee;
        border: 1px solid #2a2a30;
        border-bottom-left-radius: 4px;
    }
    .bubble .who { font-size: 0.72rem; font-weight: 800; opacity: 0.85; margin-bottom: 0.35rem; }
    .bubble-time { font-size: 0.65rem; opacity: 0.55; margin-top: 0.4rem; }
    .quick-chips { display: flex; flex-wrap: wrap; gap: 0.5rem; justify-content: center; padding: 0 0.5rem 1rem; }
    .quick-chips button {
        border: 1px solid rgba(255,157,71,0.4);
        background: transparent;
        color: #ffc078;
        border-radius: 999px;
        padding: 0.35rem 0.85rem;
        font-size: 0.8rem;
        cursor: pointer;
        transition: 0.2s;
    }
    .quick-chips button:hover { background: rgba(255,157,71,0.12); }
    .chat-foot { background: #0e0e10; border-top: 1px solid #222; padding: 0.85rem 1rem; direction: rtl; }
    .chat-foot .chat-send-form {
        display: flex;
        flex-direction: row;
        align-items: stretch;
        gap: 0;
        border: 2px solid rgba(255,157,71,0.55);
        border-radius: 16px;
        overflow: hidden;
        background: #16161a;
    }
    .chat-foot .form-control {
        background: transparent;
        border: none;
        color: #fff;
        border-radius: 0;
        padding: 14px 16px;
        font-size: 1rem;
        flex: 1;
        min-width: 0;
    }
    .chat-foot .form-control:focus {
        box-shadow: none;
        outline: none;
    }
    .chat-foot .btn-send {
        background: linear-gradient(180deg, #ffb347, var(--chat-primary));
        color: #111;
        font-weight: 900;
        border: none;
        border-inline-start: 2px solid rgba(0,0,0,0.2);
        padding: 12px 20px;
        min-width: 108px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        font-size: 1rem;
        letter-spacing: 0.02em;
        flex-shrink: 0;
    }
    .chat-foot .btn-send:hover {
        filter: brightness(1.06);
        color: #000;
    }
    .chat-foot .btn-send i { font-size: 1.1rem; }
    .chat-foot .btn-send .btn-send-label { font-weight: 900; }
    .empty-hint {
        text-align: center;
        color: #666;
        padding: 2rem 1rem;
    }
    .empty-hint i { font-size: 2.5rem; opacity: 0.25; margin-bottom: 0.75rem; display: block; }
</style>

<div class="container support-wrap pb-5">
    <div class="support-hero">
        <h1><i class="fas fa-robot text-warning me-2"></i> الدعم الذكي</h1>
        <p>اسأل عن الحجز، الدفع، الإلغاء، الأسعار، أو نقطة الانطلاق — الرد فوري حسب أسئلة المسافرين الشائعة.</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="chat-shell">
                <div class="chat-head">
                    <span class="chat-head-title"><i class="fas fa-headset me-2"></i>MeshRider Assist</span>
                    <span class="chat-head-badge">مساعد آلي — ليس محادثة حية</span>
                </div>

                <div class="quick-chips">
                    <button type="button" onclick="meshQuick('كيف أحجز رحلة؟')">كيف أحجز؟</button>
                    <button type="button" onclick="meshQuick('طرق الدفع والتأكيد')">الدفع</button>
                    <button type="button" onclick="meshQuick('سياسة الاسترداد والإلغاء')">استرداد / إلغاء</button>
                    <button type="button" onclick="meshQuick('أين نقطة الانطلاق؟')">نقطة الانطلاق</button>
                    <button type="button" onclick="meshQuick('ماذا أحضر معي في الرحلة؟')">ماذا أحضر؟</button>
                    <button type="button" onclick="meshQuick('رحلات مناسبة للعائلات والأطفال')">عائلات وأطفال</button>
                    <button type="button" onclick="meshQuick('الخريطة لا تظهر في الموقع')">مشكلة الخريطة</button>
                    <button type="button" onclick="meshQuick('نصائح لزيارة البتراء')">البتراء</button>
                    <button type="button" onclick="meshQuick('نصائح لوادي رم')">وادي رم</button>
                    <button type="button" onclick="meshQuick('مجموعة أو شركة نريد عرضاً')">مجموعات</button>
                    <button type="button" onclick="meshQuick('رحلة خاصة لعائلتنا')">رحلة خاصة</button>
                    <button type="button" onclick="meshQuick('فقدت غرضاً في الرحلة')">فقدت شيئاً</button>
                    <button type="button" onclick="meshQuick('الإنترنت والشريحة في الأردن')">إنترنت / شريحة</button>
                    <button type="button" onclick="meshQuick('كوبون خصم لا يعمل')">كوبون خصم</button>
                </div>

                <div class="card-body p-0">
                    <div id="chatBox">
                        <?php if (empty($thread)): ?>
                            <div class="empty-hint">
                                <i class="fas fa-comment-dots"></i>
                                <p class="mb-0">مرحباً <?= htmlspecialchars($display_name) ?> — اكتب سؤالك أو اختر اقتراحاً أعلاه.</p>
                            </div>
                        <?php endif; ?>

                        <?php foreach ($thread as $entry): ?>
                            <?php
                            $isUser = ($entry['role'] ?? '') === 'user';
                            $text = htmlspecialchars($entry['text'] ?? '', ENT_QUOTES, 'UTF-8');
                            $at = $entry['at'] ?? '';
                            $timeLabel = $at ? date('h:i A', strtotime($at)) : '';
                            ?>
                            <div class="bubble-row <?= $isUser ? 'user' : 'bot' ?>">
                                <div class="bubble <?= $isUser ? 'user' : 'bot' ?>">
                                    <div class="who"><?= $isUser ? htmlspecialchars($display_name) : 'MeshRider Assist' ?></div>
                                    <div><?= nl2br($text) ?></div>
                                    <?php if ($timeLabel): ?>
                                        <div class="bubble-time"><?= htmlspecialchars($timeLabel) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="chat-foot">
                    <form action="send_message.php" method="POST" class="chat-send-form" dir="rtl">
                        <input type="text" name="user_msg" id="userMsg" class="form-control"
                               placeholder="اكتب سؤالك هنا…" autocomplete="off" required maxlength="2000">
                        <button type="submit" class="btn-send" title="إرسال الرسالة">
                            <span class="btn-send-label">إرسال</span>
                            <i class="fas fa-paper-plane" aria-hidden="true"></i>
                        </button>
                    </form>
                    <?php if (!empty($thread)): ?>
                        <form action="send_message.php" method="POST" class="text-center mt-2 mb-0">
                            <input type="hidden" name="clear_chat" value="1">
                            <button type="submit" class="btn btn-link btn-sm text-secondary text-decoration-none">
                                <i class="fas fa-trash-alt me-1"></i> مسح المحادثة
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function meshQuick(t) {
    var i = document.getElementById('userMsg');
    if (i) { i.value = t; i.focus(); }
}
window.addEventListener('load', function () {
    var box = document.getElementById('chatBox');
    if (box) { box.scrollTop = box.scrollHeight; }
});
</script>

<?php include 'includes/footer.php'; ?>
