<?php
require "auth.php";
require "db.php";
$error = "";
$success = "";
$cats = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name");
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["product_name"] ?? "");
    $categoryId = (int)($_POST["category_id"] ?? 0);
    $price = (float)($_POST["price"] ?? 0);
    $stock = (int)($_POST["stock"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $imageUrl = trim($_POST["image_url"] ?? "");
    $image = "";
    if ($name === "") $error = "Product name is required.";
    elseif ($categoryId <= 0) $error = "Please select a category.";
    elseif ($price < 0) $error = "Price cannot be negative.";
    elseif ($stock < 0) $error = "Stock cannot be negative.";
    elseif ($description === "") $error = "Description is required.";
    elseif ($imageUrl !== "" && !filter_var($imageUrl, FILTER_VALIDATE_URL)) $error = "Please enter a valid image URL.";
    if ($error === "" && isset($_FILES["image"]) && $_FILES["image"]["error"] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES["image"]["error"] !== UPLOAD_ERR_OK) $error = "The image upload failed.";
        else {
            $file = $_FILES["image"];
            $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
            if (!in_array($ext, ["jpg","jpeg","png","webp"], true)) $error = "Image must be JPG, JPEG, PNG, or WEBP.";
            elseif ($file["size"] > 5242880) $error = "Image must not exceed 5MB.";
            else {
                if (!is_dir(__DIR__."/uploads")) mkdir(__DIR__."/uploads", 0777, true);
                $image = "product_".bin2hex(random_bytes(8)).".".$ext;
                if (!move_uploaded_file($file["tmp_name"], __DIR__."/uploads/".$image)) $error = "Unable to save the uploaded image.";
            }
        }
    }
    if ($error === "") {
        if ($image === "") $image = $imageUrl;
        $check = $conn->prepare("SELECT id FROM categories WHERE id=? LIMIT 1");
        $check->bind_param("i", $categoryId);
        $check->execute();
        if (!$check->get_result()->fetch_assoc()) $error = "Selected category was not found. Please choose again.";
    }
    if ($error === "") {
        $stmt = $conn->prepare("INSERT INTO products (product_name, category_id, price, stock, description, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sidiss", $name, $categoryId, $price, $stock, $description, $image);
        if ($stmt->execute()) {
            header("Location: products.php?msg=Product added successfully");
            exit;
        }
        $error = "Unable to add product: ".$conn->error;
    }
}
?>
<!doctype html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title>Add Product | Product Catalog</title><link rel="stylesheet" href="style.css"></head>
<body><header class="topbar"><a class="brand" href="index.php"><span class="brand-logo">▦</span><span>Product Catalog</span></a><nav class="nav-links"><a href="index.php">Dashboard</a><a class="active" href="products.php">Products</a><a href="categories.php">Categories</a><a href="user_view.php">User View</a></nav><div class="top-actions"><a class="add-top" href="add_product.php">+ Add Product</a><div class="user-menu"><span class="avatar">A</span><span><?= htmlspecialchars($_SESSION["username"]??"Admin") ?></span><a href="logout.php">Logout</a></div></div></header>
<main class="container narrow"><div class="page-heading"><div><div class="eyebrow">PRODUCTS</div><h1>Add Product</h1><p>Create a new item for your catalog.</p></div><a class="btn light" href="products.php">← Back</a></div><?php if($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?><form class="form-card" method="POST" enctype="multipart/form-data"><div class="form-grid"><div class="field full-width"><label>Product Name</label><input name="product_name" value="<?= htmlspecialchars($_POST["product_name"]??"") ?>" placeholder="e.g. iPhone 15 Pro Max" required></div><div class="field"><label>Category</label><div class="select-with-link"><select name="category_id" required><option value="">Select Category</option><?php while($c=$cats->fetch_assoc()): ?><option value="<?= (int)$c["id"] ?>" <?= ((int)($_POST["category_id"]??0)===(int)$c["id"]) ? "selected" : "" ?>><?= htmlspecialchars($c["category_name"]) ?></option><?php endwhile; ?></select><a href="add_category.php">+ New</a></div></div><div class="field"><label>Price (₱)</label><input type="number" name="price" value="<?= htmlspecialchars($_POST["price"]??"") ?>" min="0" step="0.01" placeholder="0.00" required></div><div class="field"><label>Stock</label><input type="number" name="stock" value="<?= htmlspecialchars($_POST["stock"]??"") ?>" min="0" placeholder="0" required></div><div class="field"><label>Upload Image</label><input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" id="imageInput"><small>Optional • JPG, PNG, WEBP • Max 5MB</small></div><div class="field full-width"><label>Image URL</label><input type="url" name="image_url" value="<?= htmlspecialchars($_POST["image_url"]??"") ?>" placeholder="https://example.com/product-image.jpg"><small>Optional • Use this if you do not want to upload a file.</small></div><div class="field full-width"><label>Description</label><textarea name="description" rows="6" placeholder="Describe the product..." required><?= htmlspecialchars($_POST["description"]??"") ?></textarea></div></div><div class="form-actions"><a class="btn light" href="products.php">Cancel</a><button class="btn primary" type="submit">Save Product</button></div></form></main></body></html>
