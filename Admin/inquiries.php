<?php
session_start();
// inquiries.php - Admin panel for managing user inquiries

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
    $user = mysqli_fetch_assoc($results);
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
</head>
<body>
    <div class="admin-panel">
        <div class="sidebar">
            <h2>Admin Panel</h2>
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
                    <?php while($inq = mysqli_fetch_assoc($inquiries)): ?>
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