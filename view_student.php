<?php
// 1. استدعاء ملف الاتصال بقاعدة البيانات
require_once 'db.php';

// 2. التحقق من وجود ID الطالب في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: display_students.php");
    exit();
}

$id = $_GET['id'];

// 3. جلب بيانات الطالب المحدد من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    die("الطالب غير موجود!");
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تفاصيل الطالب - <?= htmlspecialchars($student['full_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background-color: #f4f7f6; margin: 20px; }
        .card { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1f3c88; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #1f3c88; margin: 0; }
        
        /* تصميم قسم الملف الشخصي والصورة */
        .profile-header-container { display: flex; gap: 20px; align-items: center; background: #f8f9fa; padding: 15px; border-radius: 6px; border: 1px solid #eaeaea; margin-bottom: 20px; }
        .student-photo { width: 110px; height: 130px; border-radius: 6px; object-fit: cover; border: 2px solid #1f3c88; background-color: #e9ecef; display: flex; align-items: center; justify-content: center; text-align: center; font-size: 11px; color: #6c757d; }
        .student-quick-info h3 { margin: 0 0 8px 0; color: #1f3c88; font-size: 18px; }
        .student-quick-info p { margin: 4px 0; font-size: 14px; color: #495057; }

        .section-title { font-weight: bold; color: #1f3c88; margin-top: 20px; margin-bottom: 10px; background: #eef2f5; padding: 6px 10px; border-right: 4px solid #1f3c88; }
        .grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .info-item { background: #fafafa; padding: 8px 12px; border: 1px solid #eaeaea; border-radius: 4px; }
        .info-label { font-size: 12px; color: #666; font-weight: bold; display: block; margin-bottom: 3px; }
        .info-value { font-size: 14px; color: #222; font-weight: bold; }
        .full-width { grid-column: span 2; }
        
        .actions { margin-top: 25px; display: flex; gap: 10px; }
        .btn { padding: 8px 18px; text-decoration: none; border-radius: 4px; font-weight: bold; color: white; font-size: 14px; cursor: pointer; border: none; display: inline-flex; align-items: center; gap: 6px; }
        .btn-back { background: #6c757d; }
        .btn-edit { background: #007bff; }
        .btn-print { background: #28a745; }
        .btn:hover { opacity: 0.9; }
        
        @media print {
            .actions, .btn-back { display: none; }
            body { background: white; margin: 0; }
            .card { box-shadow: none; border: none; padding: 0; }
        }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        <h2>بطاقة بيانات الطالب</h2>
        <div class="actions">
            <button onclick="window.print()" class="btn btn-print"><i class="fa-solid fa-print"></i> طباعة</button>
            <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-edit"><i class="fa-solid fa-pen-to-square"></i> تعديل</a>
            <a href="display_students.php" class="btn btn-back"><i class="fa-solid fa-arrow-right"></i> رجوع</a>
        </div>
    </div>

    <!-- بطاقة تعريفية علوية تحتوي على الصورة والاسم الأساسي -->
    <div class="profile-header-container">
        <div>
            <?php if (!empty($student['photo']) && file_exists('uploads/' . $student['photo'])): ?>
                <img src="uploads/<?= htmlspecialchars($student['photo']) ?>" alt="صورة الطالب" class="student-photo">
            <?php else: ?>
                <div class="student-photo">
                    <span><i class="fa-solid fa-user fa-2x" style="color: #ccc; margin-bottom: 5px;"></i><br>لا توجد صورة</span>
                </div>
            <?php endif; ?>
        </div>
        <div class="student-quick-info">
            <h3><?= htmlspecialchars($student['full_name']) ?></h3>
            <p><strong>الرقم الدراسي:</strong> <?= htmlspecialchars($student['academic_id']) ?></p>
            <p><strong>البرنامج الدراسي:</strong> <?= htmlspecialchars($student['program_name']) ?> (<?= htmlspecialchars($student['academic_level']) ?>)</p>
            <p><strong>صفة القيد:</strong> <?= htmlspecialchars($student['enrollment_status']) ?></p>
        </div>
    </div>

    <!-- 1. البيانات الأساسية والهوية -->
    <div class="section-title">البيانات الأساسية والهوية</div>
    <div class="grid">
        <div class="info-item"><span class="info-label">الرقم الدراسي</span><span class="info-value"><?= htmlspecialchars($student['academic_id']) ?></span></div>
        <div class="info-item"><span class="info-label">الاسم الرباعي</span><span class="info-value"><?= htmlspecialchars($student['full_name']) ?></span></div>
        <div class="info-item"><span class="info-label">الجنس</span><span class="info-value"><?= htmlspecialchars($student['gender']) ?></span></div>
        <div class="info-item"><span class="info-label">الجنسية</span><span class="info-value"><?= htmlspecialchars($student['nationality']) ?></span></div>
        <div class="info-item"><span class="info-label">الرقم الوطني / القومي</span><span class="info-value"><?= htmlspecialchars($student['national_id'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">اسم الأم</span><span class="info-value"><?= htmlspecialchars($student['mother_name'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">تاريخ الميلاد</span><span class="info-value"><?= htmlspecialchars($student['birth_date'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">مكان الميلاد</span><span class="info-value"><?= htmlspecialchars($student['birth_place'] ?? '-') ?></span></div>
    </div>

    <!-- 2. بيانات السفر والإقامة -->
    <div class="section-title">بيانات السفر والإقامة</div>
    <div class="grid">
        <div class="info-item"><span class="info-label">رقم جواز السفر</span><span class="info-value"><?= htmlspecialchars($student['passport_number'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">تاريخ صلاحية الجواز</span><span class="info-value"><?= htmlspecialchars($student['passport_expiry'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">نوع الإقامة</span><span class="info-value"><?= htmlspecialchars($student['residency_type'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">تاريخ انتهاء الإقامة</span><span class="info-value"><?= htmlspecialchars($student['residency_expiry'] ?? '-') ?></span></div>
        <div class="info-item full-width"><span class="info-label">العنوان الحالي</span><span class="info-value"><?= htmlspecialchars($student['current_address'] ?? '-') ?></span></div>
    </div>

    <!-- 3. البيانات الأكاديمية -->
    <div class="section-title">البيانات الأكاديمية</div>
    <div class="grid">
        <div class="info-item"><span class="info-label">العام الجامعي</span><span class="info-value"><?= htmlspecialchars($student['academic_year'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">تاريخ التسجيل</span><span class="info-value"><?= htmlspecialchars($student['registration_date']) ?></span></div>
        <div class="info-item"><span class="info-label">سنة القبول</span><span class="info-value"><?= htmlspecialchars($student['admission_year']) ?></span></div>
        <div class="info-item"><span class="info-label">المستوى / الفصل الدراسي</span><span class="info-value"><?= htmlspecialchars($student['academic_level']) ?></span></div>
        <div class="info-item"><span class="info-label">البرنامج الدراسي</span><span class="info-value"><?= htmlspecialchars($student['program_name']) ?></span></div>
        <div class="info-item"><span class="info-label">صفة القيد</span><span class="info-value"><?= htmlspecialchars($student['enrollment_status']) ?></span></div>
    </div>

    <!-- 4. مؤهل الثانوية العامة -->
    <div class="section-title">مؤهل الثانوية العامة</div>
    <div class="grid">
        <div class="info-item"><span class="info-label">سنة الحصول عليها</span><span class="info-value"><?= htmlspecialchars($student['high_school_year'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">التقدير</span><span class="info-value"><?= htmlspecialchars($student['high_school_grade'] ?? '-') ?></span></div>
        <div class="info-item"><span class="info-label">نسبة النجاح</span><span class="info-value"><?= $student['high_school_percentage'] ? $student['high_school_percentage'] . '%' : '-' ?></span></div>
        <div class="info-item"><span class="info-label">معادلة الثانوية</span><span class="info-value"><?= $student['is_equated'] ? 'نعم (معادلة)' : 'لا' ?></span></div>
    </div>
</div>

</body>
</html>