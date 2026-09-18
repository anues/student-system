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

    // إنشاء جدول المستخدمين لتسجيل الدخول
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT NOT NULL,
        password TEXT NOT NULL
    )");

    // إنشاء جدول الطلاب شاملاً لكافة الأعمدة التي يتطلبها نموذج الإضافة الخاص بك بدقة
    $pdo->exec("CREATE TABLE IF NOT EXISTS students (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        academic_id TEXT UNIQUE,
        full_name TEXT,
        gender TEXT,
        nationality TEXT,
        national_id TEXT,
        mother_name TEXT,
        birth_date TEXT,
        birth_place TEXT,
        passport_number TEXT,
        passport_expiry TEXT,
        residency_type TEXT,
        residency_expiry TEXT,
        current_address TEXT,
        registration_date TEXT,
        admission_year TEXT,
        academic_level TEXT,
        program_name TEXT,
        enrollment_status TEXT,
        academic_year TEXT,
        is_equated INTEGER DEFAULT 0,
        high_school_year TEXT,
        high_school_grade TEXT,
        high_school_percentage TEXT,
        photo TEXT
    )");

    // إنشاء حساب المسؤول الافتراضي (admin / 123) إن لم يكن موجوداً
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
