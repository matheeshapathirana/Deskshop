<?php
session_start();

$con = mysqli_connect("db.be-mons1.bengt.wasmernet.com", "d6c017297b5c8000c3e18e79253e", "0684d6c0-1729-7d0e-8000-e756ec962efd", "deskshop");
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <style>
    .modal,
    .modal-add {
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.4);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .modal-content {
        background: #fff;
        padding: 2em;
        max-width: 400px;
        width: 100%;
        position: relative;
        border-radius: 8px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.15);
        font-family: inherit;
    }
    .close {
        position: absolute;
        top: 10px; right: 10px;
        background: none;
        border: none;
        font-size: 1.5em;
        cursor: pointer;
        color: #888;
    }
    .modal-content h2 {
        margin-top: 0;
        margin-bottom: 1em;
        font-size: 1.3em;
        color: #333;
    }
    .modal-content label {
        display: block;
        margin-bottom: 0.7em;
        color: #222;
    }
    .modal-content input[type="text"],
    .modal-content input[type="number"],
    .modal-content input[type="file"] {
        width: 95%;
        padding: 0.5em;
        margin-top: 0.2em;
        margin-bottom: 0.5em;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1em;
    }
    .modal-content button[type="submit"] {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 0.7em 1.5em;
        border-radius: 4px;
        font-size: 1em;
        cursor: pointer;
        transition: background 0.2s;
    }
    .modal-content button[type="submit"]:hover {
        background: #0056b3;
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

    body.dark-mode {
        background-color: #121212;
        color: #e0e0e0;
    }
    body.dark-mode .modal-content {
        background: #1e1e1e;
        color: #e0e0e0;
    }
    body.dark-mode .close {
        color: #bbb;
    }
    body.dark-mode h2 {
        color: #fff;
    }
    body.dark-mode label {
        color: #ddd;
    }
    body.dark-mode input[type="text"],
    body.dark-mode input[type="number"],
    body.dark-mode input[type="file"] {
        background: #333;
        color: #fff;
        border: 1px solid #555;
    }
    body.dark-mode button[type="submit"] {
        background: #007bff;
        color: #fff;
    }
    body.dark-mode button[type="submit"]:hover {
        background: #0056b3;
    }
    body.dark-mode .sidebar {
        background: #1e1e1e;
        color: #e0e0e0;
    }
    body.dark-mode .sidebar a {
        color: #e0e0e0;
    }
    body.dark-mode .sidebar a.active {
        background: #007bff;
        color: #fff;
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
    function editProduct(code, name, price, stock, image_url = '') {
        let modal = document.createElement('div');
        modal.className = "modal";
        modal.innerHTML = `
        <div class="modal-content">
            <button class="close" onclick="this.closest('.modal').remove()">&times;</button>
            <h2>Edit Product</h2>
            <form id="editProductModalForm">
                <label>Product Code:<br><input type="text" value="${code}" disabled></label>
                <label>Name:<br><input type="text" id="modal_edit_name" value="${name}" required></label>
                <label>Price:<br><input type="number" id="modal_edit_price" value="${price}" step="0.01" required></label>
                <label>Stock:<br><input type="number" id="modal_edit_stock" value="${stock}" required></label>
                <label>Image URL:<br><input type="text" id="modal_edit_image_url" value="${image_url}"></label>
                <label>Or Upload New Image:<br><input type="file" id="modal_edit_image_file" accept="image/*"></label>
                <button type="submit" style="margin-top:1em;">Save</button>
            </form>
        </div>
        `;
        document.body.appendChild(modal);

        document.getElementById('modal_edit_image_file').addEventListener('change', function() {
            if (this.files.length > 0) {
                document.getElementById('modal_edit_image_url').value = '';
            }
        });

        document.getElementById('editProductModalForm').onsubmit = function(e) {
            e.preventDefault();
            document.getElementById('edit_code').value = code;
            document.getElementById('edit_name').value = document.getElementById('modal_edit_name').value;
            document.getElementById('edit_price').value = document.getElementById('modal_edit_price').value;
            document.getElementById('edit_stock').value = document.getElementById('modal_edit_stock').value;
            document.getElementById('edit_image_url').value = document.getElementById('modal_edit_image_url').value;

            // Handle file input
            let fileInput = document.getElementById('modal_edit_image_file');
            if (fileInput.files.length > 0) {
                let realFileInput = document.getElementById('edit_image_file');
                let dt = new DataTransfer();
                dt.items.add(fileInput.files[0]);
                realFileInput.files = dt.files;
                realFileInput.style.display = 'block';
            } else {
                document.getElementById('edit_image_file').value = '';
            }

            document.getElementById('editProductForm').submit();
        };
    }

    function deleteProduct(code) {
        if (confirm("Are you sure you want to delete this product?")) {
            document.getElementById('delete_code').value = code;
            document.getElementById('deleteProductForm').submit();
        }
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
        <div class="sidebar" id="admin-sidebar">
            <h2>Deskshop Admin Panel</h2>
            <a href="admin.php" class="active">Products</a>
            <a href="orders.php">Orders</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="inquiries.php">Inquiries</a>
            <a href="reviews.php">Reviews</a>
            <a href="../index.php">DeskShop</a>
            <a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a>
        </div>
        <div class="main-content">
            <h1>Products</h1>
            <a href="#add" class="button" onclick="document.querySelector('.modal-add').style.display='flex'">Add Product</a>
            <!-- Modal for Add Product -->
            <div class="modal-add" style="display:none;">
                <div class="modal-content">
                    <button class="close" onclick="document.querySelector('.modal-add').style.display='none'">&times;</button>
                    <h2>Add Product</h2>
                    <form id="addProductForm" action="add_product.php" method="post" enctype="multipart/form-data">
                        <label>Product Code:<br><input type="text" name="code" required></label>
                        <label>Name:<br><input type="text" name="name" required></label>
                        <label>Price:<br><input type="number" name="price" step="0.01" required></label>
                        <label>Stock:<br><input type="number" name="stock" required></label>
                        <label>Image:<br><input type="file" name="image_file" accept="image/*" required></label>
                        <label>Categories (comma separated):<br><input type="text" name="categories" required></label>
                        <button type="submit" style="margin-top:1em;">Add Product</button>
                    </form>
                </div>
            </div>
            <form id="editProductForm" action="edit_product.php" method="post" enctype="multipart/form-data" style="display:none;">
                <input type="hidden" name="code" id="edit_code">
                <input type="hidden" name="name" id="edit_name">
                <input type="hidden" name="price" id="edit_price">
                <input type="hidden" name="stock" id="edit_stock">
                <input type="hidden" name="image_url" id="edit_image_url">
                <input type="file" name="image_file" id="edit_image_file" accept="image/*" style="display:none;">
            </form>
            <form id="deleteProductForm" action="delete_product.php" method="post" style="display:none;">
                <input type="hidden" name="code" id="delete_code">
            </form>
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
                    <?php
                    $sql = "SELECT p.product_code, p.name, p.price, p.stock, p.image_url
                            FROM products p";
                    $result = mysqli_query($con, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_array($result)) {
                            echo "<tr>";
                            echo "<td>" . $row['product_code'] . "</td>";
                            echo "<td>" . $row['name'] . "</td>";
                            echo "<td>Rs. " . $row['price'] . "</td>";
                            echo "<td>" . $row['stock'] . "</td>";
                            echo '<td class="action-buttons">
                                    <button class="edit-button" type="button" onclick="editProduct('
                                        . '\'' . $row['product_code'] . '\','
                                        . '\'' . $row['name'] . '\','
                                        . '\'' . $row['price'] . '\','
                                        . '\'' . $row['stock'] . '\','
                                        . '\'' . $row['image_url'] . '\')">Edit</button>
                                    <button class="delete-button" type="button" onclick="deleteProduct('
                                        . '\'' . $row['product_code'] . '\')">Delete</button>
                                </td>';
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No products found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
