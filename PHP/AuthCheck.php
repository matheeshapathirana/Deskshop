<?php
// User authentication and deactivated status check helper
function get_user_by_email($email, $con) {
    $sql = "SELECT * FROM user WHERE email = '$email'";
    $results = mysqli_query($con, $sql);
    return ($results && mysqli_num_rows($results) > 0) ? mysqli_fetch_array($results) : null;
}

function check_user_auth_and_status() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if (!$con) {
        die("Database connection failed.");
    }
    // Auto-login using cookie if session not set
    if (!isset($_SESSION['email']) && isset($_COOKIE['user_email'])) {
        $user = get_user_by_email($_COOKIE['user_email'], $con);
        if ($user) {
            $_SESSION['email'] = $user['email'];
        }
    }
    if (!isset($_SESSION['email']) && !isset($_COOKIE['user_email'])) {
        mysqli_close($con);
        echo "<script>alert('You must be signed in to access this page.'); window.location.href='signin.php';</script>";
        exit();
    }
    $user_email = isset($_SESSION['email']) ? $_SESSION['email'] : $_COOKIE['user_email'];
    $user = get_user_by_email($user_email, $con);
    if ($user && isset($user['status']) && $user['status'] === 'deactive') {
        session_unset();
        session_destroy();
        setcookie("user_email", "", time() - 3600, "/");
        mysqli_close($con);
        echo "<script>alert('Your account has been deactivated. Please contact support.'); window.location.href='contact-us.php?deactivated=1';</script>";
        exit();
    }
    mysqli_close($con);
    return $user;
}
?>
