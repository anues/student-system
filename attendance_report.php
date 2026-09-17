<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('Africa/Tripoli');

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'db.php';

// استقبال القيم من صفحة الفلتر
$academic_level = trim($_GET['academic_level'] ?? '');
$academic_year  = trim($_GET['academic_year'] ?? '');

// التحقق من وجود القيم
if (empty($academic_level) || empty($academic_year)) {
    die("<h3 style='font-family:Tahoma; color:red; text-align:center; margin-top:50px;'>خطأ: يرجى تحديد الفصل الدراسي والعام الجامعي من صفحة الفلتر أولاً!</h3><div style='text-align:center;'><a href='attendance_filter.php' style='font-family:Tahoma;'>الرجوع لصفحة الفلتر</a></div>");
}

// تنظيف الفصل الدراسي لمعالجة اختلاف الهمزات (مثل الأول / الاول)
$clean_level = str_replace(['أ', 'إ', 'آ'], 'ا', $academic_level);

// استعلام دقيق مطابق تماماً لهيكل الجدول الجديد
$stmt = $pdo->prepare("SELECT * FROM students 
                       WHERE (REPLACE(REPLACE(REPLACE(academic_level, 'أ', 'ا'), 'إ', 'ا'), 'آ', 'ا') = :clean_level 
                              OR academic_level = :orig_level) 
                       AND academic_year = :academic_year 
                       ORDER BY full_name ASC");

$stmt->execute([
    ':clean_level'   => $clean_level,
    ':orig_level'    => $academic_level,
    ':academic_year' => $academic_year
]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

$current_date = date('Y / m / d');
?>
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>كشف حضور وغياب - <?= htmlspecialchars($academic_level) ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }

        body {
            font-family: 'Traditional Arabic', 'Amiri', 'Times New Roman', serif;
            background-color: #525659;
            margin: 0;
            padding: 20px;
            color: #000;
        }

        .paper {
            background: #fff;
            width: 280mm;
            min-height: 195mm;
            padding: 10mm 15mm;
            margin: auto;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
            box-sizing: border-box;
            position: relative;
        }

        .page-border {
            border: 2px solid #000;
            outline: 1px solid #000;
            outline-offset: 4px;
            height: 100%;
            min-height: 180mm;
            padding: 12px 15px;
            position: relative;
            box-sizing: border-box;
        }

        .corner-tl { position: absolute; top: -8px; left: -8px; width: 20px; height: 20px; border-top: 4px solid #000; border-left: 4px solid #000; }
        .corner-tr { position: absolute; top: -8px; right: -8px; width: 20px; height: 20px; border-top: 4px solid #000; border-right: 4px solid #000; }
        .corner-bl { position: absolute; bottom: -8px; left: -8px; width: 20px; height: 20px; border-bottom: 4px solid #000; border-left: 4px solid #000; }
        .corner-br { position: absolute; bottom: -8px; right: -8px; width: 20px; height: 20px; border-bottom: 4px solid #000; border-right: 4px solid #000; }

        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .header-right { text-align: right; vertical-align: top; width: 35%; }
        .header-center { text-align: center; vertical-align: top; width: 30%; }
        .header-left { text-align: left; vertical-align: top; width: 35%; }

        .logo-img { max-width: 90px; height: auto; display: block; margin: 0 auto; }
        .univ-title-ar { font-size: 20px; font-weight: bold; color: #0d2861; margin: 0 0 3px 0; }
        .dept-title { font-size: 16px; font-weight: bold; color: #111; }

        .date-box {
            border: 2px solid #000;
            border-radius: 5px;
            padding: 2px 8px;
            font-weight: bold;
            font-size: 13px;
            text-align: center;
            display: inline-block;
            background: #fff;
        }

        .doc-title {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 5px 0 15px 0;
            text-decoration: underline;
            text-underline-offset: 5px;
        }

        .info-bar {
            display: flex;
            justify-content: space-between;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }

        .attendance-table th, .attendance-table td {
            border: 1px solid #000;
            padding: 7px 4px;
            text-align: center;
        }

        .attendance-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .signatures-table { width: 100%; margin-top: 30px; border-collapse: collapse; }
        .signatures-table td { vertical-align: top; font-size: 16px; font-weight: bold; }
        .sig-right { text-align: right; width: 50%; }
        .sig-left { text-align: left; width: 50%; }
        .sig-box-left { display: inline-block; text-align: right; }

        .no-print { text-align: center; margin-bottom: 15px; }
        .btn-print { background-color: #28a745; color: white; border: none; padding: 10px 25px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .btn-back { background-color: #6c757d; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; text-decoration: none; margin-right: 10px; }

        @media print {
            .no-print { display: none !important; }
            body { background: white; padding: 0; }
            .paper { box-shadow: none; margin: 0; width: 100%; min-height: auto; padding: 0; }
            .page-border { min-height: 185mm; }
        }
    </style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()" class="btn-print">طباعة الكشف</button>
    <a href="attendance_filter.php" class="btn-back">تغيير التصفية</a>
</div>

<div class="paper">
    <div class="page-border">
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>
        <div class="corner-bl"></div>
        <div class="corner-br"></div>

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
                    <div class="date-box">تاريخ السحب: <?= $current_date ?> م</div>
                </td>
            </tr>
        </table>

        <div class="doc-title">سجل كشف الحضور والغياب للطلاب</div>

        <div class="info-bar">
            <span>الفصل الدراسي: <?= htmlspecialchars($academic_level) ?></span>
            <span>العام الجامعي: <?= htmlspecialchars($academic_year) ?> م</span>
            <span>المادة/المقرر: .......................................</span>
            <span>أستاذ المادة: .......................................</span>
        </div>

        <table class="attendance-table">
            <thead>
                <tr>
                    <th style="width: 4%;">#</th>
                    <th style="width: 14%;">رقم القيد</th>
                    <th style="width: 32%;">اسم الطالب/ة الرباعي</th>
                    <th style="width: 10%;">المحاضرة 1</th>
                    <th style="width: 10%;">المحاضرة 2</th>
                    <th style="width: 10%;">المحاضرة 3</th>
                    <th style="width: 10%;">المحاضرة 4</th>
                    <th style="width: 10%;">ملاحظات</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($students) > 0): ?>
                    <?php foreach ($students as $index => $std): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= htmlspecialchars($std['academic_id']) ?></td>
                            <td style="text-align: right; padding-right: 8px; font-weight: bold;"><?= htmlspecialchars($std['full_name']) ?></td>
                            <td></td><td></td><td></td><td></td><td></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="padding: 20px; font-weight: bold; color: red;">لا يوجد طلاب مسجلون في هذا الفصل وهذا العام الجامعي في قاعدة البيانات.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <table class="signatures-table">
            <tr>
                <td class="sig-right">
                    توقيع أستاذ المادة/<br>
                    ............................................
                </td>
                <td class="sig-left">
                    <div class="sig-box-left">
                        يعتمد / مسجل الكلية<br>
                        ............................................
                    </div>
                </td>
            </tr>
        </table>

    </div>
</div>

</body>
</html>