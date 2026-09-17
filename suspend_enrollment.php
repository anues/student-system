<?php
// 1. ضبط المنطقة الزمنية لتطابق التوقيت المحلي
date_default_timezone_set('Africa/Tripoli');

// 2. منع المتصفح من تخزين التاريخ القديم (Cache)
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

require_once 'db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: display_students.php");
    exit();
}

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    die("الطالب غير موجود!");
}

// 3. جلب التاريخ المباشر بناءً على التوقيت المحلي
$current_date = date('Y / m / d');
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>نموذج طلب إيقاف قيد - <?= htmlspecialchars($student['full_name']) ?></title>
    <style>
        @page {
            size: A4;
            margin: 10mm;
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
            width: 210mm;
            min-height: 297mm;
            padding: 15mm 20mm;
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
            min-height: 265mm;
            padding: 20px 25px;
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
            margin-bottom: 25px;
        }

        .header-right {
            text-align: right;
            vertical-align: top;
            width: 38%;
        }

        .header-center {
            text-align: center;
            vertical-align: top;
            width: 24%;
        }

        .header-left {
            text-align: left;
            vertical-align: top;
            width: 38%;
        }

        .logo-img {
            max-width: 110px;
            height: auto;
            display: block;
            margin: 0 auto;
        }

        .univ-title-ar {
            font-size: 24px;
            font-weight: bold;
            color: #0d2861;
            margin: 0 0 6px 0;
            line-height: 1.1;
        }

        .dept-title {
            font-size: 19px;
            font-weight: bold;
            color: #111;
        }

        .meta-container {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 8px;
        }

        .code-box, .date-box {
            border: 2px solid #000;
            border-radius: 5px;
            padding: 3px 12px;
            font-weight: bold;
            font-size: 15px;
            text-align: center;
            min-width: 160px;
            box-sizing: border-box;
            background: #fff;
        }

        .doc-title {
            text-align: center;
            font-size: 23px;
            font-weight: bold;
            margin: 25px 0 35px 0;
            text-decoration: underline;
            text-underline-offset: 8px;
        }

        .student-info-table {
            width: 100%;
            border-collapse: collapse;
            margin: 25px 0;
            font-size: 19px;
            line-height: 2;
        }

        .student-info-table td {
            padding: 8px 5px;
            vertical-align: middle;
        }

        .label-cell {
            white-space: nowrap;
            width: 1%;
        }

        .value-cell {
            font-weight: bold;
            border-bottom: 1px dotted #000;
            padding: 0 10px !important;
        }

        .closing-text {
            text-align: center;
            font-size: 20px;
            font-weight: bold;
            margin: 50px 0 40px 0;
            line-height: 2;
        }

        .signatures-table {
            width: 100%;
            margin-top: 60px;
            border-collapse: collapse;
        }

        .signatures-table td {
            vertical-align: top;
            font-size: 18px;
            font-weight: bold;
        }

        .sig-right { 
            text-align: right; 
            width: 50%; 
        }

        .sig-left { 
            text-align: left; 
            width: 50%; 
        }

        .sig-box-left {
            display: inline-block;
            text-align: right;
        }

        .no-print { text-align: center; margin-bottom: 15px; }
        .btn-print { background-color: #28a745; color: white; border: none; padding: 10px 25px; font-size: 16px; font-weight: bold; border-radius: 5px; cursor: pointer; }
        .btn-back { background-color: #6c757d; color: white; padding: 10px 20px; font-size: 16px; font-weight: bold; border-radius: 5px; text-decoration: none; margin-right: 10px; }

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
    <button onclick="window.print()" class="btn-print">طباعة النموذج</button>
    <a href="display_students.php" class="btn-back">رجوع لقائمة الطلاب</a>
</div>

<div class="paper">
    <div class="page-border">
        <div class="corner-tl"></div>
        <div class="corner-tr"></div>
        <div class="corner-bl"></div>
        <div class="corner-br"></div>

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
                    <div class="meta-container">
                        <div class="code-box">ج.م.س / 04 / 003</div>
                        <div class="date-box">التاريخ: <?= $current_date ?> م</div>
                    </div>
                </td>
            </tr>
        </table>

        <div class="doc-title">نموذج طلب إيقاف قيد</div>

        <div style="font-size: 19px; line-height: 2;">أتقدم إليكم أنا الطالب/ة الموضحة بياناتي أدناه بطلبي هذا الموافقة على إيقاف قيدي لهذا العام:</div>

        <table class="student-info-table">
            <tr>
                <td class="label-cell">اسم الطالب/ة:</td>
                <td class="value-cell" colspan="3"><?= htmlspecialchars($student['full_name']) ?></td>
            </tr>
            <tr>
                <td class="label-cell">الكلية / البرنامج:</td>
                <td class="value-cell"><?= htmlspecialchars($student['program_name']) ?></td>
                <td class="label-cell" style="padding-right: 20px;">الجنسية:</td>
                <td class="value-cell"><?= htmlspecialchars($student['nationality'] ?? 'ليبي') ?></td>
            </tr>
            <tr>
                <td class="label-cell">الرقم الوطني / القومي:</td>
                <td class="value-cell"><?= htmlspecialchars($student['national_id'] ?? '-') ?></td>
                <td class="label-cell" style="padding-right: 20px;">رقم جواز السفر:</td>
                <td class="value-cell"><?= htmlspecialchars($student['passport_number'] ?? '-') ?></td>
            </tr>
            <tr>
                <td class="label-cell">رقم القيد الدراسي:</td>
                <td class="value-cell"><?= htmlspecialchars($student['academic_id']) ?></td>
                <td class="label-cell" style="padding-right: 20px;">الفصل الدراسي:</td>
                <td class="value-cell"><?= htmlspecialchars($student['academic_level']) ?></td>
            </tr>
            <tr>
                <td class="label-cell">للعام الجامعي:</td>
                <td class="value-cell" colspan="3"><?= htmlspecialchars($student['admission_year']) ?> / <?= htmlspecialchars($student['admission_year'] + 1) ?> م</td>
            </tr>
        </table>

        <div class="closing-text">
            شاكرين حسن تعاونكم معنا<br>
            وتفضلوا بقبول فائق الاحترام والتقدير
        </div>

        <!-- التوقيعات -->
        <table class="signatures-table">
            <tr>
                <td class="sig-right">
                    توقيع مقدم الطلب/<br>
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