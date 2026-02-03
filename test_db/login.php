<?php
session_start();
require 'db.php';

$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // แยกเส้นทางตามสิทธิ์
        switch ($user['role']) {
            case 'admin': header("Location: admin_dashboard.php"); break;
            case 'user': header("Location: user_dashboard.php"); break;
            case 'customer': header("Location: customer_dashboard.php"); break;
            case 'employee': header("Location: employee_dashboard.php"); break;
        }
        exit();
    } else {
        $error = "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>เข้าสู่ระบบ - Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f0f7ff; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .login-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #3498db; margin-top: 0; }
        label { display: block; margin-bottom: 5px; font-size: 14px; color: #666; }
        input { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Kanit'; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        button:hover { background: #2980b9; }
        .error-msg { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; border: 1px solid #f5c6cb; }
        .reg-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .reg-link a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>❄️ เข้าสู่ระบบ</h2>
    
    <?php if($error): ?>
        <div class="error-msg"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>ชื่อผู้ใช้ (Username)</label>
        <input type="text" name="username" placeholder="กรอกชื่อผู้ใช้" required>

        <label>รหัสผ่าน (Password)</label>
        <input type="password" name="password" placeholder="กรอกรหัสผ่าน" required>

        <button type="submit" name="login">Login</button>
    </form>

    <div class="reg-link">
        ยังไม่มีบัญชี? <a href="register.php">สมัครสมาชิกที่นี่</a>
    </div>
</div>

</body>
</html>