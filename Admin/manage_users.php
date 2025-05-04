<?php
// manage_users.php - Admin panel for managing users

// Example: Connect to database (update with your DB credentials)
$conn = mysqli_connect("localhost", "root", "", "deskshop");
if(!$conn)
{
    die("Sorry, Connection failed: " . mysqli_connect_error());
}

// Handle add, edit, and role change actions here

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Add user
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $contact_number = $_POST['contact_number'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Directly insert values (not recommended for production)
        $sql = "INSERT INTO user (username, email, contact_number, password, role) VALUES (
            '$username',
            '$email',
            '$contact_number',
            '$password',
            '$role'
        )";
        mysqli_query($conn, $sql);
    }

    // Update user details
    if (isset($_POST['update_user'])) {
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $contact_number = $_POST['contact_number'];
        $password = $_POST['password'];

        // If password is empty, don't update it
        if (!empty($password)) {
            $sql = "UPDATE user SET username = '$username', contact_number = '$contact_number', password = '$password' WHERE ID = '$user_id'";
            mysqli_query($conn, $sql);
        } else {
            $sql = "UPDATE user SET username = '$username', contact_number = '$contact_number' WHERE ID = '$user_id'";
            mysqli_query($conn, $sql);
        }
        // Refresh to clear edit form
        header("Location: manage_users.php");
        exit;
    }

    // Change user role
    if (isset($_POST['change_role'])) {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $sql = "UPDATE user SET role = '$role' WHERE ID = '$user_id'";
        mysqli_query($conn, $sql);
    }

    // Toggle user status (activate/deactivate)
    if (isset($_POST['toggle_status'])) {
        $user_id = $_POST['user_id'];
        $current_status = $_POST['current_status'];
        $new_status = ($current_status === 'active') ? 'deactive' : 'active';
        $sql = "UPDATE user SET status = '$new_status' WHERE ID = '$user_id'";
        mysqli_query($conn, $sql);
    }

    // Delete user
    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $sql = "DELETE FROM user WHERE ID = '$user_id'";
        mysqli_query($conn, $sql);
        // Refresh to clear modal and update list
        header("Location: manage_users.php");
        exit;
    }
}

