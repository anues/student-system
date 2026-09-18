<?php
require 'db.php';
$username = 'admin';
$password = password_hash('123456', PASSWORD_DEFAULT); // كلمة المرور ستكون 123456

$stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
try {
    $stmt->execute([$username, $password]);
    echo "تم إنشاء حساب المسؤول بنجاح! اسم المستخدم: admin | كلمة المرور: 123456";
} catch (PDOException $e) {
    echo "الحساب موجود مسبقاً أو حدث خطأ: " . $e->getMessage();
}
?>
