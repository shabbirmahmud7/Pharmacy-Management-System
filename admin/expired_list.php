<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';


$current_date = date('Y-m-d');
$sql = "SELECT p.*, c.name as cat_name, t.name as type_name, s.supplier_name 
        FROM product_list p 
        LEFT JOIN category_list c ON p.category_id = c.id 
        LEFT JOIN type_list t ON p.type_id = t.id 
        LEFT JOIN supplier_list s ON p.supplier_id = s.id 
        WHERE p.expiry_date <= '$current_date' 
        ORDER BY p.expiry_date ASC";

$result = $conn->query($sql);
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold text-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i> Expired Medicine List</h3>
                <span class="badge bg-danger p-2 fs-6">Alert: Past Expiry Date</span>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>SKU</th>
                                    <th>Medicine Name</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Supplier</th>
                                    <th>Expiry Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && $result->num_rows > 0): ?>
                                    <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                        <tr class="table-danger">
                                            <td><?php echo $i++; ?></td>
                                            <td><?php echo htmlspecialchars($row['sku'] ?? ''); ?></td>
                                            <td><strong><?php echo htmlspecialchars($row['name'] ?? ''); ?></strong></td>
                                            <td><?php echo htmlspecialchars($row['cat_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['type_name'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($row['supplier_name'] ?? 'N/A'); ?></td>
                                            <td class="fw-bold text-danger"><?php echo htmlspecialchars($row['expiry_date'] ?? ''); ?></td>
                                            <td>
                                                <a href="delete_medicine.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this expired medicine?');"><i class="fa-solid fa-trash"></i> Remove</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-4">Great! No expired medicines found in the inventory.</td>
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