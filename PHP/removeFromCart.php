<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    $con = mysqli_connect("db.be-mons1.bengt.wasmernet.com", "d6c017297b5c8000c3e18e79253e", "0684d6c0-1729-7d0e-8000-e756ec962efd", "deskshop");
    if ($con) {
        // Optional: check if the cart item belongs to the logged-in user
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '$user_email' LIMIT 1");
        $user_row = mysqli_fetch_array($user_q);
        if ($user_row) {
            $user_id = $user_row['id'];
            // Only delete if the cart item belongs to this user
            mysqli_query($con, "DELETE FROM cart WHERE cart_id = $cart_id AND ID = $user_id");
        }
        mysqli_close($con);
    }
}
header("Location: ../cart.php");
exit();
