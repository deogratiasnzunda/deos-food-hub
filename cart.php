<?php
session_start();
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);

if (!$isLoggedIn) {
    header("Location: login.php?message=Please login to view your cart");
    exit();
}

// Get cart items from database - FIXED FOR DUPLICATES
$cart_items = [];
$subtotal = 0;

if ($isLoggedIn) {
    try {
        $database = new Database();
        $conn = $database->getConnection();
        
        $user_id = $_SESSION['user_id'];
        
        // ✅ FIXED: Proper duplicate handling with subquery
        $query = "SELECT 
                    p.id as product_id,
                    p.name,
                    p.price,
                    p.description,
                    p.image_url,
                    p.image,
                    p.category_id,
                    (SELECT SUM(quantity) FROM cart_items ci2 WHERE ci2.user_id = :user_id AND ci2.product_id = p.id) as quantity
                 FROM products p
                 WHERE p.id IN (SELECT DISTINCT product_id FROM cart_items WHERE user_id = :user_id)
                 ORDER BY p.name";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Filter out items with quantity 0 or null
        $cart_items = array_filter($cart_items, function($item) {
            return !empty($item['quantity']) && $item['quantity'] > 0;
        });
        
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
        $cart_items = [];
    }
}

// Handle clear cart
if (isset($_GET['action']) && $_GET['action'] == 'clear') {
    if ($isLoggedIn) {
        try {
            $database = new Database();
            $conn = $database->getConnection();
            $stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $_SESSION['user_id']);
            $stmt->execute();
        } catch(Exception $e) {
            // Ignore error
        }
    }
    header("Location: cart.php");
    exit();
}

