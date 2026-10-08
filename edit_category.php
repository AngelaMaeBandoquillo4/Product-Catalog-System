<?php
require "auth.php";
require "db.php";
$id = (int)($_GET["id"] ?? $_POST["id"] ?? 0);
$stmt = $conn->prepare("SELECT * FROM categories WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
if (!$category) { header("Location: categories.php?msg=error%20Category%20not%20found"); exit; }
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["category_name"] ?? "");
    if ($name === "") $error = "Category name is required.";
    else {
        
        $check = $conn->prepare("SELECT id FROM categories WHERE LOWER(category_name)=LOWER(?) AND id<>? LIMIT 1");
        $check->bind_param("si", $name, $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) $error = "That category already exists.";
        else {
            $update = $conn->prepare("UPDATE categories SET category_name=? WHERE id=?");
            $update->bind_param("si", $name, $id);
            if ($update->execute()) { header("Location: categories.php?msg=Category updated successfully"); exit; }
            $error = "Unable to update category: ".$conn->error;
        }
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Edit Category | Product Catalog</title><link rel="stylesheet" href="style.css"></head><body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-logo">▦</span><span>Product Catalog</span></a><nav class="nav-links"><a href="index.php">Dashboard</a><a href="products.php">Products</a><a class="active" href="categories.php">Categories</a><a href="user_view.php">User View</a></nav><div class="top-actions"><a class="add-top" href="add_product.php">+ Add Product</a><div class="user-menu"><span class="avatar">A</span><span><?= htmlspecialchars($_SESSION["username"]??"Admin") ?></span><a href="logout.php">Logout</a></div></div></header>
<main class="container narrow"><div class="page-heading"><div><div class="eyebrow">CATEGORIES</div><h1>Edit Category</h1><p>Update this category name.</p></div><a class="btn light" href="categories.php">← Back</a></div><?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?><form class="form-card" method="POST"><input type="hidden" name="id" value="<?= $id ?>"><div class="field"><label>Category Name</label><input name="category_name" value="<?= htmlspecialchars($_POST["category_name"]??$category["category_name"]) ?>" required></div><div class="form-actions"><a class="btn light" href="categories.php">Cancel</a><button class="btn primary" type="submit">Update Category</button></div></form></main></body></html>
