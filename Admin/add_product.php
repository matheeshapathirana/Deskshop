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
        echo "<script>alert('Access denied. Admins only.'); window.location.href='../signin.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please sign in to access admin panel.'); window.location.href='../signin.php';</script>";
    exit();
}

header('Content-Type: text/html');

function addProductWithCategories($con, $product, $categories) {
    // Remove duplicate and empty category names
    $categories = array_unique(array_filter(array_map('trim', $categories)));
    if (empty($categories)) return false;

    // Insert product
    $code = $product['code'];
    $name = $product['name'];
    $price = $product['price'];
    $stock = $product['stock'];
    $image_url = $product['image_url'];
    $sql = "INSERT INTO products (product_code, name, price, stock, image_url) VALUES ('$code', '$name', '$price', '$stock', '$image_url')";
    if (!mysqli_query($con, $sql)) {
        return false;
    }

    foreach ($categories as $category) {
        // 1. Try to get the category_id
        $stmt = $con->prepare("SELECT category_id FROM categories WHERE category_name = ?");
        $stmt->bind_param("s", $category);
        $stmt->execute();
        $stmt->bind_result($category_id);
        if ($stmt->fetch()) {
            $stmt->close();
        } else {
            $stmt->close();
            // 2. Insert new category if not exists
            $stmt = $con->prepare("INSERT INTO categories (category_name) VALUES (?)");
            $stmt->bind_param("s", $category);
            if (!$stmt->execute()) {
                $stmt->close();
                return false;
            }
            $category_id = $con->insert_id;
            $stmt->close();
        }
        // 3. Link product and category (avoid duplicates by using INSERT IGNORE)
        $stmt = $con->prepare("INSERT IGNORE INTO product_categories (product_code, category_id) VALUES (?, ?)");
        $stmt->bind_param("si", $product['code'], $category_id);
        if (!$stmt->execute()) {
            $stmt->close();
            return false;
        }
        $stmt->close();
    }
    return true;
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
