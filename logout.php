<?php
session_start();
// حذف جميع متغيرات الجلسة
$_SESSION = array();

// تدمير الجلسة بالكامل
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// التوجيه إلى صفحة تسجيل الدخول
header("Location: login.php");
exit();
?>