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
    <title>Admin Panel</title>
    <link rel="stylesheet" href="admin.css">
    <script>
    function addProduct() {
        let code = prompt("Enter product code:");
        if (!code) return;
        let name = prompt("Enter product name:");
        if (!name) return;
        let price = prompt("Enter product price:");
        if (!price) return;
        let stock = prompt("Enter product stock:");
        if (!stock) return;
        let image_url = prompt("Enter image URL:");
        if (!image_url) return;
        let categories = prompt("Enter categories (comma separated):");
        if (!categories) return;

        // Fill form fields
        document.getElementById('add_code').value = code;
        document.getElementById('add_name').value = name;
        document.getElementById('add_price').value = price;
        document.getElementById('add_stock').value = stock;
        document.getElementById('add_image_url').value = image_url;
        document.getElementById('add_categories').value = categories;

        // Submit the form
        document.getElementById('addProductForm').submit();
    }

    function editProduct(code, name, price, stock) {
        let newName = prompt("Edit product name:", name);
        if (newName === null) return;
        let newPrice = prompt("Edit product price:", price);
        if (newPrice === null) return;
        let newStock = prompt("Edit product stock:", stock);
        if (newStock === null) return;

        document.getElementById('edit_code').value = code;
        document.getElementById('edit_name').value = newName;
        document.getElementById('edit_price').value = newPrice;
        document.getElementById('edit_stock').value = newStock;
        document.getElementById('editProductForm').submit();
    }

    function deleteProduct(code) {
        if (confirm("Are you sure you want to delete this product?")) {
            document.getElementById('delete_code').value = code;
            document.getElementById('deleteProductForm').submit();
        }
    }
    </script>
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
            <a href="#add" class="button" onclick="addProduct()">Add Product</a>
            <form id="addProductForm" action="add_product.php" method="post" style="display:none;">
                <input type="hidden" name="code" id="add_code">
                <input type="hidden" name="name" id="add_name">
                <input type="hidden" name="price" id="add_price">
                <input type="hidden" name="stock" id="add_stock">
                <input type="hidden" name="image_url" id="add_image_url">
                <input type="hidden" name="categories" id="add_categories">
            </form>
            <form id="editProductForm" action="edit_product.php" method="post" style="display:none;">
                <input type="hidden" name="code" id="edit_code">
                <input type="hidden" name="name" id="edit_name">
                <input type="hidden" name="price" id="edit_price">
                <input type="hidden" name="stock" id="edit_stock">
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
                    $sql = "SELECT p.product_code, p.name, p.price, p.stock
                            FROM products p";
                    $result = mysqli_query($con, $sql);
                    if ($result && mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($row['product_code']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                            echo "<td>$" . htmlspecialchars($row['price']) . "</td>";
                            echo "<td>" . htmlspecialchars($row['stock']) . "</td>";
                            echo '<td class="action-buttons">
                                    <button class="edit-button" type="button" onclick="editProduct('
                                        . '\'' . addslashes($row['product_code']) . '\','
                                        . '\'' . addslashes($row['name']) . '\','
                                        . '\'' . addslashes($row['price']) . '\','
                                        . '\'' . addslashes($row['stock']) . '\')">Edit</button>
                                    <button class="delete-button" type="button" onclick="deleteProduct(\''
                                        . addslashes($row['product_code']) . '\')">Delete</button>
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
