<?php
session_start();
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

// --- Wishlist handlers (with quantity) ---
if (isset($_POST['wishlist_action'], $_POST['product_code'])) {
    if ($isLoggedIn) {
        $con = mysqli_connect("localhost", "root", "", "deskshop");
        if ($con) {
            $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
            $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
            $user_row = mysqli_fetch_assoc($user_q);
            if ($user_row) {
                $id = $user_row['id'];
                $product_code = mysqli_real_escape_string($con, $_POST['product_code']);
                $qty = isset($_POST['wishlist_qty']) ? max(1, intval($_POST['wishlist_qty'])) : 1;
                if ($_POST['wishlist_action'] === 'add') {
                    // Try to insert with quantity, or update if exists
                    $check = mysqli_query($con, "SELECT * FROM wishlists WHERE id=$id AND product_code='$product_code'");
                    if (mysqli_num_rows($check) == 0) {
                        mysqli_query($con, "INSERT INTO wishlists (id, product_code, quantity) VALUES ($id, '$product_code', $qty)");
                    } else {
                        mysqli_query($con, "UPDATE wishlists SET quantity=$qty WHERE id=$id AND product_code='$product_code'");
                    }
                } elseif ($_POST['wishlist_action'] === 'remove') {
                    mysqli_query($con, "DELETE FROM wishlists WHERE id=$id AND product_code='$product_code'");
                }
            }
            mysqli_close($con);
        }
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . (isset($_GET['code']) ? '?code=' . urlencode($_GET['code']) : ''));
    exit;
}

// --- Add to Cart handler ---
if (isset($_POST['cart_action'], $_POST['product_code']) && $_POST['cart_action'] === 'add') {
    if ($isLoggedIn) {
        $con = mysqli_connect("localhost", "root", "", "deskshop");
        if ($con) {
            $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
            $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
            $user_row = mysqli_fetch_assoc($user_q);
            if ($user_row) {
                $id = $user_row['id'];
                $product_code = mysqli_real_escape_string($con, $_POST['product_code']);
                $qty = isset($_POST['cart_qty']) ? max(1, intval($_POST['cart_qty'])) : 1;
                // Check if already in cart
                $check = mysqli_query($con, "SELECT * FROM cart WHERE id=$id AND product_code='$product_code'");
                if (mysqli_num_rows($check) == 0) {
                    mysqli_query($con, "INSERT INTO cart (id, product_code, quantity) VALUES ($id, '$product_code', $qty)");
                } else {
                    mysqli_query($con, "UPDATE cart SET quantity=$qty WHERE id=$id AND product_code='$product_code'");
                }
            }
            mysqli_close($con);
        }
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . (isset($_GET['code']) ? '?code=' . urlencode($_GET['code']) : ''));
    exit;
}

// --- Review submission handler ---
$review_error = '';
if (isset($_POST['review_action'], $_POST['review_text'], $_POST['product_code']) && $_POST['review_action'] === 'add') {
    if ($isLoggedIn) {
        $con = mysqli_connect("localhost", "root", "", "deskshop");
        if ($con) {
            $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
            $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
            $user_row = mysqli_fetch_assoc($user_q);
            if ($user_row) {
                $id = $user_row['id'];
                $product_code = mysqli_real_escape_string($con, $_POST['product_code']);
                $review_text = mysqli_real_escape_string($con, trim($_POST['review_text']));
                if ($review_text !== '') {
                    mysqli_query($con, "INSERT INTO reviews (ID, product_code, review_text) VALUES ($id, '$product_code', '$review_text')");
                } else {
                    $review_error = "Review cannot be empty.";
                }
            }
            mysqli_close($con);
        }
        // Redirect to avoid resubmission
        header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . (isset($_GET['code']) ? '?code=' . urlencode($_GET['code']) : ''));
        exit;
    } else {
        $review_error = "You must be logged in to submit a review.";
    }
}

// Fetch user's wishlist product_codes for marking icons
$user_wishlist_product_ids = [];
$user_wishlist_qty = [];
if ($isLoggedIn) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
        $user_row = mysqli_fetch_assoc($user_q);
        if ($user_row) {
            $id = $user_row['id'];
            $wl_q = mysqli_query($con, "SELECT product_code, quantity FROM wishlists WHERE id = $id");
            while ($wl_row = mysqli_fetch_assoc($wl_q)) {
                $user_wishlist_product_ids[] = $wl_row['product_code'];
                $user_wishlist_qty[$wl_row['product_code']] = $wl_row['quantity'];
            }
        }
        mysqli_close($con);
    }
}

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

