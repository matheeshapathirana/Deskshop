<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    echo "<script>alert('Please sign in to access your cart.'); window.location.href='signin.php';</script>";
    exit();
}
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

$user_cart = [];
$total = 0;
$delivery_fee = 150;
$grand_total = 0;

if ($isLoggedIn) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '".mysqli_real_escape_string($con, $user_email)."' LIMIT 1");
        $user_row = mysqli_fetch_assoc($user_q);
        if ($user_row) {
            $id = $user_row['id'];
            // Handle checkout POST
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkout'])) {
                // Get form data
                $contact_number = isset($_POST['phone']) ? intval($_POST['phone']) : '';
                $address = isset($_POST['address']) ? mysqli_real_escape_string($con, $_POST['address']) : '';
                $city = isset($_POST['city']) ? mysqli_real_escape_string($con, $_POST['city']) : '';
                $postalcode = isset($_POST['postal']) ? intval($_POST['postal']) : '';
                $paymentmethod = isset($_POST['payment']) ? mysqli_real_escape_string($con, $_POST['payment']) : '';

                // Fetch cart items again for safety
                $cart_q = mysqli_query($con, "
                    SELECT c.cart_id, c.product_code, c.quantity
                    FROM cart c
                    WHERE c.ID = $id
                ");
                $orders_inserted = 0;
                while ($cart_row = mysqli_fetch_assoc($cart_q)) {
                    $product_code = mysqli_real_escape_string($con, $cart_row['product_code']);
                    $quantity = (int)$cart_row['quantity'];
                    $insert_q = mysqli_query($con, "
                        INSERT INTO orders (user_id, product_code, quantity, date, contact_number, address, city, postalcode, paymentmethod)
                        VALUES ($id, '$product_code', $quantity, NOW(), $contact_number, '$address', '$city', $postalcode, '$paymentmethod')
                    ");
                    if ($insert_q) $orders_inserted++;
                }
                // Clear user's cart
                mysqli_query($con, "DELETE FROM cart WHERE ID = $id");
                // Show success message and reload
                echo "<script>alert('Order placed successfully!'); window.location.href='cart.php';</script>";
                exit();
            }

            $cart_q = mysqli_query($con, "
                SELECT c.cart_id, c.product_code, c.quantity, p.name AS product_name, p.price, p.image_url
                FROM cart c
                JOIN products p ON c.product_code = p.product_code
                WHERE c.ID = $id
            ");
            while ($cart_row = mysqli_fetch_assoc($cart_q)) {
                $user_cart[] = [
                    'cart_id' => $cart_row['cart_id'],
                    'product_code' => $cart_row['product_code'],
                    'product_name' => $cart_row['product_name'],
                    'quantity' => $cart_row['quantity'],
                    'price' => $cart_row['price'],
                    'image_path' => $cart_row['image_url']
                ];
                $total += $cart_row['price'] * $cart_row['quantity'];
            }
        }
        mysqli_close($con);
    }
}
$grand_total = $total + $delivery_fee;
?>
<html>
    <head>
        <title>DeskShop - Cart</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="cart.css">
        <link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
        <style>
          @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
          .footer{
            top: 120px;
          }
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
                  <?php if (empty($user_cart)): ?>
                      <p>Your cart is empty.</p>
                  <?php else: ?>
                      <?php foreach ($user_cart as $item): ?>
                          <article class="cart-item">
                              <img
                                  src="<?php echo htmlspecialchars($item['image_path']); ?>"
                                  alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                  class="product-image"
                              />
                              <div class="product-details">
                                  <div class="product-info">
                                      <div class="product-text">
                                          <h3 class="product-name"><?php echo htmlspecialchars($item['product_name']); ?></h3>
                                          <p class="product-quantity">Qty : <?php echo $item['quantity']; ?></p>
                                      </div>
                                      <form method="post" action="removeFromCart.php" style="display:inline;">
                                          <input type="hidden" name="cart_id" value="<?php echo $item['cart_id']; ?>">
                                          <button class="remove-item" type="submit">
                                              <img
                                                  src="Assets/remove-from-cart-cross.svg"
                                                  alt="Remove item"
                                                  class="remove-icon"
                                              />
                                          </button>
                                      </form>
                                  </div>
                                  <p class="product-price">Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></p>
                              </div>
                          </article>
                      <?php endforeach; ?>
                  <?php endif; ?>
                </div>
              </section>
          
              <section class="payment-section">
                <!-- Change form to POST and submit to same page -->
                <form class="payment-form" method="post" action="cart.php">
                  <h2 class="form-title">Payment Details</h2>
          
                  <div class="form-group">
                    <label for="fullname">Full Name</label>
                    <input
                      type="text"
                      id="fullname"
                      class="form-input"
                      placeholder="Name"
                      required
                      name="fullname"
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
                      name="phone"
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
                      name="address"
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
                        name="city"
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
                        name="postal"
                      />
                    </div>
                  </div>
          
                  <div class="payment-divider"></div>
          
                  <div class="payment-method">
                    <input type="radio" id="cash-on-delivery" name="payment" class="checkbox" value="Cash On Delivery" required/>
                    <label for="cash-on-delivery" class="method-label">Cash On Delivery</label>

                    <input type="radio" id="bank-transfer" name="payment" class="checkbox" value="Bank Transfer" required/>
                    <label for="bank-transfer" class="method-label">Bank Transfer</label>
                  </div>

                  <!-- Bank Transfer Account Details -->
                  <div id="bank-details" style="display:none; margin: 15px 0; padding: 15px; border: 1px solid #ccc; border-radius: 8px; background:rgb(0, 0, 0);">
                    <strong>Bank Transfer Details:</strong><br>
                    <span>Account Name: <b>DeskShop Pvt Ltd</b></span><br>
                    <span>Account Number: <b>1234567890</b></span><br>
                    <span>Bank Name: <b>National Savings Bank</b></span><br>
                    <span>Branch: <b>Homagama Branch</b></span>
                  </div>
          
                  <div class="order-summary">
                    <div class="summary-row">
                      <span class="summary-label">Subtotal</span>
                      <span class="summary-value">Rs. <?php echo number_format($total, 2); ?></span>
                    </div>
                    <div class="summary-row">
                      <span class="summary-label">Delivery Fee</span>
                      <span class="summary-value">Rs. <?php echo number_format($delivery_fee, 2); ?></span>
                    </div>
                    <div class="summary-row">
                      <span class="summary-label">Total (Tax incl.)</span>
                      <span class="summary-value">Rs. <?php echo number_format($grand_total, 2); ?></span>
                    </div>
                  </div>
          
                  <button type="submit" class="checkout-button" name="checkout" value="1">
                    <span class="total-amount">Rs. <?php echo number_format($grand_total, 2); ?></span>
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
                <script>
                  // Show bank details if "Bank Transfer" is selected
                  document.addEventListener('DOMContentLoaded', function() {
                    const bankRadio = document.getElementById('bank-transfer');
                    const codRadio = document.getElementById('cash-on-delivery');
                    const bankDetails = document.getElementById('bank-details');
                    function toggleBankDetails() {
                      if (bankRadio.checked) {
                        bankDetails.style.display = 'block';
                      } else {
                        bankDetails.style.display = 'none';
                      }
                    }
                    bankRadio.addEventListener('change', toggleBankDetails);
                    codRadio.addEventListener('change', toggleBankDetails);
                    // On page load, check if bank transfer is pre-selected
                    toggleBankDetails();
                  });
                </script>
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
