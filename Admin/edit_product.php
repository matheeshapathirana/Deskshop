<?php
session_start();

if (!isset($_SESSION['email'])) {
    header("Location: ../signin.php");
    exit();
}

$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (
    isset($_POST['code']) &&
    isset($_POST['name']) &&
    isset($_POST['price']) &&
    isset($_POST['stock'])
) {
    $code = $_POST['code'];
    $name = $_POST['name'];
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    // Handle image upload if present
    $image_url = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "../Assets/Product Page/";
        $filename = basename($_FILES["image_file"]["name"]);
        $target_file = $target_dir . $filename;
        if (move_uploaded_file($_FILES["image_file"]["tmp_name"], $target_file)) {
            $image_url = "Assets/Product Page/" . $filename;
        } else {
            echo "<script>alert('Image upload failed.'); window.location.href='admin.php';</script>";
            exit();
        }
    } else if (isset($_POST['image_url']) && $_POST['image_url'] !== '') {
        $image_url = $_POST['image_url'];
    }

    if ($image_url !== '') {
        $sql = "UPDATE products SET name='$name', price=$price, stock=$stock, image_url='$image_url' WHERE product_code='$code'";
    } else {
        $sql = "UPDATE products SET name='$name', price=$price, stock=$stock WHERE product_code='$code'";
    }

    if (mysqli_query($con, $sql)) {
        header("Location: admin.php");
        exit();
    } else {
        echo "<script>alert('Failed to update product.'); window.location.href='admin.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Invalid request.'); window.location.href='admin.php';</script>";
    exit();
}
?>
