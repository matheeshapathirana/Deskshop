<?php
require_once __DIR__ . '/../db.php';
session_start();

$con = db_connect();
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'], $_POST['order_id'], $_POST['status'])) {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $update_sql = "UPDATE orders SET status='$status' WHERE order_id=$order_id";
    mysqli_query($con, $update_sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_order'], $_POST['order_id'])) {
    $order_id = $_POST['order_id'];
    $delete_sql = "DELETE FROM orders WHERE order_id=$order_id";
    mysqli_query($con, $delete_sql);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_all_delivered'])) {
    $delete_delivered_sql = "DELETE FROM orders WHERE status='Delivered'";
    mysqli_query($con, $delete_delivered_sql);
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

$order_query = "
    SELECT o.order_id, u.ID AS customer_id, u.username AS customer_name, o.status, o.quantity, o.product_code, p.price, p.name,
    o.contact_number, o.address, o.city, o.postalcode, o.paymentmethod
    FROM orders o
    JOIN user u ON o.user_id = u.ID
    JOIN products p ON o.product_code = p.product_code
    ORDER BY o.order_id DESC
";
$order_result = mysqli_query($con, $order_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel - Orders</title>
  <link rel="stylesheet" href="orders.css">
  <style>
    .modal {
      display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%;
      overflow: auto; background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
      background: #fff; margin: 10% auto; padding: 20px; border: 1px solid #888; width: 350px; border-radius: 8px; position: relative;
    }
    .close-modal {
      color: #aaa; position: absolute; top: 10px; right: 16px; font-size: 28px; font-weight: bold; cursor: pointer;
    }
    .modal-content h2 { margin-top: 0; }
    .modal-content .detail-row { margin-bottom: 10px; }
    .modal-content .detail-label { font-weight: bold; }
    @media (max-width: 600px) {
      .modal-content {
        width: 95vw;
        padding: 10px 4vw;
        min-width: unset;
        max-width: 99vw;
      }
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
<body class="dark-mode">
  <button class="hamburger" aria-label="Open navigation" aria-expanded="false" aria-controls="admin-sidebar" onclick="toggleSidebar()">
    <span class="bar"></span>
    <span class="bar"></span>
    <span class="bar"></span>
  </button>
  <div id="sidebar-backdrop" class="sidebar-backdrop" onclick="closeSidebar()"></div>
  <div class="admin-panel">
    <aside class="sidebar" id="admin-sidebar">
      <h2>Deskshop Admin Panel</h2>
      <ul>
        <li><a href="admin.php">Products</a></li>
        <li><a href="#" class="active">Orders</a></li>
        <li><a href="manage_users.php">Manage Users</a></li>
        <li><a href="inquiries.php">Inquiries</a></li>
        <li><a href="reviews.php">Reviews</a></li>
        <li><a href="../index.php">DeskShop</a></li>
        <li><a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a></li>
      </ul>
    </aside>
    <main class="main-content">
      <h1>Orders</h1>
      <form method="post" style="margin-bottom: 16px;" onsubmit="return confirm('Are you sure you want to delete all delivered orders?');">
        <button type="submit" name="delete_all_delivered" class="delete-button button" style="background:#d9534f;color:#fff;">Delete All Delivered Orders</button>
      </form>
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer ID</th>
            <th>Customer</th>
            <th>Product Name</th>
            <th>Product Code</th>
            <th>Quantity</th>
            <th>Total</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($order_result && mysqli_num_rows($order_result) > 0): ?>
            <?php while ($row = mysqli_fetch_array($order_result)): ?>
              <tr>
                <td><?php echo htmlspecialchars($row['order_id']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_id']); ?></td>
                <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['product_code']); ?></td>
                <td><?php echo htmlspecialchars($row['quantity']); ?></td>
                <td>
                  <?php
                    $total = $row['quantity'] * $row['price'];
                    echo 'Rs.' . number_format($total, 2);
                  ?>
                </td>
                <td>
                  <form method="post" style="display:inline;">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <select name="status" class="status-dropdown">
                      <option value="Pending" <?php if ($row['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                      <option value="Shipped" <?php if ($row['status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                      <option value="Delivered" <?php if ($row['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                    </select>
                    <button type="submit" name="update_status" class="update-status-button button" title="Update Status">Update</button>
                  </form>
                </td>
                <td class="action-buttons">
                  <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this order?');">
                    <input type="hidden" name="order_id" value="<?php echo $row['order_id']; ?>">
                    <button type="submit" name="delete_order" class="delete-button button" title="Delete">Delete</button>
                  </form>
                  <a href="../product.php?code=<?php echo urlencode($row['product_code']); ?>" class="view-button button" title="View">View</a>
                  <button type="button"
                    class="details-button button"
                    title="Details"
                    onclick="showDetailsModal(
                      '<?php echo htmlspecialchars($row['customer_name'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($row['contact_number'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($row['address'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($row['city'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($row['postalcode'], ENT_QUOTES); ?>',
                      '<?php echo htmlspecialchars($row['paymentmethod'], ENT_QUOTES); ?>'
                    )"
                    style="background:#0275d8;color:#fff;margin-left:4px;"
                  >Details</button>
                </td>
              </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No orders found.</td></tr>
        <?php endif; ?>
        </tbody>
      </table>
      <!-- Modal for order details -->
      <div id="detailsModal" class="modal">
        <div class="modal-content">
          <span class="close-modal" onclick="closeDetailsModal()">&times;</span>
          <h2>Order Details</h2>
          <div class="detail-row"><span class="detail-label">Customer:</span> <span id="modalCustomer"></span></div>
          <div class="detail-row"><span class="detail-label">Contact Number:</span> <span id="modalContact"></span></div>
          <div class="detail-row"><span class="detail-label">Address:</span> <span id="modalAddress"></span></div>
          <div class="detail-row"><span class="detail-label">City:</span> <span id="modalCity"></span></div>
          <div class="detail-row"><span class="detail-label">Postal Code:</span> <span id="modalPostal"></span></div>
          <div class="detail-row"><span class="detail-label">Payment Method:</span> <span id="modalPayment"></span></div>
        </div>
      </div>
      <script>
        function showDetailsModal(customer, contact, address, city, postal, payment) {
          document.getElementById('modalCustomer').textContent = customer;
          document.getElementById('modalContact').textContent = contact;
          document.getElementById('modalAddress').textContent = address;
          document.getElementById('modalCity').textContent = city;
          document.getElementById('modalPostal').textContent = postal;
          document.getElementById('modalPayment').textContent = payment;
          document.getElementById('detailsModal').style.display = 'block';
        }
        function closeDetailsModal() {
          document.getElementById('detailsModal').style.display = 'none';
        }
        window.onclick = function(event) {
          var modal = document.getElementById('detailsModal');
          if (event.target == modal) {
            closeDetailsModal();
          }
        }
      </script>
    </main>
  </div>
</body>
</html>
