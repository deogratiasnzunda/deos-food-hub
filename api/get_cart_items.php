<?php
require_once '../config/database.php';

header('Content-Type: application/json');

$database = new Database();
$db = $database->getConnection();

$data = json_decode(file_get_contents('php://input'), true);
$cart = $data['cart'] ?? [];

$items = [];
$total = 0;

foreach ($cart as $cart_item) {
    $query = "SELECT * FROM products WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute([':id' => $cart_item['id']]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product) {
        $subtotal = $product['price'] * $cart_item['quantity'];
        $total += $subtotal;
        
        $items[] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['price'],
            'image_url' => $product['image_url'],
            'quantity' => $cart_item['quantity'],
            'subtotal' => $subtotal
        ];
    }
}

echo json_encode([
    'success' => true,
    'items' => $items,
    'total' => $total
]);
?>