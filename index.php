<?php
session_start();

/* Initialize cart count */
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

/* Login state */
$is_logged_in = false;
$user_name = "";
$user_type = "user";

if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $is_logged_in = true;
    $user_name = $_SESSION['user']['name'] ?? "";
    $user_type = $_SESSION['user']['type'] ?? "user";
}

/* Search query */
$searchQuery = isset($_GET['q']) ? htmlspecialchars($_GET['q']) : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>G🍪🍪dies Cookies | Homemade Cookies</title>

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<!-- CSS -->
<link rel="stylesheet" href="style.css">
</head>

<body>

<!-- 🍪 HEADER -->
<header class="header">
    <div class="logo">SHAZ Cookies</div>

    <nav class="navbar">
        <a href="index.php">Home</a>
        <a href="shop.php">Shop</a>
        <a href="about.php">About</a>
        <a href="contact.php">Contact</a>
    </nav>

    <div class="header-right">
        <?php if ($is_logged_in): ?>
            <span class="user-greeting">👋 <?= htmlspecialchars($user_name); ?></span>
            <a href="logout.php" class="nav-auth-btn">Logout</a>
        <?php else: ?>
            <a href="login.php" class="nav-auth-btn">Login</a>
            <a href="register.php" class="nav-auth-btn primary">Register</a>
        <?php endif; ?>

        <a href="admin_login.php" class="nav-admin-link">👤Admin</a>

        <a href="cart.php" class="cart-btn">
            🛒 <span class="badge"><?= (int)$_SESSION['cart_count']; ?></span>
        </a>
    </div>
</header>



<!-- 🍪 HERO SECTION -->
<section class="hero">
    <img src="images/hero.jpg" alt="Homemade Cookies">
    <div class="hero-text">
        <h1> G🍪🍪dies Cookies</h1>
        <h2>100% Homemade</h2>
        <p>Freshly baked cookies made with love</p>
        <a href="shop.php" class="btn">Shop Now</a>
    </div>
</section>

<!-- ⭐ FEATURES -->
<section class="features">
    <h2>Why Choose G🍪🍪dies Cookies?</h2>

    <div class="features-grid">
        <div class="feature-card">
            <span>🍪</span>
            <h3>Freshly Baked</h3>
            <p>Cookies baked daily using quality ingredients.</p>
        </div>

        <div class="feature-card">
            <span>🌿</span>
            <h3>Natural Ingredients</h3>
            <p>No preservatives, only natural goodness.</p>
        </div>

        <div class="feature-card">
            <span>🎁</span>
            <h3>Perfect Gifts</h3>
            <p>Beautifully packed for every occasion.</p>
        </div>

        <div class="feature-card">
            <span>🚚</span>
            <h3>Fast Delivery</h3>
            <p>Quick doorstep delivery across Sri Lanka.</p>
        </div>
    </div>
</section>

<!-- 💬 CHAT BUTTON -->
<a href="contact.php" class="chat-btn">CONTACT WITH US</a>

<!-- 🧁 FOOTER -->
<footer>
    <p>©  SHAZ Cookies | Homemade Cookies with Love 💕</p>
</footer>

</body>
</html>
