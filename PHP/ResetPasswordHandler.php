<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $contact_number = $_POST['contact_number'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.'); window.history.back();</script>";
        exit;
    }

    $conn = mysqli_connect("db.be-mons1.bengt.wasmernet.com", "d6c017297b5c8000c3e18e79253e", "0684d6c0-1729-7d0e-8000-e756ec962efd", "deskshop");
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if contact number exists
    $check_sql = "SELECT * FROM user WHERE contact_number='$contact_number'";
    $check_result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($check_result) === 0) {
        echo "<script>alert('Phone number not found.'); window.history.back();</script>";
        mysqli_close($conn);
        exit;
    }

    $sql = "UPDATE user SET password='$password' WHERE contact_number='$contact_number'";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Password reset successful. Please sign in.'); window.location.href='../signin.php';</script>";
    } else {
        echo "<script>alert('Failed to reset password.'); window.history.back();</script>";
    }
    mysqli_close($conn);
} else {
    header("Location: ../reset-password.php");
    exit;
}
?>