// Check for messages
$message = $_GET['message'] ?? '';
$error_msg = $_GET['error'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .cart-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
            font-size: 2.5rem;
        }
        
        .cart-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        
        @media (max-width: 768px) {
            .cart-content {
                grid-template-columns: 1fr;
            }
        }
        
        .cart-items {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .cart-item {
            display: grid;
            grid-template-columns: 100px 2fr 1fr 1fr auto;
            gap: 20px;
            padding: 20px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }
        
        @media (max-width: 900px) {
            .cart-item {
                grid-template-columns: 80px 1fr;
                gap: 15px;
            }
            
            .cart-item-price, 
            .cart-item-quantity,
            .cart-item-actions {
                grid-column: 2;
            }
        }
        
        .cart-item:last-child {
            border-bottom: none;
        }
        
        .cart-item-image img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
            background: #f5f5f5;
            border: 1px solid #eee;
        }
        
        .cart-item-info h4 {
            margin: 0 0 5px 0;
            color: #333;
            font-size: 1.1rem;
        }
        
        .cart-item-info p {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .qty-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #ddd;
            background: #f8f9fa;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.2rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .qty-btn:hover {
            background: #e9ecef;
        }
        
        .quantity-input {
            width: 60px;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }
        
        .cart-item-price {
            font-weight: bold;
            color: #ff6b6b;
            font-size: 1.1rem;
            text-align: center;
        }
        
        .cart-item-actions {
            text-align: center;
        }
        
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-delete:hover {
            background: #c82333;
        }
        
        .cart-summary {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            height: fit-content;
        }
        
        .summary-title {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ff6b6b;
            color: #333;
            font-size: 1.5rem;
        }
        
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .summary-total {
            font-size: 1.3rem;
            font-weight: bold;
            color: #ff6b6b;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #ff6b6b;
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin: 30px 0;
        }
        
        .empty-cart-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .cart-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 500;
            border: none;
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn-primary {
            background: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background: #45a049;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-checkout {
            background: #ff6b6b;
            color: white;
            width: 100%;
            text-align: center;
            margin-top: 20px;
            font-size: 1.1rem;
            padding: 15px;
        }
        
        .btn-checkout:hover {
            background: #ff5252;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }
        
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <?php include 'includes/header.php'; ?>
    
    <!-- CART CONTENT -->
    <div class="cart-container">
        <h1 class="page-title">🛒 Your Shopping Cart</h1>
        
        <?php if ($message): ?>
            <div class="message success"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        
        <?php if ($error_msg): ?>
            <div class="message error"><?php echo htmlspecialchars($error_msg); ?></div>
        <?php endif; ?>
        
        <?php if (empty($cart_items)): ?>
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Looks like you haven't added any items to your cart yet.</p>
                <a href="menu.php" class="btn btn-primary" style="margin-top: 20px;">
                    Browse Menu
                </a>
            </div>
        <?php else: ?>
            <div class="cart-content">
                <!-- Cart Items -->
                <div class="cart-items">
                    <form id="cart-form" method="POST" action="update_cart.php">
                        <?php 
                        $subtotal = 0;
                        foreach ($cart_items as $item): 
                            $item_total = $item['price'] * $item['quantity'];
                            $subtotal += $item_total;
                            
                            // Get image
                            $image_file = 'default-food.jpg';
                            if (!empty($item['image_url'])) {
                                $img = basename($item['image_url']);
                                if (!empty($img) && $img != 'default-food.jpg') {
                                    $image_file = $img;
                                }
                            }
                            
                            // Fallback to image column
                            if ($image_file == 'default-food.jpg' && !empty($item['image']) && $item['image'] != 'default-food.jpg') {
                                $img = basename($item['image']);
                                if (!empty($img)) {
                                    $image_file = $img;
                                }
                            }
                            
                            // Smart image detection
                            $product_name = strtolower($item['name']);
                            if (strpos($product_name, 'pizza') !== false && $image_file == 'default-food.jpg') {
                                $image_file = 'pizza.jpg';
                            } elseif (strpos($product_name, 'burger') !== false && $image_file == 'default-food.jpg') {
                                $image_file = 'burger.jpg';
                            } elseif (strpos($product_name, 'cola') !== false && $image_file == 'default-food.jpg') {
                                $image_file = 'coca_cola.jpg';
                            }
                        ?>
                            <div class="cart-item" id="cart-item-<?php echo $item['product_id']; ?>">
                                <div class="cart-item-image">
                                    <img src="assets/images/<?php echo htmlspecialchars($image_file); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                                         onerror="this.onerror=null; this.src='assets/images/default-food.jpg';">
                                </div>
                                
                                <div class="cart-item-info">
                                    <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p>Price: $<?php echo number_format($item['price'], 2); ?> each</p>
                                    <?php if (!empty($item['description'])): ?>
                                        <p style="font-size: 0.8rem; color: #888; margin-top: 5px;">
                                            <?php echo htmlspecialchars($item['description']); ?>
                                        </p>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="cart-item-quantity">
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <button type="button" class="qty-btn" 
                                                onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">
                                            -
                                        </button>
                                        
                                        <input type="number" 
                                               name="quantity[<?php echo $item['product_id']; ?>]" 
                                               id="qty-<?php echo $item['product_id']; ?>"
                                               value="<?php echo $item['quantity']; ?>" 
                                               min="1" 
                                               max="99"
                                               class="quantity-input">
                                        
                                        <button type="button" class="qty-btn" 
                                                onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">
                                            +
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="cart-item-price">
                                    $<?php echo number_format($item_total, 2); ?>
                                </div>
                                
                                <div class="cart-item-actions">
                                    <a href="remove_cart_item.php?id=<?php echo $item['product_id']; ?>" 
                                       class="btn-delete"
                                       onclick="return confirm('Are you sure you want to remove this item?')">
                                        Remove
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="cart-actions">
                            <a href="cart.php?action=clear" 
                               class="btn btn-danger"
                               onclick="return confirm('Clear all items from cart?')">
                                🗑️ Clear Cart
                            </a>
                            
                            <div style="display: flex; gap: 15px;">
                                <a href="menu.php" class="btn btn-secondary">
                                    ← Continue Shopping
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Update Cart
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Order Summary -->
                <div class="cart-summary">
                    <h3 class="summary-title">Order Summary</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Delivery Fee</span>
                        <span><?php echo ($subtotal > 30) ? 'FREE' : '$2.99'; ?></span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Tax (10%)</span>
                        <span>$<?php echo number_format($subtotal * 0.1, 2); ?></span>
                    </div>
                    
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>
                            $<?php 
                            $delivery = ($subtotal > 30) ? 0 : 2.99;
                            $tax = $subtotal * 0.1;
                            $total = $subtotal + $delivery + $tax;
                            echo number_format($total, 2); 
                            ?>
                        </span>
                    </div>
                    
                    <?php if ($subtotal > 0): ?>
                        <a href="checkout.php" class="btn btn-checkout">
                            ✅ Proceed to Checkout
                        </a>
                    <?php endif; ?>
                    
                    <?php if ($subtotal < 30): ?>
                        <p style="text-align: center; margin-top: 15px; color: #666; font-size: 0.9rem;">
                            <small>Add $<?php echo number_format(30 - $subtotal, 2); ?> more for FREE delivery!</small>
                        </p>
                    <?php else: ?>
                        <p style="text-align: center; margin-top: 15px; color: #28a745; font-size: 0.9rem;">
                            <small>🎉 You qualify for FREE delivery!</small>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Function to update quantity
        function updateQuantity(productId, change) {
            const input = document.getElementById('qty-' + productId);
            let newQty = parseInt(input.value) + change;
            
            if (newQty < 1) newQty = 1;
            if (newQty > 99) newQty = 99;
            
            input.value = newQty;
            submitCartUpdate();
        }
        
        // Function to submit cart update
        function submitCartUpdate() {
            const formData = new FormData(document.getElementById('cart-form'));
            
            fetch('update_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update cart');
            });
        }
        
        // Auto-submit when quantity changes
        document.querySelectorAll('.quantity-input').forEach(input => {
            input.addEventListener('change', function() {
                let newQty = parseInt(this.value);
                if (newQty < 1) this.value = 1;
                if (newQty > 99) this.value = 99;
                submitCartUpdate();
            });
        });
        
        // Handle form submission
        document.getElementById('cart-form').addEventListener('submit', function(e) {
            e.preventDefault();
            submitCartUpdate();
        });
    </script>
</body>
</html>