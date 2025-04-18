<?php 
session_start();

// Auto-login using cookie if session not set
if (!isset($_SESSION['email']) && isset($_COOKIE['user_email'])) {
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if ($con) {
        $email = $_COOKIE['user_email'];
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $results = mysqli_query($con, $sql);
        if (mysqli_num_rows($results) > 0) {
            $user = mysqli_fetch_assoc($results);
            $_SESSION['email'] = $user['email'];
            // Optionally, set other session variables if needed
        }
        mysqli_close($con);
    }
}

if (isset($_POST['login-btn'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $remember = isset($_POST['remember']); // Check if "Remember Me" is selected

    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password'";
    $results = mysqli_query($con, $sql);

    if (mysqli_num_rows($results) > 0) {
        $user = mysqli_fetch_assoc($results);
        $_SESSION['email'] = $email;

        // Set a cookie if "Remember Me" is checked
        if ($remember) {
            setcookie("user_email", $email, time() + (86400 * 30), "/"); // Cookie valid for 30 days
        }

        // Redirect based on role
        if ($user['role'] === 'admin') {
            header("Location: ../Admin/admin.php");
        } else {
            header("Location: ../profile.php");
        }
    } else {
        header("Location: ../signin.php");
    }
}
?>