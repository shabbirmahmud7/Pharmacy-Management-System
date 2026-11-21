<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$id = intval($_GET['id'] ?? 0);

// Fetch receiving info
$rec_q = $conn->query("SELECT r.*, s.supplier_name, s.phone, s.address 
                       FROM receiving_list r 
                       LEFT JOIN supplier_list s ON r.supplier_id = s.id 
                       WHERE r.id = $id");
if (!$rec_q || $rec_q->num_rows == 0) {
    header("Location: receivings.php");
    exit();
}
$receiving = $rec_q->fetch_assoc();

// Fetch items
$items_q = $conn->query("SELECT ri.*, p.name as product_name 
                         FROM receiving_items ri 
                         LEFT JOIN product_list p ON ri.product_id = p.id 
                         WHERE ri.receiving_id = $id");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice me-2 text-primary"></i> Receiving Invoice #<?php echo $receiving['id']; ?></h3>
                <div>
                    <button onclick="window.print();" class="btn btn-dark fw-bold me-2"><i class="fa-solid fa-print me-1"></i> Print</button>
                    <a href="receivings.php" class="btn btn-secondary fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Back</a>
                </div>
            </div>

            <div class="card shadow-sm border-0 p-4 bg-white">
                <div class="row mb-4">
                    <div class="col-sm-6">
                        <h5 class="fw-bold text-primary">Supplier Details:</h5>
                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($receiving['supplier_name'] ?? 'N/A'); ?></p>
                        <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($receiving['contact'] ?? 'N/A'); ?></p>
                        <p class="mb-0"><strong>Address:</strong> <?php echo htmlspecialchars($receiving['address'] ?? 'N/A'); ?></p>
                    </div>
                    <div class="col-sm-6 text-sm-end">
                        <h5 class="fw-bold text-dark">Purchase Info:</h5>
                        <p class="mb-1"><strong>Receiving ID:</strong> #<?php echo $receiving['id']; ?></p>
                        <p class="mb-0"><strong>Date:</strong> <?php echo $receiving['date_created']; ?></p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Medicine Name</th>
                                <th>Quantity Received</th>
                                <th>Unit Purchase Price (৳)</th>
                                <th>Total (৳)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($items_q && $items_q->num_rows > 0): ?>
                                <?php $i = 1; while ($item = $items_q->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $i++; ?></td>
                                        <td class="fw-bold"><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td><?php echo $item['qty']; ?></td>
                                        <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                                        <td class="fw-bold text-success">৳ <?php echo number_format($item['total'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </tbody>
                        tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Grand Total:</th>
                                <th class="text-success fs-5">৳ <?php echo number_format($receiving['total_amount'], 2); ?></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?> 