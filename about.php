<?php
session_start();
if (!isset($_SESSION['cart_count'])) {
    $_SESSION['cart_count'] = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>About | G🍪🍪dies Cookies</title>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">

<style>
/* =========================
   ABOUT PAGE STYLE
========================= */
.about-page{padding:100px 20px;background:#fffafc;min-height:80vh; font-family:'Poppins',sans-serif;}
.about-title{text-align:center;margin-bottom:60px;opacity:0;transform:translateY(20px);animation:fadeIn 1s forwards;}
.about-title h2{font-size:40px;color:var(--primary-pink-dark); margin-bottom:10px;}
.about-title p{font-size:18px;color:#555;line-height:1.6;}

/* Fade-in animation */
@keyframes fadeIn{to{opacity:1;transform:translateY(0);}}

/* Story Section */
.about-section{max-width:1200px;margin:auto;display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:40px;align-items:center;opacity:0;transform:translateY(20px);animation:fadeIn 1.2s forwards;}
.about-section img{width:100%;border-radius:25px;box-shadow:0 8px 30px rgba(0,0,0,0.1);transition:transform .4s}
.about-section img:hover{transform:scale(1.05);}

.about-text h3{color:var(--accent-cookie);margin-bottom:15px;font-size:28px;font-weight:600}
.about-text p{font-size:16px;color:#555;line-height:1.8;margin-bottom:15px}

/* Team Section */
.team-section{margin-top:80px;text-align:center;opacity:0;transform:translateY(20px);animation:fadeIn 1.4s forwards;}
.team-section h2{font-size:36px;color:var(--primary-pink-dark);margin-bottom:50px}

.team-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:25px;max-width:1100px;margin:auto}

.team-card{background:#fff;padding:30px;border-radius:25px;box-shadow:0 10px 30px rgba(0,0,0,0.1);transition:.4s;opacity:0;transform:translateY(20px);}
.team-card:hover{transform:translateY(-10px) scale(1.03);}
.team-card img{width:120px;height:120px;border-radius:50%;object-fit:cover;margin-bottom:15px;transition:transform .3s;}
.team-card img:hover{transform:scale(1.1);}
.team-card h4{color:var(--primary-pink-dark);margin-bottom:5px;font-size:20px;font-weight:600;}
.team-card p{font-size:15px;color:#777;line-height:1.5;}

/* Responsive adjustments */
@media (max-width: 768px) {
  .about-page{padding:40px 15px;}
  .about-title{margin-bottom:30px;}
  .about-title h2{font-size:28px;}
  .about-title p{font-size:15px;}
  .about-section{grid-template-columns:1fr;gap:25px;text-align:center;}
  .about-text h3{font-size:24px;}
  .team-section{margin-top:50px;}
  .team-section h2{font-size:28px;margin-bottom:30px;}
  .team-grid{gap:20px;}
}

@media (max-width: 480px) {
  .team-grid{grid-template-columns:1fr;}
}

/* Footer adjustment */
footer{margin-top:60px;}
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
<a href="cart.php" class="cart-btn">🛒 <span class="badge"><?= $_SESSION['cart_count']; ?></span></a>
</header>

<!-- ABOUT PAGE TITLE -->
<section class="about-page">
<div class="about-title">
<h2>About G🍪🍪dies Cookies</h2>
<p>We craft freshly baked cookies with love and passion. Every cookie tells a story of happiness and quality ingredients.</p>
</div>

<!-- STORY -->
<div class="about-section">
<div class="about-text">
<h3>Our Story</h3>
<p>Starting as a small home bakery, G🍪🍪dies Cookies brings you freshly baked cookies made with natural ingredients and lots of love. Our mission is to spread joy through every bite.</p>

<h3>Our Mission</h3>
<p>Deliver happiness, freshness, and quality with every cookie, directly to your doorstep in Sri Lanka.</p>

<h3>Our Vision</h3>
<p>Be the most loved cookie bakery in the country, known for unique flavors and uncompromised quality.</p>
</div>
<div>
<img src="images/about.jpg" alt="Our Bakery">
</div>
</div>

<!-- TEAM SECTION -->
<section class="team-section">
<h2>Meet Our Team</h2>
<div class="team-grid">
<div class="team-card">
<img src="images/team1.jpg" alt="Baker 1">
<h4>Shaza</h4>
<p>Head Baker</p>
</div>
<div class="team-card">
<img src="images/team2.jpg" alt="Baker 2">
<h4>Amy Rose</h4>
<p>Pastry Chef</p>
</div>
<div class="team-card">
<img src="images/team4.jpg" alt="Baker 3">
<h4>David Lee</h4>
<p>Delivery Coordinator</p>
</div>
<div class="team-card">
<img src="images/team3.jpg" alt="Baker 4">
<h4>Lina Grace</h4>
<p>Quality Control</p>
</div>
</div>
</section>
</section>

<footer>
<p>© SHAZ Cookies | Made with Love 💕</p>
</footer>

<script>
// Scroll fade-in effect
const faders = document.querySelectorAll('.about-title, .about-section, .team-section, .team-card');
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