<?php
require_once __DIR__ . '/config.php';

$id = $_GET['id'];

if(isset($_POST['update'])){
    $name = $_POST['name'];
    $category = $_POST['category'];
    $flavour = $_POST['flavour'];
    $price = $_POST['price'];

    mysqli_query($conn,
    "UPDATE products SET 
    name='$name',
    category='$category',
    flavour='$flavour',
    price='$price'
    WHERE id='$id'");

    header("location: manage_product.php");
    exit;
}

$product = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id='$id'"));
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Edit Product</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:Poppins, sans-serif;background:#fffafc;margin:0;padding:20px 15px;overflow-x:hidden;}
form{width:100%;max-width:440px;margin:30px auto;background:#fff;padding:30px 22px;
border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
h3{text-align:center;color:#e86a92;margin-top:0;font-size:22px;}
input{width:100%;padding:12px;margin-bottom:15px;border-radius:8px;border:1px solid #ddd;font-family:Poppins, sans-serif;font-size:14px;}
button{background:#e86a92;color:#fff;border:none;padding:13px;width:100%;
border-radius:8px;font-weight:600;font-family:Poppins, sans-serif;cursor:pointer;font-size:15px;transition:.3s;}
button:hover{opacity:.9;}
.back-link{display:block;text-align:center;margin-top:15px;color:#e86a92;text-decoration:none;font-size:14px;font-weight:500;}
.back-link:hover{text-decoration:underline;}
</style>
</head>
<body>

<form method="post">
<h3>🍪 Edit Product</h3>
<input type="text" name="name" value="<?= htmlspecialchars($product['name'] ?? ''); ?>" required>
<input type="text" name="category" value="<?= htmlspecialchars($product['category'] ?? ''); ?>" required>
<input type="text" name="flavour" value="<?= htmlspecialchars($product['flavour'] ?? ''); ?>" required>
<input type="number" name="price" value="<?= htmlspecialchars($product['price'] ?? ''); ?>" required>
<button name="update">Update Product</button>
<a href="manage_product.php" class="back-link">← Back to Manage Products</a>
</form>

</body>
</html>
