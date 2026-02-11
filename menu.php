<?php
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Get categories
$categories_query = "SELECT * FROM categories ORDER BY name";
$categories_stmt = $db->prepare($categories_query);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get products
$products_query = "SELECT p.*, c.name as category_name 
                   FROM products p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.is_available = 1 
                   ORDER BY p.name";
$products_stmt = $db->prepare($products_query);
$products_stmt->execute();
$products = $products_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu - Deo's Food Hub</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .menu-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }
        
        .page-title {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2.5rem;
        }
        
        .search-box {
            max-width: 500px;
            margin: 0 auto 40px;
            position: relative;
        }
        
        .search-box input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #ddd;
            border-radius: 50px;
            font-size: 16px;
            padding-left: 50px;
        }
        
        .search-box::before {
            content: "🔍";
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 18px;
            color: #666;
        }
        
        .category-filters {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 40px;
        }
        
        .no-products {
            text-align: center;
            padding: 50px;
            color: #666;
            font-size: 1.2rem;
            grid-column: 1 / -1;
        }
    </style>
</head>
<body>
    <!-- HEADER -->
    <?php include 'includes/header.php'; ?>
    
    <!-- MENU CONTENT -->
    <div class="menu-container">
        <h1 class="page-title">Our Delicious Menu</h1>
        
        <!-- SEARCH BOX -->
        <div class="search-box">
            <input type="text" id="search-input" placeholder="Search for dishes..." onkeyup="searchProducts()">
        </div>
        
        <!-- CATEGORY FILTERS -->
        <div class="category-filters">
            <button class="category-btn active" onclick="filterProducts('all')">All Items</button>
            <?php foreach($categories as $category): ?>
                <button class="category-btn" onclick="filterProducts(<?php echo $category['id']; ?>)">
                    <?php echo htmlspecialchars($category['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <!-- PRODUCTS GRID -->
        <div class="products-grid" id="products-container">
            <?php if(count($products) > 0): ?>
                <?php foreach($products as $product): ?>
                    <div class="product-card" data-category="<?php echo $product['category_id']; ?>">
                        <div class="product-image">
                            <?php 
                            $image_src = 'assets/images/default-food.jpg';
                            if(!empty($product['image_url'])) {
                                $image_src = $product['image_url'];
                            }
                            ?>
                            <img src="<?php echo $image_src; ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-food.jpg'">
                        </div>
                        
                        <div class="product-info">
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </div>
                            <h3 class="product-name">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                            <p class="product-description">
                                <?php echo htmlspecialchars($product['description'] ?? 'Delicious food item'); ?>
                            </p>
                            <div class="product-price">
                                $<?php echo number_format($product['price'], 2); ?>
                            </div>
                            <div class="product-actions">
                                <button class="btn btn-cart" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        data-product-name="<?php echo addslashes($product['name']); ?>"
                                        data-product-price="<?php echo $product['price']; ?>">
                                    Add to Cart
                                </button>
                                <button class="btn btn-secondary" 
                                        onclick="showProductDetails(<?php echo $product['id']; ?>)">
                                    Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>No products available at the moment. Please check back later.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- FOOTER -->
    <?php include 'includes/footer.php'; ?>
    
    <script>
        // Filter products by category
        function filterProducts(categoryId) {
            const buttons = document.querySelectorAll('.category-btn');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            const products = document.querySelectorAll('.product-card');
            products.forEach(product => {
                if(categoryId === 'all' || product.dataset.category == categoryId) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
        
        // Search products
        function searchProducts() {
            const searchTerm = document.getElementById('search-input').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const name = product.querySelector('.product-name').textContent.toLowerCase();
                const desc = product.querySelector('.product-description').textContent.toLowerCase();
                
                if(name.includes(searchTerm) || desc.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
        
        // Show product details
        function showProductDetails(productId) {
            alert('Product details will be shown here in next version!');
        }
    </script>
</body>
</html>