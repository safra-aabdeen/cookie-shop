<?php
session_start();
require_once __DIR__ . '/config.php';

if(isset($_POST['add_product'])){

    $name     = $_POST['name'];
    $category = $_POST['category'];
    $flavour  = $_POST['flavour'];
    $price    = $_POST['price'];

    /* Image upload */
    $imageName = $_FILES['image']['name'];
    $imageTmp  = $_FILES['image']['tmp_name'];
    $imagePath = "images/" . time() . "_" . $imageName;

    move_uploaded_file($imageTmp, $imagePath);

    $query = "INSERT INTO products (name, category, flavour, price, image)
              VALUES ('$name','$category','$flavour','$price','$imagePath')";

    mysqli_query($conn, $query);

    header("Location: manage_product.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Product</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:Poppins, sans-serif;background:#fffafc;margin:0;padding:20px 15px;overflow-x:hidden;}
form{
width:100%;max-width:440px;margin:30px auto;background:#fff;
padding:30px 22px;border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.1);
}
h2{text-align:center;color:#e86a92;margin-top:0;font-size:24px;}
input,select{
width:100%;padding:12px;margin-bottom:15px;
border-radius:8px;border:1px solid #ddd;font-family:Poppins, sans-serif;
font-size:14px;
}
button{
background:#e86a92;color:#fff;border:none;
padding:13px;width:100%;
border-radius:8px;font-weight:600;font-family:Poppins, sans-serif;
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

<input type="text" name="name" placeholder="Product Name" required>

<select name="category" required>
<option value="">Select Category</option>
<option>Classic</option>
<option>Premium</option>
<option>Healthy</option>
</select>

<input type="text" name="flavour" placeholder="Flavour" required>

<input type="number" name="price" placeholder="Price" required>

<input type="file" name="image" required>

<button name="add_product">Add Product</button>
<a href="manage_product.php" class="back-link">← Back to Manage Products</a>
</form>

</body>
</html>
