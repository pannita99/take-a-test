<?php
session_start();
require 'db.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ✅ 1. ตรวจสอบสิทธิ์ (Security Check)
// หากไม่ได้ล็อกอิน หรือ สิทธิ์ไม่ใช่ user ให้กลับไปหน้า login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit();
}

// ✅ 2. ดึงข้อมูลสินค้าแนะนำ (is_featured) มาแสดงโชว์สมาชิก
try {
    $stmt = $conn->query("SELECT * FROM products WHERE is_featured = 1 LIMIT 4");
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $featured_products = [];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - สมาชิก Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --blue: #5dade2; --dark: #34495e; --bg: #f8fbff; }
        body { font-family: 'Kanit', sans-serif; background: var(--bg); margin: 0; color: var(--dark); }
        
        nav { 
            background: white; 
            padding: 15px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .container { width: 90%; max-width: 1100px; margin: 30px auto; }

        /* User Profile Header */
        .user-header {
            background: white;
            padding: 25px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.02);
            margin-bottom: 30px;
            border: 1px solid #edf2f7;
        }
        .avatar {
            width: 70px;
            height: 70px;
            background: var(--blue);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
        }

        /* Menu Tabs */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .menu-card {
            background: white;
            padding: 20px;
            border-radius: 15px;
            text-align: center;
            text-decoration: none;
            color: var(--dark);
            transition: 0.3s;
            border: 1px solid #eee;
        }
        .menu-card:hover {
            border-color: var(--blue);
            color: var(--blue);
            transform: translateY(-3px);
        }
        .menu-card i { font-size: 24px; margin-bottom: 10px; display: block; }

        /* Featured Section */
        .section-title { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }
        .product-item {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            border: 1px solid #eee;
            transition: 0.3s;
        }
        .product-item:hover { box-shadow: 0 10px 20px rgba(0,0,0,0.05); }
        .product-img { width: 100%; height: 180px; object-fit: cover; }
        .product-info { padding: 15px; }
        
        .btn-logout {
            color: #95a5a6;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-logout:hover { color: #e74c3c; }
    </style>
</head>
<body>

<nav>
    <div style="display:flex; align-items:center; gap:10px;">
        <span style="font-size:24px;">❄️</span>
        <h2 style="margin:0; color:var(--blue); font-size:20px;">Winter Cool Client</h2>
    </div>
    <a href="logout.php" class="btn-logout">
        <i class="fa-solid fa-arrow-right-from-bracket"></i> ออกจากระบบ
    </a>
</nav>

<div class="container">
    <div class="user-header">
        <div class="avatar">
            <i class="fa-solid fa-user-circle"></i>
        </div>
        <div>
            <h2 style="margin:0;">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?></h2>
            <span style="color: #27ae60; font-size: 14px;">● บัญชีสมาชิกปกติ (Member)</span>
        </div>
    </div>

    <div class="menu-grid">
        <a href="shop.php" class="menu-card">
            <i class="fa-solid fa-bag-shopping"></i>
            ไปที่หน้าสินค้า
        </a>
        <a href="#" class="menu-card">
            <i class="fa-solid fa-clock-rotate-left"></i>
            ประวัติการสั่งซื้อ
        </a>
        <a href="#" class="menu-card">
            <i class="fa-solid fa-user-pen"></i>
            แก้ไขข้อมูลส่วนตัว
        </a>
        <a href="#" class="menu-card">
            <i class="fa-solid fa-heart"></i>
            สินค้าที่ชอบ
        </a>
    </div>

    <div class="section-title">
        <h3 style="margin:0;">🌟 สินค้าแนะนำสำหรับคุณ</h3>
        <a href="#" style="color:var(--blue); text-decoration:none; font-size:14px;">ดูทั้งหมด</a>
    </div>

    <div class="product-grid">
        <?php if (count($featured_products) > 0): ?>
            <?php foreach($featured_products as $p): ?>
            <div class="product-item">
                <img src="../uploads/<?php echo $p['image']; ?>" class="product-img" onerror="this.src='https://via.placeholder.com/200x180?text=No+Image'">
                <div class="product-info">
                    <div style="font-size:12px; color:var(--blue);"><?php echo $p['category']; ?></div>
                    <div style="font-weight:500; margin:5px 0;"><?php echo $p['name']; ?></div>
                    <div style="color:#e67e22; font-weight:bold;">฿<?php echo number_format($p['price'], 2); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color:#999;">ขณะนี้ยังไม่มีสินค้าแนะนำ</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>