<?php
require "auth.php";
require "db.php";
$totalProducts = (int)($conn->query("SELECT COUNT(*) total FROM products")->fetch_assoc()["total"] ?? 0);
$totalStock = (int)($conn->query("SELECT COALESCE(SUM(stock),0) total FROM products")->fetch_assoc()["total"] ?? 0);
$totalCategories = (int)($conn->query("SELECT COUNT(*) total FROM categories")->fetch_assoc()["total"] ?? 0);
$lowStock = (int)($conn->query("SELECT COUNT(*) total FROM products WHERE stock BETWEEN 1 AND 5")->fetch_assoc()["total"] ?? 0);
$outOfStock = (int)($conn->query("SELECT COUNT(*) total FROM products WHERE stock=0")->fetch_assoc()["total"] ?? 0);
$recent = $conn->query("SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id ORDER BY p.created_at DESC, p.id DESC LIMIT 6");
?>
<!doctype html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Dashboard | Product Catalog</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-logo">▦</span><span>Product Catalog</span></a><nav class="nav-links"><a class="active" href="index.php">Dashboard</a><a href="products.php">Products</a><a href="categories.php">Categories</a><a href="user_view.php">User View</a></nav><div class="top-actions"><a class="add-top" href="add_product.php">+ Add Product</a><div class="user-menu"><span class="avatar">A</span><span><?= htmlspecialchars($_SESSION["username"] ?? "Admin") ?></span><a href="logout.php">Logout</a></div></div></header>
<main class="container">
<section class="hero-panel"><div><div class="eyebrow">OVERVIEW</div><h1>Good day, <?= htmlspecialchars(ucfirst($_SESSION["username"] ?? "Admin")) ?>!</h1><p>Manage your products, categories, and inventory from one place.</p></div><a class="hero-button" href="user_view.php">Open User Catalog <span>→</span></a></section>
<section class="stats-grid">
<div class="stat-card"><div class="stat-icon teal">▦</div><div><span>Total Products</span><strong><?= $totalProducts ?></strong></div></div>
<div class="stat-card"><div class="stat-icon mint">◫</div><div><span>Total Stock</span><strong><?= number_format($totalStock) ?></strong></div></div>
<div class="stat-card"><div class="stat-icon blue">◈</div><div><span>Categories</span><strong><?= $totalCategories ?></strong></div></div>
<div class="stat-card"><div class="stat-icon amber">!</div><div><span>Low Stock</span><strong><?= $lowStock ?></strong></div></div>
<div class="stat-card"><div class="stat-icon red">×</div><div><span>Out of Stock</span><strong><?= $outOfStock ?></strong></div></div>
</section>
<section class="section-card"><div class="section-header"><div><div class="eyebrow">CATALOG</div><h2>Recently Added</h2><p>Your latest products.</p></div><a class="btn light" href="products.php">View All Products →</a></div><div class="mini-grid">
<?php while($p=$recent->fetch_assoc()): $img=$p["image"]??""; $local=$img!=="" && file_exists(__DIR__."/uploads/".basename($img)); ?>
<article class="mini-product"><a class="mini-image" href="view_product.php?id=<?= (int)$p["id"] ?>"><?php if($local): ?><img src="uploads/<?= htmlspecialchars(basename($img)) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php elseif(filter_var($img,FILTER_VALIDATE_URL)): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php else: ?><div class="image-placeholder">▦</div><?php endif; ?></a><div><span class="category-pill"><?= htmlspecialchars($p["category_name"] ?? "Uncategorized") ?></span><h3><?= htmlspecialchars($p["product_name"]) ?></h3><strong>₱<?= number_format((float)$p["price"],2) ?></strong></div></article>
<?php endwhile; ?></div></section>
</main>
</body></html>
