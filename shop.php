<?php
session_start();
require_once __DIR__ . '/config.php';

/* ---------------- CART INITIALIZATION ---------------- */
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

/* ---------------- ADD TO CART LOGIC ---------------- */
if (isset($_GET['add_to_cart'])) {

    $item = [
        'id'       => isset($_GET['id']) ? (int)$_GET['id'] : 0,
        'name'     => $_GET['name'] ?? '',
        'category' => $_GET['category'] ?? '',
        'flavour'  => $_GET['flavour'] ?? '',
        'price'    => (float)($_GET['price'] ?? 0),
        'image'    => $_GET['image'] ?? '',
        'quantity' => 1
    ];

    $_SESSION['cart'][] = $item;
    $_SESSION['cart_count'] = count($_SESSION['cart']);

    header("Location: shop.php");
    exit;
}

/* ---------------- FILTER VALUES ---------------- */
$category = $_GET['category'] ?? '';
$flavour  = $_GET['flavour'] ?? '';

/* ---------------- PRODUCTS ---------------- */
$products = [

    // ===== CLASSIC =====
    [
        'id'       => 7,
        'name'     => 'Chocolate Chip Cookie',
        'category' => 'Classic',
        'flavour'  => 'Chocolate',
        'price'    => 650,
        'images'   => [
            'images/Classic chocolate.jpg',
            'images/Classic chocolate1.jpg',
            'images/Classic chocolate2.jpg'
        ]
    ],
    [
        'id'       => 2,
        'name'     => 'Vanilla Butter Cookie',
        'category' => 'Classic',
        'flavour'  => 'Vanilla',
        'price'    => 550,
        'images'   => [
            'images/Classic vanilla.jpg',
            'images/Classic vanilla2.jpg'
        ]
    ],

    // ===== PREMIUM =====
    [
        'id'       => 3,
        'name'     => 'Strawberry Cream Cookie',
        'category' => 'Premium',
        'flavour'  => 'Strawberry',
        'price'    => 720,
        'images'   => [
            'images/Premium strawberry.jpg',
            'images/Premium strawberry1.jpg'
        ]
    ],
    [
        'id'       => 4,
        'name'     => 'Double Chocolate Deluxe',
        'category' => 'Premium',
        'flavour'  => 'Chocolate',
        'price'    => 780,
        'images'   => [
            'images/Premium chocolate.jpg',
            'images/Premium chocolate1.jpg',
            'images/Premium chocolate2.jpg'
        ]
    ],

    // ===== HEALTHY =====
    [
        'id'       => 5,
        'name'     => 'Oatmeal Healthy Cookie',
        'category' => 'Healthy',
        'flavour'  => 'Oats',
        'price'    => 600,
        'images'   => [
            'images/Premium oats.jpg',
            'images/Premium oats2.jpg',
            'images/Premium oats3.jpg'
        ]
    ],
    [
        'id'       => 6,
        'name'     => 'Honey Almond Cookie',
        'category' => 'Healthy',
        'flavour'  => 'Almond',
        'price'    => 650,
        'images'   => [
            'images/Healthty almond1.jpg',
            'images/Healthty almond2.jpg'
        ]
    ],
];

