<?php
require "auth.php";
require "db.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["category_name"] ?? "");
    if ($name === "") $error = "Category name is required.";
    else {
        $check = $conn->prepare("SELECT id FROM categories WHERE LOWER(category_name)=LOWER(?) LIMIT 1");
        $check->bind_param("s", $name);
        $check->execute();
        if ($check->get_result()->num_rows > 0) $error = "That category already exists.";
        else {
            $stmt = $conn->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $name);
            if ($stmt->execute()) { header("Location: categories.php?msg=Category added successfully"); exit; }
            $error = "Unable to add category: ".$conn->error;
        }
    }
}
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Category | Product Catalog</title><link rel="stylesheet" href="style.css"></head><body>
<header class="topbar">
    <a class="brand" href="index.php">
        <span class="brand-logo">▦</span>
        <span>Product Catalog</span></a><nav class="nav-links">
            <a href="index.php">Dashboard</a>
            <a href="products.php">Products</a>
            <a class="active" href="categories.php">Categories</a>
            <a href="user_view.php">User View</a>
        </nav><div class="top-actions">
            <a class="add-top" href="add_product.php">+ Add Product</a>
            <div class="user-menu">
                <span class="avatar">A</span>
                <span><?= htmlspecialchars($_SESSION["username"]??"Admin") ?></span>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>
    
<main class="container narrow"><div class="page-heading"><div><div class="eyebrow">CATEGORIES</div><h1>Add Category</h1><p>Create a new category for your products.</p></div><a class="btn light" href="categories.php">← Back</a></div><?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?><form class="form-card" method="POST"><div class="field"><label>Category Name</label><input name="category_name" value="<?= htmlspecialchars($_POST["category_name"]??"") ?>" placeholder="e.g. Gaming" required></div><div class="form-actions"><a class="btn light" href="categories.php">Cancel</a><button class="btn primary" type="submit">Save Category</button></div></form></main></body></html>
