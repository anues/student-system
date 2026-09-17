<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

// جلب جميع البيانات من جدول الطلاب مرتبة من الأحدث إلى الأقدم
$stmt = $pdo->query("SELECT * FROM students ORDER BY id DESC");
$students = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الطلاب المسجلين</title>
    <!-- خط Cairo والأيقونات الرسمية -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --primary: #1e3a8a;
            --primary-hover: #1e40af;
            --success: #10b981;
            --success-hover: #059669;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --info: #0284c7;
            --info-hover: #0369a1;
            --warning: #f59e0b;
            --purple: #8b5cf6;
            --purple-hover: #7c3aed;
            --bg-body: #f3f4f6;
            --card-bg: #ffffff;
            --text-main: #1f2937;
            --text-muted: #6b7280;
            --border-color: #e5e7eb;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-main);
            padding: 30px 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* البطاقة الرئيسية */
        .main-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            padding: 24px;
            border: 1px solid var(--border-color);
        }

        /* الشريط العلوي للتحكم */
        .card-header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid var(--border-color);
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-tools {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* صندوق البحث */
        .search-box {
            position: relative;
            min-width: 250px;
        }

        .search-box input {
            width: 100%;
            padding: 9px 38px 9px 14px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            outline: none;
            font-size: 14px;
            background-color: #f9fafb;
        }

        .search-box input:focus {
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(30, 58, 138, 0.1);
        }

        .search-box i {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
        }

        /* الأزرار الرئيسية */
        .btn-main {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            color: #fff;
            white-space: nowrap;
        }

        .btn-add { background-color: var(--success); }
        .btn-attendance { background-color: var(--info); }
        .btn-logout { background-color: var(--danger); }
        .btn-logout:hover { background-color: var(--danger-hover); }

        /* الجدول */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .custom-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            white-space: nowrap;
        }

        .custom-table thead {
            background-color: #f8fafc;
        }

        .custom-table th {
            padding: 14px 12px;
            font-size: 13px;
            font-weight: 700;
            color: #334155;
            border-bottom: 2px solid var(--border-color);
        }

        .custom-table td {
            padding: 10px 12px;
            font-size: 13px;
            color: var(--text-main);
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .custom-table tbody tr:hover {
            background-color: #f8fafc;
        }

        /* أزرار الإجراءات داخل الجدول */
        .action-group {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
        }

        .btn-action {
            padding: 5px 10px;
            font-size: 12px;
            border-radius: 6px;
            color: #fff;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-weight: 600;
            border: none;
            cursor: pointer;
        }

        .btn-view { background-color: var(--info); }
        .btn-edit { background-color: var(--warning); }
        .btn-delete { background-color: var(--danger); }

        /* القائمة المنسدلة */
        .dropdown {
            display: inline-block;
        }

        .dropdown-btn {
            background-color: var(--purple);
            color: white;
            padding: 5px 10px;
            font-size: 12px;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .dropdown-btn:hover { background-color: var(--purple-hover); }

        .dropdown-content {
            display: none;
            position: fixed;
            background-color: #ffffff;
            min-width: 190px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 999999;
            border: 1px solid var(--border-color);
            overflow: hidden;
            text-align: right;
        }

        .dropdown-content.show {
            display: block !important;
        }

        .dropdown-content a {
            color: var(--text-main);
            padding: 9px 12px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 12px;
            transition: background 0.15s;
            border-bottom: 1px solid #f3f4f6;
        }

        .dropdown-content a:last-child {
            border-bottom: none;
        }

        .dropdown-content a:hover {
            background-color: #f3f4f6;
            color: var(--primary);
        }

        .badge-id {
            background-color: #e0f2fe;
            color: #0369a1;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="main-card">
        
        <!-- الشريط العلوي -->
        <div class="card-header-actions">
            <h2 class="page-title">
                <i class="fa-solid fa-user-graduate"></i>
                قائمة الطلاب المسجلين
            </h2>

            <div class="action-tools">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" onkeyup="searchTable()" placeholder="ابحث عن اسم الطالب...">
                </div>

                <a href="add_student.php" class="btn-main btn-add">
                    <i class="fa-solid fa-user-plus"></i> إضافة طالب جديد
                </a>

                <a href="attendance_filter.php" class="btn-main btn-attendance">
                    <i class="fa-solid fa-clipboard-user"></i> كشف الحضور والغياب
                </a>

                <a href="logout.php" class="btn-main btn-logout">
                    <i class="fa-solid fa-lock"></i> تسجيل خروج
                </a>
            </div>
        </div>

        <!-- جدول البيانات -->
        <div class="table-responsive">
            <table class="custom-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>الرقم الدراسي</th>
                        <th>الاسم الرباعي</th>
                        <th>الجنس</th>
                        <th>الجنسية</th>
                        <th>الرقم الوطني / القومي</th>
                        <th>المستوى الدراسي</th>
                        <th>تاريخ التسجيل</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($students) > 0): ?>
                        <?php foreach ($students as $index => $student): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><span class="badge-id"><?= htmlspecialchars($student['academic_id']) ?></span></td>
                                <td style="font-weight: 700;"><?= htmlspecialchars($student['full_name']) ?></td>
                                <td><?= htmlspecialchars($student['gender']) ?></td>
                                <td><?= htmlspecialchars($student['nationality']) ?></td>
                                <td><?= htmlspecialchars($student['national_id'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($student['academic_level']) ?></td>
                                <td><?= htmlspecialchars($student['registration_date']) ?></td>
                                <td>
                                    <div class="action-group">
                                        <!-- 1. عرض -->
                                        <a href="view_student.php?id=<?= $student['id'] ?>" class="btn-action btn-view">
                                            <i class="fa-solid fa-eye"></i> عرض
                                        </a>

                                        <!-- 2. تعديل -->
                                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn-action btn-edit">
                                            <i class="fa-solid fa-pen-to-square"></i> تعديل
                                        </a>

                                        <!-- 3. حذف -->
                                        <a href="delete_student.php?id=<?= $student['id'] ?>" 
                                           class="btn-action btn-delete"
                                           onclick="return confirm('هل أنت تأكد من رغبتك في حذف الطالب: <?= htmlspecialchars($student['full_name']) ?>؟');">
                                            <i class="fa-solid fa-trash"></i> حذف
                                        </a>

                                        <!-- 4. النماذج -->
                                        <div class="dropdown">
                                            <button type="button" class="dropdown-btn" onclick="toggleMenu(event, this)">
                                                <i class="fa-solid fa-file-lines"></i> النماذج <i class="fa-solid fa-chevron-up" style="font-size: 9px;"></i>
                                            </button>
                                            <div class="dropdown-content">
                                                <a href="request_proof.php?id=<?= $student['id'] ?>" target="_blank">
                                                    <i class="fa-solid fa-certificate"></i> إثبات مستوى دراسي
                                                </a>
                                                <a href="suspend_enrollment.php?id=<?= $student['id'] ?>" target="_blank">
                                                    <i class="fa-solid fa-user-slash"></i> طلب إيقاف قيد
                                                </a>
                                                <a href="student_survey.php?id=<?= $student['id'] ?>" target="_blank">
                                                    <i class="fa-solid fa-square-poll-vertical"></i> استبيان طالب
                                                </a>
                                                <a href="clearance.php?id=<?= $student['id'] ?>" target="_blank">
                                                    <i class="fas fa-file-alt"></i> إخلاء طرف وسحب ملف
                                                </a>
                                                <a href="undertaking.php?id=<?= $student['id'] ?>" target="_blank">
    <i class="fa-solid fa-file-signature"></i> نموذج تعهد
</a>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" style="padding: 20px; color: var(--text-muted);">لا توجد بيانات طلاب مسجلة حتى الآن.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<script>
function searchTable() {
    let input = document.getElementById("searchInput");
    let filter = input.value.toLowerCase();
    let table = document.querySelector(".custom-table tbody");
    let tr = table.getElementsByTagName("tr");

    for (let i = 0; i < tr.length; i++) {
        let tdName = tr[i].getElementsByTagName("td")[2]; 
        if (tdName) {
            let txtValue = tdName.textContent || tdName.innerText;
            if (txtValue.toLowerCase().indexOf(filter) > -1) {
                tr[i].style.display = "";
            } else {
                tr[i].style.display = "none";
            }
        }
    }
}

function toggleMenu(e, btn) {
    e.stopPropagation();
    let menu = btn.nextElementSibling;
    
    // إغلاق باقي القوائم المفتوحة
    document.querySelectorAll('.dropdown-content').forEach(function(item) {
        if (item !== menu) {
            item.classList.remove('show');
        }
    });

    // إذا كانت القائمة مغلقة، نقوم بحساب الموضع وفتحها
    if (!menu.classList.contains('show')) {
        let rect = btn.getBoundingClientRect();
        
        // إظهار القائمة لحساب أبعادها
        menu.classList.add('show');
        
        // تحديد موضع القائمة بناءً على موقع الزر على الشاشة
        menu.style.top = (rect.bottom + 5) + 'px';
        menu.style.left = rect.left + 'px';
        
        // في حال كانت القائمة ستخرج أسفل الشاشة، يتم فتحها للأعلى
        let menuRect = menu.getBoundingClientRect();
        if (menuRect.bottom > window.innerHeight) {
            menu.style.top = (rect.top - menuRect.height - 5) + 'px';
        }
    } else {
        menu.classList.remove('show');
    }
}

// إغلاق القائمة عند النقر في أي مكان آخر أو أثناء التمرير
document.addEventListener('click', function() {
    document.querySelectorAll('.dropdown-content').forEach(function(menu) {
        menu.classList.remove('show');
    });
});

window.addEventListener('scroll', function() {
    document.querySelectorAll('.dropdown-content').forEach(function(menu) {
        menu.classList.remove('show');
    });
}, true);
</script>

</body>
</html>