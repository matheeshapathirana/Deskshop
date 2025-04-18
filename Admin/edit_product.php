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
    $code = mysqli_real_escape_string($con, $_POST['code']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);

    $sql = "UPDATE products SET name='$name', price=$price, stock=$stock WHERE product_code='$code'";
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
