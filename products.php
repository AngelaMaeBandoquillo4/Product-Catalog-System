<?php
require "auth.php";
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
    $params = [$term, $term, $term];
}
if ($category > 0) {
    $sql .= " AND p.category_id = ?";
    $types .= "i";
    $params[] = $category;
}
$sql .= " ORDER BY p.created_at DESC, p.id DESC";
$stmt = $conn->prepare($sql);
if ($types !== "") $stmt->bind_param($types, ...$params);
$stmt->execute();
$products = $stmt->get_result();
?>
<!doctype html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Products | Product Catalog</title><link rel="stylesheet" href="style.css"></head>
<body>
<header class="topbar"><a class="brand" href="index.php"><span class="brand-logo">▦</span><span>Product Catalog</span></a><nav class="nav-links"><a href="index.php">Dashboard</a><a class="active" href="products.php">Products</a><a href="categories.php">Categories</a><a href="user_view.php">User View</a></nav><div class="top-actions"><a class="add-top" href="add_product.php">+ Add Product</a><div class="user-menu"><span class="avatar">A</span><span><?= htmlspecialchars($_SESSION["username"] ?? "Admin") ?></span><a href="logout.php">Logout</a></div></div></header>
<main class="container">
<?php if(isset($_GET["msg"])): ?><div class="alert <?= str_starts_with($_GET["msg"],"error") ? "error" : "success" ?>"><?= htmlspecialchars($_GET["msg"]) ?></div><?php endif; ?>
<div class="page-heading"><div><div class="eyebrow">INVENTORY</div><h1>Product Catalog</h1><p>Browse, search, and manage your products.</p></div><a class="btn primary" href="add_product.php">+ Add Product</a></div>
<form class="filter-panel" method="GET"><div class="search-box"><span>⌕</span><input name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Search product, category, or description..."></div><select name="category"><option value="0">All Categories</option><?php while($c=$categories->fetch_assoc()): ?><option value="<?= (int)$c["id"] ?>" <?= $category===(int)$c["id"] ? "selected" : "" ?>><?= htmlspecialchars($c["category_name"]) ?></option><?php endwhile; ?></select><button class="btn primary" type="submit">Search</button><a class="btn light" href="products.php">Clear</a></form>
<div class="catalog-meta"><span><b><?= $products->num_rows ?></b> product<?= $products->num_rows===1?"":"s" ?> found</span><a href="categories.php">Manage Categories →</a></div>
<div class="product-grid">
<?php if($products->num_rows): while($p=$products->fetch_assoc()): $img=$p["image"]??""; $local=$img!=="" && file_exists(__DIR__."/uploads/".basename($img)); ?>
<article class="product-card"><a class="product-image" href="view_product.php?id=<?= (int)$p["id"] ?>"><?php if($local): ?><img src="uploads/<?= htmlspecialchars(basename($img)) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php elseif(filter_var($img,FILTER_VALIDATE_URL)): ?><img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($p["product_name"]) ?>"><?php else: ?><div class="image-placeholder">▦</div><?php endif; ?></a><div class="product-info"><div class="card-top"><span class="category-pill"><?= htmlspecialchars($p["category_name"] ?? "Uncategorized") ?></span><span class="product-id">#<?= (int)$p["id"] ?></span></div><h3><?= htmlspecialchars($p["product_name"]) ?></h3><p><?= htmlspecialchars($p["description"]) ?></p><div class="product-bottom"><strong>₱<?= number_format((float)$p["price"],2) ?></strong><?php if((int)$p["stock"]===0): ?><span class="stock out">Out of Stock</span><?php elseif((int)$p["stock"]<=5): ?><span class="stock low"><?= (int)$p["stock"] ?> left</span><?php else: ?><span class="stock in"><?= (int)$p["stock"] ?> in stock</span><?php endif; ?></div><div class="card-actions"><a href="view_product.php?id=<?= (int)$p["id"] ?>">View</a><a href="edit_product.php?id=<?= (int)$p["id"] ?>">Edit</a><a class="danger-link" href="delete_product.php?id=<?= (int)$p["id"] ?>" onclick="return confirm('Delete this product?')">Delete</a></div></div></article>
<?php endwhile; else: ?><div class="empty-state"><div>⌕</div><h3>No products found</h3><p>Try another search or add a new product.</p><a class="btn primary" href="add_product.php">+ Add Product</a></div><?php endif; ?>
</div></main></body></html>
