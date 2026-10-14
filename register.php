<?php
session_start();
require_once __DIR__ . '/config.php';

// If user is already logged in, redirect them
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
$name = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name             = trim($_POST['name'] ?? '');
    $email            = trim($_POST['email'] ?? '');
    $password         = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($name === '' || $email === '' || $password === '' || $confirm_password === '') {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match. Please try again.";
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "An account with this email already exists. Please log in instead.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            // Hash password and store with type='user'
            $hashed_pass = password_hash($password, PASSWORD_DEFAULT);
            $type = 'user';

            // Generate permanent unique User ID that is never reused
            $new_user_id = generate_next_id('users', $conn);

            $insert_stmt = $conn->prepare("INSERT INTO users (id, name, email, password, type) VALUES (?, ?, ?, ?, ?)");
            $insert_stmt->bind_param("issss", $new_user_id, $name, $email, $hashed_pass, $type);

            if ($insert_stmt->execute()) {
                $insert_stmt->close();
                // Redirect to login page with registered flag and email
                header("Location: login.php?registered=1&email=" . urlencode($email));
                exit;
            } else {
                $error = "Failed to create account. Please try again.";
                $insert_stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register | SHAZ Cookies</title>
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
    max-width: 440px;
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
    margin-bottom: 16px;
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
    padding: 12px 15px;
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
.error-msg {
    color: #d8000c;
    background: #ffd2d2;
    padding: 10px 15px;
    border-radius: 10px;
    margin-bottom: 18px;
    font-size: 13px;
    border-left: 4px solid #d8000c;
}
.auth-links {
    text-align: center;
    margin-top: 20px;
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
.back-link {
    display: block;
    text-align: center;
    margin-top: 15px;
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
        <h2>Create an Account</h2>
        <p>Join SHAZ Cookies for the sweetest treats 💕</p>
    </div>

    <?php if ($error): ?>
        <div class="error-msg">⚠️ <?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" placeholder="e.g. Sarah Silva" value="<?= htmlspecialchars($name); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="e.g. sarah@example.com" value="<?= htmlspecialchars($email); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="At least 6 characters" minlength="6" required>
        </div>

        <div class="form-group">
            <label for="confirm_password">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" minlength="6" required>
        </div>

        <button type="submit" class="btn-submit">Sign Up 🍪</button>
    </form>

    <div class="auth-links">
        Already have an account? <a href="login.php">Log In</a>
    </div>

    <a href="index.php" class="back-link">← Back to Cookie Shop</a>
</div>

</body>
</html>
