<?php
session_start();
if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
    header("Location: signin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        // Optional: check if the cart item belongs to the logged-in user
        $user_email = $_SESSION['email'] ?? $_COOKIE['user_email'];
        $user_q = mysqli_query($con, "SELECT id FROM user WHERE email = '$user_email' LIMIT 1");
        $user_row = mysqli_fetch_assoc($user_q);
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
