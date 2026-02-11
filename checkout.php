<?php
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if (!$isLoggedIn) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process order
    header("Location: order_success.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .checkout-container {
            max-width: 800px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2.5rem;
        }
        
        .checkout-box {
            background: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .order-summary {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }
        
        .checkout-button {
            width: 100%;
            padding: 15px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="checkout-container">
        <h1 class="page-title">Checkout</h1>
        
        <div class="checkout-box">
            <h2>Order Summary</h2>
            <div class="order-summary">
                <p>Your order total will be calculated at checkout.</p>
                <p>Delivery address will be confirmed via phone.</p>
            </div>
            
            <form method="POST" action="">
                <h3>Payment Method</h3>
                <select style="width:100%;padding:10px;margin:10px 0;">
                    <option>Cash on Delivery</option>
                    <option>Mobile Money</option>
                    <option>Credit Card</option>
                </select>
                
                <button type="submit" class="checkout-button">
                    Place Order & Pay on Delivery
                </button>
            </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>