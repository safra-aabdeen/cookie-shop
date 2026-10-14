<?php
session_start();


require_once __DIR__ . '/config.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $error = "Please fill all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email address.";
    } else {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);
        if ($stmt->execute()) {
            $success = true;
        } else {
            $error = "Failed to send message. Please try again.";
        }
        $stmt->close();
    }
}



/* Initialize cart count for header */
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}

/* Login state */
$is_logged_in = false;
$user_name = "";
if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
    $is_logged_in = true;
    $user_name = $_SESSION['user']['name'] ?? "";
}

/* Form submission simulation */
$form_submitted = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message = htmlspecialchars($_POST['message']);
    $form_submitted = true;

    // In real project: save or send email
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact | G🍪🍪dies Cookies</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
/* =========================
   CONTACT PAGE STYLE
========================= */
.contact-page{padding:100px 20px;background:#fffafc;min-height:80vh;font-family:'Poppins',sans-serif;}
.contact-title{text-align:center;margin-bottom:60px;opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;}
.contact-title h2{font-size:40px;color:var(--primary-pink-dark);margin-bottom:10px;}
.contact-title p{font-size:18px;color:#555;line-height:1.6;}

/* Fade-in animation */
@keyframes fadeIn{to{opacity:1;transform:translateY(0);}}

/* Contact Section */
.contact-section{max-width:1100px;margin:auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:40px;align-items:start;opacity:0;transform:translateY(20px);animation:fadeIn 1.2s forwards;}
.contact-section img{width:100%;border-radius:25px;box-shadow:0 8px 30px rgba(0,0,0,0.1);transition:transform .4s}
.contact-section img:hover{transform:scale(1.05);}

/* Form Card */
.contact-form{background:#fff;padding:30px;border-radius:25px;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:.4s;opacity:0;transform:translateY(20px);}
.contact-form input, .contact-form textarea{
width:100%;padding:14px;margin:10px 0;border-radius:12px;border:1px solid #ddd;font-family:'Poppins',sans-serif;
font-size:15px;box-sizing:border-box;
}
.contact-form button{
padding:14px 20px;background:var(--primary-pink);color:#fff;font-weight:600;
border-radius:25px;border:none;cursor:pointer;margin-top:12px;width:100%;transition:.3s;
}
.contact-form button:hover{background:var(--primary-pink-dark);transform:translateY(-3px);}

/* Success Message */
.success-msg{text-align:center;background:#d4edda;color:#155724;padding:15px;
border-radius:15px;margin-bottom:20px;box-shadow:0 6px 20px rgba(0,0,0,.1);opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;}

/* Contact Info Card */
.contact-info{background:#fff;padding:25px;border-radius:25px;box-shadow:0 10px 30px rgba(0,0,0,0.1);opacity:0;transform:translateY(20px);animation:fadeIn 1.2s forwards;}
.contact-info h4{color:var(--accent-cookie);margin-bottom:15px;}
.contact-info p{font-size:15px;color:#555;margin-bottom:10px;}
.contact-info a{color:var(--primary-pink-dark);text-decoration:none;font-weight:500;}
.contact-info a:hover{color:var(--primary-pink);}

/* Responsive adjustments */
@media (max-width: 768px) {
  .contact-page{padding:40px 15px;}
  .contact-title{margin-bottom:30px;}
  .contact-title h2{font-size:28px;}
  .contact-title p{font-size:15px;}
  .contact-section{grid-template-columns:1fr;gap:25px;}
  .contact-form{padding:22px 18px;}
  .contact-info{padding:20px 18px;}
}
</style>
</head>
<body>

<!-- HEADER -->
<header class="header">
<div class="logo">SHAZ Cookies</div>
<nav class="navbar">
<a href="index.php">Home</a>
<a href="shop.php">Shop</a>
<a href="about.php">About</a>
<a href="contact.php">Contact</a>
</nav>
<div class="header-right">
    <?php if ($is_logged_in): ?>
        <span class="user-greeting">👋 <?= htmlspecialchars($user_name); ?></span>
        <a href="logout.php" class="nav-auth-btn">Logout</a>
    <?php else: ?>
        <a href="login.php" class="nav-auth-btn">Login</a>
        <a href="register.php" class="nav-auth-btn primary">Register</a>
    <?php endif; ?>
    <a href="admin_login.php" class="nav-admin-link">👤Admin</a>
    <a href="cart.php" class="cart-btn">🛒 <span class="badge"><?= (int)$_SESSION['cart_count']; ?></span></a>
</div>
</header>

<!-- CONTACT TITLE -->
<section class="contact-page">
<div class="contact-title">
<h2>Contact Us 🍪</h2>
<p>We’d love to hear from you! Send us a message or visit us 💌</p>
</div>

<!-- CONTACT SECTION -->
<div class="contact-section">
<!-- IMAGE -->
<div>
<img src="images/contact.jpg" alt="Contact Bakery">
</div>

<!-- FORM + INFO -->
<div>
<?php if($form_submitted): ?>
    <?php if($success): ?>
    <p style="color:green; font-weight:bold;">Thank you! Your message has been sent.</p>
<?php elseif($error): ?>
    <p style="color:red; font-weight:bold;"><?= htmlspecialchars($error) ?></p>
<?php endif; ?>

<div class="success-msg">
<h3>✅ Message sent successfully!</h3>
<p>Thank you, <?= $name ?>. We will get back to you soon 💕</p>
</div>
<?php endif; ?>

<div class="contact-form">
<form method="POST">
<input type="text" name="name" placeholder="Your Name" required>
<input type="email" name="email" placeholder="Email Address" required>
<textarea name="message" placeholder="Your Message" rows="5" required></textarea>
<button type="submit">Send Message</button>
</form>
</div>

<div class="contact-info">
<h4>Our Contact Info</h4>
<p>📍 Address: 123 Cookie Lane, Colombo, Sri Lanka</p>
<p>📞 Phone: <a href="tel:+94111234567">+94 11 123 4567</a></p>
<p>✉ Email: <a href="mailto:info@goodiescookies.lk">info@goodiescookies.lk</a></p>
</div>
</div>
</div>

</section>

<footer>
<p>© <?= date('Y') ?> G🍪🍪dies Cookies | Made with Love 💕</p>
</footer>

<script>
// Scroll fade-in effect
const faders = document.querySelectorAll('.contact-title, .contact-section, .contact-form, .contact-info');
const appearOptions = { threshold: 0.1, rootMargin: "0px 0px -50px 0px" };
const appearOnScroll = new IntersectionObserver(function(entries, observer){
    entries.forEach(entry=>{
        if(!entry.isIntersecting) return;
        entry.target.style.opacity = 1;
        entry.target.style.transform = 'translateY(0)';
        observer.unobserve(entry.target);
    });
}, appearOptions);

faders.forEach(fader => appearOnScroll.observe(fader));
</script>

</body>
</html>