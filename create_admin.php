<?php
require_once 'db.php';

// إنشاء جدول المستخدمين إن لم يكن موجوداً
$pdo->exec("CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL
)");

// إنشاء جدول الطلاب الرئيسي إن لم يكن موجوداً
$pdo->exec("CREATE TABLE IF NOT EXISTS students (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT,
    phone TEXT,
    department TEXT
)");

// التأكد من وجود حساب المسؤول (admin / 123)
$stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
$stmt->execute();
if ($stmt->fetchColumn() == 0) {
    $password = md5('123');
    $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $insert->execute(['admin', $password]);
}

echo "تم إنشاء الجداول بنجاح تام!<br>";
echo '<a href="login.php">اضغط هنا للذهاب إلى تسجيل الدخول والانتقال للبنود</a>';
?>
