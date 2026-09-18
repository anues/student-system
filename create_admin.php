<?php
require_once 'db.php';

// حذف الجدول القديم إن وجد لضمان نظافته
$pdo->exec("DROP TABLE IF EXISTS users");

// إنشاء الجدول من جديد
$pdo->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL
)");

// كلمة المرور هنا مشفرة بـ md5 لتتوافق مع كود login.php (كلمة المرور هي: 123)
$username = 'admin';
$password = md5('123');

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute([$username, $password]);

echo "تم إنشاء الحساب بنجاح!<br>";
echo "اسم المستخدم: <b>admin</b><br>";
echo "كلمة المرور: <b>123</b><br>";
echo '<a href="login.php">اضغط هنا للانتقال لصفحة تسجيل الدخول</a>';
?>