// Fetch reviews for this product
$reviews = [];
if ($product) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        $code = mysqli_real_escape_string($con, $product['product_code']);
        $sql = "SELECT r.review_text, r.review_date, u.username as reviewer_name
                FROM reviews r
                JOIN user u ON r.ID = u.ID
                WHERE r.product_code = '$code'
                ORDER BY r.review_date DESC";
        $result = mysqli_query($con, $sql);
        while ($row = mysqli_fetch_assoc($result)) {
            $reviews[] = $row;
        }
        mysqli_close($con);
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
                    <span class="quantity-value" id="quantity-value"><?php
                        // If in wishlist, show wishlist quantity, else 1
                        $wishlist_qty = (isset($user_wishlist_qty[$product['product_code']]) ? $user_wishlist_qty[$product['product_code']] : 1);
                        echo $wishlist_qty;
                    ?></span>
                    <img
                      src="Assets/icons/plus.svg"
                      alt="Increase"
                      class="quantity-btn"
                      id="increase-quantity"
                    />
                  </div>
                  <!-- Add to Cart form -->
                  <form method="post" id="cart-form" style="display:inline;">
                    <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                    <input type="hidden" name="cart_action" value="add">
                    <input type="hidden" name="cart_qty" id="cart-qty-field" value="<?php echo $wishlist_qty; ?>">
                    <button type="submit" class="add-to-cart-btn" id="add-to-cart-btn" style="padding: 14px 36px; font-size: 1.2em; min-width: 180px;position: relative;top: 13px;/*! size: 10; */scale: 140%;left: 40px;">Add to Cart</button>
                  </form>
                </div>
                <div class="wishlist-action">
                  <form method="post" id="wishlist-form" style="display:inline;">
                    <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                    <input type="hidden" name="wishlist_action" id="wishlist-action-field" value="<?php echo in_array($product['product_code'], $user_wishlist_product_ids) ? 'remove' : 'add'; ?>">
                    <input type="hidden" name="wishlist_qty" id="wishlist-qty-field" value="<?php echo $wishlist_qty; ?>">
                    <button type="submit" id="wishlist-btn" style="background:none;border:none;padding:0;cursor:pointer;">
                      <img
                        src="Assets/icons/<?php echo in_array($product['product_code'], $user_wishlist_product_ids) ? 'wishlist-select.svg' : 'wishlist.svg'; ?>"
                        alt="Wishlist Icon"
                        id="wishlist-icon"
                      />
                    </button>
                  </form>
                  <span class="wishlist-text" id="wishlist-text"><?php echo in_array($product['product_code'], $user_wishlist_product_ids) ? 'Remove from wishlist' : 'Add to Wish List'; ?></span>
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
          <?php if ($isLoggedIn): ?>
          <form class="review-form" method="post" action="">
            <textarea class="review-input" name="review_text" required></textarea>
            <input type="hidden" name="review_action" value="add">
            <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
            <button type="submit" class="submit-btn">Submit</button>
          </form>
          <?php if ($review_error): ?>
            <div style="color:red; margin-top:8px;"><?php echo htmlspecialchars($review_error); ?></div>
          <?php endif; ?>
          <?php else: ?>
            <div style="margin-bottom:16px; color:#a00;">You must <a href="signup.php">log in</a> to submit a review.</div>
          <?php endif; ?>

          <div class="reviews-container">
            <?php if (count($reviews) === 0): ?>
              <div style="padding:20px; color:#888;">No reviews yet.</div>
            <?php else: ?>
              <?php foreach ($reviews as $review): ?>
                <article class="review-row">
                  <div class="review-item">
                    <div class="review-content">
                      <div class="review-header">
                        <div class="review-text-container">
                          <h5 class="reviewer-name"><?php echo htmlspecialchars($review['reviewer_name']); ?></h5>
                          <p class="review-text">
                            <?php echo nl2br(htmlspecialchars($review['review_text'])); ?>
                          </p>
                        </div>
                      </div>
                      <hr class="review-divider" />
                    </div>
                    <p class="review-date">Posted on <?php echo date('F d, Y', strtotime($review['review_date'])); ?></p>
                  </div>
                </article>
              <?php endforeach; ?>
            <?php endif; ?>
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
      // Wishlist logic for product page
      window.onload = function() {
          // Quantity selector logic
          const decreaseButton = document.getElementById('decrease-quantity');
          const increaseButton = document.getElementById('increase-quantity');
          const quantityValue = document.getElementById('quantity-value');
          const wishlistQtyField = document.getElementById('wishlist-qty-field');
          const cartQtyField = document.getElementById('cart-qty-field');

          function updateWishlistQty() {
              if (wishlistQtyField && quantityValue) {
                  wishlistQtyField.value = quantityValue.textContent;
              }
              if (cartQtyField && quantityValue) {
                  cartQtyField.value = quantityValue.textContent;
              }
          }

          if (decreaseButton && increaseButton && quantityValue) {
              decreaseButton.onclick = function() {
                  let currentValue = parseInt(quantityValue.textContent);
                  if (currentValue > 1) {
                      quantityValue.textContent = currentValue - 1;
                      updateWishlistQty();
                  }
              };
              increaseButton.onclick = function() {
                  let currentValue = parseInt(quantityValue.textContent);
                  quantityValue.textContent = currentValue + 1;
                  updateWishlistQty();
              };
          }

          // Wishlist icon submits the form (toggle action before submit)
          const wishlistForm = document.getElementById('wishlist-form');
          const wishlistBtn = document.getElementById('wishlist-btn');
          const wishlistActionField = document.getElementById('wishlist-action-field');
          if (wishlistForm && wishlistBtn && wishlistActionField) {
              wishlistForm.onsubmit = function(e) {
                  // Always set the correct action before submit
                  // If currently in wishlist, remove; else add
                  <?php if (in_array($product['product_code'], $user_wishlist_product_ids)): ?>
                      wishlistActionField.value = 'remove';
                  <?php else: ?>
                      wishlistActionField.value = 'add';
                  <?php endif; ?>
                  // allow submit
              };
          }

          // Add to Cart button submits with current quantity
          const cartForm = document.getElementById('cart-form');
          const addToCartBtn = document.getElementById('add-to-cart-btn');
          if (cartForm && addToCartBtn && cartQtyField && quantityValue) {
              cartForm.onsubmit = function(e) {
                  cartQtyField.value = quantityValue.textContent;
                  // allow submit
              };
          }
      };
    </script>
  </body>
</html>
