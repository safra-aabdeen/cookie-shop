<?php
session_start();
require_once __DIR__ . '/config.php';

/* ADMIN CHECK */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: admin_login.php');
    exit;
}

/* DELETE USER (ONLY REGISTERED WEBSITE USERS, NEVER ADMIN) */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND type = 'user'");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php?msg=deleted");
    exit;
}

/* FETCH ONLY REGISTERED WEBSITE USERS (ADMIN DETAILS EXCLUDED) */
$query = "SELECT id, name, email, type FROM users WHERE type = 'user' ORDER BY id DESC";
$result = mysqli_query($conn, $query);
$user_count = $result ? mysqli_num_rows($result) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage Users | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; }
body{
    font-family:'Poppins',sans-serif;
    background:#fffafc;
    margin:0;
    overflow-x:hidden;
}
.container{
    max-width:1100px;
    margin:30px auto;
    padding:15px;
}
.header-box{
    text-align:center;
    margin-bottom:25px;
}
h2{
    color:#e86a92;
    margin:0 0 8px 0;
    font-size:26px;
}
.subtitle{
    color:#777;
    margin:0;
    font-size:14px;
}
.msg{
    background:#d4edda;
    color:#155724;
    border:1px solid #c3e6cb;
    padding:12px 20px;
    border-radius:10px;
    margin-bottom:20px;
    text-align:center;
    font-size:14px;
}

/* TABLE */
.table-responsive{
    width:100%;
    overflow-x:auto;
    -webkit-overflow-scrolling:touch;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
    background:#fff;
    margin-bottom:20px;
}
table{
    width:100%;
    min-width:600px;
    border-collapse:collapse;
    background:#fff;
}
th,td{
    padding:14px;
    text-align:center;
}
th{
    background:#f7b2c4;
    color:#fff;
    white-space:nowrap;
    font-weight:600;
}
tr:nth-child(even){
    background:#fff0f6;
}
.role-badge{
    background:#eef2ff;
    color:#4f46e5;
    padding:4px 12px;
    border-radius:12px;
    font-size:13px;
    font-weight:500;
    display:inline-block;
}

/* BUTTONS */
.btn{
    padding:6px 14px;
    border-radius:6px;
    text-decoration:none;
    font-size:14px;
    color:#fff;
    display:inline-block;
    margin:2px;
    transition:.2s;
}
.edit{background:#6fcf97;}
.edit:hover{background:#58b880;}
.delete{background:#eb5757;}
.delete:hover{background:#d43f3f;}

.back-btn{
    display:inline-block;
    margin-top:10px;
    background:#e86a92;
    color:#fff;
    padding:10px 18px;
    border-radius:8px;
    text-decoration:none;
    transition:.3s;
    font-weight:500;
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

<div class="header-box">
    <h2>👥 Manage Users</h2>
    <p class="subtitle">Registered Website Customers (Total: <?= $user_count; ?>)</p>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'deleted'): ?>
    <div class="msg">✅ User deleted successfully.</div>
<?php elseif (isset($_GET['msg']) && $_GET['msg'] === 'updated'): ?>
    <div class="msg">✅ User details updated successfully.</div>
<?php endif; ?>

<!-- USERS TABLE -->
<div class="table-responsive">
<table>
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Actions</th>
</tr>
</thead>
<tbody>
<?php if ($result && mysqli_num_rows($result) > 0): ?>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><strong><?= format_id($row['id']); ?></strong></td>
        <td><?= htmlspecialchars($row['name']); ?></td>
        <td><?= htmlspecialchars($row['email']); ?></td>
        <td><span class="role-badge"><?= ucfirst(htmlspecialchars($row['type'])); ?></span></td>
        <td>
            <a href="edit_user.php?id=<?= (int)$row['id']; ?>" class="btn edit">Edit</a>
            <a href="manage_users.php?delete=<?= (int)$row['id']; ?>"
               class="btn delete"
               onclick="return confirm('Are you sure you want to delete this user?')">
               Delete
            </a>
        </td>
    </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="5" style="padding:30px;color:#888;font-size:15px;">
            No registered users found yet. Users who sign up on the website will be displayed here automatically. 🍪
        </td>
    </tr>
<?php endif; ?>
</tbody>
</table>
</div>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</body>
</html>
