<?php
session_start();

/* INITIALIZE CART */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

/* REMOVE ITEM LOGIC */
if (isset($_GET['remove'])) {
    $removeIndex = intval($_GET['remove']);
    if (isset($_SESSION['cart'][$removeIndex])) {
        unset($_SESSION['cart'][$removeIndex]);
        $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
        $_SESSION['cart_count'] = count($_SESSION['cart']);
    }
    header("Location: cart.php");
    exit;
}

/* CALCULATE TOTAL */
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Cart | G🍪🍪dies Cookies</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
.cart-page{padding:80px 25px;background:#fffafc;min-height:80vh}
.cart-title{text-align:center;margin-bottom:40px}
.cart-title h2{font-size:36px;color:var(--primary-pink-dark)}

.cart-table{max-width:1000px;margin:auto;background:#fff;
border-radius:15px;overflow:hidden;overflow-x:auto;-webkit-overflow-scrolling:touch;box-shadow:0 6px 20px rgba(0,0,0,.08);}
.cart-table table{width:100%;min-width:600px;border-collapse:collapse}
.cart-table th, .cart-table td{text-align:center;padding:15px;border-bottom:1px solid #eee}
.cart-table th{background:var(--primary-pink-dark);color:#fff;font-weight:500;white-space:nowrap}
.cart-table img{width:70px;height:70px;object-fit:cover;border-radius:8px}

.remove-btn{
padding:6px 12px;background:#ff6b6b;color:#fff;
border-radius:8px;text-decoration:none;font-weight:600;
display:inline-block;transition:.3s;
}
.remove-btn:hover{opacity:.9;}

.total-section{text-align:right;max-width:1000px;margin:20px auto;font-size:20px;font-weight:600;color:var(--accent-cookie)}

.checkout-btn{
display:block;width:200px;margin:25px auto;padding:12px 0;
text-align:center;background:var(--primary-pink);
color:#fff;font-weight:600;border-radius:25px;text-decoration:none;
transition:.3s
}
.checkout-btn:hover{background:var(--primary-pink-dark)}

/* Responsive adjustments */
@media (max-width: 768px) {
  .cart-page{padding:40px 15px;}
  .cart-title{margin-bottom:25px;}
  .cart-title h2{font-size:28px;}
  .total-section{text-align:center;font-size:18px;margin:15px auto;}
  .checkout-btn{width:100%;max-width:280px;padding:14px 20px;}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
<div class="logo">G🍪🍪dies Cookies</div>
<nav class="navbar">
<a href="index.php">Home</a>
<a href="shop.php">Shop</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
</nav>
<a href="cart.php" class="cart-btn">
🛒 <span class="badge"><?= $_SESSION['cart_count']; ?></span>
</a>
</header>

<section class="cart-page">
<div class="cart-title">
<h2>Your Cart 🍪🛒</h2>
</div>

<div class="cart-table">
<table>
<tr>
<th>#</th>
<th>Image</th>
<th>Name</th>
<th>Category | Flavour</th>
<th>Price (Rs.)</th>
<th>Quantity</th>
<th>Action</th>
</tr>

<?php if(count($_SESSION['cart'])>0):
foreach($_SESSION['cart'] as $index=>$item): ?>
<tr>
<td><?= $index+1 ?></td>
<td><img src="<?= $item['image']; ?>" alt="<?= $item['name']; ?>"></td>
<td><?= $item['name']; ?></td>
<td><?= $item['category'] ?? '—'; ?> | <?= $item['flavour'] ?? '—'; ?></td>
<td><?= $item['price']; ?></td>
<td><?= $item['quantity']; ?></td>
<td>
<a class="remove-btn" href="cart.php?remove=<?= $index ?>">Remove</a>
</td>
</tr>
<?php endforeach; else: ?>
<tr>
<td colspan="7" style="text-align:center;font-weight:600;padding:20px">Your cart is empty 🍪</td>
</tr>
<?php endif; ?>

</table>
</div>

<?php if(count($_SESSION['cart'])>0): ?>
<div class="total-section">Total: Rs. <?= $total; ?></div>
<a href="checkout.php" class="checkout-btn">Proceed to Checkout</a>
<?php endif; ?>

</section>

<footer>
<p>© <?= date('Y') ?> G🍪🍪dies Cookies | Made with Love 💕</p>
</footer>

</body>
</html>