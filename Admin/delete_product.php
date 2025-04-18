<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Check admin access
if (!isset($_SESSION['email'])) {
    header("Location: ../signin.html");
    exit();
}
$email = $_SESSION['email'];
$sql = "SELECT role FROM user WHERE email = '$email'";
$result = mysqli_query($con, $sql);
if (!$result || mysqli_num_rows($result) == 0) {
    header("Location: ../signin.html");
    exit();
}
$user = mysqli_fetch_assoc($result);
if ($user['role'] !== 'admin') {
    header("Location: ../signin.html");
    exit();
}

// Handle deletion
if (isset($_POST['code'])) {
    $code = mysqli_real_escape_string($con, $_POST['code']);
    $delete_sql = "DELETE FROM products WHERE product_code = '$code'";
    mysqli_query($con, $delete_sql);
}

mysqli_close($con);
header("Location: admin.php");
exit();
?>
