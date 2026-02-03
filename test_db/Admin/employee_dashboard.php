<?php
session_start();
require 'db.php'; // ไฟล์เชื่อมต่อฐานข้อมูล

// ✅ 1. ตรวจสอบสิทธิ์ (Security Check)
// หากไม่ได้ล็อกอิน หรือ สิทธิ์ไม่ใช่ employee ให้กลับไปหน้า login
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'employee') {
    header("Location: login.php");
    exit();
}

// ✅ 2. ดึงข้อมูลที่พนักงานต้องดูแล (เช่น สินค้าที่สต็อกใกล้หมด)
try {
    // ดึงสินค้าที่สต็อกต่ำกว่า 10 ชิ้น
    $stmt = $conn->query("SELECT * FROM products WHERE stock < 10 ORDER BY stock ASC");
    $low_stock_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // นับจำนวนสินค้าทั้งหมด
    $total_items = $conn->query("SELECT COUNT(*) FROM products")->fetchColumn();
} catch (PDOException $e) {
    $low_stock_items = [];
    $total_items = 0;
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - พนักงาน Winter Cool</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --orange: #f39c12; --teal: #16a085; --dark: #2c3e50; }
        body { font-family: 'Kanit', sans-serif; background: #f0f2f5; margin: 0; color: var(--dark); }
        
        nav { 
            background: var(--dark); 
            padding: 15px 5%; 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            color: white;
        }

        .container { width: 90%; max-width: 1200px; margin: 30px auto; }

        /* Status Cards */
        .status-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .status-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            border-left: 5px solid var(--teal);
        }
        .warning { border-left-color: var(--orange); }

        /* Quick Actions */
        .action-list {
            display: flex;
            gap: 15px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .action-btn {
            background: white;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            color: var(--dark);
            font-weight: 500;
            border: 1px solid #ddd;
            transition: 0.3s;
        }
        .action-btn:hover { background: var(--teal); color: white; border-color: var(--teal); }

        /* Stock Table */
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { color: #7f8c8d; font-weight: 500; }
        .stock-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
        }
        .low { background: #fdf2e9; color: #e67e22; }

        .btn-logout { color: #ecf0f1; text-decoration: none; border: 1px solid #7f8c8d; padding: 5px 15px; border-radius: 5px; }
    </style>
</head>
<body>

<nav>
    <div style="display:flex; align-items:center; gap:10px;">
        <i class="fa-solid fa-clipboard-user" style="font-size:24px; color:var(--teal);"></i>
        <h2 style="margin:0; font-size:20px;">Staff Operation Center</h2>
    </div>
    <a href="logout.php" class="btn-logout">Log Out</a>
</nav>

<div class="container">
    <div style="margin-bottom: 25px;">
        <h2 style="margin:0;">ยินดีต้อนรับ, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</h2>
        <p style="color:#7f8c8d; margin:5px 0;">วันนี้มีงานอะไรให้ช่วยไหม? (ตำแหน่ง: พนักงานดูแลสต็อก)</p>
    </div>

    <div class="status-grid">
        <div class="status-card">
            <div style="font-size:14px; color:#7f8c8d;">สินค้าทั้งหมดในระบบ</div>
            <div style="font-size:28px; font-weight:bold; color:var(--teal);"><?php echo $total_items; ?> <small style="font-size:14px;">รายการ</small></div>
        </div>
        <div class="status-card warning">
            <div style="font-size:14px; color:#7f8c8d;">สินค้าที่สต็อกใกล้หมด (< 10)</div>
            <div style="font-size:28px; font-weight:bold; color:var(--orange);"><?php echo count($low_stock_items); ?> <small style="font-size:14px;">รายการ</small></div>
        </div>
    </div>

    <div class="action-list">
        <a href="manage_products.php" class="action-btn"><i class="fa-solid fa-plus-circle"></i> เพิ่มสต็อกสินค้า</a>
        <a href="#" class="action-btn"><i class="fa-solid fa-list-check"></i> ตรวจสอบคำสั่งซื้อ</a>
        <a href="#" class="action-btn"><i class="fa-solid fa-print"></i> พิมพ์ใบจ่าหน้า</a>
    </div>

    <div class="table-container">
        <h3 style="margin-top:0;"><i class="fa-solid fa-triangle-exclamation" style="color:var(--orange);"></i> สินค้าที่ต้องเติมสต็อกด่วน</h3>
        <table>
            <thead>
                <tr>
                    <th>สินค้า</th>
                    <th>หมวดหมู่</th>
                    <th>คงเหลือ</th>
                    <th>จัดการ</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($low_stock_items) > 0): ?>
                    <?php foreach($low_stock_items as $item): ?>
                    <tr>
                        <td><strong><?php echo htmlspecialchars($item['name']); ?></strong></td>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><span class="stock-badge low"><?php echo $item['stock']; ?> ชิ้น</span></td>
                        <td><a href="manage_products.php" style="color:var(--teal); text-decoration:none;">เติมของ</a></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" style="text-align:center; color:#999; padding:30px;">ยังไม่มีสินค้าที่ต้องเติมสต็อก</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>