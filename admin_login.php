<?php
session_start();
$error = "";

// Dummy admin credentials for demo
$admin_email = "admin@cookies.lk";
$admin_password = "admin123"; // In real project: hash password

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if($email === $admin_email && $password === $admin_password){
        $_SESSION['user_type'] = 'admin';
        $_SESSION['user'] = ['name' => 'Admin', 'type'=>'admin'];
        header('location: admin_dashboard.php');
        exit;
    } else {
        $error = "Invalid email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | G🍪🍪dies Cookies</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
body{margin:0;padding:20px 15px;font-family:'Poppins',sans-serif;background:linear-gradient(135deg,#f4a7b9,#c6c9ea);display:flex;align-items:center;justify-content:center;min-height:100vh;box-sizing:border-box;}
.login-card{background:#fff;padding:35px 25px;border-radius:25px;box-shadow:0 10px 30px rgba(0,0,0,.1);width:100%;max-width:380px;text-align:center;opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;box-sizing:border-box;}
.login-card h2{color:var(--primary-pink-dark);margin-bottom:20px;}
.login-card input{width:100%;padding:14px;margin:10px 0;border-radius:12px;border:1px solid #ddd;font-size:15px;box-sizing:border-box;}
.login-card button{width:100%;padding:14px;background:var(--primary-pink);color:#fff;font-weight:600;border:none;border-radius:25px;cursor:pointer;margin-top:15px;transition:.3s;}
.login-card button:hover{background:var(--primary-pink-dark);transform:translateY(-3px);}
.error-msg{color:#d8000c;background:#ffd2d2;padding:10px;border-radius:12px;margin-bottom:10px;font-size:14px;}
@keyframes fadeIn{to{opacity:1;transform:translateY(0);}}
</style>
</head>
<body>

<div class="login-card">
<h2>Admin Login</h2>

<?php if($error): ?>
<div class="error-msg"><?= $error ?></div>
<?php endif; ?>

<form method="POST">
<input type="email" name="email" placeholder="Email" required>
<input type="password" name="password" placeholder="Password" required>
<button type="submit">Login</button>
</form>
</div>

</body>
</html>