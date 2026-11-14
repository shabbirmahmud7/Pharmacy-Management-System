<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$id = intval($_GET['id'] ?? 0);

// Fetch receiving details with supplier info
$sql = "SELECT r.*, s.supplier_name, s.phone as supplier_phone, s.address as supplier_address, s.email as supplier_email 
        FROM receiving_list r 
        LEFT JOIN supplier_list s ON r.supplier_id = s.id 
        WHERE r.id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    header("Location: receivings.php");
    exit();
}

$row = $result->fetch_assoc();
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Receiving Details #<?php echo $row['id']; ?></h3>
                <a href="receivings.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">
                            Stock Purchase Summary
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3">
                                <div class="col-sm-6">
                                    <p class="text-muted mb-1">Receiving ID / Invoice:</p>
                                    <h5 class="fw-bold">#<?php echo $row['id']; ?></h5>
                                </div>
                                <div class="col-sm-6 text-sm-end">
                                    <p class="text-muted mb-1">Date & Time:</p>
                                    <h5 class="fw-bold"><?php echo htmlspecialchars($row['date_created'] ?? 'N/A'); ?></h5>
                                </div>
                            </div>

                            <hr>

                            <h5 class="fw-bold text-primary mb-3">Supplier Information</h5>
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Supplier Name:</strong> <?php echo htmlspecialchars($row['supplier_name'] ?? 'Walking Supplier'); ?></p>
                                    <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($row['supplier_phone'] ?? 'N/A'); ?></p>
                                </div>
                                <div class="col-sm-6">
                                    <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($row['supplier_email'] ?? 'N/A'); ?></p>
                                    <p class="mb-1"><strong>Address:</strong> <?php echo htmlspecialchars($row['supplier_address'] ?? 'N/A'); ?></p>
                                </div>
                            </div>

                            <hr>

                            <h5 class="fw-bold text-success mb-3">Purchase Summary</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tr class="table-light">
                                        <th>Total Received Items Quantity:</th>
                                        <td class="fw-bold"><?php echo htmlspecialchars($row['total_items']); ?> Units</td>
                                    </tr>
                                    <tr class="table-light">
                                        <th>Total Purchase Amount:</th>
                                        <td class="fw-bold text-success">৳ <?php echo number_format($row['total_amount'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>

                            <div class="text-center mt-4">
                                <button onclick="window.print();" class="btn btn-outline-secondary"><i class="fa-solid fa-print me-1"></i> Print Receipt</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>