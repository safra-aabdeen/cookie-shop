<?php
session_start();
require_once __DIR__ . '/config.php';

/* Admin check */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('location: admin_login.php');
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: manage_product.php");
    exit;
}

if (isset($_POST['update'])) {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $flavour  = trim($_POST['flavour'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);

    $stmt = $conn->prepare("UPDATE products SET name=?, category=?, flavour=?, price=? WHERE id=?");
    $stmt->bind_param("sssdi", $name, $category, $flavour, $price, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_product.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
    header("Location: manage_product.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:'Poppins', sans-serif;background:#fffafc;margin:0;padding:20px 15px;overflow-x:hidden;}
form{width:100%;max-width:440px;margin:30px auto;background:#fff;padding:30px 22px;
border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
h3{text-align:center;color:#e86a92;margin-top:0;font-size:22px;}
label{display:block;margin-bottom:6px;font-size:13px;font-weight:600;color:#555;}
input{width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:1px solid #ddd;font-family:'Poppins', sans-serif;font-size:14px;}
input:focus{outline:none;border-color:#e86a92;}
button{background:#e86a92;color:#fff;border:none;padding:13px;width:100%;
border-radius:8px;font-weight:600;font-family:'Poppins', sans-serif;cursor:pointer;font-size:15px;transition:.3s;}
button:hover{opacity:.9;}
.back-link{display:block;text-align:center;margin-top:15px;color:#e86a92;text-decoration:none;font-size:14px;font-weight:500;}
.back-link:hover{text-decoration:underline;}
</style>
</head>
<body>

<form method="post">
<h3>🍪 Edit Product</h3>

<label for="prod_id">Product ID</label>
<input type="text" id="prod_id" value="<?= format_id($product['id']); ?>" readonly style="background:#f5f5f5;color:#888;font-weight:600;cursor:not-allowed;">

<label for="name">Product Name</label>
<input type="text" id="name" name="name" value="<?= htmlspecialchars($product['name'] ?? ''); ?>" required>

<label for="category">Category</label>
<input type="text" id="category" name="category" value="<?= htmlspecialchars($product['category'] ?? ''); ?>" required>

<label for="flavour">Flavour</label>
<input type="text" id="flavour" name="flavour" value="<?= htmlspecialchars($product['flavour'] ?? ''); ?>" required>

<label for="price">Price (Rs)</label>
<input type="number" id="price" name="price" step="0.01" value="<?= htmlspecialchars($product['price'] ?? ''); ?>" required>

<button name="update">Update Product</button>
<a href="manage_product.php" class="back-link">← Back to Manage Products</a>
</form>

</body>
</html>
