<?php
session_start();
require_once __DIR__ . '/config.php';

/* Admin check */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('location: admin_login.php');
    exit;
}

$next_id = peek_next_id('products', $conn);

if (isset($_POST['add_product'])) {
    $name     = trim($_POST['name'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $flavour  = trim($_POST['flavour'] ?? '');
    $price    = (float)($_POST['price'] ?? 0);

    /* Generate permanent unique ID that is never reused */
    $new_id = generate_next_id('products', $conn);

    /* Image upload */
    $imagePath = 'images/Biskat.jpg';
    if (!empty($_FILES['image']['name'])) {
        $imageName = $_FILES['image']['name'];
        $imageTmp  = $_FILES['image']['tmp_name'];
        $imagePath = "images/" . time() . "_" . preg_replace('/[^a-zA-Z0-9._-]/', '_', $imageName);
        move_uploaded_file($imageTmp, $imagePath);
    }

    $stmt = $conn->prepare("INSERT INTO products (id, name, category, flavour, price, image) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssds", $new_id, $name, $category, $flavour, $price, $imagePath);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_product.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:'Poppins', sans-serif;background:#fffafc;margin:0;padding:20px 15px;overflow-x:hidden;}
form{
width:100%;max-width:440px;margin:30px auto;background:#fff;
padding:30px 22px;border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.1);
}
h2{text-align:center;color:#e86a92;margin-top:0;font-size:24px;}
label{display:block;margin-bottom:6px;font-size:13px;font-weight:600;color:#555;}
input,select{
width:100%;padding:12px;margin-bottom:15px;
border-radius:8px;border:1px solid #ddd;font-family:'Poppins', sans-serif;
font-size:14px;
}
input:focus,select:focus{
outline:none;border-color:#e86a92;
}
button{
background:#e86a92;color:#fff;border:none;
padding:13px;width:100%;
border-radius:8px;font-weight:600;font-family:'Poppins', sans-serif;
cursor:pointer;font-size:15px;transition:.3s;
}
button:hover{opacity:.9;}
.back-link{display:block;text-align:center;margin-top:15px;color:#e86a92;text-decoration:none;font-size:14px;font-weight:500;}
.back-link:hover{text-decoration:underline;}
</style>
</head>

<body>

<form method="post" enctype="multipart/form-data">
<h2>🍪 Add New Product</h2>

<label for="prod_id">Product ID (Auto-generated)</label>
<input type="text" id="prod_id" value="<?= format_id($next_id); ?>" readonly style="background:#f5f5f5;color:#888;font-weight:600;cursor:not-allowed;">

<label for="name">Product Name</label>
<input type="text" id="name" name="name" placeholder="Product Name" required>

<label for="category">Category</label>
<select id="category" name="category" required>
<option value="">Select Category</option>
<option>Classic</option>
<option>Premium</option>
<option>Healthy</option>
</select>

<label for="flavour">Flavour</label>
<input type="text" id="flavour" name="flavour" placeholder="Flavour" required>

<label for="price">Price (Rs)</label>
<input type="number" id="price" name="price" step="0.01" placeholder="Price" required>

<label for="image">Product Image</label>
<input type="file" id="image" name="image" required>

<button name="add_product">Add Product</button>
<a href="manage_product.php" class="back-link">← Back to Manage Products</a>
</form>

</body>
</html>
