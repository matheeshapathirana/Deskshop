<?php
session_start();
session_unset();
session_destroy();
setcookie("user_email", "", time() - 3600, "/"); // Remove cookie
header("Location: ../signin.html");
exit();
?>