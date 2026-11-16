<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_user'])) {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $type = intval($_POST['type'] ?? 2);

    if (!empty($name) && !empty($username) && !empty($password)) {
        // Sanitize inputs for simple query
        $safe_name = $conn->real_escape_string($name);
        $safe_user = $conn->real_escape_string($username);

     
        $check = $conn->query("SELECT id FROM users WHERE username = '$safe_user'");

        if ($check && $check->num_rows > 0) {
            $error = "Username '$username' is already taken!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, username, password, type) VALUES ('$safe_name', '$safe_user', '$hashed_password', $type)";

            if ($conn->query($sql) === TRUE) {
                $success = "New user ($username) added successfully!";
            } else {
                $error = "Database Error: " . $conn->error;
            }
        }
    } else {
        $error = "Please fill in all required fields!";
    }
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $uid = intval($_POST['user_id'] ?? 0);
    $new_pass = trim($_POST['new_password'] ?? '');

    if ($uid > 0 && !empty($new_pass)) {
        $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = '$hashed' WHERE id = $uid";

        if ($conn->query($sql) === TRUE) {
            $success = "Password updated successfully!";
        } else {
            $error = "Failed to update password.";
        }
    } else {
        $error = "Please enter a valid new password!";
    }
}

if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);

    if ($del_id == $_SESSION['user_id']) {
        $error = "You cannot delete your own active account!";
    } elseif ($del_id > 0) {
        $conn->query("DELETE FROM users WHERE id = $del_id");
        $success = "User deleted successfully!";
    }
}

$users = $conn->query("SELECT * FROM users ORDER BY id ASC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-user-gear me-2 text-primary"></i> User Management</h3>
                    <p class="text-muted mb-0">Manage system administrators and staff accounts</p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row g-4">
             
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="fa-solid fa-user-plus me-1"></i> Add New User
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="e.g. Rahim Uddin" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" placeholder="e.g. rahim_cashier" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">User Role</label>
                                    <select name="type" class="form-control" required>
                                        <option value="2">Staff / Cashier</option>
                                        <option value="1">Admin</option>
                                    </select>
                                </div>
                                <button type="submit" name="add_user" class="btn btn-success w-100 fw-bold">
                                    <i class="fa-solid fa-save me-1"></i> Create Account
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Users List Table -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">
                            <i class="fa-solid fa-users me-1"></i> System Users List
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Role</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($users && $users->num_rows > 0): ?>
                                            <?php $i = 1; while ($row = $users->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td class="fw-bold">
                                                        <?php echo htmlspecialchars($row['name'] ?? 'Admin User'); ?>
                                                        <?php if ($row['id'] == $_SESSION['user_id']): ?>
                                                            <span class="badge bg-info text-dark ms-1">You</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                                                    <td>
                                                        <?php if (($row['type'] ?? 1) == 1): ?>
                                                            <span class="badge bg-primary">Admin</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-secondary">Staff / Cashier</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td>
                                                        <!-- Password Reset Button -->
                                                        <button type="button" class="btn btn-sm btn-warning text-dark" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#resetModal<?php echo $row['id']; ?>">
                                                            <i class="fa-solid fa-key me-1"></i> Reset Pass
                                                        </button>

                                                        <!-- Delete Button (Hidden for current user) -->
                                                        <?php if ($row['id'] != $_SESSION['user_id']): ?>
                                                            <a href="users.php?delete_id=<?php echo $row['id']; ?>" 
                                                               class="btn btn-sm btn-danger" 
                                                               onclick="return confirm('Are you sure you want to delete this user?');">
                                                                <i class="fa-solid fa-trash"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>

                                                <!-- Password Reset Modal for each user -->
                                                <div class="modal fade" id="resetModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <form method="POST" action="">
                                                                <div class="modal-header">
                                                                    <h6 class="modal-title fw-bold">Reset Password</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <p class="small text-muted mb-2">User: <strong><?php echo htmlspecialchars($row['username']); ?></strong></p>
                                                                    <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                                                                    <label class="form-label small">New Password</label>
                                                                    <input type="password" name="new_password" class="form-control form-control-sm" placeholder="Enter new password" required>
                                                                </div>
                                                                <div class="modal-footer py-1">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" name="reset_password" class="btn btn-sm btn-primary">Save</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No users found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

```