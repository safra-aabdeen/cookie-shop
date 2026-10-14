<?php
session_start();
require_once __DIR__ . '/config.php';

/* Admin check */
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] != 'admin'){
    header('location: admin_login.php');
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Products | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:'Poppins',sans-serif;background:#fffafc;margin:0;padding:0;overflow-x:hidden;}
.container{max-width:1200px;margin:30px auto;padding:15px;}
h2{text-align:center;color:#e86a92;margin-bottom:25px;font-size:26px;}

.table-responsive{
width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;
border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1);background:#fff;margin-bottom:20px;
}
table{width:100%;min-width:650px;border-collapse:collapse;background:#fff;}

th,td{padding:14px;text-align:center;}
th{background:#f7b2c4;color:#fff;font-weight:500;white-space:nowrap;}
tr:nth-child(even){background:#fff0f6;}

img{width:60px;height:60px;object-fit:cover;border-radius:10px;}

.btn{padding:6px 14px;border-radius:6px;text-decoration:none;font-size:14px;display:inline-block;margin:2px;}
.edit{background:#6fcf97;color:#fff;}
.delete{background:#eb5757;color:#fff;}

.add-btn{
display:inline-block;margin-bottom:15px;
background:#e86a92;color:#fff;
padding:10px 18px;border-radius:8px;
text-decoration:none;font-weight:500;transition:.3s;
}
.add-btn:hover{opacity:.9;}

@media (max-width: 600px) {
  .container{padding:10px;}
  h2{font-size:22px;}
  th,td{padding:10px 8px;font-size:13px;}
}
</style>
</head>

<body>

<div class="container">
<h2>🍪 Manage Products</h2>

<a href="add_product.php" class="add-btn">+ Add New Product</a>

<div class="table-responsive">
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

<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)){ ?>
    <tr>
    <td><strong><?= format_id($row['id']); ?></strong></td>
    <td><img src="<?= htmlspecialchars($row['image']); ?>" alt="<?= htmlspecialchars($row['name']); ?>"></td>
    <td><?= htmlspecialchars($row['name']); ?></td>
    <td><?= htmlspecialchars($row['category']); ?></td>
    <td><?= htmlspecialchars($row['flavour']); ?></td>
    <td><?= number_format($row['price'],2); ?></td>
    <td>
    <a href="edit_product.php?id=<?= (int)$row['id']; ?>" class="btn edit">Edit</a>
    <a href="delete_product.php?id=<?= (int)$row['id']; ?>" 
       class="btn delete"
       onclick="return confirm('Are you sure you want to delete this product?')">
    Delete</a>
    </td>
    </tr>
    <?php } ?>
<?php else: ?>
    <tr>
    <td colspan="7" style="padding:25px;color:#888;">No products found. Click "+ Add New Product" to add one.</td>
    </tr>
<?php endif; ?>

</table>
</div>

<a href="admin_dashboard.php" class="add-btn"> ←Back to Dashboard</a>

</div>

</body>
</html>
