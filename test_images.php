<?php
echo "<h1>Testing Images</h1>";

$images = [
    'default-food.jpg',
    'hero-bg.jpg',
    'pizza1.jpg',
    'burger1.jpg',
    'drink1.jpg',
    'dessert1.jpg'
];

foreach ($images as $image) {
    $path = "assets/images/$image";
    
    if (file_exists($path)) {
        echo "<p style='color:green'>✅ Found: $image</p>";
        echo "<img src='$path' style='width:200px; margin:10px; border:2px solid green'>";
    } else {
        echo "<p style='color:red'>❌ Missing: $image</p>";
    }
}

echo "<h2>Testing Website with Images</h2>";
echo "<a href='index.php'>Go to Homepage</a><br>";
echo "<a href='menu.php'>Go to Menu</a>";
?>