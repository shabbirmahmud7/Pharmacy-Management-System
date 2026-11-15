<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

// Fetch all sales with customer information
$sql = "SELECT s.*, c.name as customer_name, c.phone as customer_phone 
        FROM sales_list s 
        LEFT JOIN customer_list c ON s.customer_id = c.id 
        ORDER BY s.id DESC";
$result = $conn->query($sql);
$total_sales = $result ? $result->num_rows : 0;
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice-dollar me-2"></i> Sales History / Invoices</h3>
                    <p class="text-muted mb-0">Total Completed Sales: <span class="badge bg-success"><?php echo $total_sales; ?></span></p>
                </div>
                <a href="pos.php" class="btn btn-primary"><i class="fa-solid fa-cash-register me-1"></i> Go to POS</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#ID</th>
                                    <th>Ref No</th>
                                    <th>Customer Name</th>
                                    <th>Total Amount (৳)</th>
                                    <th>Tendered</th>
                                    <th>Change</th>
                                    <th>Date & Time</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold">#<?php echo $row['id']; ?></td>
                                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['ref_no'] ?? 'N/A'); ?></span></td>
                                            <td><?php echo htmlspecialchars($row['customer_name'] ?? 'Walk-in Customer'); ?></td>
                                            <td class="fw-bold text-success">৳ <?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td>৳ <?php echo number_format($row['amount_tendered'], 2); ?></td>
                                            <td>৳ <?php echo number_format($row['amount_change'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                            <td>
                                                <a href="view_sale.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-eye me-1"></i> View Invoice</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No sales records found.</td>
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

<?php include '../includes/footer.php'; ?>