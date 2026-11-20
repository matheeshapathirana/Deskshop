-- DeskShop schema (reconstructed from the queries used in the PHP code).
-- Import with: mysql -uroot < database/deskshop.sql


CREATE TABLE IF NOT EXISTS user (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    contact_number VARCHAR(20) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'customer',
    status VARCHAR(20) NOT NULL DEFAULT 'active'
);

CREATE TABLE IF NOT EXISTS products (
    product_code VARCHAR(50) PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    image_url VARCHAR(255) DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS categories (
    category_id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS product_categories (
    product_code VARCHAR(50) NOT NULL,
    category_id INT NOT NULL,
    PRIMARY KEY (product_code, category_id),
    FOREIGN KEY (product_code) REFERENCES products(product_code) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS cart (
    cart_id INT AUTO_INCREMENT PRIMARY KEY,
    ID INT NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    FOREIGN KEY (ID) REFERENCES user(ID) ON DELETE CASCADE,
    FOREIGN KEY (product_code) REFERENCES products(product_code) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS wishlists (
    id INT NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    PRIMARY KEY (id, product_code),
    FOREIGN KEY (id) REFERENCES user(ID) ON DELETE CASCADE,
    FOREIGN KEY (product_code) REFERENCES products(product_code) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    date DATETIME DEFAULT CURRENT_TIMESTAMP,
    contact_number VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    postalcode VARCHAR(20),
    paymentmethod VARCHAR(50),
    status VARCHAR(20) NOT NULL DEFAULT 'Pending',
    FOREIGN KEY (user_id) REFERENCES user(ID) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    review_id INT AUTO_INCREMENT PRIMARY KEY,
    ID INT NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    review_text TEXT NOT NULL,
    review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ID) REFERENCES user(ID) ON DELETE CASCADE,
    FOREIGN KEY (product_code) REFERENCES products(product_code) ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS ContactInquiries (
    InquiryID INT AUTO_INCREMENT PRIMARY KEY,
    FullName VARCHAR(100) NOT NULL,
    EmailAddress VARCHAR(150) NOT NULL,
    PhoneNumber VARCHAR(20),
    Message TEXT NOT NULL,
    SubmissionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS NewsletterSubscriptions (
    SubscriptionID INT AUTO_INCREMENT PRIMARY KEY,
    EmailAddress VARCHAR(150) NOT NULL UNIQUE,
    SubscriptionDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
