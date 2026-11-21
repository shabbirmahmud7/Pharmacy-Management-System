<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

// Handle Delete Receiving
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    
    // Optional: revert stock if needed, or simply delete record and items
    $items = $conn->query("SELECT * FROM receiving_items WHERE receiving_id = $del_id");
    if ($items) {
        while ($item = $items->fetch_assoc()) {
            $p_id = $item['product_id'];
            $qty = $item['qty'];
            // Reduce stock back when receiving record is deleted
            $conn->query("UPDATE product_list SET quantity = quantity - $qty WHERE id = $p_id");
        }
    }
    
    $conn->query("DELETE FROM receiving_items WHERE receiving_id = $del_id");
    $conn->query("DELETE FROM receiving_list WHERE id = $del_id");
    
    header("Location: receivings.php?msg=deleted");
    exit();
}

// Fetch all receivings with supplier names
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
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-truck-ramp-box me-2 text-primary"></i> Stock Receivings & Purchases</h3>
                    <p class="text-muted mb-0">Track medicine purchases from suppliers and stock updates</p>
                </div>
                <a href="add_receiving.php" class="btn btn-primary fw-bold">
                    <i class="fa-solid fa-plus me-1"></i> Add New Receiving
                </a>
            </div>

            <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
                <div class="alert alert-success">Receiving record deleted successfully!</div>
            <?php endif; ?>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#ID</th>
                                    <th>Date</th>
                                    <th>Supplier Name</th>
                                    <th>Total Amount (৳)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td class="fw-bold">#<?php echo $row['id']; ?></td>
                                            <td><?php echo htmlspecialchars($row['date_created']); ?></td>
                                            <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'General Supplier'); ?></td>
                                            <td class="fw-bold text-success">৳ <?php echo number_format($row['total_amount'], 2); ?></td>
                                            <td>
                                                <a href="view_receiving.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info text-white">
                                                    <i class="fa-solid fa-eye me-1"></i> View Details
                                                </a>
                                                <a href="receivings.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this receiving record? Stock will be adjusted.');">
                                                    <i class="fa-solid fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">No stock receiving records found.</td>
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