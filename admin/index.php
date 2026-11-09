<?php
// Admin Login Page
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin Login - Pharmacy Management</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body class="bg-dark d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow-lg p-4" style="width: 400px; border-radius: 10px;">
    <div class="text-center mb-3">
        <i class="fa-solid fa-prescription-bottle-medical fa-3x text-primary"></i>
        <h4 class="mt-2 fw-bold">Admin Portal</h4>
        <p class="text-muted small">Pharmacy Sales & Inventory</p>
    </div>
    <form action="dashboard.php" method="POST">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" class="form-control" id="username" placeholder="Enter username" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" class="form-control" id="password" placeholder="Enter password" required>
        </div>
        <button type="submit" class="btn btn-primary w-100 mt-2">Login</button>
    </form>
</div>

<?php include '../includes/admin_script.php'; ?>
</body>
</html>