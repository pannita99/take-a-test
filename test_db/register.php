<?php
require 'db.php';
$message = "";
$message_type = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role']; // รับค่าสิทธิ์ที่เลือก

    // 1. ตรวจสอบว่ารหัสผ่านตรงกันไหม
    if ($password !== $confirm_password) {
        $message = "รหัสผ่านไม่ตรงกัน!";
        $message_type = "error";
    } else {
        // 2. เช็คว่าชื่อผู้ใช้ซ้ำไหม
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $check->execute([$username]);
        
        if ($check->rowCount() > 0) {
            $message = "ชื่อผู้ใช้นี้ถูกใช้ไปแล้ว!";
            $message_type = "error";
        } else {
            // 3. แฮชรหัสผ่านเพื่อความปลอดภัย (Hashing)
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            try {
                $sql = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt->execute([$username, $hashed_password, $role])) {
                    $message = "สมัครสมาชิกสำเร็จ! ✨ <a href='login.php'>เข้าสู่ระบบที่นี่</a>";
                    $message_type = "success";
                }
            } catch (PDOException $e) {
                $message = "เกิดข้อผิดพลาด: " . $e->getMessage();
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>สมัครสมาชิก - Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #f0f7ff; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .reg-box { background: white; padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
        h2 { text-align: center; color: #3498db; margin-top: 0; }
        label { display: block; margin-bottom: 5px; font-size: 14px; color: #666; }
        input, select { width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: 'Kanit'; }
        button { width: 100%; padding: 12px; background: #3498db; color: white; border: none; border-radius: 8px; cursor: pointer; font-size: 16px; transition: 0.3s; }
        button:hover { background: #2980b9; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .login-link { text-align: center; margin-top: 20px; font-size: 14px; }
        .login-link a { color: #3498db; text-decoration: none; }
    </style>
</head>
<body>

<div class="reg-box">
    <h2>❄️ สร้างบัญชีใหม่</h2>
    
    <?php if($message): ?>
        <div class="alert <?= $message_type ?>"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>ชื่อผู้ใช้ (Username)</label>
        <input type="text" name="username" placeholder="ภาษาอังกฤษหรือตัวเลข" required>

        <label>รหัสผ่าน (Password)</label>
        <input type="password" name="password" placeholder="อย่างน้อย 6 ตัวอักษร" required>

        <label>ยืนยันรหัสผ่าน</label>
        <input type="password" name="confirm_password" placeholder="กรอกรหัสผ่านอีกครั้ง" required>

        <label>ประเภทผู้ใช้งาน (Role)</label>
        <select name="role" required>
            <option value="customer">ลูกค้า (Customer)</option>
            <option value="user">สมาชิกทั่วไป (User)</option>
            <option value="employee">พนักงาน (Employee)</option>
            <option value="admin">ผู้ดูแลระบบ (Admin)</option>
        </select>

        <button type="submit" name="register">สร้างบัญชีผู้ใช้</button>
    </form>

    <div class="login-link">
        มีบัญชีอยู่แล้ว? <a href="login.php">เข้าสู่ระบบ</a>
    </div>
</div>

</body>
</html>