<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// 1. استدعاء الاتصال بقاعدة البيانات
require_once 'db.php';

// 2. جلب جميع الأعوام الجامعية المسجلة للطلاب بدون تكرار
$query = "SELECT DISTINCT academic_year FROM students WHERE academic_year IS NOT NULL ORDER BY academic_year DESC";
$stmt = $pdo->query($query);
$years = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>استخراج كشف الحضور والغياب</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 0;
            padding: 50px 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
        }

        .filter-card {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
            position: relative;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #1f3c88;
            padding-bottom: 12px;
        }

        .filter-card h3 {
            color: #1f3c88;
            margin: 0;
            font-size: 18px;
        }

        .btn-logout {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 12px;
            transition: background 0.2s;
        }

        .btn-logout:hover {
            background-color: #c82333;
        }

        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
        }

        label {
            font-weight: bold;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }

        select {
            padding: 10px 12px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
            background-color: #fff;
        }

        select:focus {
            border-color: #007bff;
        }

        .btn-submit {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s;
            margin-top: 10px;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .btn-back {
            display: block;
            text-align: center;
            background-color: #6c757d;
            color: white;
            text-decoration: none;
            padding: 10px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 6px;
            margin-top: 10px;
            transition: background 0.2s;
        }

        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>

<div class="filter-card">
    <div class="top-bar">
        <h3>استخراج كشف الحضور</h3>
       
    </div>

    <form action="attendance_report.php" method="GET" target="_blank">
        
        <div class="form-group">
            <label>اختر الفصل الدراسي:</label>
            <select name="academic_level" required>
                <option value="">-- اختر الفصل --</option>
                <option value="الفصل الأول">الفصل الأول</option>
                <option value="الفصل الثاني">الفصل الثاني</option>
                <option value="الفصل الثالث">الفصل الثالث</option>
                <option value="الفصل الرابع">الفصل الرابع</option>
                <option value="الفصل الخامس">الفصل الخامس</option>
                <option value="الفصل السادس">الفصل السادس</option>
                <option value="الفصل السابع">الفصل السابع</option>
                <option value="الفصل الثامن">الفصل الثامن</option>
                <option value="الفصل التاسع">الفصل التاسع</option>
                <option value="الفصل العاشر">الفصل العاشر</option>
            </select>
        </div>

        <div class="form-group">
            <label>اختر العام الجامعي:</label>
            <select name="academic_year" required>
                <option value="">-- اختر العام --</option>
                <?php foreach ($years as $row): ?>
                    <option value="<?= htmlspecialchars($row['academic_year']) ?>">
                        <?= htmlspecialchars($row['academic_year']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn-submit">عرض وطباعة الكشف 🖨️</button>
        <a href="display_students.php" class="btn-back">إلغاء والرجوع لقائمة الطلاب</a>
    </form>
</div>

</body>
</html>