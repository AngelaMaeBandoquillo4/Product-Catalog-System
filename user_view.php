<?php
require "db.php";
$search = trim($_GET["search"] ?? "");
$category = (int)($_GET["category"] ?? 0);
$categories = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name");
$sql = "SELECT p.*, c.category_name FROM products p LEFT JOIN categories c ON c.id=p.category_id WHERE 1=1";
$types = "";
$params = [];
if ($search !== "") {
    $sql .= " AND (p.product_name LIKE ? OR c.category_name LIKE ? OR p.description LIKE ?)";
    $term = "%$search%";
    $types = "sss";
    $params = [$term,$term,$term];
}
if ($category > 0) {
    $sql .= " AND p.category_id=?";
    $types .= "i";
    $params[] = $category;
}
$sql .= " ORDER BY p.created_at DESC, p.id DESC";
$stmt = $conn->prepare($sql);
if ($types !== "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Shop | Product Catalog</title><link rel="stylesheet" href="style.css"></head><body class="user-page">
<header class="user-topbar"><a class="brand" href="user_view.php"><span class="brand-logo">▦</span><span>Product Catalog</span></a><div class="user-top-actions"><a href="login.php" class="admin-link">Admin Login</a></div></header>
<main class="user-container"><section class="user-hero"><div><span class="eyebrow">WELCOME TO OUR CATALOG</span><h1>Find the product you need.</h1><p>Explore our collection of electronics, devices, and accessories.</p></div><div class="user-hero-mark">▦</div></section>
<form class="filter-panel user-filter" method="GET"><div class="search-box"><span>⌕</span><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search products..."></div><select name="category"><option value="0">All Categories</option><?php while($c=$categories->fetch_assoc()): ?><option value="<?= (int)$c["id"] ?>" <?= $category===(int)$c["id"]?"selected":"" ?>><?= htmlspecialchars($c["category_name"]) ?></option><?php endwhile; ?></select><button class="btn primary" type="submit">Search</button><a class="btn light" href="user_view.php">Clear</a></form>
<div class="catalog-meta user-meta"><span><b><?= $products->num_rows ?></b> products available</span><span>Browse freely • No account required</span></div>
<div class="product-grid user-products">
<?php if($products->num_rows): while($p=$products->fetch_assoc()): $img=$p["image"]??""; $local=$img!=="" && file_exists(__DIR__."/uploads/".basename($img)); ?><article class="product-card user-product-card"><div class="product-image"><?php if($local): ?><img src="uploads/<?= htmlspecialchars(basename($img)) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php elseif(filter_var($img,FILTER_VALIDATE_URL)): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php else: ?><div class="image-placeholder">▦</div><?php endif; ?></div><div class="product-info"><span class="category-pill"><?= htmlspecialchars($p["category_name"]??"Uncategorized") ?></span><h3><?= htmlspecialchars($p["product_name"]) ?></h3><p><?= htmlspecialchars($p["description"]) ?></p><div class="product-bottom"><strong>₱<?= number_format((float)$p["price"],2) ?></strong><?php if((int)$p["stock"]===0): ?><span class="stock out">Out of Stock</span><?php elseif((int)$p["stock"]<=5): ?><span class="stock low"><?= (int)$p["stock"] ?> left</span><?php else: ?><span class="stock in">Available</span><?php endif; ?></div></div></article><?php endwhile; else: ?><div class="empty-state"><div>⌕</div><h3>No products found</h3><p>Try another search or category.</p></div><?php endif; ?></div></main><footer class="user-footer">Product Catalog Management System</footer></body></html>
