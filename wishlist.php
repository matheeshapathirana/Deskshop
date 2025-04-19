<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    echo "<script>alert('Please sign in to access your wish list.'); window.location.href='signin.php';</script>";
    exit();
}
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
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Fetch user's wishlist product_codes and quantities
$user_wishlist = [];
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
                $user_wishlist[] = [
                    'product_code' => $wl_row['product_code'],
                    'quantity' => $wl_row['quantity']
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
  <title>Wishlist - DeskShop</title>
  <link rel="stylesheet" href="wishlist.css">
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
        <a href="shop.php" class="back-link">Continue Shoping</a>
      </div>
  
      <h1 class="wishlist-title">Wish List</h1>
  
      <section class="product-list">
        <?php if (empty($user_wishlist)): ?>
          <div style="padding:40px; text-align:center; color:#888; font-size:20px;">
            Your wishlist is empty.
          </div>
        <?php else: ?>
          <?php
          // Fetch product details for each wishlist item
          $con = mysqli_connect("localhost", "root", "", "deskshop");
          foreach ($user_wishlist as $item):
              $code = mysqli_real_escape_string($con, $item['product_code']);
              $p_q = mysqli_query($con, "SELECT product_code, name, price, image_url FROM products WHERE product_code='$code' LIMIT 1");
              $product = mysqli_fetch_assoc($p_q);
              if (!$product) continue;
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
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                    <input type="hidden" name="wishlist_action" value="remove">
                    <button class="remove-button" type="submit" title="Remove from wishlist">
                      <img
                        src="Assets/remove-from-cart-cross.svg"
                        alt="Remove item"
                        class="remove-icon"
                      />
                    </button>
                  </form>
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
