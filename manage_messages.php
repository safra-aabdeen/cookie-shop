<?php
session_start();
require_once __DIR__ . '/config.php';

/* ======================
   Admin Authentication
====================== */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

/* ======================
   Delete Message (Secure)
====================== */
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];

    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_messages.php");
    exit;
}

/* ======================
   Fetch Messages
====================== */
$result = $conn->query(
    "SELECT id, name, email, message, submitted_at 
     FROM contact_messages 
     ORDER BY submitted_at DESC"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin | Manage Messages</title>

<style>
*, *::before, *::after { box-sizing: border-box; }
body{
    font-family: Arial, sans-serif;
    background:#fffafc;
    padding:25px 15px;
    max-width:1000px;
    margin:auto;
    overflow-x:hidden;
}
h2{
    text-align:center;
    color:#e86a92;
    margin-bottom:25px;
    font-size:24px;
}
.table-responsive{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
    background:#fff;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,.1);
    margin-bottom:20px;
}
table{
    width:100%;
    min-width:650px;
    border-collapse:collapse;
    background:#fff;
}
th, td{
    padding:14px;
    border-bottom:1px solid #eee;
    vertical-align:top;
}
th{
    background:#f7b2c4;
    color:#fff;
    white-space:nowrap;
}
tr:hover{
    background:#fff0f6;
}
.delete-btn{
    color:#eb5757;
    text-decoration:none;
    font-weight:bold;
}
.delete-btn:hover{
    text-decoration:underline;
}
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
  body{padding:15px 10px;}
  h2{font-size:20px;}
  th, td{padding:10px 8px;font-size:13px;}
}
</style>

<script>
function confirmDelete(){
    return confirm("Are you sure you want to delete this message?");
}
</script>
</head>

<body>


<h2>📬 Manage Contact Messages</h2>

<?php if ($result && $result->num_rows > 0): ?>
<div class="table-responsive">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Message</th>
    <th>Date</th>
    <th>Action</th>
</tr>
</thead>
<tbody>
<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><strong><?= format_id($row['id']) ?></strong></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td><?= htmlspecialchars($row['email']) ?></td>
    <td>
        <?= nl2br(htmlspecialchars($row['message'])) ?>
    </td>
    <td><?= htmlspecialchars($row['submitted_at']) ?></td>
    <td>
        <a class="delete-btn"
           href="manage_messages.php?delete_id=<?= $row['id'] ?>"
           onclick="return confirmDelete()">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</div>



<?php else: ?>
<p style="text-align:center;font-weight:bold;">No messages found.</p>
<?php endif; ?>
<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</body>
</html>
