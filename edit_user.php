<?php
session_start();
require_once __DIR__ . '/config.php';

/* Admin check */
if (!isset($_SESSION['user_type']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: admin_login.php");
    exit;
}

$error = '';
$user_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($user_id <= 0) {
    header("Location: manage_users.php");
    exit;
}

// Fetch user (only regular users, protect admin)
$stmt = $conn->prepare("SELECT id, name, email FROM users WHERE id = ? AND type = 'user' LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
    header("Location: manage_users.php");
    exit;
}

// Handle update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if ($name === '' || $email === '') {
        $error = "Name and Email cannot be empty.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please provide a valid email address.";
    } else {
        // Check if email is already in use by another user
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1");
        $check_stmt->bind_param("si", $email, $user_id);
        $check_stmt->execute();
        $check_stmt->store_result();
        if ($check_stmt->num_rows > 0) {
            $error = "This email address is already in use by another user.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            if (!empty($new_password)) {
                $hash = password_hash($new_password, PASSWORD_DEFAULT);
                $upd_stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, password = ? WHERE id = ? AND type = 'user'");
                $upd_stmt->bind_param("sssi", $name, $email, $hash, $user_id);
            } else {
                $upd_stmt = $conn->prepare("UPDATE users SET name = ?, email = ? WHERE id = ? AND type = 'user'");
                $upd_stmt->bind_param("ssi", $name, $email, $user_id);
            }

            if ($upd_stmt->execute()) {
                $upd_stmt->close();
                header("Location: manage_users.php?msg=updated");
                exit;
            } else {
                $error = "Failed to update user. Please try again.";
                $upd_stmt->close();
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
<title>Edit User | Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*, *::before, *::after { box-sizing: border-box; }
body {
    font-family: 'Poppins', sans-serif;
    background: #fffafc;
    margin: 0;
    padding: 30px 15px;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.card {
    background: #fff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.08);
    width: 100%;
    max-width: 480px;
}
h2 {
    color: #e86a92;
    text-align: center;
    margin-top: 0;
    margin-bottom: 20px;
}
.error-msg {
    background: #ffebee;
    color: #c62828;
    padding: 10px 15px;
    border-radius: 8px;
    font-size: 14px;
    margin-bottom: 15px;
    border: 1px solid #ffcdd2;
}
.form-group {
    margin-bottom: 16px;
}
label {
    display: block;
    margin-bottom: 6px;
    font-weight: 500;
    color: #444;
    font-size: 14px;
}
input {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
}
input:focus {
    outline: none;
    border-color: #e86a92;
}
.hint {
    font-size: 12px;
    color: #888;
    margin-top: 4px;
}
.btn-group {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}
.btn-submit {
    flex: 1;
    background: #e86a92;
    color: #fff;
    border: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    font-family: 'Poppins', sans-serif;
    transition: .3s;
}
.btn-submit:hover { opacity: .9; }
.btn-cancel {
    flex: 1;
    background: #e0e0e0;
    color: #333;
    text-align: center;
    text-decoration: none;
    padding: 12px;
    border-radius: 8px;
    font-weight: 600;
    transition: .3s;
}
.btn-cancel:hover { background: #d5d5d5; }
</style>
</head>

<body>
<div class="card">
    <h2>✏️ Edit User</h2>

    <?php if ($error): ?>
        <div class="error-msg"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label>User ID</label>
            <input type="text" value="<?= format_id($user['id']); ?>" readonly style="background:#f9f9f9;color:#777;font-weight:600;cursor:not-allowed;">
        </div>

        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="new_password">New Password</label>
            <input type="password" id="new_password" name="new_password" placeholder="Leave blank to keep current password">
            <div class="hint">Only enter a new password if you wish to reset it.</div>
        </div>

        <div class="btn-group">
            <button type="submit" class="btn-submit">Save Changes</button>
            <a href="manage_users.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
