<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$sale_id = intval($_GET['id'] ?? 0);

// Fetch Sale details with customer info
$sale_res = $conn->query("SELECT s.*, c.name as customer_name, c.phone as customer_phone, c.address as customer_address 
                          FROM sales_list s 
                          LEFT JOIN customer_list c ON s.customer_id = c.id 
                          WHERE s.id = $sale_id");

if (!$sale_res || $sale_res->num_rows == 0) {
    header("Location: sales.php");
    exit();
}

$sale = $sale_res->fetch_assoc();

// Fetch Sale Items (Medicines purchased)
$items_res = $conn->query("SELECT si.*, p.name as product_name, p.sku 
                           FROM sale_items si 
                           LEFT JOIN product_list p ON si.product_id = p.id 
                           WHERE si.sale_id = $sale_id");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Invoice #<?php echo $sale['id']; ?></h3>
                <div>
                    <a href="sales.php" class="btn btn-secondary me-2"><i class="fa-solid fa-arrow-left me-1"></i> Back to Sales</a>
                    <button onclick="window.print();" class="btn btn-dark"><i class="fa-solid fa-print me-1"></i> Print Invoice</button>
                </div>
            </div>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm border-0 p-4">
                        <!-- Invoice Header -->
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary">PHARMACY MANAGEMENT SYSTEM</h2>
                            <p class="text-muted mb-1">Official Sales Receipt / Invoice</p>
                            <span class="badge bg-secondary">Ref: <?php echo htmlspecialchars($sale['ref_no']); ?></span>
                        </div>

                        <hr>

                        <!-- Customer & Invoice Info -->
                        <div class="row mb-4">
                            <div class="col-sm-6">
                                <h6 class="fw-bold text-dark">Customer Details:</h6>
                                <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer'); ?></p>
                                <p class="mb-1"><strong>Phone:</strong> <?php echo htmlspecialchars($sale['customer_phone'] ?? 'N/A'); ?></p>
                                <p class="mb-1"><strong>Address:</strong> <?php echo htmlspecialchars($sale['customer_address'] ?? 'N/A'); ?></p>
                            </div>
                            <div class="col-sm-6 text-sm-end">
                                <h6 class="fw-bold text-dark">Invoice Details:</h6>
                                <p class="mb-1"><strong>Sale ID:</strong> #<?php echo $sale['id']; ?></p>
                                <p class="mb-1"><strong>Date & Time:</strong> <?php echo htmlspecialchars($sale['date_created']); ?></p>
                            </div>
                        </div>

                        <!-- Purchased Items Table -->
                        <div class="table-responsive mb-4">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Medicine Name</th>
                                        <th>Price (৳)</th>
                                        <th>Qty</th>
                                        <th>Total (৳)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($items_res && $items_res->num_rows > 0): ?>
                                        <?php $i = 1; while ($item = $items_res->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item['product_name']); ?></strong><br>
                                                    <small class="text-muted">SKU: <?php echo htmlspecialchars($item['sku'] ?? 'N/A'); ?></small>
                                                </td>
                                                <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['qty']; ?></td>
                                                <td class="fw-bold">৳ <?php echo number_format($item['total'], 2); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Bill Calculation Summary -->
                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <table class="table table-sm table-borderless">
                                    <tr>
                                        <td class="fw-bold">Grand Total:</td>
                                        <td class="text-end fw-bold text-success fs-5">৳ <?php echo number_format($sale['total_amount'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Amount Tendered (Paid):</td>
                                        <td class="text-end">৳ <?php echo number_format($sale['amount_tendered'], 2); ?></td>
                                    </tr>
                                    <tr>
                                        <td>Change Returned:</td>
                                        <td class="text-end fw-bold">৳ <?php echo number_format($sale['amount_change'], 2); ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <div class="text-center mt-5 text-muted small">
                            <p class="mb-0">Thank you for your purchase! Get well soon.</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>