<?php
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

if (isset($_POST['wishlist_action'], $_POST['product_code'])) {
    if ($isLoggedIn) {
        $con = mysqli_connect("localhost", "root", "", "deskshop");
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

// --- Add to Cart handler ---
if (isset($_POST['cart_action'], $_POST['product_code']) && $_POST['cart_action'] === 'add') {
    if ($isLoggedIn) {
        $con = mysqli_connect("localhost", "root", "", "deskshop");
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
    header("Location: " . strtok($_SERVER["REQUEST_URI"], '?') . (isset($_GET['code']) ? '?code=' . urlencode($_GET['code']) : ''));
    exit;
}

$user_wishlist_product_ids = [];
if ($isLoggedIn) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
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

$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch products with their categories
$products = [];
$sql = "SELECT p.product_code, p.name, p.price, p.image_url, GROUP_CONCAT(c.category_name SEPARATOR ' ') as categories
        FROM products p
        LEFT JOIN product_categories pc ON p.product_code = pc.product_code
        LEFT JOIN categories c ON pc.category_id = c.category_id
        GROUP BY p.product_code";
$result = mysqli_query($con, $sql);
if ($result) {
    while ($row = mysqli_fetch_array($result)) {
        $products[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Products - DeskShop</title>
    <link rel="stylesheet" href="shop.css">
    <link rel="icon" type="image/x-icon" href="Assets/icons/favicon.ico?v=2">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Mynerve&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap');
        .product-title a{
            text-decoration: none;
            color: inherit;
        }
        .wishlist-icon-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 2;
        }
        .wishlist-icon-btn img.selected {
            filter: drop-shadow(0 0 2px #e74c3c);
        }
        .wishlist-icon {
            display: none;
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
        <div class="search-container">
            <div class="search-bar">
                <img src="Assets/icons/search.svg" alt="Search" class="search-icon" />
                <input type="text" placeholder="Search for products..." class="search-input" />
            </div>
        </div>

        <section class="banner-section slideshow-container">
            <div class="mySlides fade">
                <img src="Assets/Offers Banners/crop banner 3.png" alt="Banner 1" class="banner-image" />
            </div>
            <div class="mySlides fade">
                <img src="Assets/Offers Banners/crop banner 2.png" alt="Banner 2" class="banner-image" />
            </div>
            <div class="mySlides fade">
                <img src="Assets/Offers Banners/crop banner 1.png" alt="Banner 3" class="banner-image" />
            </div>
        </section>

        <section class="products-section">
            <h2 class="section-title">All Products</h2>
            <div class="filter-container">
                <div class="sort-dropdown">
                    <button class="sort-dropdown-button">Sort by category</button>
                    <div class="sort-dropdown-content">
                        <button data-category="default" id="default-button">All Items</button>
                        <button data-category="office-items" id="office-items-button">Office Items</button>
                        <button data-category="school-items" id="school-items-button">School Items</button>
                        <button data-category="books" id="books-button">Books</button>
                        <button data-category="writing-tools" id="writing-tools-button">Writing Tools</button>
                        <button data-category="colour-products" id="colour-products-button">Colour Products</button>
                        <button data-category="desk-accessories" id="desk-accessories-button">Desk Essentials</button>
                    </div>
                </div>

                <div class="sort-dropdown">
                    <button class="sort-dropdown-button">Sort by</button>
                    <div class="sort-dropdown-content">
                        <button data-sort="az">A-Z</button>
                        <button data-sort="za">Z-A</button>
                        <button data-sort="low-high">Price: Low to High</button>
                        <button data-sort="high-low">Price: High to Low</button>
                    </div>
                </div>
            </div>

            <div class="product-grid">
                <?php foreach ($products as $product): 
                    $is_in_wishlist = in_array($product['product_code'], $user_wishlist_product_ids);
                ?>
                <article class="product-card" data-category="<?php echo htmlspecialchars($product['categories']); ?>">
                    <div class="product-image-container" style="position:relative;">
                        <!-- Wishlist form button -->
                        <?php if ($isLoggedIn): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="product_code" value="<?php echo htmlspecialchars($product['product_code']); ?>">
                            <input type="hidden" name="wishlist_action" value="<?php echo $is_in_wishlist ? 'remove' : 'add'; ?>">
                            <button type="submit" class="wishlist-icon-btn">
                                <img src="Assets/icons/<?php echo $is_in_wishlist ? 'wishlist-select.svg' : 'wishlist.svg'; ?>" alt="Wishlist Icon" class="<?php echo $is_in_wishlist ? 'selected' : ''; ?>" />
                            </button>
                        </form>
                        <?php else: ?>
                        <button class="wishlist-icon-btn" title="Sign in to use wishlist" onclick="window.location.href='signup.php'">
                            <img src="Assets/icons/wishlist.svg" alt="Wishlist Icon" />
                        </button>
                        <?php endif; ?>
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
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
                </article>
                <?php endforeach; ?>
            </div>

            <nav class="pagination" aria-label="Page navigation">
                <button class="page-btn next" id="load-more-btn">Load More</button>
                <span id="end-message" style="display:none; font-weight:600; color:#555; margin-top:1em;">You came to the end of the page</span>
            </nav>
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

    <script>
    window.onload = function() {
        showSlides();
    };
    let slideIndex = 0;
    function showSlides() {
        let i;
        let slides = document.getElementsByClassName("mySlides");

        for (i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;

        if (slideIndex > slides.length) {
            slideIndex = 1;
        }

        slides[slideIndex - 1].style.display = "block";
        setTimeout(showSlides, 5000);
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.sort-dropdown-content button[data-category]').forEach(button => {
            button.addEventListener('click', function () {
                const selectedCategory = this.getAttribute('data-category');
                const products = document.querySelectorAll('.product-card');
                products.forEach(product => {
                    const productCategories = product.getAttribute('data-category').split(' ');
                    if (selectedCategory === 'default' || productCategories.includes(selectedCategory)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.querySelector('.search-input');
        const products = document.querySelectorAll('.product-card');

        searchInput.addEventListener('input', function () {
            const searchTerm = searchInput.value.toLowerCase();

            products.forEach(product => {
                const productTitle = product.querySelector('.product-title').textContent.toLowerCase();

                if (productTitle.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        const category = urlParams.get('category');

        if (category) {
            const button = document.querySelector(`.sort-dropdown-content button[data-category="${category}"]`);
            if (button) {
                button.click();
            }
        }
    });

    // Pagination logic for "Load More"
    document.addEventListener('DOMContentLoaded', function () {
        const products = Array.from(document.querySelectorAll('.product-card'));
        const loadMoreBtn = document.getElementById('load-more-btn');
        const endMessage = document.getElementById('end-message');
        const PRODUCTS_PER_PAGE = 9;
        let currentIndex = 0;

        function showProducts() {
            products.forEach((product, idx) => {
                product.style.display = (idx < currentIndex) ? 'block' : 'none';
            });
        }

        function loadMore() {
            const prevIndex = currentIndex;
            currentIndex += PRODUCTS_PER_PAGE;
            products.forEach((product, idx) => {
                if (idx < currentIndex) product.style.display = 'block';
            });
            if (currentIndex >= products.length) {
                loadMoreBtn.style.display = 'none';
                endMessage.style.display = 'inline-block';
            }
        }

        products.forEach((product, idx) => {
            product.style.display = (idx < PRODUCTS_PER_PAGE) ? 'block' : 'none';
        });
        currentIndex = PRODUCTS_PER_PAGE;
        if (products.length <= PRODUCTS_PER_PAGE) {
            loadMoreBtn.style.display = 'none';
            endMessage.style.display = 'inline-block';
        }

        loadMoreBtn.addEventListener('click', loadMore);

        function resetPagination() {
            const visibleProducts = products.filter(p => p.style.display !== 'none');
            visibleProducts.forEach((product, idx) => {
                product.style.display = (idx < PRODUCTS_PER_PAGE) ? 'block' : 'none';
            });
            currentIndex = PRODUCTS_PER_PAGE;
            if (visibleProducts.length <= PRODUCTS_PER_PAGE) {
                loadMoreBtn.style.display = 'none';
                endMessage.style.display = 'inline-block';
            } else {
                loadMoreBtn.style.display = 'inline-block';
                endMessage.style.display = 'none';
            }
        }

        document.querySelectorAll('.sort-dropdown-content button[data-category]').forEach(button => {
            button.addEventListener('click', function () {
                const selectedCategory = this.getAttribute('data-category');
                products.forEach(product => {
                    const productCategories = product.getAttribute('data-category').split(' ');
                    if (selectedCategory === 'default' || productCategories.includes(selectedCategory)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
                resetPagination();
            });
        });

        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const searchTerm = searchInput.value.toLowerCase();
                products.forEach(product => {
                    const productTitle = product.querySelector('.product-title').textContent.toLowerCase();
                    if (productTitle.includes(searchTerm)) {
                        product.style.display = 'block';
                    } else {
                        product.style.display = 'none';
                    }
                });
                resetPagination();
            });
        }

        // --- SORT BY FUNCTIONALITY ---
        const sortButtons = document.querySelectorAll('.sort-dropdown-content button[data-sort]');
        const productGrid = document.querySelector('.product-grid');
        sortButtons.forEach(button => {
            button.addEventListener('click', function () {
                let sortedProducts = [...products];
                const sortType = this.getAttribute('data-sort');
                if (sortType === 'az') {
                    sortedProducts.sort((a, b) => {
                        const nameA = a.querySelector('.product-title').textContent.trim().toLowerCase();
                        const nameB = b.querySelector('.product-title').textContent.trim().toLowerCase();
                        return nameA.localeCompare(nameB);
                    });
                } else if (sortType === 'za') {
                    sortedProducts.sort((a, b) => {
                        const nameA = a.querySelector('.product-title').textContent.trim().toLowerCase();
                        const nameB = b.querySelector('.product-title').textContent.trim().toLowerCase();
                        return nameB.localeCompare(nameA);
                    });
                } else if (sortType === 'low-high') {
                    sortedProducts.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector('.product-price').textContent.replace(/[^\d.]/g, ''));
                        const priceB = parseFloat(b.querySelector('.product-price').textContent.replace(/[^\d.]/g, ''));
                        return priceA - priceB;
                    });
                } else if (sortType === 'high-low') {
                    sortedProducts.sort((a, b) => {
                        const priceA = parseFloat(a.querySelector('.product-price').textContent.replace(/[^\d.]/g, ''));
                        const priceB = parseFloat(b.querySelector('.product-price').textContent.replace(/[^\d.]/g, ''));
                        return priceB - priceA;
                    });
                }
                sortedProducts.forEach(product => productGrid.appendChild(product));
                resetPagination();
            });
        });
    });
    </script>
</body>
</html>