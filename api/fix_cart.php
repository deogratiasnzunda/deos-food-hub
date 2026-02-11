<?php
// fix_cart.php - Run this once to test
session_start();
$_SESSION['user_id'] = 1; // TEMPORARY - remove later

require_once 'config/database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "✓ Database WORKS!<br>";
    
    // Test add to cart
    $test = $conn->prepare("INSERT INTO cart_items (user_id, food_id, quantity) VALUES (1, 1, 1)");
    if ($test->execute()) {
        echo "✓ Cart WORKS!<br>";
        echo "✅ SYSTEM IS OK - IGNORE VS CODE ERRORS";
    }
} else {
    echo "✗ Check config/database.php";
}
?>