<?php
session_start();
require_once __DIR__ . '/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_type'])) {
    if ($_SESSION['user_type'] === 'admin') {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        header("Location: index.php");
        exit;
    }
}

$error = '';
$success = '';
$email = $_GET['email'] ?? '';

if (isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success = "🎉 Account created successfully! Please log in to continue.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $error = "Please enter both email and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, type FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            // Verify password using password_verify or legacy plain text
            $is_valid_pw = password_verify($password, $user['password']) || ($password === $user['password']);

            if ($is_valid_pw) {
                // Set session variables
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_type'] = $user['type'];
                $_SESSION['user'] = [
                    'id'    => (int)$user['id'],
                    'name'  => $user['name'],
                    'email' => $user['email'],
                    'type'  => $user['type']
                ];

                $stmt->close();

                // If admin, go to dashboard; else go to checkout if cart has items, or shop
                if ($user['type'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } elseif (!empty($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                    header("Location: checkout.php");
                } else {
                    header("Location: shop.php");
                }
                exit;
            } else {
                $error = "Invalid email or password. Please try again.";
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Customer Login | SHAZ Cookies</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
*, *::before, *::after { box-sizing: border-box; }
body {
    margin: 0;
    padding: 30px 15px;
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(135deg, #fce4ec 0%, #f4a7b9 50%, #c6c9ea 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}
.auth-card {
    background: #ffffff;
    padding: 40px 35px;
    border-radius: 25px;
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
    width: 100%;
    max-width: 420px;
    opacity: 0;
    transform: translateY(20px);
    animation: fadeIn 0.8s forwards;
}
.brand-title {
    text-align: center;
    margin-bottom: 25px;
}
.brand-title .cookie-icon {
    font-size: 42px;
    display: inline-block;
    margin-bottom: 5px;
    animation: bounce 2s infinite;
}
.brand-title h2 {
    color: var(--primary-pink-dark, #e38aa0);
    margin: 0 0 5px 0;
    font-size: 26px;
    font-weight: 700;
}
.brand-title p {
    color: #777;
    margin: 0;
    font-size: 14px;
}
.form-group {
    margin-bottom: 18px;
}
.form-group label {
    display: block;
    margin-bottom: 6px;
    font-size: 13px;
    font-weight: 600;
    color: #444;
}
.form-group input {
    width: 100%;
    padding: 13px 15px;
    border: 1px solid #ddd;
    border-radius: 12px;
    font-size: 14px;
    font-family: 'Poppins', sans-serif;
    transition: all 0.3s ease;
    background: #fafafa;
}
.form-group input:focus {
    outline: none;
    border-color: #f4a7b9;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(244, 167, 185, 0.25);
}
.btn-submit {
    width: 100%;
    padding: 13px;
    background: linear-gradient(90deg, #f4a7b9, #e38aa0);
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    border: none;
    border-radius: 25px;
    cursor: pointer;
    margin-top: 10px;
    box-shadow: 0 4px 15px rgba(227, 138, 160, 0.4);
    transition: all 0.3s ease;
    font-family: 'Poppins', sans-serif;
}
.btn-submit:hover {
    background: linear-gradient(90deg, #e38aa0, #d1758c);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(227, 138, 160, 0.5);
}
.btn-submit:active {
    transform: translateY(0);
}
.success-msg {
    color: #155724;
    background: #d4edda;
    border-left: 4px solid #28a745;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 13px;
}
.error-msg {
    color: #d8000c;
    background: #ffd2d2;
    border-left: 4px solid #d8000c;
    padding: 12px 15px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 13px;
}
.auth-links {
    text-align: center;
    margin-top: 22px;
    font-size: 14px;
    color: #666;
}
.auth-links a {
    color: var(--primary-pink-dark, #e38aa0);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.2s;
}
.auth-links a:hover {
    color: #b85e74;
    text-decoration: underline;
}
.divider {
    text-align: center;
    position: relative;
    margin: 20px 0;
}
.divider::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    border-top: 1px solid #eee;
}
.divider span {
    position: relative;
    background: #fff;
    padding: 0 12px;
    font-size: 12px;
    color: #aaa;
}
.admin-link {
    display: block;
    text-align: center;
    margin-top: 10px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
}
.admin-link:hover {
    color: #e38aa0;
}
.back-link {
    display: block;
    text-align: center;
    margin-top: 12px;
    font-size: 13px;
    color: #888;
    text-decoration: none;
    transition: color 0.2s;
}
.back-link:hover {
    color: #444;
}

@keyframes fadeIn {
    to { opacity: 1; transform: translateY(0); }
}
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-6px); }
}

@media (max-width: 480px) {
    .auth-card {
        padding: 30px 20px;
    }
    .brand-title h2 {
        font-size: 22px;
    }
}
</style>
</head>

<body>

<div class="auth-card">
    <div class="brand-title">
        <span class="cookie-icon">🍪</span>
        <h2>Welcome Back!</h2>
        <p>Log in to order your favorite cookies 💕</p>
    </div>

    <?php if ($success): ?>
        <div class="success-msg"><?= htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-msg">⚠️ <?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" value="<?= htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
        </div>

        <button type="submit" class="btn-submit">Log In 🍪</button>
    </form>

    <div class="auth-links">
        Don't have an account? <a href="register.php">Sign Up</a>
    </div>

    <div class="divider"><span>OR</span></div>

    <a href="admin_login.php" class="admin-link">Are you an admin? Admin Login 👑</a>
    <a href="index.php" class="back-link">← Back to Cookie Shop</a>
</div>

</body>
</html>
