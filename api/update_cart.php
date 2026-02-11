<?php
// update_cart.php
session_start();
require_once 'config/database.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

try {
    $database = new Database();
    $conn = $database->getConnection();
    $user_id = $_SESSION['user_id'];
    
    if (isset($_POST['quantity']) && is_array($_POST['quantity'])) {
        foreach ($_POST['quantity'] as $product_id => $quantity) {
            $product_id = intval($product_id);
            $quantity = intval($quantity);
            
            if ($quantity > 0) {
                // Update quantity
                $stmt = $conn->prepare(
                    "UPDATE cart_items SET quantity = ? 
                     WHERE user_id = ? AND product_id = ?"
                );
                $stmt->execute([$quantity, $user_id, $food_id]);
            } else {
                // Remove if quantity is 0
                $stmt = $conn->prepare(
                    "DELETE FROM cart_items 
                     WHERE user_id = ? AND product_id = ?"
                );
                $stmt->execute([$user_id, $product_id]);
            }
        }
        
        echo json_encode(['success' => true, 'message' => 'Cart updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No items to update']);
    }
    
} catch(Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>