<?php
if (isset($_POST['submit-btn'])) {
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $message = $_POST['message'];

    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "INSERT INTO ContactInquiries (FullName, EmailAddress, PhoneNumber, Message)
            VALUES ('$fullName', '$email', '$phone', '$message')";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $success = "Thank you for contacting us!";
    } else {
        $error = "Error: " . mysqli_error($con);
    }

    mysqli_close($con);
}
?>
<!doctype html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Contact Us - DeskShop</title>
<link rel="stylesheet" href="contact-us.css" />
<link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
</style>
</head>
<body>
<div class="page-container">
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
                <a href="about-us.php" class="nav-item">About Us</a>
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
            <button class="signup-btn" onclick="window.location.href='signup.php'">Sign up</button>
        </div>
    </header>

    <main>
    <h2 class="page-title">Contact Us</h2>

    <section class="contact-form-container">
        <form class="contact-form" method="POST" action="">
        <input
            type="text"
            name="fullName"
            placeholder="Full Name"
            class="form-input name-input"
            required
        />
        <input
            type="email"
            name="email"
            placeholder="Email Address"
            class="form-input email-input"
            required
        />
        <input
            type="tel"
            name="phone"
            placeholder="Phone Number"
            class="form-input phone-input"
            required
            maxlength="10"
            pattern="[0-9]{10}"
        />
        <textarea
            name="message"
            placeholder="Message"
            class="form-input message-input"
            required
        ></textarea>
        <button type="submit" name="submit-btn" class="submit-btn">Submit</button>
        </form>
        <?php if (!empty($success)) { echo "<p class='success-msg'>$success</p>"; } ?>
        <?php if (!empty($error)) { echo "<p class='error-msg'>$error</p>"; } ?>
    </section>
    </main>

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
</div>
</body>
</html>
