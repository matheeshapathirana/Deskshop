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
    $user = mysqli_fetch_array($results);
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
        $sql = "SELECT category_id FROM categories WHERE category_name = '$category'";
        $result = mysqli_query($con, $sql);
        if ($row = mysqli_fetch_array($result)) {
            $category_id = $row['category_id'];
        } else {
            $sql = "INSERT INTO categories (category_name) VALUES ('$category')";
            if (!mysqli_query($con, $sql)) {
                return false;
            }
            $category_id = mysqli_insert_id($con);
        }
        $sql = "INSERT IGNORE INTO product_categories (product_code, category_id) VALUES ('$code', $category_id)";
        if (!mysqli_query($con, $sql)) {
            return false;
        }
    }
    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle image upload
    $image_url = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../Assets/Product Page/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $filename = basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            $image_url = "Assets/Product Page/" . $filename;
        } else {
            echo "<script>alert('Image upload failed.');window.location.href='admin.php';</script>";
            $con->close();
            exit;
        }
    } else if (!empty($_POST['image_url'])) {
        $image_url = $_POST['image_url'];
    }

    $product = array(
        "code" => $_POST['code'],
        "name" => $_POST['name'],
        "price" => floatval($_POST['price']),
        "stock" => intval($_POST['stock']),
        "image_url" => $image_url
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