// Fetch all users
$users = mysqli_query($conn, 'SELECT * FROM user');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin</title>
    <link rel="stylesheet" href="admin.css">
    <link rel="stylesheet" href="manage_users.css">
    <style>
        /* Remove table row hover effect */
        table tr:hover {
            background: none !important;
        }
        .modal {
            display: block;
            position: fixed;
            z-index: 1000;
            left: 0; top: 0; width: 100vw; height: 100vh;
            background: rgba(0,0,0,0.4);
            justify-content: center; align-items: center;
        }
        .modal-content {
            background: #fff;
            margin: 5% auto;
            padding: 30px 30px 20px 30px;
            border-radius: 8px;
            width: 400px;
            position: relative;
            box-shadow: 0 2px 16px rgba(0,0,0,0.2);
        }
        .modal .close {
            position: absolute;
            right: 16px;
            top: 10px;
            font-size: 24px;
            background: none;
            border: none;
            cursor: pointer;
        }
        .modal label {
            display: block;
            margin-bottom: 10px;
        }
        .modal input[type="text"], .modal input[type="number"], .modal input[type="password"] {
            width: 100%;
            padding: 6px 8px;
            margin-top: 2px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }
        .modal button[type="submit"] {
            margin-top: 1em;
        }
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
            <a href="manage_users.php" class="active">Manage Users</a>
            <a href="inquiries.php">Inquiries</a>
            <a href="../index.php">DeskShop</a>
            <a href="#logout" onclick="window.location.href='../PHP/logout.php'">Logout</a>
        </div>
        <div class="main-content">
            <h1>Manage Users</h1>
            <form method="POST" action="" class="horizontal-form" style="overflow:visible;max-width:1000px;margin-bottom:30px;">
                <div class="form-group">
                    <label>Username:</label>
                    <input type="text" name="username" required>
                </div>
                <div class="form-group">
                    <label>Email:</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Contact Number:</label>
                    <input type="number" name="contact_number" required>
                </div>
                <div class="form-group">
                    <label>Password:</label>
                    <input type="password" name="password" required>
                </div>
                <div class="form-group">
                    <label>Role:</label>
                    <select name="role">
                        <option value="customer">Customer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-group" style="align-self: flex-end;">
                    <button type="submit" name="add_user" class="button">Add User</button>
                </div>
            </form>
            <h2>All Users</h2>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Contact Number</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php while($user = mysqli_fetch_assoc($users)): ?>
                        <tr>
                            <td><?= $user['ID'] ?></td>
                            <td><?= $user['username'] ?></td>
                            <td><?= $user['email'] ?></td>
                            <td><?= $user['contact_number'] ?></td>
                            <td><?= $user['role'] ?></td>
                            <td><?= $user['status'] ?></td>
                            <td>
                                <button type="button"
                                    class="edit-button button"
                                    onclick="openEditUserModal(this)"
                                    data-id="<?= $user['ID'] ?>"
                                    data-username="<?= $user['username'] ?>"
                                    data-contact="<?= $user['contact_number'] ?>"
                                >Edit</button>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['ID'] ?>">
                                    <input type="hidden" name="current_status" value="<?= $user['status'] ?>">
                                    <button type="submit" name="toggle_status" class="deactivate-button button">
                                        <?= $user['status']=='deactive' ? 'Activate' : 'Deactivate' ?>
                                    </button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="user_id" value="<?= $user['ID'] ?>">
                                    <select name="role" class="role-select">
                                        <option value="customer" <?= $user['role']=='customer'?'selected':'' ?>>Customer</option>
                                        <option value="admin" <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
                                    </select>
                                    <button type="submit" name="change_role" class="change-role-button button">Change Role</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <!-- Modal for editing user -->
            <div id="editUserModal" class="modal" style="display:none;">
                <div class="modal-content">
                    <button class="close" onclick="closeEditUserModal()">×</button>
                    <h2>Edit User</h2>
                    <form method="POST" action="" id="editUserModalForm">
                        <input type="hidden" name="user_id" id="modal_edit_user_id">
                        <label>Username:<br>
                            <input type="text" name="username" id="modal_edit_username" required>
                        </label>
                        <label>Contact Number:<br>
                            <input type="number" name="contact_number" id="modal_edit_contact" required>
                        </label>
                        <label>Password:<br>
                            <input type="password" name="password" id="modal_edit_password" placeholder="Leave blank to keep current password">
                        </label>
                        <button type="submit" name="update_user" class="button">Update User</button>
                    </form>
                    <form method="POST" action="" id="deleteUserModalForm" style="margin-top:10px;">
                        <input type="hidden" name="user_id" id="modal_delete_user_id">
                        <button type="submit" name="delete_user" class="button" style="background:#d9534f;color:#fff;width:100%;" onclick="return confirm('Are you sure you want to delete this user?');">Delete User</button>
                    </form>
                </div>
            </div>
            <script>
            function openEditUserModal(btn) {
                document.getElementById('modal_edit_user_id').value = btn.getAttribute('data-id');
                document.getElementById('modal_edit_username').value = btn.getAttribute('data-username');
                document.getElementById('modal_edit_contact').value = btn.getAttribute('data-contact');
                document.getElementById('modal_edit_password').value = '';
                // Set delete form user_id as well
                document.getElementById('modal_delete_user_id').value = btn.getAttribute('data-id');
                document.getElementById('editUserModal').style.display = 'block';
            }
            function closeEditUserModal() {
                document.getElementById('editUserModal').style.display = 'none';
            }
            // Optional: Close modal when clicking outside modal-content
            window.onclick = function(event) {
                var modal = document.getElementById('editUserModal');
                if (event.target === modal) {
                    closeEditUserModal();
                }
            }
            </script>
        </div>
    </div>
</body>
</html>
<?php $conn->close(); ?>