<?php
// remove_cart_item.php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get product ID from URL
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($product_id <= 0) {
    header("Location: cart.php?error=Invalid+product+ID");
    exit();
}

$user_id = $_SESSION['user_id'];

// Connect to database
require_once 'config/database.php';

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    // Get product name before deleting
    $stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    $product_name = $product['name'] ?? 'Item';
    
    // Delete from cart_items
    $delete_stmt = $conn->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $delete_stmt->execute([$user_id, $product_id]);
    
    if ($delete_stmt->rowCount() > 0) {
        $message = urlencode("✅ '" . $product_name . "' removed from cart");
    } else {
        $message = urlencode("⚠️ Item not found in cart");
    }
    
} catch(PDOException $e) {
    $message = urlencode("❌ Error: " . $e->getMessage());
}

// Redirect back to cart
header("Location: cart.php?message=" . $message);
exit();
?>