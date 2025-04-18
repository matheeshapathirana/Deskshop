<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    echo "<script>alert('Please sign in to access your cart.'); window.location.href='signin.php';</script>";
    exit();
}
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);
?>
<html>
    <head>
        <title>DeskShop - Cart</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="cart.css">
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
                <?php if ($isLoggedIn): ?>
                <button class="signup-btn" onclick="window.location.href='profile.php'">Profile</button>
                <?php else: ?>
                <button class="signup-btn" onclick="window.location.href='signup.php'">Sign up</button>
                <?php endif; ?>
            </div>
        </header>
          
          <main class="main-content">
            <div class="content-container">
              <section class="cart-section">
                <div class="back-to-shop">
                  <img
                    src="Assets/Arrows/black-back-arrow.png"
                    alt="Back arrow"
                    class="back-arrow"
                  />
                  <a href="shop.php" class="continue-shopping">Continue Shopping</a>
                </div>
          
                <div class="cart-items-container">
                  <h2 class="cart-title">Shopping Cart</h2>
          
                  <article class="cart-item">
                    <img
                      src="Assets/Product Page/Atlas-eraser.png"
                      alt="Atlas Eraser"
                      class="product-image"
                    />
                    <div class="product-details">
                      <div class="product-info">
                        <div class="product-text">
                          <h3 class="product-name">Atlas Eraser</h3>
                          <p class="product-quantity">Qty : 2</p>
                        </div>
                        <button class="remove-item">
                          <img
                            src="Assets/remove-from-cart-cross.svg"
                            alt="Remove item"
                            class="remove-icon"
                          />
                        </button>
                      </div>
                      <p class="product-price">Rs. 60.00</p>
                    </div>
                  </article>
          
                  <article class="cart-item">
                    <img
                      src="Assets/Product Page/Atlas A5 Practical Book Blank 80Pgs.png"
                      alt="Atlas Notebook"
                      class="product-image"
                    />
                    <div class="product-details">
                      <div class="product-info">
                        <div class="product-text">
                          <h3 class="product-name">
                            Atlas A5 Practical Blank Book 80 Pages
                          </h3>
                          <p class="product-quantity">Qty : 1</p>
                        </div>
                        <button class="remove-item">
                          <img
                            src="Assets/remove-from-cart-cross.svg"
                            alt="Remove item"
                            class="remove-icon"
                          />
                        </button>
                      </div>
                      <p class="product-price">Rs. 50.00</p>
                    </div>
                  </article>
          
                  <article class="cart-item">
                    <img
                      src="Assets/Product Page/Homerun Colour Pencil 12 Colours.jpg"
                      alt="Color Pencils"
                      class="product-image"
                    />
                    <div class="product-details">
                      <div class="product-info">
                        <div class="product-text">
                          <h3 class="product-name">Homerun Colour Pencil 12 Colours</h3>
                          <p class="product-quantity">Qty : 1</p>
                        </div>
                        <button class="remove-item">
                          <img
                            src="Assets/remove-from-cart-cross.svg"
                            alt="Remove item"
                            class="remove-icon"
                          />
                        </button>
                      </div>
                      <p class="product-price">Rs. 250.00</p>
                    </div>
                  </article>
          
                  <article class="cart-item">
                    <img
                      src="Assets/Product Page/Mango Plastic Ruler 15cm.png"
                      alt="Ruler"
                      class="product-image"
                    />
                    <div class="product-details">
                      <div class="product-info">
                        <div class="product-text">
                          <h3 class="product-name">Mango Plastic Ruler 15cm</h3>
                          <p class="product-quantity">Qty : 1</p>
                        </div>
                        <button class="remove-item">
                          <img
                            src="Assets/remove-from-cart-cross.svg"
                            alt="Remove item"
                            class="remove-icon"
                          />
                        </button>
                      </div>
                      <p class="product-price">Rs. 20.00</p>
                    </div>
                  </article>
                </div>
              </section>
          
              <section class="payment-section">
                <form class="payment-form">
                  <h2 class="form-title">Payment Details</h2>
          
                  <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input
                      type="text"
                      id="fullname"
                      class="form-input"
                      placeholder="Name"
                      required
                    />
                  </div>
          
                  <div class="form-group">
                    <label for="phone">Telephone Number</label>
                    <input
                      type="tel"
                      id="phone"
                      class="form-input"
                      placeholder="Telephone Number"
                      required
                      pattern="[0-9]{10}"
                      maxlength="10"
                    />
                  </div>
          
                  <div class="form-group">
                    <label for="address">Address</label>
                    <input
                      type="text"
                      id="address"
                      class="form-input"
                      placeholder="Address"
                      required
                    />
                  </div>
          
                  <div class="form-row">
                    <div class="form-group half">
                      <label for="city">City</label>
                      <input
                        type="text"
                        id="city"
                        class="form-input"
                        placeholder="City"
                        required
                      />
                    </div>
                    <div class="form-group half">
                      <label for="postal">Postal Code</label>
                      <input
                        type="text"
                        id="postal"
                        class="form-input"
                        placeholder="Postal Code"
                        maxlength="5"
                        required
                        pattern="[0-9]{5}"
                      />
                    </div>
                  </div>
          
                  <div class="payment-divider"></div>
          
                  <div class="payment-method">
                    <input type="radio" id="cash-on-delivery" name="payment" class="checkbox"/>
                    <label for="cash-on-delivery" class="method-label">Cash On Delivery</label>

                    <input type="radio" id="bank-transfer" name="payment" class="checkbox"/>
                    <label for="bank-transfer" class="method-label">Bank Transfer</label>
                  </div>
          
                  <div class="order-summary">
                    <div class="summary-row">
                      <span class="summary-label">Subtotal</span>
                      <span class="summary-value">Rs. 380.00</span>
                    </div>
                    <div class="summary-row">
                      <span class="summary-label">Delivery Fee</span>
                      <span class="summary-value">Rs. 150.00</span>
                    </div>
                    <div class="summary-row">
                      <span class="summary-label">Total (Tax incl.)</span>
                      <span class="summary-value">Rs. 530.00</span>
                    </div>
                  </div>
          
                  <button type="submit" class="checkout-button">
                    <span class="total-amount">Rs. 530.00</span>
                    <div class="checkout-text">
                      <span>Checkout</span>
                      <img
                        src="Assets/Arrows/cart-checkout-arrow.png"
                        alt="Checkout arrow"
                        class="checkout-arrow"
                      />
                    </div>
                  </button>
                </form>
              </section>
            </div>
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
          
    </body>
</html>
