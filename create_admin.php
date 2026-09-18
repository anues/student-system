<?php
require 'db.php';

// حذف الجدول القديم إن وجد لإنشاء جدول نظيف ومتوافق
$pdo->exec("DROP TABLE IF EXISTS users");

// إنشاء الجدول من جديد بالأعمدة القياسية
$pdo->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL
)");

// إدخال حساب المسؤول (اسم المستخدم: admin ، كلمة المرور: 123)
$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
$stmt->execute(['admin', '123']);

echo "تم إنشاء قاعدة البيانات والحساب بنجاح!<br>";
echo "اسم المستخدم: <b>admin</b><br>";
echo "كلمة المرور: <b>123</b><br>";
echo '<a href="login.php">اضغط هنا الانتقال لصفحة تسجيل الدخول</a>';
?>
