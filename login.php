<?php
session_start();
if (isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}
require "db.php";
$error = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    if ($username === "" || $password === "") {
        $error = "Please enter your username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE username = ? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        if ($user && password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            header("Location: index.php");
            exit;
        }
        $error = "Invalid login details.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Product Catalog</title>
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
<div class="login-bg-shape shape-one"></div>
<div class="login-bg-shape shape-two"></div>
<div class="login-shell">
    <div class="login-brand"><span class="brand-logo">▦</span><div><strong>Product Catalog</strong><small>Management System</small></div></div>
    <div class="login-card">
        <div class="login-icon">▦</div>
        <div class="eyebrow">ADMIN ACCESS</div>
        <h1>Welcome back</h1>
        <p>Sign in to manage your product catalog.</p>
        <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <form method="POST" class="login-form">
            <div class="field"><label>Username</label><input type="text" name="username" placeholder="Enter your username" autocomplete="username" required></div>
            <div class="field"><label>Password</label><input type="password" name="password" placeholder="Enter your password" autocomplete="current-password" required></div>
            <button class="btn primary full" type="submit">Sign In</button>
        </form>
        <a class="user-view-button" href="user_view.php"><span>◉</span> Continue as User</a>
    </div>
    <div class="login-footer">Product Catalog • Simple. Organized. Accessible.</div>
</div>
</body>
</html>
