<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

// Fetch Receivings / Stock Purchases with Supplier details
$sql = "SELECT r.*, s.supplier_name 
        FROM receiving_list r 
        LEFT JOIN supplier_list s ON r.supplier_id = s.id 
        ORDER BY r.id DESC";
$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Stock Receivings (Purchases)</h3>
                <a href="add_receiving.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add New Receiving</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Receiving Date</th>
                                    <th>Supplier Name</th>
                                    <th>Total Items</th>
                                    <th>Total Amount</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($row['date_created'] ?? $row['created_at'] ?? 'N/A'); ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['supplier_name'] ?? 'Walking Supplier'); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['total_items'] ?? '0'); ?></td>
                                            <td>৳ <?php echo number_format($row['total_amount'] ?? 0, 2); ?></td>
                                            <td>
                                                <a href="view_receiving.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white"><i class="fa-solid fa-eye"></i> View</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">No stock receivings found.</td>
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