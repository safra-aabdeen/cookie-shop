<?php
session_start();
require_once __DIR__ . '/config.php';

$order_success = false;

/* LOGIN STATE */
$is_logged_in = false;
$user_name = "";
$user_email = "";
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $is_logged_in = true;
    $user_name = $_SESSION['user']['name'] ?? "";
    $user_email = $_SESSION['user']['email'] ?? "";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_SESSION['cart'])) {

    $user_id = $_SESSION['user_id'] ?? 0;

    // Calculate total
    $grand_total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $grand_total += ($item['price'] * $item['quantity']);
    }

    // Insert order
    $stmt = $conn->prepare(
        "INSERT INTO orders (user_id, total_price, status, order_date)
         VALUES (?, ?, 'Pending', NOW())"
    );
    $stmt->bind_param("id", $user_id, $grand_total);
    $stmt->execute();

    // Get order ID
    $order_id = $conn->insert_id;

    // Insert order items
    $item_stmt = $conn->prepare(
        "INSERT INTO order_items (order_id, product_id, quantity, price)
         VALUES (?, ?, ?, ?)"
    );

    foreach ($_SESSION['cart'] as $item) {
        $prod_id = isset($item['id']) && (int)$item['id'] > 0 ? (int)$item['id'] : 0;
        
        // If product_id is not set or 0, find it in the products table by name
        if ($prod_id <= 0 && !empty($item['name'])) {
            $stmt_p = $conn->prepare("SELECT id FROM products WHERE name = ? LIMIT 1");
            $stmt_p->bind_param("s", $item['name']);
            $stmt_p->execute();
            $res_p = $stmt_p->get_result();
            if ($row_p = $res_p->fetch_assoc()) {
                $prod_id = (int)$row_p['id'];
            }
            $stmt_p->close();
        }

        // Fallback: If still not found, create a product record so it has an ID
        if ($prod_id <= 0 && !empty($item['name'])) {
            $stmt_ins = $conn->prepare("INSERT INTO products (name, category, flavour, price, image) VALUES (?, ?, ?, ?, ?)");
            $cat = $item['category'] ?? 'Classic';
            $flav = $item['flavour'] ?? 'Classic';
            $prc = (float)($item['price'] ?? 0);
            $img = $item['image'] ?? 'images/Biskat.jpg';
            $stmt_ins->bind_param("sssds", $item['name'], $cat, $flav, $prc, $img);
            $stmt_ins->execute();
            $prod_id = (int)$conn->insert_id;
            $stmt_ins->close();
        }

        $item_stmt->bind_param(
            "iiid",
            $order_id,
            $prod_id,
            $item['quantity'],
            $item['price']
        );
        $item_stmt->execute();
    }

    // Clear cart
    $_SESSION['cart'] = [];
    $_SESSION['cart_count'] = 0;

    $order_success = true; // ✅ fixed
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Checkout | G🍪🍪dies Cookies</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
.checkout-page{padding:80px 25px;background:#fffafc;min-height:80vh}
.checkout-title{text-align:center;margin-bottom:40px}
.checkout-title h2{font-size:36px;color:var(--primary-pink-dark)}

.cart-summary{max-width:900px;margin:auto;background:#fff;padding:25px;border-radius:18px;
box-shadow:0 6px 20px rgba(0,0,0,.08);margin-bottom:30px;overflow-x:auto;-webkit-overflow-scrolling:touch;}
.cart-summary table{width:100%;min-width:480px;border-collapse:collapse}
.cart-summary th, .cart-summary td{text-align:center;padding:12px;border-bottom:1px solid #eee}
.cart-summary th{background:var(--primary-pink-dark);color:#fff;font-weight:500;white-space:nowrap}
.cart-summary img{width:60px;height:60px;object-fit:cover;border-radius:8px}

.checkout-form{max-width:500px;margin:auto;background:#fff;padding:25px;border-radius:18px;
box-shadow:0 6px 20px rgba(0,0,0,.08);}
.checkout-form input, .checkout-form textarea{
width:100%;padding:12px;margin:8px 0;border-radius:8px;border:1px solid #ddd;font-family:'Poppins',sans-serif;
box-sizing:border-box;
}
.checkout-form button{
padding:12px 20px;background:var(--primary-pink);color:#fff;font-weight:600;
border-radius:25px;border:none;cursor:pointer;margin-top:12px;width:100%;
transition:.3s
}
.checkout-form button:hover{background:var(--primary-pink-dark)}

.success-msg{text-align:center;background:#d4edda;color:#155724;padding:15px;
border-radius:12px;margin-bottom:20px;box-shadow:0 4px 12px rgba(0,0,0,.1)}

/* Responsive adjustments */
@media (max-width: 768px) {
  .checkout-page{padding:40px 15px;}
  .checkout-title{margin-bottom:25px;}
  .checkout-title h2{font-size:28px;}
  .cart-summary{padding:15px;}
  .checkout-form{padding:20px 15px;}
}
</style>
</head>

<body>

<!-- HEADER -->
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
        🛒 <span class="badge"><?= (int)($_SESSION['cart_count'] ?? 0); ?></span>
    </a>
</div>
</header>

<section class="checkout-page">

<div class="checkout-title">
<h2>Checkout 🛒</h2>
<p>Review your order and fill in your details</p>
</div>

<?php if($order_success): ?>
<div class="success-msg">
<h3>✅ Your order has been placed successfully!</h3>
<p>Thank you for shopping with G🍪🍪dies Cookies 💕</p>
<a href="shop.php" style="color:var(--primary-pink-dark);font-weight:600;text-decoration:none">Back to Shop</a>
</div>
<?php endif; ?>

<?php if(count($_SESSION['cart'])>0): ?>

<div class="cart-summary">
<table>
<tr>
<th>#</th>
<th>Image</th>
<th>Name</th>
<th>Category | Flavour</th>
<th>Price (Rs.)</th>
<th>Quantity</th>
<th>Subtotal</th>
</tr>

<?php
$i=1;
$total=0;
foreach($_SESSION['cart'] as $item):
    $quantity = $item['quantity'] ?? 1;
    $price = $item['price'] ?? 0;
    $subtotal = $quantity * $price;
    $total += $subtotal;
?>
<tr>
<td><?= $i ?></td>
<td><img src="<?= $item['image'] ?? ''; ?>" alt="<?= $item['name'] ?? ''; ?>"></td>
<td><?= $item['name'] ?? 'Unknown'; ?></td>
<td><?= $item['category'] ?? '—'; ?> | <?= $item['flavour'] ?? '—'; ?></td>
<td><?= $price; ?></td>
<td><?= $quantity; ?></td>
<td><?= $subtotal; ?></td>
</tr>
<?php $i++; endforeach; ?>
<tr>
<td colspan="6" style="text-align:right;font-weight:600">Total</td>
<td style="font-weight:600;color:var(--primary-pink-dark)">Rs. <?= $total; ?></td>
</tr>
</table>
</div>

<div class="checkout-form">
<form method="POST">
<input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($user_name); ?>" required>
<input type="email" name="email" placeholder="Email Address" value="<?= htmlspecialchars($user_email); ?>" required>
<textarea name="address" placeholder="Delivery Address" rows="3" required></textarea>
<input type="text" name="contact" placeholder="Contact Number" required>
<button type="submit">Place Order</button>
</form>
</div>

<?php else: ?>
<p style="text-align:center;font-weight:600">Your cart is empty 🍪 <a href="shop.php" style="color:var(--primary-pink-dark)">Shop Now</a></p>
<?php endif; ?>

</section>

<footer>
<p>© <?= date('Y') ?> G🍪🍪dies Cookies | Made with Love 💕</p>
</footer>

</body>
</html>
