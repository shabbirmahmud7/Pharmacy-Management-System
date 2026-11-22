<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (!in_array(intval($_SESSION['user_type'] ?? 1), [1, 3])) {
    header("Location: dashboard.php");
    exit();
}
include '../config/db_connect.php';


$sql = "SELECT p.*, c.name as category_name, s.supplier_name 
        FROM product_list p 
        LEFT JOIN category_list c ON p.category_id = c.id 
        LEFT JOIN supplier_list s ON p.supplier_id = s.id 
        ORDER BY p.id DESC";
$result = $conn->query($sql);

$total_medicines = $result ? $result->num_rows : 0;
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0">Medicine List</h3>
                    <p class="text-muted mb-0">Total Medicines in Database: <span class="badge bg-primary"><?php echo $total_medicines; ?></span></p>
                </div>
                <a href="add_medicine.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add New Medicine</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine Name</th>
                                    <th>Category</th>
                                    <th>Price (৳)</th>
                                    <th>Stock Quantity</th>
                                    <th>Status</th>
                                    <th>Expiry Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): 
                                        $stock = intval($row['quantity'] ?? 0);
                                    ?>
                                        <tr>
                                            <td><?php echo $i++; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['name']); ?></strong><br>
                                                <small class="text-muted">SKU: <?php echo htmlspecialchars($row['sku'] ?? 'N/A'); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['category_name'] ?? 'Uncategorized'); ?></td>
                                            <td>৳ <?php echo number_format($row['price'] ?? 0, 2); ?></td>
                                            <td>
                                                <span class="fw-bold <?php echo $stock > 0 ? 'text-success' : 'text-danger'; ?>">
                                                    <?php echo $stock; ?> Units
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($stock > 10): ?>
                                                    <span class="badge bg-success">In Stock</span>
                                                <?php elseif ($stock > 0 && $stock <= 10): ?>
                                                    <span class="badge bg-warning text-dark">Low Stock</span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger">Out of Stock</span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['expiry_date'] ?? 'N/A'); ?></td>
                                            <td>
                                                <a href="edit_medicine.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-primary"><i class="fa-solid fa-pen-to-square"></i></a>
                                                <a href="delete_medicine.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this medicine?');"><i class="fa-solid fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">No medicines found in database.</td>
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