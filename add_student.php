<?php
// 1. استدعاء ملف الاتصال بقاعدة البيانات
require_once 'db.php';

$message = '';

// 2. التحقق مما إذا كان المستخدم قد ضغط على زر الحفظ (إرسال البيانات)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // معالجة رفع الصورة
        $photo_name = null;
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            // الامتدادات المسموح بها
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

            if (in_array($fileExtension, $allowedExtensions)) {
                // تسمية الملف بشكل فريد لمنع التكرار
                $newFileName = 'student_' . time() . '_' . rand(1000, 9999) . '.' . $fileExtension;
                $uploadFileDir = './uploads/';

                // إنشاء المجلد إذا لم يكن موجوداً
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $photo_name = $newFileName;
                }
            }
        }

        // استعلام الإضافة متطابق تماماً مع هيكلة قاعدة البيانات الجديدة
        $sql = "INSERT INTO students (
            academic_id, full_name, gender, nationality, national_id, mother_name,
            birth_date, birth_place, passport_number, passport_expiry, residency_type,
            residency_expiry, current_address, registration_date, admission_year,
            academic_level, program_name, enrollment_status, academic_year, is_equated,
            high_school_year, high_school_grade, high_school_percentage, photo
        ) VALUES (
            :academic_id, :full_name, :gender, :nationality, :national_id, :mother_name,
            :birth_date, :birth_place, :passport_number, :passport_expiry, :residency_type,
            :residency_expiry, :current_address, :registration_date, :admission_year,
            :academic_level, :program_name, :enrollment_status, :academic_year, :is_equated,
            :high_school_year, :high_school_grade, :high_school_percentage, :photo
        )";

        $stmt = $pdo->prepare($sql);

        // ربط الحقول بالقيم المدخلة
        $stmt->execute([
            ':academic_id'            => $_POST['academic_id'],
            ':full_name'              => $_POST['full_name'],
            ':gender'                 => $_POST['gender'],
            ':nationality'            => $_POST['nationality'],
            ':national_id'            => $_POST['national_id'] ?: null,
            ':mother_name'            => $_POST['mother_name'] ?: null,
            ':birth_date'             => $_POST['birth_date'] ?: null,
            ':birth_place'            => $_POST['birth_place'] ?: null,
            ':passport_number'        => $_POST['passport_number'] ?: null,
            ':passport_expiry'        => $_POST['passport_expiry'] ?: null,
            ':residency_type'         => $_POST['residency_type'] ?: null,
            ':residency_expiry'       => $_POST['residency_expiry'] ?: null,
            ':current_address'        => $_POST['current_address'] ?: null,
            ':registration_date'      => $_POST['registration_date'],
            ':admission_year'         => $_POST['admission_year'],
            ':academic_level'         => $_POST['academic_level'],
            ':program_name'           => $_POST['program_name'],
            ':enrollment_status'      => $_POST['enrollment_status'],
            ':academic_year'          => $_POST['academic_year'] ?: null,
            ':is_equated'             => isset($_POST['is_equated']) ? 1 : 0,
            ':high_school_year'       => $_POST['high_school_year'] ?: null,
            ':high_school_grade'      => $_POST['high_school_grade'] ?: null,
            ':high_school_percentage' => $_POST['high_school_percentage'] ?: null,
            ':photo'                  => $photo_name
        ]);

        // الانتقال التلقائي للشاشة الرئيسية بعد الحفظ (يمكنك تغيير index.php إلى اسم ملف الشاشة الرئيسية لديك)
       header("Location: display_students.php");
