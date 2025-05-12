<!doctype html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Sign Up - DeskShop</title>
<link rel="stylesheet" href="signup.css" />
<link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
<style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
</style>
</head>
<body>
        <div class="main-container">
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

        <main class="content-wrapper">
                <section class="right-section">
                <form class="signup-form" method="post" action="PHP/RegistrationHandler.php">
                        <h2 class="form-title">Get Started Now</h2>
                        <div class="form-group">
                        <label class="label" for="name">Name</label>
                        <input
                                type="text"
                                name="username"
                                id="name"
                                placeholder="Enter your name"
                                class="input-field"
                                required
                                />
                        </div>
                        <div class="form-group">
                        <label class="label" for="email">Email address</label>
                        <input
                                type="email"
                                name="email"
                                id="email"
                                placeholder="Enter your email"
                                class="input-field"
                                required
                                />
                        </div>

                        <div class="form-group">
                                <label class="label" for="email">Phone Number</label>
                                <input
                                        type="tp"
                                        name="contact_number"
                                        id="tp"
                                        placeholder="Enter your Phone Number"
                                        class="input-field"
                                        required
                                        pattern="[0-9]{10}"
                                        maxlength="10"
                                />
                        </div>


                        <div class="form-group">
                        <label class="label" for="password">Password</label>
                        <input
                                required
                                type="password"
                                name="password"
                                id="password"
                                placeholder="Enter your password"
                                class="input-field"
                        />
                        <img src="Assets/icons/eye-off.svg" alt="Show/Hide Password" class="eye-icon" id="togglePassword" />

                        <label class="confpasslabel" for="confirm-password">Confirm Password</label>
                        <input
                                required
                                type="password"
                                id="confirm-password"
                                placeholder="Confirm your password"
                                class="input-field"
                        />
                        <img src="Assets/icons/eye-off.svg" alt="Show/Hide Password" class="eye-icon-confirm" id="toggleConfirmPassword" />
                        
                        <div class="terms-checkbox">
                                <input type="checkbox" id="terms" class="input" />
                                <label for="terms">
                                        <span>I agree to the</span>
                                        <span class="underline"><a href="about-us.php#terms">terms & policy</a></span>
                                </label>
                        </div>
                        <button type="submit" name="signup-submit-btn" class="signup-submit">Signup</button>
                        <div class="login-link">
                        <span>Have an account?</span>
                        <a href="signin.php" class="sign-in">Sign In</a>
                </div>
                </form>
        </section>
        </main>
        </div>

        <div class="footer-container">
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
        <script>
        document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordField = document.getElementById('password');
        const eyeIcon = document.getElementById('togglePassword');
        
        if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.src = 'Assets/icons/eye-on.svg';
        } else {
                passwordField.type = 'password';
                eyeIcon.src = 'Assets/icons/eye-off.svg';
        }
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function () {
        const confirmPasswordField = document.getElementById('confirm-password');
        const eyeIconConfirm = document.getElementById('toggleConfirmPassword');

        if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                eyeIconConfirm.src = 'Assets/icons/eye-on.svg';
        } else {
                confirmPasswordField.type = 'password';
                eyeIconConfirm.src = 'Assets/icons/eye-off.svg';
        }
        });

        document.querySelector('.signup-form').addEventListener('submit', function (event) {
                const termsCheckbox = document.getElementById('terms');
                if (!termsCheckbox.checked) {
                        event.preventDefault();
                        alert('You must agree to the terms & policy before signing up.');
                }
        });

        document.querySelector('.signup-form').addEventListener('submit', function (event) {
                const password = document.getElementById('password').value;
                const confirmPassword = document.getElementById('confirm-password').value;                // Password validation: at least 8 chars, must contain at least one letter and one number (special characters allowed, optional)
                const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d).{8,}$/;
                if (!passwordRegex.test(password)) {
                        event.preventDefault();
                        alert('Password must be at least 8 characters long and contain at least one letter and one number.');
                        return;
                }
                if (password !== confirmPassword) {
                        event.preventDefault();
                        alert('Passwords do not match. Please try again.');
                }
        });
</script>
</body>
</html>
