<?php
session_start();

$con = mysqli_connect("db.be-mons1.bengt.wasmernet.com", "d6c017297b5c8000c3e18e79253e", "0684d6c0-1729-7d0e-8000-e756ec962efd", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check admin access
if (!isset($_SESSION['email'])) {
    header("Location: ../signin.php");
    exit();
}
$email = $_SESSION['email'];
$sql = "SELECT role FROM user WHERE email = '$email'";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: ../signin.php");
    exit();
}
$user = mysqli_fetch_array($result);
if ($user['role'] !== 'admin') {
    header("Location: ../signin.php");
    exit();
}

// Handle deletion
if (isset($_POST['code'])) {
    $code = $_POST['code'];
    $delete_sql = "DELETE FROM products WHERE product_code = '$code'";
    mysqli_query($con, $delete_sql);
}

mysqli_close($con);
header("Location: admin.php");
exit();
?>
