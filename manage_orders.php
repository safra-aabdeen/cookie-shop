<?php
session_start();
require_once __DIR__ . '/config.php';

/* Admin check */
if(!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin'){
    header("Location: admin_login.php");
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

// Auto-heal legacy order items that have missing product_id
@$conn->query("
    UPDATE order_items oi
    JOIN products p ON oi.price = p.price
    SET oi.product_id = p.id
    WHERE (oi.product_id IS NULL OR oi.product_id = 0)
");

/* Fetch orders with product info */
$query = "
SELECT 
    orders.id,
    orders.user_id,
    COALESCE(NULLIF(GROUP_CONCAT(DISTINCT products.name SEPARATOR ', '), ''), 'No products') AS product_names,
    COALESCE(SUM(order_items.quantity), 0) AS quantity,
    orders.total_price,
    orders.status,
    orders.order_date
FROM orders
LEFT JOIN order_items ON orders.id = order_items.order_id
LEFT JOIN products ON (
    order_items.product_id = products.id
    OR (
        (order_items.product_id IS NULL OR order_items.product_id = 0)
        AND order_items.price = products.price
    )
)

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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Orders | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; }
body{font-family:Poppins,sans-serif;background:#fffafc;margin:0;padding:0;overflow-x:hidden;}
.container{max-width:1200px;margin:30px auto;padding:15px;}
h2{text-align:center;color:#e86a92;margin-bottom:25px;font-size:26px;}

.table-responsive{
width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch;
border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1);background:#fff;margin-bottom:20px;
}
table{
width:100%;min-width:650px;border-collapse:collapse;background:#fff;
}
th,td{padding:14px;text-align:center;}
th{background:#f7b2c4;color:#fff;white-space:nowrap;}
tr:nth-child(even){background:#fff0f6;}

select{padding:6px;border-radius:6px;border:1px solid #ddd;}
button{
padding:6px 12px;border:none;border-radius:6px;
background:#e86a92;color:#fff;cursor:pointer;transition:.3s;
}
button:hover{opacity:.9;}

.status-Pending{color:#f2994a;font-weight:600;}
.status-Completed{color:#27ae60;font-weight:600;}
.status-Cancelled{color:#eb5757;font-weight:600;}

.back-btn{
    display:inline-block;
    margin-top:10px;
    background:#e86a92;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    transition:.3s;
}
.back-btn:hover{opacity:.9;}

@media (max-width: 600px) {
  .container{padding:10px;}
  h2{font-size:22px;}
  th,td{padding:10px 8px;font-size:13px;}
}
</style>
</head>
<body>

<div class="container">
<h2>📦 Manage Orders</h2>

<div class="table-responsive">
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
</div>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</body>
</html>



