<?php
// 1. ضبط المنطقة الزمنية للتوقيت المحلي
date_default_timezone_set('Africa/Tripoli');

// 2. منع المتصفح من تخزين Cache
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: display_students.php");
    exit();
}

$id = $_GET['id'];

// جلب كافة بيانات الطالب من قاعدة البيانات
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    die("الطالب غير موجود!");
}

$current_date = date('Y / m / d');
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>استبيان طالب - <?= htmlspecialchars($student['full_name'] ?? '') ?></title>
    <style>
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Amiri', 'Traditional Arabic', 'Arial', sans-serif;
            background-color: #525659;
            margin: 0;
            padding: 20px;
            color: #000;
            direction: rtl;
        }

        .paper {
            background: #fff;
            width: 210mm;
            min-height: 297mm;
            padding: 12mm 15mm;
            margin: 0 auto;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            position: relative;
        }

        .page-border {
            border: 2px solid #000;
            outline: 1px solid #000;
            outline-offset: 4px;
            min-height: 270mm;
            padding: 20px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        /* الترويسة الرسمية */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }

        .header-right { text-align: right; vertical-align: middle; width: 40%; }
        .header-center { text-align: center; vertical-align: middle; width: 20%; }
        .header-left { text-align: left; vertical-align: middle; width: 40%; }

        .logo-img { max-height: 90px; width: auto; display: block; margin: 0 auto; }
        .univ-title-ar { font-size: 22px; font-weight: bold; color: #0d2861; margin: 0; line-height: 1.2; }
        .dept-title { font-size: 17px; font-weight: bold; color: #222; margin-top: 4px; }

        .date-box {
            border: 2px solid #000;
            border-radius: 4px;
            padding: 4px 12px;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            background: #fff;
            display: inline-block;
        }

        .doc-title {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin: 10px 0 20px 0;
            letter-spacing: 0.5px;
            background: #f0f0f0;
            padding: 6px;
            border: 1px solid #000;
        }

        /* رؤوس الأقسام */
        .section-title {
            font-size: 17px;
            font-weight: bold;
            color: #fff;
            background-color: #0d2861;
            padding: 6px 12px;
            margin-top: 18px;
            margin-bottom: 0;
            border: 1px solid #0d2861;
        }

        /* جداول البيانات المصممة لملء الصفحة */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 10px 12px;
            font-size: 16px;
            vertical-align: middle;
        }

        .lbl {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #111;
            white-space: nowrap;
            width: 18%;
        }

        .val {
            font-weight: bold;
            color: #000;
            width: 32%;
        }

        /* منطقة توقيع الطالب فقط */
        .footer-signatures {
            margin-top: auto;
            padding-top: 20px;
            border-top: 2px dashed #000;
            display: flex;
            justify-content: flex-end;
            padding-left: 30px;
        }

        .sig-box {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            min-width: 250px;
        }

        .sig-space {
            height: 60px;
        }

        /* أزرار التحكم */
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn { padding: 10px 25px; font-size: 16px; font-weight: bold; border-radius: 4px; cursor: pointer; border: none; text-decoration: none; display: inline-block; }
        .btn-print { background-color: #198754; color: white; margin-left: 10px; }
        .btn-back { background-color: #6c757d; color: white; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .paper { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .page-border { min-height: 275mm; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn btn-print">طباعة الاستبيان</button>
    <a href="display_students.php" class="btn btn-back">رجوع لقائمة الطلاب</a>
</div>

<div class="paper">
    <div class="page-border">
        <div>
            <!-- الترويسة -->
            <table class="header-table">
                <tr>
                    <td class="header-right">
                        <div class="univ-title-ar">جامعة المنار الساطع الطبية</div>
                        <div class="dept-title">إدارة المُسجّل العام</div>
                    </td>
                    
                    <td class="header-center">
                        <img src="logo.jpg" alt="شعار الجامعة" class="logo-img">
                    </td>

                    <td class="header-left">
                        <div class="date-box">التاريخ: <?= $current_date ?> م</div>
                    </td>
                </tr>
            </table>

            <div class="doc-title">استبيان بيانات طالب</div>

            <!-- أولاً: البيانات الشخصية والهوية والعنوان -->
            <div class="section-title">أولاً: البيانات الشخصية والهوية والعنوان</div>
            <table class="data-table">
                <tr>
                    <td class="lbl">الاسم الرباعي:</td>
                    <td class="val" colspan="3"><?= htmlspecialchars($student['full_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">اسم الأم الثلاثي:</td>
                    <td class="val" colspan="3"><?= htmlspecialchars($student['mother_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">تاريخ الميلاد:</td>
                    <td class="val"><?= htmlspecialchars($student['birth_date'] ?? '-') ?></td>
                    <td class="lbl">مكان الميلاد:</td>
                    <td class="val"><?= htmlspecialchars($student['birth_place'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">الجنس:</td>
                    <td class="val"><?= htmlspecialchars($student['gender'] ?? '-') ?></td>
                    <td class="lbl">الجنسية:</td>
                    <td class="val"><?= htmlspecialchars($student['nationality'] ?? 'ليبي') ?></td>
                </tr>
                <tr>
                    <td class="lbl">الرقم الوطني:</td>
                    <td class="val"><?= htmlspecialchars($student['national_id'] ?? '-') ?></td>
                    <td class="lbl">العنوان الحالي:</td>
                    <td class="val"><?= htmlspecialchars($student['current_address'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">رقم الجواز:</td>
                    <td class="val"><?= htmlspecialchars($student['passport_number'] ?? '-') ?></td>
                    <td class="lbl">صلاحية الجواز:</td>
                    <td class="val"><?= htmlspecialchars($student['passport_expiry'] ?? $student['passport_expiry_date'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">نوع / رقم الإقامة:</td>
                    <td class="val"><?= htmlspecialchars($student['residency_type'] ?? $student['residency_number'] ?? '-') ?></td>
                    <td class="lbl">انتهاء الإقامة:</td>
                    <td class="val"><?= htmlspecialchars($student['residency_expiry'] ?? $student['residency_expiry_date'] ?? '-') ?></td>
                </tr>
            </table>

            <!-- ثانياً: البيانات الأكاديمية والتسجيل -->
            <div class="section-title">ثانياً: البيانات الأكاديمية وحالة القيد بالجامعة</div>
            <table class="data-table">
                <tr>
                    <td class="lbl">رقم القيد الدراسي:</td>
                    <td class="val"><?= htmlspecialchars($student['academic_id'] ?? '-') ?></td>
                    <td class="lbl">الكلية / البرنامج:</td>
                    <td class="val"><?= htmlspecialchars($student['program_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">المستوى / الفصل:</td>
                    <td class="val"><?= htmlspecialchars($student['academic_level'] ?? '-') ?></td>
                    <td class="lbl">حالة القيد:</td>
                    <td class="val"><?= htmlspecialchars($student['enrollment_status'] ?? 'نظامي') ?></td>
                </tr>
                <tr>
                    <td class="lbl">سنة القبول:</td>
                    <td class="val"><?= htmlspecialchars($student['admission_year'] ?? '-') ?> م</td>
                    <td class="lbl">العام الجامعي:</td>
                    <td class="val"><?= htmlspecialchars($student['academic_year'] ?? $student['high_school_name'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">تاريخ التسجيل:</td>
                    <td class="val" colspan="3"><?= htmlspecialchars($student['registration_date'] ?? '-') ?></td>
                </tr>
            </table>

            <!-- ثالثاً: بيانات المؤهل السابق -->
            <div class="section-title">ثالثاً: بيانات الشهادة الثانوية / المؤهل السابق</div>
            <table class="data-table">
                <tr>
                    <td class="lbl">سنة الحصول عليها:</td>
                    <td class="val"><?= htmlspecialchars($student['high_school_year'] ?? '-') ?></td>
                    <td class="lbl">التقدير العام:</td>
                    <td class="val"><?= htmlspecialchars($student['high_school_grade'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td class="lbl">نسبة النجاح:</td>
                    <td class="val"><?= htmlspecialchars($student['high_school_percentage'] ?? '-') ?> %</td>
                    <td class="lbl">معادلة الشهادة:</td>
                    <td class="val"><?= (!empty($student['is_equated']) && $student['is_equated'] == 1) ? 'نعم (معادلة)' : 'لا' ?></td>
                </tr>
            </table>
        </div>

        <!-- رابعاً: توقيع الطالب فقط -->
        <div class="footer-signatures">
            <div class="sig-box">
                توقيع الطالب/ة
                <div class="sig-space"></div>
                ..........................................
            </div>
        </div>

    </div>
</div>

</body>
</html>