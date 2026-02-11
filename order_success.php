<?php
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

// Clear cart after successful order
echo '<script>localStorage.removeItem("cart");</script>';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Successful - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .success-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .success-icon {
            font-size: 5rem;
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .success-message h1 {
            color: #4CAF50;
            margin-bottom: 20px;
        }
        
        .order-info {
            background: white;
            border-radius: 10px;
            padding: 30px;
            margin: 30px 0;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="success-container">
        <div class="success-icon">✅</div>
        
        <div class="success-message">
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for your order. We're preparing your food with love!</p>
        </div>
        
        <div class="order-info">
            <h3>Order Details</h3>
            <p><strong>Order Number:</strong> #<?php echo rand(1000, 9999); ?></p>
            <p><strong>Estimated Delivery:</strong> 30-45 minutes</p>
            <p><strong>Payment:</strong> Cash on Delivery</p>
            <p><strong>Status:</strong> 🚚 Preparing your order</p>
        </div>
        
        <div class="actions">
            <a href="index.php" class="btn btn-primary">
                Back to Home
            </a>
            <a href="my_orders.php" class="btn btn-secondary">
                View My Orders
            </a>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>