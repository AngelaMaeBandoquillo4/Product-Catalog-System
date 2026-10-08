<?php
$conn = new mysqli("localhost", "root", "", "product_catalog");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$conn->query("CREATE TABLE IF NOT EXISTS categories (id INT AUTO_INCREMENT PRIMARY KEY, category_name VARCHAR(100) NOT NULL UNIQUE, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS products (id INT AUTO_INCREMENT PRIMARY KEY, product_name VARCHAR(150) NOT NULL, category_id INT NOT NULL, price DECIMAL(10,2) NOT NULL DEFAULT 0, stock INT NOT NULL DEFAULT 0, description TEXT NOT NULL, image VARCHAR(500) DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX(category_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$conn->query("CREATE TABLE IF NOT EXISTS users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(100) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

$categories = ["Smartphone", "Laptop", "Electronics", "Accessories", "Monitor", "Printer"];
foreach ($categories as $category) {
    $stmt = $conn->prepare("INSERT IGNORE INTO categories (category_name) VALUES (?)");
    $stmt->bind_param("s", $category);
    $stmt->execute();
}

$adminPassword = password_hash("admin123", PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT IGNORE INTO users (username, password) VALUES ('admin', ?)");
$stmt->bind_param("s", $adminPassword);
$stmt->execute();

$sampleProducts = [
    ["iPhone 15 Pro Max", "Smartphone", 69999, 10, "Premium smartphone with a powerful processor, advanced camera system, and high-resolution display.", "https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?auto=format&fit=crop&w=900&q=85"],
    ["Acer Aspire 5", "Laptop", 25999, 10, "Reliable everyday laptop for students, office work, browsing, and productivity.", "https://images.unsplash.com/photo-1496181133206-80ce9b88a853?auto=format&fit=crop&w=900&q=85"],
    ["Wireless Headphones", "Electronics", 650, 15, "Comfortable wireless headphones with clear sound for music, study, and entertainment.", "https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=85"],
    ["Mechanical Keyboard", "Accessories", 1899, 12, "Responsive mechanical keyboard designed for comfortable typing and gaming.", "https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&w=900&q=85"],
    ["Samsung 24-inch Monitor", "Monitor", 5499, 7, "Slim Full HD monitor with vivid colors and a clean workspace-friendly design.", "https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&w=900&q=85"],
    ["Epson Ink Tank Printer", "Printer", 6299, 5, "Practical ink tank printer for school documents, home use, and office printing.", "https://images.unsplash.com/photo-1612815154858-60aa4c59eaa6?auto=format&fit=crop&w=900&q=85"]
];

foreach ($sampleProducts as $product) {
    $check = $conn->prepare("SELECT id FROM products WHERE product_name = ? LIMIT 1");
    $check->bind_param("s", $product[0]);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        $cat = $conn->prepare("SELECT id FROM categories WHERE category_name = ? LIMIT 1");
        $cat->bind_param("s", $product[1]);
        $cat->execute();
        $catRow = $cat->get_result()->fetch_assoc();
        if ($catRow) {
            $insert = $conn->prepare("INSERT INTO products (product_name, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
            $insert->bind_param("sidiss", $product[0], $catRow["id"], $product[2], $product[3], $product[4], $product[5]);
            $insert->execute();
        }
    }
}
?>
