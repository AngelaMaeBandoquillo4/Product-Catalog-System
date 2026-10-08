<?php
require "auth.php";
require "db.php";
$id = (int)($_GET["id"] ?? 0);
if ($id > 0) {
    $stmt = $conn->prepare("SELECT image FROM products WHERE id=? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $delete = $conn->prepare("DELETE FROM products WHERE id=?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) {
        if ($product && !empty($product["image"]) && !filter_var($product["image"], FILTER_VALIDATE_URL) && file_exists(__DIR__."/uploads/".basename($product["image"]))) unlink(__DIR__."/uploads/".basename($product["image"]));
        header("Location: products.php?msg=Product deleted successfully");
        exit;
    }
}
header("Location: products.php?msg=error%20Unable%20to%20delete%20product");
exit;
?>
