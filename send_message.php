<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_chat'])) {
    unset($_SESSION['meshrider_support_chat']);
    header('Location: chat.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['user_msg'])) {
    header('Location: chat.php');
    exit();
}

require_once __DIR__ . '/includes/support_bot.php';

$user_msg = trim((string) $_POST['user_msg']);
if ($user_msg === '') {
    header('Location: chat.php');
    exit();
}

if (mb_strlen($user_msg, 'UTF-8') > 2000) {
    $user_msg = mb_substr($user_msg, 0, 2000, 'UTF-8');
}

if (!isset($_SESSION['meshrider_support_chat']) || !is_array($_SESSION['meshrider_support_chat'])) {
    $_SESSION['meshrider_support_chat'] = [];
}

$now = date('Y-m-d H:i:s');
$reply = meshrider_support_bot_reply($user_msg);

$_SESSION['meshrider_support_chat'][] = [
    'role' => 'user',
    'text' => $user_msg,
    'at' => $now,
];
$_SESSION['meshrider_support_chat'][] = [
    'role' => 'bot',
    'text' => $reply,
    'at' => $now,
];

if (count($_SESSION['meshrider_support_chat']) > 60) {
    $_SESSION['meshrider_support_chat'] = array_slice($_SESSION['meshrider_support_chat'], -60);
}

header('Location: chat.php');
exit();
