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
    $user = mysqli_fetch_array($results);
    if ($user['role'] !== 'admin') {
        echo "<script>alert('Access denied. Admins only.'); window.location.href='../signin.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('Please sign in to access admin panel.'); window.location.href='../signin.php';</script>";
    exit();
}

// Handle delete review
if (isset($_GET['delete'])) {
    $reviewId = intval($_GET['delete']);
    mysqli_query($con, "DELETE FROM reviews WHERE review_id = $reviewId");
    header("Location: reviews.php");
    exit();
}

$result = mysqli_query($con, "SELECT * FROM reviews ORDER BY review_date DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Reviews - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="reviews.css">
    <style>
    .delete-btn {
        background: #e53935;
        color: #fff;
        border: none;
        padding: 8px 18px;
        border-radius: 6px;
        font-size: 1em;
        font-family: inherit;
        cursor: pointer;
        margin: 8px 0;
        transition: background 0.2s;
        font-weight: 500;
        box-shadow: 0 2px 6px rgba(229,57,53,0.08);
        display: inline-block;
    }
    .delete-btn:hover {
        background: #b71c1c;
    }
    .hamburger {
      display: none;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      width: 40px;
      height: 40px;
      background: none;
      border: none;
      cursor: pointer;
      z-index: 1200;
      margin: 8px 0 8px 8px;
      position: fixed;
      top: 16px;
      left: 16px;
    }
    .hamburger .bar {
      width: 28px;
      height: 3px;
      background: #2b1b12;
      margin: 4px 0;
      border-radius: 2px;
      transition: 0.3s;
    }
    @media (max-width: 900px) {
      .hamburger {
        display: flex;
      }
      .sidebar {
        position: fixed;
        left: -260px;
        top: 0;
        height: 100vh;
        z-index: 1100;
        transition: left 0.3s;
        width: 220px;
        box-shadow: 2px 0 8px rgba(0,0,0,0.08);
      }
      .sidebar.open {
        left: 0;
      }
      .sidebar-backdrop {
        display: none;
        position: fixed;
        top: 0; left: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.2);
        z-index: 1099;
      }
      .sidebar-backdrop.active {
        display: block;
      }
      .admin-container {
        margin-left: 0 !important;
      }
      .main-content, .admin-container {
        max-width: 100vw;
        width: 100vw;
        padding: 0 2vw;
        margin: 0;
        box-sizing: border-box;
      }
      .admin-table {
        font-size: 0.92em;
        width: 100%;
        min-width: 600px;
        max-width: 100vw;
        overflow-x: auto;
        display: block;
      }
      .admin-table th, .admin-table td {
        padding: 4px 6px;
        min-width: 80px;
        font-size: 0.95em;
        word-break: break-word;
        height: 28px;
      }
      .admin-main h2 {
        font-size: 1.2em;
        margin-left: 8px;
      }
    }
    @media (max-width: 600px) {
      .main-content, .admin-container {
        padding: 0 1vw;
      }
      .admin-table {
        min-width: 400px;
      }
      .admin-table th, .admin-table td {
        padding: 2px 2px;
        min-width: 60px;
        font-size: 0.9em;
        height: 24px;
      }
      .admin-main h2 {
        font-size: 1em;
        margin-left: 4px;
      }
    }
    </style>
</head>
<body>
    <button class="hamburger" aria-label="Open navigation" aria-expanded="false" aria-controls="admin-sidebar" onclick="toggleSidebar()">
        <span class="bar"></span>
        <span class="bar"></span>
        <span class="bar"></span>
    </button>
    <div id="sidebar-backdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>
    <div class="admin-panel">
        <div class="sidebar" id="admin-sidebar">
            <h2>Deskshop Admin Panel</h2>
            <a href="admin.php">Products</a>
            <a href="orders.php">Orders</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="inquiries.php">Inquiries</a>
            <a href="reviews.php" class="active">Reviews</a>
            <a href="../index.php">DeskShop</a>
            <a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a>
        </div>
        <div class="main-content">
            <div class="admin-container">
                <main class="admin-main">
                    <h2>All Reviews</h2>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>User ID</th>
                                <th>Product Code</th>
                                <th>Comment</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo $row['review_id']; ?></td>
                                <td><?php echo $row['ID']; ?></td>
                                <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                                <td><?php echo htmlspecialchars($row['review_text']); ?></td>
                                <td><?php echo $row['review_date']; ?></td>
                                <td><a href="?delete=<?php echo $row['review_id']; ?>" class="delete-btn" onclick="return confirm('Delete this review?');">Delete</a></td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                </main>
            </div>
        </div>
    </div>
    <script>
    function toggleSidebar() {
      var sidebar = document.getElementById('admin-sidebar');
      var backdrop = document.getElementById('sidebar-backdrop');
      sidebar.classList.toggle('open');
      backdrop.classList.toggle('active');
    }
    function closeSidebar() {
      var sidebar = document.getElementById('admin-sidebar');
      var backdrop = document.getElementById('sidebar-backdrop');
      sidebar.classList.remove('open');
      backdrop.classList.remove('active');
    }
    </script>
</body>
</html>
<?php mysqli_close($con); ?>
