<?php
session_start();
require 'config.php';

/* Admin check */
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
    header('location: login.php');
    exit;
}

/* Fetch products */
$query = "SELECT * FROM products ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Products | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
body{font-family:'Poppins',sans-serif;background:#fffafc;margin:0;padding:0;}
.container{max-width:1200px;margin:40px auto;padding:20px;}
h2{text-align:center;color:#e86a92;margin-bottom:30px;}

table{width:100%;border-collapse:collapse;background:#fff;
box-shadow:0 10px 30px rgba(0,0,0,.1);border-radius:12px;overflow:hidden;}

th,td{padding:14px;text-align:center;}
th{background:#f7b2c4;color:#fff;font-weight:500;}
tr:nth-child(even){background:#fff0f6;}

img{width:70px;border-radius:10px;}

.btn{padding:6px 14px;border-radius:6px;text-decoration:none;font-size:14px;}
.edit{background:#6fcf97;color:#fff;}
.delete{background:#eb5757;color:#fff;}

.add-btn{
display:inline-block;margin-bottom:20px;
background:#e86a92;color:#fff;
padding:10px 18px;border-radius:8px;
text-decoration:none;font-weight:500;
}
.add-btn:hover{opacity:.9;}
</style>
</head>

<body>

<div class="container">
<h2>🍪 Manage Products</h2>

<a href="add_product.php" class="add-btn">+ Add New Product</a>

<table>
<tr>
<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Category</th>
<th>Flavour</th>
<th>Price (Rs)</th>
<th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?= $row['id']; ?></td>
<td><img src="<?= $row['image']; ?>"></td>
<td><?= $row['name']; ?></td>
<td><?= $row['category']; ?></td>
<td><?= $row['flavour']; ?></td>
<td><?= number_format($row['price'],2); ?></td>
<td>
<a href="edit_product.php?id=<?= $row['id']; ?>" class="btn edit">Edit</a>
<a href="delete_product.php?id=<?= $row['id']; ?>" 
   class="btn delete"
   onclick="return confirm('Are you sure you want to delete this product?')">
Delete</a>
</td>
</tr>
<?php } ?>

</table><br>
<br>

<a href="admin_dashboard.php" class="add-btn"> ←Back to Dashboard</a>

</div>

</body>
</html>
