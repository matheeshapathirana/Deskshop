<?php
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];

    $conn = mysqli_connect("localhost", "root", "", "deskshop");
    if(!$conn)
    {
        die("Sorry, Connection failed: " . mysqli_connect_error());
    }

    $sql = "INSERT INTO user (username, email, contact_number, password) VALUES ('$username', '$email', '$contact_number', '$password')";
    mysqli_query($conn, $sql);
    header("Location: ../signin.html");
?>
