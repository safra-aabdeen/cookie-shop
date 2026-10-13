<?php
session_start();
require_once __DIR__ . '/config.php';

/* ADMIN CHECK */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header('location: admin_login.php');
    exit;
}

/* ADD USER */
if (isset($_POST['add'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $type  = $_POST['type'];

    mysqli_query($conn, "INSERT INTO users (name,email,password,type)
                         VALUES ('$name','$email','$pass','$type')");
    header("Location: manage_users.php");
    exit;
}

/* DELETE USER (NOT ADMIN) */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM users WHERE id='$id' AND type!='admin'");
    header("Location: manage_users.php");
    exit;
}

/* FETCH USERS */
$result = mysqli_query($conn, "SELECT id,name,email,type FROM users ORDER BY id DESC");
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
h2{
    text-align:center;
    color:#e86a92;
    margin-bottom:25px;
    font-size:26px;
}

/* ADD FORM */
.form-box{
    background:#fff;
    padding:20px;
    border-radius:12px;
    box-shadow:0 10px 30px rgba(0,0,0,.1);
    margin-bottom:25px;
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}
.form-box input, .form-box select{
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ccc;
    flex:1 1 200px;
    font-family:'Poppins',sans-serif;
}
.form-box button{
    background:#e86a92;
    color:#fff;
    border:none;
    border-radius:8px;
    padding:10px 20px;
    cursor:pointer;
    font-weight:600;
    transition:.3s;
}
.form-box button:hover{opacity:.9;}

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
}
tr:nth-child(even){
    background:#fff0f6;
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
}
.edit{background:#6fcf97;}
.delete{background:#eb5757;}

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
  .form-box{padding:15px;gap:8px;}
  .form-box input, .form-box select, .form-box button{flex:1 1 100%;width:100%;}
  th,td{padding:10px 8px;font-size:13px;}
}
</style>
</head>

<body>
<div class="container">

<h2>👥 Manage Users</h2>

<!-- ADD USER -->
<form method="post" class="form-box">
    <input type="text" name="name" placeholder="Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="type">
        <option value="user">User</option>
        <option value="admin">Admin</option>
    </select>

    <button name="add">Add User</button>
</form>

<!-- USERS TABLE -->
<div class="table-responsive">
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $row['id']; ?></td>
    <td><?= htmlspecialchars($row['name']); ?></td>
    <td><?= htmlspecialchars($row['email']); ?></td>
    <td><?= ucfirst($row['type']); ?></td>
    <td>
        <a href="edit_user.php?id=<?= $row['id']; ?>" class="btn edit">Edit</a>

        <?php if($row['type'] != 'admin'){ ?>
        <a href="manage_users.php?delete=<?= $row['id']; ?>"
           class="btn delete"
           onclick="return confirm('Delete this user?')">
           Delete
        </a>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>
</div>

<a href="admin_dashboard.php" class="back-btn">← Back to Dashboard</a>

</div>
</body>
</html>


