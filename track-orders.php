<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    echo "<script>alert('Please sign in to access your orders.'); window.location.href='signin.php';</script>";
    exit();
}
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

// Fetch user's orders (using new orders table structure)
$user_orders = [];
if ($isLoggedIn) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT ID FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
        $user_row = mysqli_fetch_assoc($user_q);
        if ($user_row) {
            $id = $user_row['ID'];
            // orders table: order_id, user_id, product_code, quantity, status
            $order_q = mysqli_query($con, "SELECT product_code, quantity, status FROM orders WHERE user_id = $id");
            while ($order_row = mysqli_fetch_assoc($order_q)) {
                $user_orders[] = [
                    'product_code' => $order_row['product_code'],
                    'quantity' => $order_row['quantity'],
                    'status' => $order_row['status']
                ];
            }
        }
        mysqli_close($con);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Track Orders - DeskShop</title>
  <link rel="stylesheet" href="track-orders.css">
  <link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
  <style>
    .order-status {
      font-weight: 600;
      font-size: 16px;
      border-radius: 8px;
      padding: 6px 24px;
      display: inline-block;
      margin-left: 20px;
      margin-top: 10px;
    }
    .status-processing { background: #ffe97a; color: #2b1b12; }
    .status-shipping { background: #4285f4; color: #fff; }
    .status-delivered { background: #2ecc40; color: #fff; }
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
        <?php if ($isLoggedIn): ?>
        <button class="signup-btn" onclick="window.location.href='profile.php'">Profile</button>
        <?php else: ?>
        <button class="signup-btn" onclick="window.location.href='signup.php'">Sign up</button>
        <?php endif; ?>
      </div>
    </header>

    <main class="wishlist-content">
      <div class="back-navigation">
        <img
          src="Assets/Arrows/black-back-arrow.png"
          alt="Back arrow"
          class="back-icon"
        />
        <a href="shop.php" class="back-link">Continue Shopping</a>
      </div>

      <h1 class="wishlist-title">Track Orders</h1>

      <section class="product-list">
        <?php if (empty($user_orders)): ?>
          <div style="padding:40px; text-align:center; color:#888; font-size:20px;">
            You have no orders yet.
          </div>
        <?php else: ?>
          <?php
          $con = mysqli_connect("localhost", "root", "", "deskshop");
          foreach ($user_orders as $item):
              $code = mysqli_real_escape_string($con, $item['product_code']);
              $p_q = mysqli_query($con, "SELECT product_code, name, price, image_url FROM products WHERE product_code='$code' LIMIT 1");
              $product = mysqli_fetch_assoc($p_q);
              if (!$product) continue;
              // Status styling
              $status = strtolower($item['status']);
              $statusClass = '';
              $statusLabel = '';
              if ($status === 'processing') {
                  $statusClass = 'status-processing';
                  $statusLabel = 'Processing';
              } elseif ($status === 'shipping') {
                  $statusClass = 'status-shipping';
                  $statusLabel = 'Shipping';
              } elseif ($status === 'delivered') {
                  $statusClass = 'status-delivered';
                  $statusLabel = 'Delivered';
              } else {
                  $statusClass = 'status-processing';
                  $statusLabel = ucfirst($status);
              }
          ?>
          <article class="product-card">
            <div class="product-layout">
              <div class="product-image-container">
                <img
                  src="<?php echo htmlspecialchars($product['image_url']); ?>"
                  alt="<?php echo htmlspecialchars($product['name']); ?>"
                  class="product-image"
                />
              </div>
              <div class="product-details">
                <div class="product-info">
                  <div class="product-text">
                    <h2 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h2>
                    <p class="product-quantity">Qty : <?php echo intval($item['quantity']); ?></p>
                  </div>
                  <span class="order-status <?php echo $statusClass; ?>"><?php echo $statusLabel; ?></span>
                </div>
                <p class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></p>
              </div>
            </div>
          </article>
          <?php endforeach;
          if ($con) mysqli_close($con); ?>
        <?php endif; ?>
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