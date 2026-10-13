<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/Cookie/config.php';

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
<title>Add Product</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">

<style>
body{font-family:Poppins;background:#fffafc;}
form{
max-width:420px;margin:60px auto;background:#fff;
padding:30px;border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,.1);
}
h2{text-align:center;color:#e86a92;}
input,select{
width:100%;padding:10px;margin-bottom:15px;
border-radius:6px;border:1px solid #ddd;
}
button{
background:#e86a92;color:#fff;border:none;
padding:12px;width:100%;
border-radius:8px;font-weight:600;
cursor:pointer;
}
button:hover{opacity:.9;}
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
</form>

</body>
</html>
