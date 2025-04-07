<?php session_start();
    if(isset($_POST['login-btn'])){
        $email = $_POST['email'];
        $password = $_POST['password'];
    }

    $con = mysqli_connect("localhost", "root", "", "deskshop");
    if(!$con)
    {
        die("Sorry, Connection failed: ");
    }
    $sql = "SELECT * FROM user WHERE email = '$email' AND password = '$password'";
    $results = mysqli_query($con, $sql);

    if(mysqli_num_rows($results)>0){
        $_SESSION['email'] = $email;
        header("Location: ../profile.html");
    }
    else{
        header("Location: ../signin.html");
    }
?>