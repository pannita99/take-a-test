<?php
session_start();
require 'db.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ✅ 1. ตรวจสอบสิทธิ์ (Security Check)
// หากไม่ได้ล็อกอิน หรือ สิทธิ์ไม่ใช่ customer ให้กลับไปหน้า login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

// ✅ 2. ดึงข้อมูลสินค้าล่าสุดมาแสดงโชว์ลูกค้า
try {
    $stmt = $conn->query("SELECT * FROM products ORDER BY product_id DESC LIMIT 6");
    $latest_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $latest_products = [];
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - ร้านค้า Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --soft-blue: #85c1e9; --navy: #2e4053; --bg-gray: #f2f4f4; }
        body { font-family: 'Kanit', sans-serif; background: var(--bg-gray); margin: 0; }
        
        /* Top Navigation */
        nav { 
            background: white; 
            padding: 12px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .container { width: 90%; max-width: 1100px; margin: 30px auto; }

        /* Banner Section */
        .promo-banner {
            background: linear-gradient(to right, #2e4053, #5499c7);
            color: white;
            padding: 40px;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 30px;
        }

        /* Quick Action Cards */
        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .action-card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            gap: 20px;
            text-decoration: none;
            color: var(--navy);
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        }
        .action-card:hover { transform: translateY(-5px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .icon-circle {
            width: 50px;
            height: 50px;
            background: #ebf5fb;
            color: #3498db;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        /* Product Cards */
        .product-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
        }
        .p-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            transition: 0.3s;
            border: 1px solid #eee;
        }
        .p-card img { width: 100%; height: 160px; object-fit: cover; }
        .p-details { padding: 12px; }
        .p-price { color: #cb4335; font-weight: bold; font-size: 18px; margin-top: 5px; }
        
        .logout-link { color: #a93226; text-decoration: none; font-size: 14px; font-weight: 500; }
    </style>
</head>
<body>

<nav>
    <div style="display:flex; align-items:center; gap:8px;">
        <i class="fa-solid fa-store" style="color:var(--soft-blue); font-size:22px;"></i>
        <h2 style="margin:0; font-size:18px; color:var(--navy);">Winter Cool Shop</h2>
    </div>
    <div style="display:flex; align-items:center; gap:20px;">
        <span style="font-size:14px; color:#7f8c8d;">สวัสดี, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
        <a href="logout.php" class="logout-link"><i class="fa-solid fa-power-off"></i> ออกจากระบบ</a>
    </div>
</nav>

<div class="container">
    <div class="promo-banner">
        <h1 style="margin:0; font-size:28px;">สัมผัสความอุ่นสบายกับคอลเลกชันใหม่ ❄️</h1>
        <p style="opacity:0.8;">ส่วนลดพิเศษ 10% สำหรับลูกค้าคนสำคัญเช่นคุณ</p>
    </div>

    <div class="action-grid">
        <a href="product_all.php" class="action-card">
            <div class="icon-circle"><i class="fa-solid fa-magnifying-glass"></i></div>
            <div>
                <strong style="display:block;">เลือกซื้อสินค้า</strong>
                <small style="color:#888;">ดูสินค้าทั้งหมดในร้าน</small>
            </div>
        </a>
        <a href="my_orders.php" class="action-card">
            <div class="icon-circle"><i class="fa-solid fa-truck-fast"></i></div>
            <div>
                <strong style="display:block;">ติดตามคำสั่งซื้อ</strong>
                <small style="color:#888;">เช็คสถานะการจัดส่ง</small>
            </div>
        </a>
        <a href="profile.php" class="action-card">
            <div class="icon-circle"><i class="fa-solid fa-address-card"></i></div>
            <div>
                <strong style="display:block;">ที่อยู่ของฉัน</strong>
                <small style="color:#888;">จัดการข้อมูลจัดส่ง</small>
            </div>
        </a>
    </div>

    <h3 style="color:var(--navy); border-left: 4px solid var(--soft-blue); padding-left: 10px; margin-bottom: 20px;">🆕 สินค้ามาใหม่ล่าสุด</h3>
    
    <div class="product-list">
        <?php if (count($latest_products) > 0): ?>
            <?php foreach($latest_products as $p): ?>
            <div class="p-card">
                <img src="../uploads/<?php echo $p['image']; ?>" onerror="this.src='https://via.placeholder.com/180x160?text=Winter+Cool'">
                <div class="p-details">
                    <div style="font-size:13px; font-weight:500; height: 40px; overflow: hidden;"><?php echo htmlspecialchars($p['name']); ?></div>
                    <div class="p-price">฿<?php echo number_format($p['price'], 2); ?></div>
                    <button style="width:100%; margin-top:10px; padding:6px; border:1px solid var(--soft-blue); background:white; color:var(--soft-blue); border-radius:5px; cursor:pointer;">ดูรายละเอียด</button>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align:center; padding:40px; color:#999;">
                <i class="fa-solid fa-box-open" style="font-size:40px; margin-bottom:10px;"></i>
                <p>ยังไม่มีสินค้าใหม่ในขณะนี้</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>