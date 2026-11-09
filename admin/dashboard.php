<?php
// Admin Dashboard Control Panel
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - Pharmacy Management</title>
    <?php include '../includes/admin_css.php'; ?>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar Included -->
    <?php include '../includes/sidebar.php'; ?>

    <!-- Main Content Area -->
    <div class="w-100 bg-light">
        <!-- Topbar Included -->
        <?php include '../includes/topbar.php'; ?>

        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4">Dashboard Overview</h3>
            
            <!-- Information Cards -->
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total Medicines</h6>
                            <h2 class="fw-bold">8</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Total Sales Today</h6>
                            <h2 class="fw-bold">BDT 860.00</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-dark shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Expired Medicines</h6>
                            <h2 class="fw-bold">1</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white shadow-sm">
                        <div class="card-body">
                            <h6 class="card-title">Suppliers</h6>
                            <h2 class="fw-bold">3</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-4 shadow-sm">
                <div class="card-header bg-white fw-bold">
                    System Quick Activity
                </div>
                <div class="card-body">
                    <p class="card-text">Welcome to the Pharmacy Sales and Inventory System administration page.</p>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/admin_script.php'; ?>
</body>
</html>