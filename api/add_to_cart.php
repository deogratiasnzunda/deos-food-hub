<?php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Login required']);
    exit();
}

$product_id = $_POST['product_id'] ?? $_GET['id'] ?? 0;
$quantity = $_POST['quantity'] ?? 1;

if ($product_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid product']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    $user_id = $_SESSION['user_id'];
    
    // ✅ FIX: Check if already in cart and update instead of inserting new
    $check = $conn->prepare("SELECT id, quantity FROM cart_items WHERE user_id = ? AND product_id = ?");
    $check->execute([$user_id, $product_id]);
    
    if ($check->rowCount() > 0) {
        // Update existing
        $item = $check->fetch(PDO::FETCH_ASSOC);
        $new_qty = $item['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart_items SET quantity = ? WHERE id = ?");
        $update->execute([$new_qty, $item['id']]);
        $action = 'Quantity updated';
    } else {
        // Insert new
        $insert = $conn->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->execute([$user_id, $product_id, $quantity]);
        $action = 'Added to cart';
    }
    
    echo json_encode(['success' => true, 'message' => $action]);
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>