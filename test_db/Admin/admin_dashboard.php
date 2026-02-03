<?php
session_start();
require 'db.php'; // เรียกไฟล์คอนฟิกฐานข้อมูล

// ✅ 1. ตรวจสอบสิทธิ์ (Security Check)
// ถ้าไม่มีการล็อกอิน หรือ สิทธิ์ไม่ใช่ admin ให้เด้งไปหน้า login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// ✅ 2. ดึงข้อมูลสถิติเบื้องต้นมาโชว์ (Optional - เพิ่มความสมบูรณ์)
try {
    // นับจำนวนสินค้าทั้งหมด
    $count_products = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
    // นับจำนวนสมาชิกทั้งหมด
    $count_users = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();
} catch (PDOException $e) {
    $count_products = 0;
    $count_users = 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --blue: #5dade2; --dark: #2c3e50; --light: #ebf5fb; }
        body { font-family: 'Kanit', sans-serif; background: #f4fbff; margin: 0; }
        
        /* Navigation Bar */
        nav { 
            background: white; 
            padding: 15px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .container { width: 90%; max-width: 1200px; margin: 40px auto; }
        
        /* Welcome Section */
        .welcome-card {
            background: linear-gradient(135deg, var(--blue), #a5d6f7);
            color: white;
            padding: 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 10px 20px rgba(93, 173, 226, 0.2);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            background: white;
            padding: 20px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            border: 1px solid #e1f0ff;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            background: var(--light);
            color: var(--blue);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .menu-item {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: 0.3s;
            border: 1px solid #eee;
        }
        .menu-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
            border-color: var(--blue);
        }
        .menu-item i { font-size: 40px; color: var(--blue); margin-bottom: 15px; }

        .btn-logout {
            background: #ff7675;
            color: white;
            padding: 8px 20px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 14px;
        }
    </style>
</head>
<body>

<nav>
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-size:24px;">❄️</span>
        <h2 style="margin:0; color:var(--dark); font-size:20px;">Winter Cool Admin</h2>
    </div>
    <a href="logout.php" class="btn-logout"><i class="fa-solid fa-power-off"></i> ออกจากระบบ</a>
</nav>

<div class="container">
    <div class="welcome-card">
        <h1 style="margin:0;">สวัสดีคุณ <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
        <p style="opacity:0.9;">ยินดีต้อนรับสู่ระบบจัดการหลังบ้าน ระดับสิทธิ์: <strong>Administrator</strong></p>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="stat-icon"><i class="fa-solid fa-box"></i></div>
            <div>
                <div style="font-size:14px; color:#888;">สินค้าทั้งหมด</div>
                <div style="font-size:24px; font-weight:bold;"><?php echo $count_products; ?> รายการ</div>
            </div>
        </div>
        <div class="stat-box">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div>
                <div style="font-size:14px; color:#888;">สมาชิกในระบบ</div>
                <div style="font-size:24px; font-weight:bold;"><?php echo $count_users; ?> คน</div>
            </div>
        </div>
    </div>

    <h3 style="color:var(--dark); margin-bottom:20px;">🛠️ เมนูการจัดการ</h3>
    <div class="menu-grid">
        <a href="manage_products.php" class="menu-item">
            <i class="fa-solid fa-shirt"></i>
            <div>จัดการสินค้า</div>
        </a>
        
        <a href="#" class="menu-item">
            <i class="fa-solid fa-users-gear"></i>
            <div>จัดการผู้ใช้งาน</div>
        </a>

        <a href="#" class="menu-item">
            <i class="fa-solid fa-file-invoice-dollar"></i>
            <div>รายการสั่งซื้อ</div>
        </a>

        <a href="#" class="menu-item">
            <i class="fa-solid fa-chart-line"></i>
            <div>รายงานการขาย</div>
        </a>
    </div>
</div>

</body>
</html>