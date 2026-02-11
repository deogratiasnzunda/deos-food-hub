<?php
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .orders-container {
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2.5rem;
        }
        
        .order-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .order-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .order-status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }
        
        .status-pending { background: #fff3cd; color: #856404; }
        .status-processing { background: #d1ecf1; color: #0c5460; }
        .status-delivered { background: #d4edda; color: #155724; }
        
        .no-orders {
            text-align: center;
            padding: 50px;
            color: #666;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="orders-container">
        <h1 class="page-title">📦 My Orders</h1>
        
        <div class="no-orders">
            <h3>No orders yet</h3>
            <p>You haven't placed any orders yet.</p>
            <a href="menu.php" class="btn btn-primary" style="margin-top: 20px;">
                Browse Menu & Order Now
            </a>
        </div>
        
        <!-- Sample order (will be dynamic in real system) -->
        <div class="order-card" style="display: none;">
            <div class="order-header">
                <div>
                    <h3>Order #1001</h3>
                    <p>Date: <?php echo date('F j, Y'); ?></p>
                </div>
                <div class="order-status status-delivered">Delivered</div>
            </div>
            
            <p><strong>Items:</strong> Pizza, Burger, Coke</p>
            <p><strong>Total:</strong> $25.99</p>
            <p><strong>Delivery Address:</strong> 123 Main Street</p>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>