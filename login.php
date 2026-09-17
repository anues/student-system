<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = md5(trim($_POST['password'] ?? ''));

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
    $stmt->execute([':username' => $username, ':password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $user['username'];
        header("Location: display_students.php");
        exit();
    } else {
        $error = 'اسم المستخدم أو كلمة المرور غير صحيحة!';
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول للنظام</title>
    <style>
        body { font-family: Tahoma, sans-serif; background-color: #f4f7f6; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-card { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); width: 100%; max-width: 350px; }
        h3 { text-align: center; color: #1f3c88; margin-bottom: 20px; }
        .form-group { margin-bottom: 15px; display: flex; flex-direction: column; }
        label { font-weight: bold; font-size: 13px; margin-bottom: 5px; }
        input { padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; outline: none; }
        button { background-color: #007bff; color: white; border: none; padding: 10px; font-size: 15px; font-weight: bold; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 10px; }
        button:hover { background-color: #0056b3; }
        .error { color: red; font-size: 13px; text-align: center; margin-bottom: 10px; font-weight: bold; }
    </style>
</head>
<body>

<div class="login-card">
    <h3>تسجيل الدخول</h3>
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST">
        <div class="form-group">
            <label>اسم المستخدم:</label>
            <input type="text" name="username" required autocomplete="off">
        </div>
        <div class="form-group">
            <label>كلمة المرور:</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">دخول</button>
    </form>
</div>

</body>
</html>