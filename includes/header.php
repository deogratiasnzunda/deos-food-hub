<?php
// Check login status
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$username = 'Guest';
$user_type = 'customer';

if ($isLoggedIn) {
    $username = $_SESSION['username'] ?? 'User';
    $user_type = $_SESSION['user_type'] ?? 'customer';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* Additional header styles */
        .user-dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background: white;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-radius: 5px;
            z-index: 1000;
        }
        
        .dropdown-content a {
            display: block;
            padding: 10px 15px;
            color: #333;
            text-decoration: none;
            border-bottom: 1px solid #eee;
        }
        
        .dropdown-content a:hover {
            background: #f8f9fa;
            color: #ff6b6b;
        }
        
        .user-dropdown:hover .dropdown-content {
            display: block;
        }
        
        .cart-count-badge {
            background: #ff6b6b;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            margin-left: 5px;
        }
    </style>
</head>
<body>
<header class="main-header">
    <div class="container">
        <div class="logo">
            <a href="index.php">🍕 Deo's <span>Food Hub</span></a>
        </div>
        
        <nav class="main-nav">
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                🏠 Home
            </a>
            <a href="menu.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'menu.php' ? 'active' : ''; ?>">
                📋 Menu
            </a>
            <a href="cart.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'active' : ''; ?>">
                🛒 Cart <span id="nav-cart-count" class="cart-count-badge">0</span>
            </a>
            
            <?php if($isLoggedIn): ?>
                <a href="my_orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'my_orders.php' ? 'active' : ''; ?>">
                    📦 Orders
                </a>
                <div class="user-dropdown">
                    <button class="user-btn">
                        👤 <?php echo htmlspecialchars($username); ?> ▼
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php">My Profile</a>
                        <a href="my_orders.php">My Orders</a>
                        <?php if($user_type == 'admin'): ?>
                            <a href="admin/">Admin Panel</a>
                        <?php endif; ?>
                        <a href="logout.php">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="login.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>">
                    🔐 Login
                </a>
                <a href="register.php" class="btn-register">
                    📝 Register
                </a>
            <?php endif; ?>
        </nav>
        
        <button class="mobile-menu-btn" onclick="toggleMobileMenu()">☰</button>
    </div>
</header>

<?php if(isset($_GET['message'])): ?>
    <div class="message <?php echo isset($_GET['error']) ? 'error' : 'success'; ?>">
        <?php echo htmlspecialchars($_GET['message']); ?>
    </div>
<?php endif; ?>