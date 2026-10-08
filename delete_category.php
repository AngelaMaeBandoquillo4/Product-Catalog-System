<?php
require "auth.php";
require "db.php";
$id = (int)($_GET["id"] ?? 0);
if ($id > 0) {
    $check = $conn->prepare("SELECT COUNT(*) total FROM products WHERE category_id=?");
    $check->bind_param("i", $id);
    $check->execute();
    $count = (int)($check->get_result()->fetch_assoc()["total"] ?? 0);
    if ($count > 0) { header("Location: categories.php?msg=error%20Category%20has%20products"); exit; }
    $delete = $conn->prepare("DELETE FROM categories WHERE id=?");
    $delete->bind_param("i", $id);
    if ($delete->execute()) { header("Location: categories.php?msg=Category deleted successfully"); exit; }
}
header("Location: categories.php?msg=error%20Unable%20to%20delete%20category");
exit;
?>
