<?php
// 1. استدعاء الاتصال بقاعدة البيانات
require_once 'db.php';

$message = '';

// 2. التحقق من وجود ID الطالب في الرابط
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: display_students.php");
    exit();
}

$id = $_GET['id'];

// جلب بيانات الطالب الحالية أولاً للمعالجة والصورة
$stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
$stmt->execute([':id' => $id]);
$student = $stmt->fetch();

if (!$student) {
    die("الطالب غير موجود!");
}

// 3. معالجة تحديث البيانات عند إرسال النموذج (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $photo_name = $student['photo'] ?? null; // الاحتفاظ بالصورة القديمة كافتراضي

        // معالجة رفع الصورة الجديدة
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['photo']['tmp_name'];
            $fileName = $_FILES['photo']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadFileDir = './uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                // تسمية الملف باسم فريد تجنباً للتكرار
                $newFileName = 'student_' . $id . '_' . time() . '.' . $fileExtension;
                $dest_path = $uploadFileDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    // حذف الصورة القديمة إذا كانت موجودة
                    if (!empty($student['photo']) && file_exists($uploadFileDir . $student['photo'])) {
                        unlink($uploadFileDir . $student['photo']);
                    }
                    $photo_name = $newFileName;
                }
            } else {
                $message = "<div class='alert danger'>صيغة الصورة غير مدعومة! المسموح: jpg, jpeg, png, gif, webp</div>";
            }
        }

        $sql = "UPDATE students SET 
            academic_id            = :academic_id,
            full_name              = :full_name,
            gender                 = :gender,
            nationality            = :nationality,
            national_id            = :national_id,
            mother_name            = :mother_name,
            birth_date             = :birth_date,
            birth_place            = :birth_place,
            passport_number        = :passport_number,
            passport_expiry        = :passport_expiry,
            residency_type         = :residency_type,
            residency_expiry       = :residency_expiry,
            current_address        = :current_address,
            registration_date      = :registration_date,
            admission_year         = :admission_year,
            academic_level         = :academic_level,
            program_name           = :program_name,
            enrollment_status      = :enrollment_status,
            is_equated             = :is_equated,
            high_school_year       = :high_school_year,
            high_school_grade      = :high_school_grade,
            high_school_percentage = :high_school_percentage,
            academic_year          = :academic_year,
            photo                  = :photo
        WHERE id = :id";

        $stmt = $pdo->prepare($sql);

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
            ':is_equated'             => isset($_POST['is_equated']) ? 1 : 0,
            ':high_school_year'       => $_POST['high_school_year'] ?: null,
            ':high_school_grade'      => $_POST['high_school_grade'] ?: null,
            ':high_school_percentage' => $_POST['high_school_percentage'] ?: null,
            ':academic_year'          => $_POST['academic_year'] ?: null,
            ':photo'                  => $photo_name,
            ':id'                     => $id
        ]);

        $message = "<div class='alert success'>تم تحديث بيانات الطالب بنجاح!</div>";

        // إعادة جلب البيانات لتحديث العرض بعد الحفظ
        $stmt = $pdo->prepare("SELECT * FROM students WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $student = $stmt->fetch();

    } catch (PDOException $e) {
        $message = "<div class='alert danger'>حدث خطأ أثناء التحديث: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات الطالب - <?= htmlspecialchars($student['full_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: Tahoma, Arial, sans-serif;
            background-color: #f4f7f6;
            margin: 20px;
            direction: rtl;
        }

        .form-container {
            background: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            margin: 0 auto;
        }

        h2 {
            text-align: center;
            color: #1f3c88;
            margin-bottom: 20px;
            border-bottom: 2px solid #1f3c88;
            padding-bottom: 10px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1f3c88;
            background: #eef2f5;
            padding: 6px 10px;
            border-right: 4px solid #1f3c88;
            margin: 20px 0 15px 0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .full-width {
            grid-column: span 3;
        }

        label {
            font-weight: bold;
            font-size: 13px;
            color: #333;
            margin-bottom: 6px;
        }

        label .required {
            color: red;
            margin-right: 3px;
        }

        input[type="text"],
        input[type="date"],
        input[type="number"],
        input[type="file"],
        select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            width: 100%;
            outline: none;
            transition: border-color 0.2s;
        }

        input:focus, select:focus {
            border-color: #007bff;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            font-weight: normal;
            padding-top: 5px;
        }

        .avatar-preview {
            width: 100px;
            height: 120px;
            border-radius: 6px;
            object-fit: cover;
            border: 2px solid #1f3c88;
            background: #f8f9fa;
        }

        .photo-container {
            display: flex;
            align-items: center;
            gap: 20px;
            background: #fafafa;
            padding: 12px;
            border: 1px solid #eaeaea;
            border-radius: 6px;
        }

        .actions-bar {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            flex: 2;
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-submit:hover {
            background-color: #0056b3;
        }

        .btn-back {
            flex: 1;
            background-color: #6c757d;
            color: white;
            text-align: center;
            text-decoration: none;
            padding: 12px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .alert {
            padding: 12px;
            border-radius: 4px;
            margin-bottom: 15px;
            text-align: center;
            font-weight: bold;
        }
        .success { background-color: #d4edda; color: #155724; }
        .danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>تعديل بيانات الطالب الشاملة</h2>
    <?= $message ?>

    <form method="POST" action="" enctype="multipart/form-data">

        <!-- قسم الصورة الشخصية -->
        <div class="section-title">الصورة الشخصية للطالب</div>
        <div class="form-group full-width">
            <div class="photo-container">
                <div>
                    <?php if (!empty($student['photo']) && file_exists('uploads/' . $student['photo'])): ?>
                        <img src="uploads/<?= htmlspecialchars($student['photo']) ?>" alt="صورة الطالب" class="avatar-preview">
                    <?php else: ?>
                        <div class="avatar-preview" style="display: flex; flex-direction: column; align-items: center; justify-content: center; color: #666; font-size: 11px; text-align: center;">
                            <i class="fa-solid fa-user fa-2x" style="color: #ccc; margin-bottom: 5px;"></i>
                            بدون صورة
                        </div>
                    <?php endif; ?>
                </div>

                <div style="flex-grow: 1;">
                    <label>تغيير أو استبدال الصورة الشخصية</label>
                    <input type="file" name="photo" accept="image/*">
                    <small style="color: #666; display: block; margin-top: 5px;">الصيغ المسموحة: JPG, PNG, WEBP, GIF</small>
                </div>
            </div>
        </div>

        <!-- 1. البيانات الأساسية والهوية -->
        <div class="section-title">البيانات الأساسية والهوية</div>
        <div class="form-grid">
            <div class="form-group">
                <label>الرقم الدراسي <span class="required">*</span></label>
                <input type="text" name="academic_id" value="<?= htmlspecialchars($student['academic_id'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>الاسم الرباعي <span class="required">*</span></label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>اسم الأم</label>
                <input type="text" name="mother_name" value="<?= htmlspecialchars($student['mother_name'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>الجنس <span class="required">*</span></label>
                <select name="gender" required>
                    <option value="ذكر" <?= ($student['gender'] ?? '') == 'ذكر' ? 'selected' : '' ?>>ذكر</option>
                    <option value="أنثى" <?= ($student['gender'] ?? '') == 'أنثى' ? 'selected' : '' ?>>أنثى</option>
                </select>
            </div>

            <div class="form-group">
                <label>الجنسية <span class="required">*</span></label>
                <input type="text" name="nationality" value="<?= htmlspecialchars($student['nationality'] ?? 'ليبي') ?>" required>
            </div>

            <div class="form-group">
                <label>الرقم الوطني / القومي</label>
                <input type="text" name="national_id" value="<?= htmlspecialchars($student['national_id'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>تاريخ الميلاد</label>
                <input type="date" name="birth_date" value="<?= htmlspecialchars($student['birth_date'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>مكان الميلاد</label>
                <input type="text" name="birth_place" value="<?= htmlspecialchars($student['birth_place'] ?? '') ?>">
            </div>
        </div>

        <!-- 2. بيانات السفر والإقامة والعنوان -->
        <div class="section-title">بيانات السفر والإقامة والعنوان</div>
        <div class="form-grid">
            <div class="form-group">
                <label>رقم جواز السفر</label>
                <input type="text" name="passport_number" value="<?= htmlspecialchars($student['passport_number'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>تاريخ صلاحية الجواز</label>
                <input type="date" name="passport_expiry" value="<?= htmlspecialchars($student['passport_expiry'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>نوع / رقم الإقامة</label>
                <input type="text" name="residency_type" value="<?= htmlspecialchars($student['residency_type'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>تاريخ انتهاء الإقامة</label>
                <input type="date" name="residency_expiry" value="<?= htmlspecialchars($student['residency_expiry'] ?? '') ?>">
            </div>

            <!-- تم وضع العنوان الحالي بجانب تاريخ انتهاء الإقامة في نفس الصف -->
            <div class="form-group">
                <label>العنوان الحالي</label>
                <input type="text" name="current_address" value="<?= htmlspecialchars($student['current_address'] ?? '') ?>">
            </div>
        </div>

        <!-- 3. البيانات الأكاديمية -->
        <div class="section-title">البيانات الأكاديمية والتسجيل</div>
        <div class="form-grid">
            <div class="form-group">
                <label>تاريخ التسجيل <span class="required">*</span></label>
                <input type="date" name="registration_date" value="<?= htmlspecialchars($student['registration_date'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>سنة القبول <span class="required">*</span></label>
                <input type="text" name="admission_year" value="<?= htmlspecialchars($student['admission_year'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>المستوى / الفصل الدراسي <span class="required">*</span></label>
                <input type="text" name="academic_level" value="<?= htmlspecialchars($student['academic_level'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>البرنامج الدراسي <span class="required">*</span></label>
                <input type="text" name="program_name" value="<?= htmlspecialchars($student['program_name'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>صفة / حالة القيد <span class="required">*</span></label>
                <select name="enrollment_status" required>
                    <option value="نظامي" <?= ($student['enrollment_status'] ?? '') == 'نظامي' ? 'selected' : '' ?>>نظامي</option>
                    <option value="منتسب" <?= ($student['enrollment_status'] ?? '') == 'منتسب' ? 'selected' : '' ?>>منتسب</option>
                    <option value="إيقاف قيد" <?= ($student['enrollment_status'] ?? '') == 'إيقاف قيد' ? 'selected' : '' ?>>إيقاف قيد</option>
                    <option value="منقطع" <?= ($student['enrollment_status'] ?? '') == 'منقطع' ? 'selected' : '' ?>>منقطع</option>
                </select>
            </div>

            <div class="form-group">
                <label>العام الجامعي <span class="required">*</span></label>
                <input type="text" name="academic_year" value="<?= htmlspecialchars($student['academic_year'] ?? '') ?>" placeholder="مثال: 2026/2027" required>
            </div>
        </div>

        <!-- 4. مؤهل الثانوية العامة -->
        <div class="section-title">مؤهل الثانوية العامة</div>
        <div class="form-grid">
            <div class="form-group">
                <label>سنة الحصول على الثانوية</label>
                <input type="text" name="high_school_year" value="<?= htmlspecialchars($student['high_school_year'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>تقدير الثانوية</label>
                <input type="text" name="high_school_grade" value="<?= htmlspecialchars($student['high_school_grade'] ?? '') ?>">
            </div>

            <div class="form-group">
                <label>نسبة النجاح %</label>
                <input type="text" name="high_school_percentage" value="<?= htmlspecialchars($student['high_school_percentage'] ?? '') ?>">
            </div>

            <div class="form-group full-width">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_equated" value="1" <?= (!empty($student['is_equated']) && $student['is_equated'] == 1) ? 'checked' : '' ?>>
                    معادلة الشهادة الثانوية (نعم، معادلة)
                </label>
            </div>
        </div>

        <!-- أزرار الحفظ والرجوع -->
        <div class="actions-bar">
            <button type="submit" class="btn-submit">تحديث البيانات وحفظها</button>
            <a href="display_students.php" class="btn-back"><i class="fa-solid fa-arrow-right"></i>  الرجوع</a>
        </div>

    </form>
</div>

</body>
</html>