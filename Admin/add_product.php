<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$sql = "SELECT role FROM user WHERE email = '$email'";
$results = mysqli_query($con, $sql);

if ($results && mysqli_num_rows($results) > 0) {
    $user = mysqli_fetch_assoc($results);
    if ($user['role'] !== 'admin') {
        echo "<script>alert('Access denied. Admins only.'); window.location.href='../signin.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please sign in to access admin panel.'); window.location.href='../signin.html';</script>";
    exit();
}

header('Content-Type: text/html');

function addProductWithCategories($con, $product, $categories) {
    // Insert product using simple SQL (not recommended for production)
    $code = $product['code'];
    $name = $product['name'];
    $price = $product['price'];
    $stock = $product['stock'];
    $image_url = $product['image_url'];
    $sql = "INSERT INTO products (product_code, name, price, stock, image_url) VALUES ('$code', '$name', '$price', '$stock', '$image_url')";
    mysqli_query($con, $sql);

    // Insert categories and link them (keep using prepared statements for these)
    foreach ($categories as $category) {
        $stmt = $con->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->bind_result($category_id);
        if ($stmt->fetch()) {
            // No need to close $stmt here
        } else {
            $stmt = $con->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $category);
            $stmt->execute();
            $category_id = $con->insert_id;
            // No need to close $stmt here
        }
        $stmt = $con->prepare("INSERT INTO product_categories (product_code, category_id) VALUES (?, ?)");
        $stmt->bind_param("si", $product['code'], $category_id);
        $stmt->execute();
        // No need to close $stmt here
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $product = array(
        "code" => $_POST['code'],
        "name" => $_POST['name'],
        "price" => floatval($_POST['price']),
        "stock" => intval($_POST['stock']),
        "image_url" => $_POST['image_url']
    );
    $categories = array_map('trim', explode(',', $_POST['categories']));

    // Remove try-catch, use simple error handling
    if (addProductWithCategories($con, $product, $categories) === false) {
        echo "<script>alert('Error adding product.');window.location.href='admin.php';</script>";
    } else {
        echo "<script>alert('Product added successfully!');window.location.href='admin.php';</script>";
    }
    $con->close();
} else {
    header("Location: admin.php");
    exit;
}
