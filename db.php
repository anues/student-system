<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$dbPath = __DIR__ . '/college.db';

try {
    $pdo = new PDO("sqlite:$dbPath", null, null, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    // جدول المستخدمين
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL
    )");

    // جدول الطلاب شاملاً لكافة الأعمدة والأسماء البديلة المحتملة
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        academic_id TEXT,
        name TEXT,
        full_name TEXT,
        gender TEXT,
        nationality TEXT,
        mother_name TEXT,
        national_id TEXT,
        passport_no TEXT,
        passport_number TEXT,
        birth_place TEXT,
        birth_date TEXT,
        passport_expiry TEXT,
        residence_type TEXT,
        residency_type TEXT,
        residence_expiry TEXT,
        address TEXT,
        registration_date TEXT,
        admission_year TEXT,
        academic_level TEXT,
        department TEXT,
        phone TEXT,
        email TEXT
    )");

    // حساب المسؤول الافتراضي
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        $password = md5('123');
        $insert = $pdo->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
        $insert->execute(['admin', $password]);
    }

} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
