<?php
session_start();

/* Check admin login */
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
    header('location: admin_login.php');
    exit;
}

require_once __DIR__ . '/config.php';

/* Dynamic stats from database */
$total_orders = 0;
$total_users = 0;
$total_products = 0;
$total_revenue = 0;

if (isset($conn) && $conn instanceof mysqli && !$conn->connect_error) {
    // Total Orders
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM orders");
    if ($res && $row = $res->fetch_assoc()) $total_orders = (int)$row['cnt'];

    // Total Registered Customers (excluding admin)
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM users WHERE type = 'user'");
    if ($res && $row = $res->fetch_assoc()) $total_users = (int)$row['cnt'];

    // Total Products
    $res = $conn->query("SELECT COUNT(*) AS cnt FROM products");
    if ($res && $row = $res->fetch_assoc()) $total_products = (int)$row['cnt'];

    // Total Revenue (excluding cancelled orders)
    $res = $conn->query("SELECT COALESCE(SUM(total_price), 0) AS rev FROM orders WHERE status != 'Cancelled'");
    if ($res && $row = $res->fetch_assoc()) $total_revenue = (float)$row['rev'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard | G🍪🍪dies Cookies</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
/* =========================
   ADMIN DASHBOARD STYLE
========================= */
body{font-family:'Poppins',sans-serif;background:#fffafc;margin:0;padding:0;}
.header{display:flex;align-items:center;justify-content:space-between;background:linear-gradient(90deg,#f7b2c4,#f4a7b9);padding:16px 45px;position:sticky;top:0;z-index:1000;box-shadow:0 4px 15px rgba(0,0,0,.15);}
.logo{font-size:26px;font-weight:700;color:#fff;letter-spacing:2px;}
.navbar a{color:#fff;text-decoration:none;font-weight:500;margin-left:20px;transition:.3s;}
.navbar a:hover{color:#fff0f4;}
.header-right a{background:#fff;border-radius:20px;padding:8px 18px;color:var(--primary-pink-dark);font-weight:500;text-decoration:none;transition:.3s;}
.header-right a:hover{background:var(--primary-pink-dark);color:#fff;}

/* Dashboard container */
.dashboard{max-width:1200px;margin:40px auto;padding:0 20px;}

/* Welcome banner */
.welcome-banner{background:var(--banner-admin);padding:20px 30px;border-radius:15px;margin-bottom:40px;box-shadow:0 6px 20px rgba(0,0,0,.08);display:flex;justify-content:space-between;align-items:center;opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;}
.welcome-banner h2{color:#e0a600;font-size:28px;letter-spacing:1px;margin:0;}
.logout-btn{background:var(--primary-pink-dark);color:#fff;padding:8px 18px;border-radius:8px;text-decoration:none;font-weight:500;transition:.3s;}
.logout-btn:hover{background:var(--primary-pink);}

/* Cards */
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:30px;margin-bottom:40px;}
.card{background:#fff;padding:25px;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:.4s;opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;}
.card:hover{transform:translateY(-8px) scale(1.03);}
.card h3{color:var(--primary-pink-dark);margin-bottom:15px;font-size:20px;}
.card p{font-size:18px;font-weight:600;color:var(--accent-cookie);}
.card span{font-size:40px;display:block;margin-bottom:10px;}

/* Quick Links */
.quick-links{display:flex;flex-wrap:wrap;gap:25px;margin-bottom:40px;}
.quick-links a{flex:1 1 200px;text-align:center;background:#fff;padding:20px;border-radius:20px;box-shadow:0 10px 25px rgba(0,0,0,.1);text-decoration:none;color:var(--primary-pink-dark);font-weight:600;transition:.3s;opacity:0;transform:translateY(20px);animation:fadeIn 1.2s forwards;}
.quick-links a:hover{background:var(--primary-pink);color:#fff;transform:scale(1.05);}

/* Footer */
footer{background:var(--primary-pink-dark);color:#fff;text-align:center;padding:18px 0;font-size:14px;border-top:1px solid #e0a600;}

/* Animations */
@keyframes fadeIn{to{opacity:1;transform:translateY(0);}}

/* Responsive adjustments */
@media (max-width: 768px) {
  .header{padding:12px 18px;}
  .logo{font-size:20px;}
  .dashboard{margin:20px auto;padding:0 15px;}
  .welcome-banner{flex-direction:column;text-align:center;gap:12px;padding:18px 15px;margin-bottom:25px;}
  .welcome-banner h2{font-size:22px;}
  .cards{grid-template-columns:repeat(auto-fit,minmax(135px,1fr));gap:15px;margin-bottom:25px;}
  .card{padding:18px 12px;}
  .card span{font-size:30px;}
  .card h3{font-size:15px;}
  .card p{font-size:16px;}
  .quick-links{gap:12px;margin-bottom:25px;}
  .quick-links a{flex:1 1 100%;padding:14px;}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
<div class="logo">G🍪🍪dies Cookies Admin</div>
<div class="navbar header-right">
<a href="logout.php" class="logout-btn">Logout</a>
</div>
</header>

<div class="dashboard">

<!-- Welcome Banner -->
<div class="welcome-banner">
<h2>👑 Welcome Admin</h2>
<a href="manage_orders.php" class="logout-btn">View Orders</a>
</div>

<!-- Dashboard Cards -->
<div class="cards">
<div class="card">
<span>🛒</span>
<h3>Total Orders</h3>
<p><?= $total_orders ?></p>
</div>
<div class="card">
<span>👤</span>
<h3>Total Users</h3>
<p><?= $total_users ?></p>
</div>
<div class="card">
<span>🍪</span>
<h3>Total Products</h3>
<p><?= $total_products ?></p>
</div>
<div class="card">
<span>💰</span>
<h3>Total Revenue</h3>
<p>₨ <?= number_format($total_revenue) ?></p>
</div>
</div>

<!-- Quick Links -->
<div class="quick-links">
<a href="manage_product.php">Manage Products</a>
<a href="manage_orders.php">Manage Orders</a>
<a href="manage_users.php">Manage Users</a>
<a href="manage_messages.php">manage Messages</a>
</div>

</div>

<footer>
<p>© <?= date('Y') ?> G🍪🍪dies Cookies | Admin Panel 💼</p>
</footer>

<script>
// Scroll fade-in effect for cards and links
const faders = document.querySelectorAll('.welcome-banner, .card, .quick-links a');
const appearOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
const appearOnScroll = new IntersectionObserver(function(entries, observer){
    entries.forEach(entry=>{
        if(!entry.isIntersecting) return;
        entry.target.style.opacity = 1;
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
    });
}, appearOptions);

faders.forEach(fader => appearOnScroll.observe(fader));
</script>

</body>
</html>