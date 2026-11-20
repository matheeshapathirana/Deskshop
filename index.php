<?php
require_once __DIR__ . '/db.php';
session_start();
require_once 'PHP/AuthCheck.php';
auto_login_from_cookie();
$isLoggedIn = is_user_logged_in();

if (isset($_POST['wishlist_action'], $_POST['product_code'])) {
    if ($isLoggedIn) {
        $con = db_connect();
        if ($con) {
            $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
            $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '$user_email' LIMIT 1");
            $user_row = mysqli_fetch_array($user_q);
            if ($user_row) {
                $id = $user_row['id'];
                $product_code = $_POST['product_code'];
                if ($_POST['wishlist_action'] === 'add') {
                    $check = mysqli_query($con, "SELECT * FROM wishlists WHERE id=$id AND product_code='$product_code'");
                    if (!mysqli_fetch_array($check)) {
                        mysqli_query($con, "INSERT INTO wishlists (id, product_code) VALUES ($id, '$product_code')");
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

if (isset($_POST['cart_action'], $_POST['product_code']) && $_POST['cart_action'] === 'add') {
    if ($isLoggedIn) {
        $con = db_connect();
        if ($con) {
            $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
            $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '$user_email' LIMIT 1");
            $user_row = mysqli_fetch_array($user_q);
            if ($user_row) {
                $id = $user_row['id'];
                $product_code = $_POST['product_code'];
                $qty = isset($_POST['cart_qty']) ? max(1, intval($_POST['cart_qty'])) : 1;
                // Check if already in cart
                $check = mysqli_query($con, "SELECT * FROM cart WHERE id=$id AND product_code='$product_code'");
                if (!mysqli_fetch_array($check)) {
                    mysqli_query($con, "INSERT INTO cart (id, product_code, quantity) VALUES ($id, '$product_code', $qty)");
                } else {
                    mysqli_query($con, "UPDATE cart SET quantity=$qty WHERE id=$id AND product_code='$product_code'");
                }
            }
            mysqli_close($con);
        }
    }
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

$user_wishlist_product_ids = [];
if ($isLoggedIn) {
    $con = db_connect();
    if ($con) {
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '$user_email' LIMIT 1");
        $user_row = mysqli_fetch_array($user_q);
        if ($user_row) {
            $id = $user_row['id'];
            $wl_q = mysqli_query($con, "SELECT product_code FROM wishlists WHERE id = $id");
            while ($wl_row = mysqli_fetch_array($wl_q)) {
                $user_wishlist_product_ids[] = $wl_row['product_code'];
            }
        }
        mysqli_close($con);
    }
}

// Newsletter handler
if (isset($_POST['newsletter-submit'])) {
    $email = $_POST['email'];

    $con = db_connect();
    if (!$con) {
        $newsletter_message = "Connection failed: " . mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM NewsletterSubscriptions WHERE EmailAddress = '$email'";
        $result = mysqli_query($con, $sql);

        if (mysqli_fetch_array($result)) {
            $newsletter_message = "Email already subscribed!";
        } else {
            $sql = "INSERT INTO NewsletterSubscriptions (EmailAddress) VALUES ('$email')";
            $insert_result = mysqli_query($con, $sql);

            if ($insert_result) {
                $newsletter_message = "Subscription successful!";
            } else {
                $newsletter_message = "Error: " . mysqli_error($con);
            }
        }
        mysqli_close($con);
    }
}

$con = db_connect();
$hot_deals = [];
$featured_products = [];
if ($con) {
    $sql_hot = "SELECT product_code, name, price, image_url FROM products ORDER BY price ASC, product_code ASC LIMIT 6";
    $result_hot = mysqli_query($con, $sql_hot);
    if ($result_hot) {
        while ($row = mysqli_fetch_array($result_hot)) {
            $hot_deals[] = $row;
        }
    }
    $sql_feat = "SELECT product_code, name, price, image_url FROM products ORDER BY product_code DESC LIMIT 6";
    $result_feat = mysqli_query($con, $sql_feat);
    if ($result_feat) {
        while ($row = mysqli_fetch_array($result_feat)) {
            $featured_products[] = $row;
        }
    }
    mysqli_close($con);
}
?>
<!doctype html>
<html>
<head>
    <title>DeskShop Home</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
    <link rel="stylesheet" type="text/css" href="index.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
        .product-card {
            background: #fff !important;
        }
        .product-image-container {
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            position: relative;
        }
        .wishlist-icon-btn {
            position: absolute;
            top: 25px;
            right: 25px;
            z-index: 2;
        }
        div.product-row:nth-child(2) {
            margin-top: 40px;
        }
        .product-title a {
            text-decoration: none;
            color: inherit !important;
        }
    </style>
</head>
<body>
    <div class="homepage">
        <section class="hero-section">
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
            <div class="hero-content">
                <h2 class="hero-title">Your Learning<br />Essentials</h2>
                <p class="hero-description">Discover essential tools designed to enhance your learning experience effortlessly.</p>
                <?php if ($isLoggedIn): ?>
                    <button class="hero-cta" onclick="window.location.href='profile.php'">Profile</button>
                <?php else: ?>
                    <button class="hero-cta" onclick="window.location.href='signup.php'">Sign up</button>
                <?php endif; ?>
            </div>
        </section>

        <h2 class="section-title">Shop by Categories</h2>
        <div class="grid-container">
            <div class="grid-item small-width">
            <img src="Assets/writing-tools.png" alt="Writing Tools" onclick="window.location.href='shop.php?category=writing-tools'">
            <div class="overlay">
            <h3>Writing Tools</h3>
            </div>
        </div>
        <div class="grid-item large-width">
            <img src="Assets/desk-accessories.png" alt="Desk Accessories" onclick="window.location.href='shop.php?category=desk-accessories'">
            <div class="overlay">
            <h3>Desk Accessories</h3>
            </div>
        </div>
        <div class="grid-item large-width">
            <img src="Assets/office-essentials.png" alt="Office Essentials" onclick="window.location.href='shop.php?category=office-items'">
            <div class="overlay">
            <h3>Office</h3>
            <h3>Essentials</h3>
            </div>
        </div>
        <div class="grid-item small-width">
            <img src="Assets/art-supplies.png" alt="Art Supplies" onclick="window.location.href='shop.php?category=art-supplies'">
            <div class="overlay">
            <h3>Art Supplies</h3>
            </div>
        </div>
        </div>

        <?php if (!isset($hot_deals)) $hot_deals = []; ?>
        <section class="hot-deals-section">
        <div class="hot-deals-content">
            <h2 class="hot-deals-title">Hot Deals</h2>
            <div class="product-grid">
                <?php for ($row = 0; $row < 2; $row++): ?>
                <div class="product-row">
                    <?php for ($col = 0; $col < 3; $col++):
                        $idx = $row * 3 + $col;
                        if (!isset($hot_deals[$idx])) continue;
                        $product = $hot_deals[$idx];
                        $is_in_wishlist = in_array($product['product_code'], $user_wishlist_product_ids);
                    ?>
                    <div class="product-column">
                        <div class="product-card">
                            <div class="product-image-container">
                                <form method="post" style="display:inline;">
                                    <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                    <input type="hidden" name="wishlist_action" value="<?php echo $is_in_wishlist ? 'remove' : 'add'; ?>">
                                    <button type="submit" class="wishlist-icon-btn" style="background:none;border:none;padding:0;cursor:pointer;">
                                        <img src="Assets/icons/<?php echo $is_in_wishlist ? 'wishlist-select.svg' : 'wishlist.svg'; ?>" alt="Wishlist Icon" class="wishlist-toggle-img<?php echo $is_in_wishlist ? ' selected' : ''; ?>" />
                                    </button>
                                </form>
                                <img loading="lazy" src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                            </div>
                            <div class="product-details">
                                <h3 class="product-title">
                                    <a href="product.php?code=<?php echo urlencode($product['product_code']); ?>">
                                        <?php echo htmlspecialchars($product['name']); ?>
                                    </a>
                                </h3>
                                <div class="product-actions">
                                    <?php if ($isLoggedIn): ?>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                            <input type="hidden" name="cart_action" value="add">
                                            <input type="hidden" name="cart_qty" value="1">
                                            <button type="submit" class="add-to-cart-btn">Add to cart</button>
                                        </form>
                                    <?php else: ?>
                                        <button class="add-to-cart-btn" onclick="window.location.href='signup.php'">Add to cart</button>
                                    <?php endif; ?>
                                    <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
                <?php endfor; ?>
            </div>
        </div>
        </section>

        <section class="banner-section">
        <div class="banner-row">
            <div class="banner-column">
            <img loading="lazy" src="Assets/banner-pens.png" class="banner-image" alt="Classroom essentials" />
            </div>
            <div class="banner-column">
            <div class="banner-content">
                <h2 class="banner-title">Everything for the<br />Classroom</h2>
                <p class="banner-description">Find all the essentials to create a perfect learning environment.</p>
                <button class="banner-cta" onclick="window.location.href='shop.php'">Shop Now</button>
            </div>
            </div>
        </div>
        </section>

        <h2 class="section-title">Featured Products</h2>
        <section class="featured-products-section">
            <div class="product-row">
            <?php foreach (array_slice($featured_products, 0, 3) as $product):
                $is_in_wishlist = in_array($product['product_code'], $user_wishlist_product_ids);
            ?>
                <div class="product-column">
                    <div class="product-card">
                        <div class="product-image-container">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                <input type="hidden" name="wishlist_action" value="<?php echo $is_in_wishlist ? 'remove' : 'add'; ?>">
                                <button type="submit" class="wishlist-icon-btn" style="background:none;border:none;padding:0;cursor:pointer;">
                                    <img src="Assets/icons/<?php echo $is_in_wishlist ? 'wishlist-select.svg' : 'wishlist.svg'; ?>" alt="Wishlist Icon" class="wishlist-toggle-img<?php echo $is_in_wishlist ? ' selected' : ''; ?>" />
                                </button>
                            </form>
                            <img loading="lazy" src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        </div>
                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="product.php?code=<?php echo urlencode($product['product_code']); ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            <div class="product-actions">
                                <?php if ($isLoggedIn): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                        <input type="hidden" name="cart_action" value="add">
                                        <input type="hidden" name="cart_qty" value="1">
                                        <button type="submit" class="add-to-cart-btn">Add to cart</button>
                                    </form>
                                <?php else: ?>
                                    <button class="add-to-cart-btn" onclick="window.location.href='signup.php'">Add to cart</button>
                                <?php endif; ?>
                                <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>

        <section class="featured-products-section">
            <div class="product-row">
            <?php foreach (array_slice($featured_products, 3, 3) as $product):
                $is_in_wishlist = in_array($product['product_code'], $user_wishlist_product_ids);
            ?>
                <div class="product-column">
                    <div class="product-card">
                        <div class="product-image-container">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                <input type="hidden" name="wishlist_action" value="<?php echo $is_in_wishlist ? 'remove' : 'add'; ?>">
                                <button type="submit" class="wishlist-icon-btn" style="background:none;border:none;padding:0;cursor:pointer;">
                                    <img src="Assets/icons/<?php echo $is_in_wishlist ? 'wishlist-select.svg' : 'wishlist.svg'; ?>" alt="Wishlist Icon" class="wishlist-toggle-img<?php echo $is_in_wishlist ? ' selected' : ''; ?>" />
                                </button>
                            </form>
                            <img loading="lazy" src="<?php echo htmlspecialchars($product['image_url']); ?>" class="product-image" alt="<?php echo htmlspecialchars($product['name']); ?>" />
                        </div>
                        <div class="product-details">
                            <h3 class="product-title">
                                <a href="product.php?code=<?php echo urlencode($product['product_code']); ?>">
                                    <?php echo htmlspecialchars($product['name']); ?>
                                </a>
                            </h3>
                            <div class="product-actions">
                                <?php if ($isLoggedIn): ?>
                                    <form method="post" style="display:inline;">
                                        <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                                        <input type="hidden" name="cart_action" value="add">
                                        <input type="hidden" name="cart_qty" value="1">
                                        <button type="submit" class="add-to-cart-btn">Add to cart</button>
                                    </form>
                                <?php else: ?>
                                    <button class="add-to-cart-btn" onclick="window.location.href='signup.php'">Add to cart</button>
                                <?php endif; ?>
                                <span class="product-price">Rs. <?php echo number_format($product['price'], 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            </div>
        </section>

        <section class="about-section">
        <div class="about-content">
        <div class="about-row">
        <div class="about-columns">
            <div class="about-text-column">
            <div class="about-text-content">
                <h2 class="about-title">About Us</h2>
                <p class="about-description">
                Welcome to DeskShop!<br />
                We provide high-quality school and office supplies to support students, teachers, and professionals. Our mission is to simplify your shopping experience with reliable products and exceptional service. Whether for classrooms or workspaces, we've got you covered!
                </p>
            </div>
            </div>
            <div class="about-image-column">
            <img loading="lazy" src="Assets/about-us-penholder.png" class="about-image" alt="About us image 1" />
            </div>
            <div class="about-image-column-2">
            <img loading="lazy" src="Assets/about-us-paintbrush.png" class="about-image-2" alt="About us image 2" />
            </div>
        </div>
        </div>
        <div class="workspace-section">
        <div class="workspace-row">
            <div class="workspace-image-column">
            <img loading="lazy" src="Assets/banner-pens.png" class="workspace-image" alt="Workspace essentials" />
            </div>
            <div class="workspace-content-column">
            <div class="workspace-content">
                <h2 class="workspace-title">Streamline Your Workspace</h2>
                <p class="workspace-description">Upgrade your workspace with tools and essentials for efficiency.</p>
                <button class="workspace-cta" onclick="window.location.href='shop.php'">Order Now</button>
            </div>
            </div>
        </div>
        </div>
        <h2 class="faq-title">Do You Have Any Questions?<br />Get Help From Us</h2>
        <div class="faq-link">
        <img loading="lazy" src="Assets/icons/verified-email-contact-us.png" class="faq-icon" alt="FAQ icon" />
        <span class="faq-text">Browse our FAQ</span>
        </div>
        <form class="newsletter-form" method="POST">
            <div class="newsletter-input-wrapper">
            <img src="Assets/icons/email-contact-us.png" 
                class="newsletter-icon" alt="Email icon" />
            <input type="email" name="email" class="newsletter-input" placeholder="Enter your email address..." aria-label="Enter your email address" />
        </div>
        <button type="submit" name="newsletter-submit" class="newsletter-submit">Submit</button>
        </form>
        <?php if (isset($newsletter_message)): ?>
            <p class="newsletter-message"><?php echo $newsletter_message; ?></p>
        <?php endif; ?> 
    </div>
    </section>
    </div>

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
                            email@gmail.com
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
