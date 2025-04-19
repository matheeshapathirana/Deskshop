<?php
session_start();
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

// Database connection
$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get product code from URL
$product = null;
if (isset($_GET['code'])) {
    $code = mysqli_real_escape_string($con, $_GET['code']);
    $sql = "SELECT p.product_code, p.name, p.price, p.image_url, p.stock, GROUP_CONCAT(c.category_name SEPARATOR ' ') as categories
            FROM products p
            LEFT JOIN product_categories pc ON p.product_code = pc.product_code
            LEFT JOIN categories c ON pc.category_id = c.category_id
            WHERE p.product_code = '$code'
            GROUP BY p.product_code";
    $result = mysqli_query($con, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
    }
}
?>
<!doctype html>
<html lang="en">
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo $product ? htmlspecialchars($product['name']) . ' - Product Page' : 'Product Not Found'; ?></title>
    <link rel="stylesheet" href="product.css" />
    <link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
    <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
    </style>
  </head>
  <body>
    <div class="products-page">
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
        <div class="search-container">
          <div class="search-box">
            <img
              src="Assets/icons/search.svg"
              alt="Search icon"
              class="search-icon"
            />
            <input
              type="text"
              placeholder="Search for products..."
              class="search-input"
            />
          </div>
        </div>

        <section class="product-display">
          <div class="product-container">
            <?php if ($product): ?>
            <div class="product-image-column">
              <img
                src="<?php echo htmlspecialchars($product['image_url']); ?>"
                alt="<?php echo htmlspecialchars($product['name']); ?>"
                class="product-image"
              />
            </div>
            <div class="product-details-column">
              <div class="product-info">
                <h2 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h2>
                <p class="product-price">Rs.<?php echo number_format($product['price'], 2); ?></p>
                <div class="product-actions">
                  <div class="quantity-selector">
                    <img
                      src="Assets/icons/minus.svg"
                      alt="Decrease"
                      class="quantity-btn"
                      id="decrease-quantity"
                    />
                    <span class="quantity-value" id="quantity-value">1</span>
                    <img
                      src="Assets/icons/plus.svg"
                      alt="Increase"
                      class="quantity-btn"
                      id="increase-quantity"
                    />
                  </div>
                  <button class="add-to-cart-btn">Add to Cart</button>
                </div>
                <div class="wishlist-action">
                        <img src="Assets/icons/wishlist.svg" alt="Wishlist Icon" />
                  <span class="wishlist-text">Add to Wish List</span>
                </div>
                <div style="margin-top:10px; font-size:14px; color:#888;">
                  <?php echo $product['stock'] > 0 ? 'In Stock' : 'Out of Stock'; ?>
                </div>
              </div>
            </div>
            <?php else: ?>
              <div style="padding:40px; font-size:22px; color:#a00;">
                Product not found.
              </div>
            <?php endif; ?>
          </div>
        </section>

        <section class="reviews-section">
          <h3 class="section-title">Reviews</h3>
          <h4 class="review-form-title">Your Review</h4>
          <form class="review-form">
            <textarea class="review-input" required></textarea>
            <button type="submit" class="submit-btn">Submit</button>
          </form>

          <div class="reviews-container">
            <article class="review-row">
              <div class="review-item">
                <div class="review-content">
                  <div class="review-header">
                    <div class="review-text-container">
                      <h5 class="reviewer-name">Test User</h5>
                      <p class="review-text">
                        The erasers are of excellent quality, effectively
                        removing pencil marks without leaving smudges or tearing
                        the paper.
                      </p>
                    </div>
                  </div>
                  <hr class="review-divider" />
                </div>
                <p class="review-date">Posted on March 01, 2025</p>
              </div>
              <div class="review-item">
                <div class="review-content">
                  <div class="review-header">
                    <div class="review-text-container">
                      <h5 class="reviewer-name">Test User</h5>
                      <p class="review-text">
                        The erasers are of excellent quality, effectively
                        removing pencil marks without leaving smudges or tearing
                        the paper.
                      </p>
                    </div>
                  </div>
                  <hr class="review-divider" />
                </div>
                <p class="review-date">Posted on March 01, 2025</p>
              </div>
            </article>

            <article class="review-row">
              <div class="review-item">
                <div class="review-content">
                  <div class="review-header">
                    <div class="review-text-container">
                      <h5 class="reviewer-name">Test User</h5>
                      <p class="review-text">
                        The erasers are of excellent quality, effectively
                        removing pencil marks without leaving smudges or tearing
                        the paper.
                      </p>
                    </div>
                  </div>
                  <hr class="review-divider" />
                </div>
                <p class="review-date">Posted on March 01, 2025</p>
              </div>
              <div class="review-item">
                <div class="review-content">
                  <div class="review-header">
                    <div class="review-text-container">
                      <h5 class="reviewer-name">Test User</h5>
                      <p class="review-text">
                        The erasers are of excellent quality, effectively
                        removing pencil marks without leaving smudges or tearing
                        the paper.
                      </p>
                    </div>
                  </div>
                  <hr class="review-divider" />
                </div>
                <p class="review-date">Posted on March 01, 2025</p>
              </div>
            </article>
          </div>

          <button class="load-more-btn">Load More Reviews</button>
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

    <script>
      function toggleWishlist(icon, index) {
          const wishlistTexts = document.querySelectorAll('.wishlist-text');
          if (icon.src.includes('wishlist.svg')) {
          icon.src = 'Assets/icons/wishlist-select.svg';
          wishlistTexts[index].textContent = 'Remove from wishlist';
          } else {
          icon.src = 'Assets/icons/wishlist.svg';
          wishlistTexts[index].textContent = 'Add to Wish List';
          }
          icon.classList.add('animate');
          setTimeout(() => {
          icon.classList.remove('animate');
          }, 300);
      }

      function decreaseQuantity() {
          const quantityValue = document.getElementById('quantity-value');
          let currentValue = parseInt(quantityValue.textContent);
          if (currentValue > 1) {
              quantityValue.textContent = currentValue - 1;
          }
      }

      function increaseQuantity() {
          const quantityValue = document.getElementById('quantity-value');
          let currentValue = parseInt(quantityValue.textContent);
          quantityValue.textContent = currentValue + 1;
      }

      window.onload = function() {
          const wishlistIcons = document.querySelectorAll('.wishlist-action img');
          wishlistIcons.forEach((icon, index) => {
              icon.onclick = function() {
                  toggleWishlist(icon, index);
              };
          });

          const decreaseButton = document.getElementById('decrease-quantity');
          const increaseButton = document.getElementById('increase-quantity');

          decreaseButton.onclick = decreaseQuantity;
          increaseButton.onclick = increaseQuantity;
      };
    </script>
  </body>
</html>
