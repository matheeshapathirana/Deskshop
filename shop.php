<?php
session_start();
$isLoggedIn = isset($_SESSION['email']) || isset($_COOKIE['user_email']);

// Database connection
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
    while ($row = mysqli_fetch_assoc($result)) {
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
                <?php foreach ($products as $product): ?>
                <article class="product-card" data-category="<?php echo htmlspecialchars($product['categories']); ?>">
                    <div class="product-image-container">
                        <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image" />
                        <div class="wishlist-icon">
                            <img src="Assets/icons/wishlist.svg" alt="Favorite" class="favorite-icon" />
                        </div>
                    </div>
                    <div class="product-details">
                        <h3 class="product-title">
                            <a href="product.php?code=<?php echo urlencode($product['product_code']); ?>">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </a>
                        </h3>
                        <div class="product-actions">
                            <button class="add-to-cart-btn">Add to cart</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const wishlistIcons = document.querySelectorAll('.wishlist-icon img');
            wishlistIcons.forEach(icon => {
                icon.addEventListener('click', function() {
                    if (this.src.includes('wishlist.svg')) {
                        this.src = 'Assets/icons/wishlist-select.svg';
                    } else {
                        this.src = 'Assets/icons/wishlist.svg';
                    }
                    this.classList.add('animate');
                    setTimeout(() => {
                        this.classList.remove('animate');
                    }, 300);
                });
            });
        });
    </script>
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


    document.querySelectorAll('.sort-dropdown-content button').forEach(button => {
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

        // Initial display
        products.forEach((product, idx) => {
            product.style.display = (idx < PRODUCTS_PER_PAGE) ? 'block' : 'none';
        });
        currentIndex = PRODUCTS_PER_PAGE;
        if (products.length <= PRODUCTS_PER_PAGE) {
            loadMoreBtn.style.display = 'none';
            endMessage.style.display = 'inline-block';
        }

        loadMoreBtn.addEventListener('click', loadMore);

        // Integrate with category and search filters
        function resetPagination() {
            // Only visible products are paginated
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

        // Patch category filter
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

        // Patch search filter
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
    });
    </script>
</body>
</html>