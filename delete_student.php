<?php
require_once 'db.php';

// التحقق من وجود ID الطالب في الرابط
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // تنفيذ استعلام الحذف
        $stmt = $pdo->prepare("DELETE FROM students WHERE id = :id");
        $stmt->execute([':id' => $id]);
    } catch (PDOException $e) {
        die("حدث خطأ أثناء الحذف: " . $e->getMessage());
    }
}

// إعادة التوجيه التلقائي إلى صفحة عرض الطلاب
header("Location: display_students.php");
exit();
?>