exit();

    } catch (PDOException $e) {
        if ($e->errorInfo[1] == 1062) {
            $message = "<div class='alert danger'>خطأ: الرقم الدراسي مسجل مسبقاً!</div>";
        } else {
            $message = "<div class='alert danger'>حدث خطأ أثناء الحفظ: " . $e->getMessage() . "</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>إضافة طالب جديد</title>
    <!-- استدعاء مكتبة الأيقونات FontAwesome لتوفير أيقونة المنزل -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; background-color: #f4f7f6; margin: 20px; }
        .form-container { background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 900px; margin: auto; }
        
        /* تنسيق رأس الصفحة وأيقونة العودة */
        .header-flex { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1f3c88; padding-bottom: 10px; margin-bottom: 20px; }
        h2 { color: #1f3c88; margin: 0; }
        .btn-home { background-color: #6c757d; color: white; padding: 8px 15px; border-radius: 4px; text-decoration: none; font-size: 14px; display: inline-flex; align-items: center; gap: 6px; transition: background 0.2s; }
        .btn-home:hover { background-color: #5a6268; }

        .grid-container { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
        .form-group { display: flex; flex-direction: column; }
        label { font-size: 13px; font-weight: bold; margin-bottom: 5px; color: #333; }
        input, select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size: 14px; box-sizing: border-box; width: 100%; }
        input:focus, select:focus { border-color: #1f3c88; outline: none; }
        .full-width { grid-column: span 3; }
        .checkbox-group { display: flex; align-items: center; gap: 8px; height: 100%; padding-top: 15px; }
        .checkbox-group input { width: auto; cursor: pointer; }
        .btn-submit { background-color: #28a745; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; margin-top: 20px; width: 100%; transition: background 0.2s; }
        .btn-submit:hover { background-color: #218838; }
        .alert { padding: 12px; border-radius: 4px; margin-bottom: 15px; text-align: center; font-weight: bold; }
        .danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="form-container">
    <div class="header-flex">
        <h2>تسجيل طالب جديد</h2>
        <!-- زر العودة للشاشة الرئيسية -->
        <a href="display_students.php" class="btn-home"><i class="fa-solid fa-house"></i> الشاشة الرئيسية</a>
    </div>

    <?= $message ?>

    <form method="POST" action="add_student.php" enctype="multipart/form-data">
        <div class="grid-container">
            
            <div class="form-group">
                <label>الرقم الدراسي *</label>
                <input type="text" name="academic_id" required>
            </div>
            
            <div class="form-group">
                <label>الاسم الرباعي *</label>
                <input type="text" name="full_name" required>
            </div>

            <div class="form-group">
                <label>الجنس *</label>
                <select name="gender" required>
                    <option value="ذكر">ذكر</option>
                    <option value="أنثى">أنثى</option>
                </select>
            </div>

            <div class="form-group">
                <label>الجنسية *</label>
                <input type="text" name="nationality" value="ليبي" required>
            </div>

            <div class="form-group">
                <label>الرقم الوطني / القومي</label>
                <input type="text" name="national_id">
            </div>

            <div class="form-group">
                <label>اسم الأم</label>
                <input type="text" name="mother_name">
            </div>

            <div class="form-group">
                <label>تاريخ الميلاد</label>
                <input type="date" name="birth_date">
            </div>

            <div class="form-group">
                <label>مكان الميلاد</label>
                <input type="text" name="birth_place">
            </div>

            <div class="form-group">
                <label>رقم جواز السفر</label>
                <input type="text" name="passport_number">
            </div>

            <div class="form-group">
                <label>تاريخ صلاحية الجواز</label>
                <input type="date" name="passport_expiry">
            </div>

            <div class="form-group">
                <label>نوع الإقامة</label>
                <input type="text" name="residency_type">
            </div>

            <div class="form-group">
                <label>تاريخ انتهاء الإقامة</label>
                <input type="date" name="residency_expiry">
            </div>

            <div class="form-group full-width">
                <label>العنوان الحالي</label>
                <input type="text" name="current_address">
            </div>

            <div class="form-group">
                <label>تاريخ التسجيل *</label>
                <input type="date" name="registration_date" value="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>سنة القبول *</label>
                <input type="number" name="admission_year" value="<?= date('Y') ?>" required>
            </div>

            <div class="form-group">
                <label>المستوى / الفصل الدراسي *</label>
                <input type="text" name="academic_level" placeholder="مثال: الفصل الأول" required>
            </div>

            <div class="form-group">
                <label>البرنامج الدراسي *</label>
                <input type="text" name="program_name" placeholder="مثال: بكالوريوس" required>
            </div>

            <div class="form-group">
                <label>صفة القيد *</label>
                <select name="enrollment_status" required>
                    <option value="نظامي">نظامي</option>
                    <option value="منتسب">منتسب</option>
                    <option value="إيقاف قيد">إيقاف قيد</option>
                    <option value="منقطع">منقطع</option>
                </select>
            </div>

            <div class="form-group">
                <label>العام الجامعي *</label>
                <input type="text" name="academic_year" placeholder="مثال: 2026/2027" required>
            </div>

            <div class="form-group">
                <label>سنة الحصول على الثانوية</label>
                <input type="number" name="high_school_year">
            </div>

            <div class="form-group">
                <label>تقدير الثانوية</label>
                <input type="text" name="high_school_grade">
            </div>

            <div class="form-group">
                <label>نسبة النجاح %</label>
                <input type="number" step="0.01" name="high_school_percentage">
            </div>

            <div class="form-group full-width">
                <div class="checkbox-group">
                    <input type="checkbox" id="is_equated" name="is_equated" value="1">
                    <label for="is_equated" style="font-weight: normal; cursor: pointer; margin-bottom: 0;">معادلة الشهادة الثانوية (نعم، معادلة)</label>
                </div>
            </div>

            <div class="form-group full-width">
                <label>الصورة الشخصية للطالب</label>
                <input type="file" name="photo" accept="image/*">
            </div>

        </div>

        <button type="submit" class="btn-submit">حفظ بيانات الطالب والعودة للرئيسية</button>
    </form>
</div>

</body>
</html>