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
        echo "<script>alert('Access denied. Admins only.'); window.location.href='../signin.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please sign in to access admin panel.'); window.location.href='../signin.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Orders</title>
  <link rel="stylesheet" href="orders.css">
</head>
<body>
  <div class="admin-panel">
    <aside class="sidebar">
      <h2>Admin Panel</h2>
      <ul>
        <li><a href="admin.php">Products</a></li>
        <li><a href="#" class="active">Orders</a></li>
        <li><a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a></li>
      </ul>
    </aside>
    <main class="main-content">
      <h1>Orders</h1>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1001</td>
            <td>John Doe</td>
            <td>$120.00</td>
            <td>Processing</td>
            <td class="action-buttons">
              <button class="edit-button" title="Edit">Edit</button>
              <button class="delete-button" title="Delete">Delete</button>
              <button class="button" title="View">View</button>
              <button class="button" title="Mark as Shipped">Mark as Shipped</button>
              <button class="button" title="Print">Print</button>
            </td>
          </tr>
          <tr>
            <td>1002</td>
            <td>Jane Smith</td>
            <td>$250.00</td>
            <td>Shipped</td>
            <td class="action-buttons">
              <button class="edit-button" title="Edit">Edit</button>
              <button class="delete-button" title="Delete">Delete</button>
              <button class="button" title="View">View</button>
              <button class="button" title="Mark as Shipped">Mark as Shipped</button>
              <button class="button" title="Print">Print</button>
            </td>
          </tr>
        </tbody>
      </table>
    </main>
  </div>
</body>
</html>
