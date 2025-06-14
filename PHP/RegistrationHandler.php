<?php
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    $conn = mysqli_connect("db.be-mons1.bengt.wasmernet.com", "d6c017297b5c8000c3e18e79253e", "0684d6c0-1729-7d0e-8000-e756ec962efd", "deskshop");
    if(!$conn)
    {
        die("Sorry, Connection failed: " . mysqli_connect_error());
    }

    // Check if contact number already exists
    $check_sql = "SELECT id FROM user WHERE contact_number = '$contact_number' LIMIT 1";
    $result = mysqli_query($conn, $check_sql);
    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('Phone number already exists. Please use a different number.'); window.location.href='../signup.php';</script>";
        exit();
    }

    // Check if email already exists
    $check_email_sql = "SELECT id FROM user WHERE email = '$email' LIMIT 1";
    $email_result = mysqli_query($conn, $check_email_sql);
    if (mysqli_num_rows($email_result) > 0) {
        echo "<script>alert('Email already exists. Please sign in.'); window.location.href='../signin.php';</script>";
        exit();
    }

    $sql = "INSERT INTO user (username, email, contact_number, password) VALUES ('$username', '$email', '$contact_number', '$password')";
    mysqli_query($conn, $sql);
    header("Location: ../signin.php");
?>
