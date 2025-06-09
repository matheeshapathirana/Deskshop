<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "deskshop");
if(!$conn) {
    die("Sorry, Connection failed: " . mysqli_connect_error());
}

// Handle delete inquiry action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_inquiry'])) {
    $inquiry_id = $_POST['inquiry_id'];
    $delete_sql = "DELETE FROM contactinquiries WHERE InquiryID = '$inquiry_id'";
    mysqli_query($conn, $delete_sql);
    header("Location: inquiries.php");
    exit;
}

$email = $_SESSION['email'];
$sql = "SELECT role FROM user WHERE email = '$email'";
$results = mysqli_query($conn, $sql);

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

// Fetch all inquiries
$inquiries = mysqli_query($conn, 'SELECT * FROM contactinquiries ORDER BY SubmissionDate DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Inquiries - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="manage_inquiries.css">
    <style>
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
            left: -220px;
            top: 0;
            height: 100vh;
            z-index: 1100;
            transition: left 0.3s;
            width: 200px;
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
          .main-content {
            margin-left: 0 !important;
          }
        }
    </style>
    <script>
        function toggleSidebar() {
          var sidebar = document.querySelector('.sidebar');
          var backdrop = document.getElementById('sidebar-backdrop');
          sidebar.classList.toggle('open');
          backdrop.classList.toggle('active');
        }
        function closeSidebar() {
          var sidebar = document.querySelector('.sidebar');
          var backdrop = document.getElementById('sidebar-backdrop');
          sidebar.classList.remove('open');
          backdrop.classList.remove('active');
        }
    </script>
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
            <a href="inquiries.php" class="active">Inquiries</a>
            <a href="../index.php">DeskShop</a>
            <a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a>
        </div>
        <main class="main-content">
            <h1>Manage User Inquiries</h1>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Message</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($inq = mysqli_fetch_array($inquiries)): ?>
                        <tr>
                            <td><?= $inq['InquiryID'] ?></td>
                            <td><?= htmlspecialchars($inq['FullName']) ?></td>
                            <td><?= htmlspecialchars($inq['EmailAddress']) ?></td>
                            <td><?= htmlspecialchars($inq['PhoneNumber']) ?></td>
                            <td style="max-width:250px;overflow:auto;white-space:pre-line;">
                                <?= nl2br(htmlspecialchars($inq['Message'])) ?>
                            </td>
                            <td><?= $inq['SubmissionDate'] ?></td>
                            <td>
                                <form method="POST" action="" onsubmit="return confirm('Delete this inquiry?');" style="display:inline;">
                                    <input type="hidden" name="inquiry_id" value="<?= $inq['InquiryID'] ?>">
                                    <button type="submit" name="delete_inquiry" class="delete-button button">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>
<?php $conn->close(); ?>