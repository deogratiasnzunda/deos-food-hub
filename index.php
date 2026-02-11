<?php
require_once 'config/database.php';

$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
$username = 'Guest';

if ($isLoggedIn) {
    $username = $_SESSION['username'] ?? 'User';
}

// Get featured products
try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT * FROM products WHERE is_available = 1 ORDER BY RAND() LIMIT 6";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $featured_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    $featured_products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 100px 20px;
            margin-bottom: 50px;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 20px;
        }
        
        .hero p {
            font-size: 1.3rem;
            margin-bottom: 30px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .featured-products {
            max-width: 1200px;
            margin: 0 auto 50px;
            padding: 0 20px;
        }
        
        .section-title {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2.5rem;
        }
        
        .stats {
            background: #ff6b6b;
            color: white;
            padding: 60px 20px;
            text-align: center;
            margin: 50px 0;
        }
        
        .stats-container {
            max-width: 1200px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
        }
        
        .stat-item h3 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }
        
        .hero-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <?php include 'includes/header.php'; ?>
    
    <!-- HERO SECTION -->
    <section class="hero">
        <h1>Taste the Best Food in Town!</h1>
        <p>Fresh ingredients, amazing flavors, delivered fast to your home or office. Order now and get 10% off your first order!</p>
        <div class="hero-buttons">
            <a href="menu.php" class="btn btn-primary">Order Now</a>
            <a href="register.php" class="btn btn-secondary">Sign Up Free</a>
        </div>
    </section>
    
    <!-- FEATURED PRODUCTS -->
    <section class="featured-products">
        <h2 class="section-title">🔥 Popular Dishes</h2>
        <div class="products-grid">
            <?php foreach($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="<?php echo !empty($product['image_url']) ? $product['image_url'] : 'assets/images/default-food.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             onerror="this.src='assets/images/default-food.jpg'">
                    </div>
                    <div class="product-info">
                        <div class="product-category">
                            <?php 
                            // Get category name
                            $cat_query = "SELECT name FROM categories WHERE id = :id";
                            $cat_stmt = $db->prepare($cat_query);
                            $cat_stmt->execute([':id' => $product['category_id']]);
                            $category = $cat_stmt->fetch(PDO::FETCH_ASSOC);
                            echo htmlspecialchars($category['name'] ?? 'Food');
                            ?>
                        </div>
                        <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                        <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                        <button class="btn btn-cart" 
                                data-product-id="<?php echo $product['id']; ?>"
                                data-product-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-product-price="<?php echo $product['price']; ?>">
                            Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
    
    <!-- STATS -->
    <section class="stats">
        <div class="stats-container">
            <div class="stat-item">
                <h3>500+</h3>
                <p>Happy Customers</p>
            </div>
            <div class="stat-item">
                <h3>50+</h3>
                <p>Menu Items</p>
            </div>
            <div class="stat-item">
                <h3>30min</h3>
                <p>Average Delivery</p>
            </div>
            <div class="stat-item">
                <h3>24/7</h3>
                <p>Customer Support</p>
            </div>
        </div>
    </section>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
</body>
</html>