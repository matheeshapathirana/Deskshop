<?php
session_start();
require_once 'PHP/AuthCheck.php';
$user = check_user_auth_and_status();
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FAQ - DeskShop</title>
<link rel="stylesheet" href="faq.css">
<link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
</style>
</head>
<body>
<header class="header">
    <div class="header-content">
        <h1 class="logo">DeskShop</h1>
        <nav class="nav-menu">
            <a href="index.php" class="nav-item">Home</a>
            <div class="products-dropdown">
                <button class="products-dropdown-button">Products</button>
                <div class="products-dropdown-content">
                    <a href="shop.php?category=default" onclick="event.preventDefault(); window.location.href='shop.php?category=default';">All Items</a>
                    <a href="shop.php?category=school-items" onclick="event.preventDefault(); window.location.href='shop.php?category=school-items';">School Items</a>
                    <a href="shop.php?category=office-items" onclick="event.preventDefault(); window.location.href='shop.php?category=office-items';">Office Items</a>
                    <a href="shop.php?category=books" onclick="event.preventDefault(); window.location.href='shop.php?category=books';">Books</a>
                    <a href="shop.php?category=writing-tools" onclick="event.preventDefault(); window.location.href='shop.php?category=writing-tools';">Writing Tools</a>
                    <a href="shop.php?category=colour-products" onclick="event.preventDefault(); window.location.href='shop.php?category=colour-products';">Color Products</a>
                    <a href="shop.php?category=desk-accessories" onclick="event.preventDefault(); window.location.href='shop.php?category=desk-accessories';">Desk Essentials</a>
                </div>
            </div>
            <a href="about-us.php" class="nav-item">About Us   </a>
            <a href="faq.php" class="nav-item">FAQ</a>
            <a href="contact-us.php" class="nav-item">Contact us</a>
            <img
            src="Assets/icons/add-to-cart.svg"
            alt="Cart"
            class="cart-icon"
            style="cursor: pointer;"
            onclick="window.location.href='cart.php'"
            />
        </nav>
        <?php if ($isLoggedIn): ?>
            <button class="signup-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php else: ?>
            <button class="signup-btn" onclick="window.location.href='signup.php'">Sign up</button>
        <?php endif; ?>
    </div>
    
</header>

<main class="faq-page">
<h2 class="faq-title">Frequently Asked Questions</h2>

<section class="faq-grid">
    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">
        What types of stationery products do you sell?
        </h3>
    </div>
    <p class="faq-answer">
        We offer a wide range of products, including writing tools, art
        supplies, desk accessories, notebooks, office essentials, and more.
    </p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">Can I find branded stationery items?</h3>
    </div>
    <p class="faq-answer">Yes, we carry items from popular brands.</p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">What payment methods do you accept?</h3>
    </div>
    <p class="faq-answer">We accept bank transfers and cash on delivery.</p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">Do you offer delivery services?</h3>
    </div>
    <p class="faq-answer">
        Yes, we deliver islandwide. Delivery times may vary depending on your
        location.
    </p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">Can I track my order?</h3>
    </div>
    <p class="faq-answer">
        Yes, once your order has been placed, you can track the order via the
        'Delivery' option
    </p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">Do you have a physical store?</h3>
    </div>
    <p class="faq-answer">
        Yes, our store is located at 123/456, Test, Test city. Visit us during
        9.00AM - 4.00PM.
    </p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">How can I contact customer support?</h3>
    </div>
    <p class="faq-answer">
        You can reach us via email at deskshop@shop.com or call us at
        +9412345678.
    </p>
    </article>

    <article class="faq-item">
    <div class="faq-header">
        <span class="faq-icon"></span>
        <h3 class="faq-question">Can I subscribe to your newsletter?</h3>
    </div>
    <p class="faq-answer">
        Yes, sign up for our newsletter to receive updates on new arrivals,
        special offers, and more!
    </p>
    </article>
</section>

<footer class="footer" style="background: linear-gradient(0deg, rgba(115, 115, 115, 0.53) 0%, rgba(242, 242, 242, 0.45) 100%);">
    <div class="footer-content">
        <div class="footer-row">
            <div class="footer-column">
                <div class="footer-logo-wrapper">
                    <img loading="lazy" src="Assets/DeskShop Logo.png" class="footer-logo" alt="DeskShop logo" />
                    <img loading="lazy" src="Assets/icons/social-media-icons.svg" class="footer-social" alt="Social media icons" />
                    <p class="footer-copyright">©2025 Matheesha Pathirana</p>
                </div>
            </div>
            <div class="footer-columns-wrapper">
                <div class="footer-links-column">
                    <div class="footer-links">
                        <h3 class="footer-links-title">Information</h3>
                        <nav class="footer-link-list">
                            <a href="index.php">Main</a><br />
                            <a href="shop.php">Products</a><br />
                            <a href="about-us.php">About us</a><br />
                            <a href="contact-us.php">Contact us</a>
                        </nav>
                    </div>
                </div>
                <div class="footer-contact-column">
                    <div class="footer-contact">
                        <h3 class="footer-contact-title">Contact</h3>
                        <p class="footer-contact-phone">
                            <strong>Phone:</strong><br />
                            (123)456-7890
                        </p>
                        <p class="footer-contact-email">
                            <strong>Email:</strong><br />
                            pathiranamatheesha@gmail.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const faqHeaders = document.querySelectorAll('.faq-header');

    faqHeaders.forEach(header => {
        header.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            answer.classList.toggle('show');
        });
    });
});
</script>

<style>
.faq-answer {
    display: none;
}

.faq-answer.show {
    display: block;
}
</style>

</body>
</html>
