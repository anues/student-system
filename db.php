<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// بيانات الاتصال السحابي (تأكد من وضع كلمة المرور القوية الجديدة هنا)
$host    = getenv('DB_HOST')     ?: 'sql10.freesqldatabase.com';
$dbname  = getenv('DB_NAME')     ?: 'sql10837288';
$user    = getenv('DB_USER')     ?: 'sql10837288';
$pass    = getenv('DB_PASS')     ?: 'كلمة_المرور_القوية_التي_عيّنتها'; 

try {
    // إنشاء الاتصال باستخدام PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die("خطأ في الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>
