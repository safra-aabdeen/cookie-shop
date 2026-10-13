<?php
require 'config.php';

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
<title>Edit Product</title>
<style>
body{font-family:Poppins;background:#fffafc;}
form{max-width:400px;margin:60px auto;background:#fff;padding:30px;
border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1);}
input{width:100%;padding:10px;margin-bottom:15px;}
button{background:#e86a92;color:#fff;border:none;padding:10px;width:100%;
border-radius:8px;font-weight:500;}
</style>
</head>
<body>

<form method="post">
<h3>Edit Product</h3>
<input type="text" name="name" value="<?= $product['name']; ?>" required>
<input type="text" name="category" value="<?= $product['category']; ?>" required>
<input type="text" name="flavour" value="<?= $product['flavour']; ?>" required>
<input type="number" name="price" value="<?= $product['price']; ?>" required>
<button name="update">Update Product</button>
</form>

</body>
</html>
