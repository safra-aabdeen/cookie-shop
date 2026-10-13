<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/Cookie/config.php';

/* ======================
   Admin Authentication
====================== */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: login.php");
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
<title>Admin | Manage Messages</title>

<style>
body{
    font-family: Arial, sans-serif;
    background:#fffafc;
    padding:30px;
    max-width:1000px;
    margin:auto;
}
h2{
    text-align:center;
    color:#e86a92;
    margin-bottom:25px;
}
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 10px 25px rgba(0,0,0,.1);
}
th, td{
    padding:15px;
    border-bottom:1px solid #eee;
    vertical-align:top;
}
th{
    background:#f7b2c4;
    color:#fff;
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
    margin-top:20px;
    background:#e86a92;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
}
.logout{
    float:right;
    margin-top:-40px;
}
.logout a{
    color:#e86a92;
    font-weight:bold;
    text-decoration:none;
}
.logout a:hover{
    text-decoration:underline;
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
    <td><?= htmlspecialchars($row['id']) ?></td>
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



<?php else: ?>
<p style="text-align:center;font-weight:bold;">No messages found.</p>
<?php endif; ?>
<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>
</body>
</html>
