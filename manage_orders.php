<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'].'/Cookie/config.php';

/* Admin check */
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'){
    header("Location: login.php");
    exit;
}

/* Update order status */
if(isset($_POST['update_status'])){
    $order_id = $_POST['order_id'];
    $status   = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
}

/* Fetch orders with product info */
$query = "
SELECT 
    orders.id,
    orders.user_id,
    COALESCE(GROUP_CONCAT(products.name SEPARATOR ', '), 'No products') AS product_names,
    SUM(order_items.quantity) AS quantity,
    orders.total_price,
    orders.status,
    orders.order_date
FROM orders
LEFT JOIN order_items ON orders.id = order_items.order_id
LEFT JOIN products ON order_items.id = products.id

GROUP BY orders.id, orders.user_id, orders.total_price, orders.status, orders.order_date
ORDER BY orders.order_date DESC
";

$result = $conn->query($query);
if(!$result){
    die("Query failed: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Orders | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
body{font-family:Poppins,sans-serif;background:#fffafc;margin:0;padding:0;}
.container{max-width:1200px;margin:40px auto;padding:20px;}
h2{text-align:center;color:#e86a92;margin-bottom:30px;}

table{
width:100%;border-collapse:collapse;background:#fff;
box-shadow:0 10px 30px rgba(0,0,0,.1);
border-radius:12px;overflow:hidden;
}
th,td{padding:14px;text-align:center;}
th{background:#f7b2c4;color:#fff;}
tr:nth-child(even){background:#fff0f6;}

select{padding:6px;border-radius:6px;}
button{
padding:6px 12px;border:none;border-radius:6px;
background:#e86a92;color:#fff;cursor:pointer;
}
button:hover{opacity:.9;}

.status-Pending{color:#f2994a;font-weight:600;}
.status-Completed{color:#27ae60;font-weight:600;}
.status-Cancelled{color:#eb5757;font-weight:600;}

.back-btn{
    display:inline-block;
    margin-top:20px;
    background:#e86a92;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;}
</style>
</head>
<body>

<div class="container">
<h2>📦 Manage Orders</h2>

<table>
<tr>
<th>Order ID</th>
<th>Products</th>
<th>Qty</th>
<th>Total (Rs)</th>
<th>Date</th>
<th>Status</th>
<th>Update</th>
</tr>

<?php if($result->num_rows > 0): ?>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['product_names'] ?></td>
        <td><?= $row['quantity'] ?? 0 ?></td>
        <td><?= number_format($row['total_price'],2) ?></td>
        <td><?= $row['order_date'] ?></td>
        <td class="status-<?= $row['status'] ?>"><?= $row['status'] ?></td>
        <td>
            <form method="post">
                <input type="hidden" name="order_id" value="<?= $row['id'] ?>">
                <select name="status">
                    <option value="Pending" <?= $row['status']=='Pending'?'selected':'' ?>>Pending</option>
                    <option value="Completed" <?= $row['status']=='Completed'?'selected':'' ?>>Completed</option>
                    <option value="Cancelled" <?= $row['status']=='Cancelled'?'selected':'' ?>>Cancelled</option>
                </select>
                <button name="update_status">Save</button>
            </form>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="8">No orders found</td>
</tr>
<?php endif; ?>
</table>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</body>
</html>