// Synchronize with database products (including any newly added products from admin)
if (isset($conn) && !$conn->connect_error) {
    $dbQuery = @mysqli_query($conn, "SELECT * FROM products ORDER BY id ASC");
    if ($dbQuery) {
        $existingNames = array_map('strtolower', array_column($products, 'name'));
        while ($row = mysqli_fetch_assoc($dbQuery)) {
            $nameLower = strtolower($row['name']);
            $key = array_search($nameLower, $existingNames);
            if ($key !== false) {
                $products[$key]['id'] = (int)$row['id'];
            } else {
                $products[] = [
                    'id'       => (int)$row['id'],
                    'name'     => $row['name'],
                    'category' => $row['category'],
                    'flavour'  => $row['flavour'],
                    'price'    => (float)$row['price'],
                    'images'   => [!empty($row['image']) ? $row['image'] : 'images/Biskat.jpg']
                ];
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Shop Cookies | G🍪🍪dies Cookies</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
.shop-page{padding:80px 25px;background:#fffafc}
.shop-title{text-align:center;margin-bottom:40px}
.shop-title h2{font-size:36px;color:var(--primary-pink-dark)}

.filter-box{
max-width:1100px;margin:auto auto 50px;background:#fff;
padding:25px;border-radius:18px;
box-shadow:0 6px 18px rgba(0,0,0,.08);
display:flex;gap:20px;justify-content:center
}
.filter-box select,.filter-box button{
padding:12px 18px;border-radius:25px;border:1px solid #ddd;
font-family:'Poppins',sans-serif
}
.filter-box button{
background:var(--primary-pink);color:#fff;font-weight:600;cursor:pointer
}

.product-grid{
display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));
gap:35px;max-width:1200px;margin:auto
}

.product-card{
background:#fff;border-radius:22px;overflow:hidden;
box-shadow:0 6px 20px rgba(0,0,0,.08);transition:.4s
}
.product-card:hover{transform:translateY(-10px)}

.main-img img{width:100%;height:220px;object-fit:cover}

.thumb-images{
display:flex;justify-content:center;gap:8px;padding:10px
}
.thumb-images img{
width:55px;height:55px;object-fit:cover;border-radius:8px;border:2px solid #eee
}

.product-info{text-align:center;padding:20px}
.product-info h3{color:var(--accent-cookie)}
.product-info small{display:block;margin:6px 0;color:#777}

.price{
font-size:18px;font-weight:600;color:var(--primary-pink-dark);margin:10px 0
}

.add-cart-btn{
display:inline-block;padding:12px 26px;background:var(--primary-pink);color:#fff;
border-radius:25px;text-decoration:none;font-weight:600;transition:.3s;
}
.add-cart-btn:hover{background:var(--primary-pink-dark);transform:translateY(-2px);}

/* Responsive adjustments for shop */
@media (max-width: 768px) {
  .shop-page{padding:40px 15px;}
  .shop-title{margin-bottom:25px;}
  .shop-title h2{font-size:28px;}
  .filter-box{flex-wrap:wrap;padding:18px;gap:12px;margin-bottom:35px;}
  .filter-box select, .filter-box button{flex:1 1 200px;width:100%;}
  .product-grid{gap:25px;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));}
}

@media (max-width: 500px) {
  .product-grid{grid-template-columns:1fr;}
  .filter-box{flex-direction:column;}
  .filter-box select, .filter-box button{width:100%;}
  .main-img img{height:200px;}
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
<a href="cart.php" class="cart-btn">
🛒 <span class="badge"><?= $_SESSION['cart_count']; ?></span>
</a>
</header>

<section class="shop-page">

<div class="shop-title">
<h2>Shop Our Cookies</h2>
<p>Select category & flavour 🍪💕</p>
</div>

<!-- FILTER FORM -->
<form method="GET" class="filter-box">
<select name="category">
<option value="">Select Category</option>
<option value="Classic" <?= $category=='Classic'?'selected':'' ?>>Classic</option>
<option value="Premium" <?= $category=='Premium'?'selected':'' ?>>Premium</option>
<option value="Healthy" <?= $category=='Healthy'?'selected':'' ?>>Healthy</option>
</select>

<select name="flavour">
<option value="">Select Flavour</option>
<option value="Chocolate">Chocolate</option>
<option value="Vanilla">Vanilla</option>
<option value="Strawberry">Strawberry</option>
<option value="Oats">Oats</option>
<option value="Almond">Almond</option>
</select>

<button type="submit">Filter</button>
</form>

<!-- PRODUCTS -->
<div class="product-grid">

<?php
$found=false;
foreach($products as $p){

if($category && $p['category']!=$category) continue;
if($flavour && $p['flavour']!=$flavour) continue;
$found=true;
?>

<div class="product-card">
<div class="main-img">
<img src="<?= $p['images'][0]; ?>">
</div>

<div class="thumb-images">
<?php foreach($p['images'] as $img){ ?>
<img src="<?= $img; ?>">
<?php } ?>
</div>

<div class="product-info">
<h3><?= $p['name']; ?></h3>
<small><?= $p['category']; ?> | <?= $p['flavour']; ?></small>
<div class="price">Rs. <?= $p['price']; ?></div>

<a class="add-cart-btn"
href="shop.php?add_to_cart=1
&id=<?= $p['id'] ?? 0; ?>
&name=<?= urlencode($p['name']); ?>
&category=<?= urlencode($p['category']); ?>
&flavour=<?= urlencode($p['flavour']); ?>
&price=<?= $p['price']; ?>
&image=<?= urlencode($p['images'][0]); ?>">
Add to Cart
</a>

</div>
</div>

<?php }
if(!$found){
echo "<p style='text-align:center;font-weight:600'>No cookies found 🍪</p>";
}
?>

</div>
</section>

<footer>
<p>©  SHAZ Cookies | Made with Love 💕</p>
</footer>

</body>
</html>