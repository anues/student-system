<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// مسار تخزين قاعدة بيانات SQLite داخل المشروع
$dbPath = __DIR__ . '/college.db';

try {
    // إنشاء الاتصال باستخدام PDO و SQLite
    $pdo = new PDO("sqlite:$dbPath", null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // إنشاء جدول افتراضي لتسجيل الدخول إذا لم يكن موجوداً
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL
    )");

} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
