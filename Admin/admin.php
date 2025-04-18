<?php
session_start();

$con = mysqli_connect("localhost", "root", "", "deskshop");
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$sql = "SELECT role FROM user WHERE email = '$email'";
$results = mysqli_query($con, $sql);

if ($results && mysqli_num_rows($results) > 0) {
    $user = mysqli_fetch_assoc($results);
    if ($user['role'] !== 'admin') {
        echo "<script>alert('Access denied. Admins only.'); window.location.href='../signin.html';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please sign in to access admin panel.'); window.location.href='../signin.html';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="#products" class="active">Products</a>
            <a href="orders.php">Orders</a>
            <a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a>
        </div>
        <div class="main-content">
            <h1>Products</h1>
            <a href="#add" class="button">Add Product</a>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>Atlas Eraser</td>
                        <td>$30.00</td>
                        <td>100</td>
                        <td class="action-buttons">
                            <button class="edit-button">Edit</button>
                            <button class="delete-button">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Atlas Sunflower Yellow HB Pencil 12 Pack</td>
                        <td>$420.00</td>
                        <td>50</td>
                        <td class="action-buttons">
                            <button class="edit-button">Edit</button>
                            <button class="delete-button">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>3</td>
                        <td>Atlas Mathematical Box</td>
                        <td>$500.00</td>
                        <td>30</td>
                        <td class="action-buttons">
                            <button class="edit-button">Edit</button>
                            <button class="delete-button">Delete</button>
                        </td>
                    </tr>
                    <tr>
                        <td>4</td>
                        <td>Atlas Liquid Glue</td>
                        <td>$50.00</td>
                        <td>80</td>
                        <td class="action-buttons">
                            <button class="edit-button">Edit</button>
                            <button class="delete-button">Delete</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
