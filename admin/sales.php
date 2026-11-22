<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';


$filter = $_GET['filter'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';


$where_sql = "1=1";
$report_title = "All Time Sales History";

if ($filter == 'today') {
    $where_sql = "DATE(s.date_created) = CURDATE()";
    $report_title = "Today's Sales Report (" . date('M j, Y') . ")";
} elseif ($filter == '7days') {
    $where_sql = "s.date_created >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $report_title = "Last 7 Days Sales Report";
} elseif ($filter == 'month') {
    $where_sql = "MONTH(s.date_created) = MONTH(CURRENT_DATE()) AND YEAR(s.date_created) = YEAR(CURRENT_DATE())";
    $report_title = "This Month's Sales Report (" . date('F Y') . ")";
} elseif ($filter == 'custom' && !empty($start_date) && !empty($end_date)) {
    $where_sql = "DATE(s.date_created) BETWEEN '$start_date' AND '$end_date'";
    $report_title = "Sales Report ($start_date to $end_date)";
}

// Fetch filtered sales list
$sql = "SELECT s.*, c.name as customer_name, c.phone as customer_phone 
        FROM sales_list s 
        LEFT JOIN customer_list c ON s.customer_id = c.id 
        WHERE $where_sql 
        ORDER BY s.id DESC";
$result = $conn->query($sql);
$total_sales_count = $result ? $result->num_rows : 0;


$rev_sql = "SELECT SUM(s.total_amount) as total_rev FROM sales_list s WHERE $where_sql";
$rev_res = $conn->query($rev_sql);
$filtered_revenue = ($rev_res) ? $rev_res->fetch_assoc()['total_rev'] ?? 0 : 0;
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <!-- Header & Action -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i> Sales History & Reports</h3>
                    <p class="text-muted mb-0"><?php echo $report_title; ?></p>
                </div>
                <a href="pos.php" class="btn btn-success fw-bold"><i class="fa-solid fa-cash-register me-1"></i> Open POS</a>
            </div>

            <!-- Filter & Summary Bar Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body bg-white">
                    <form method="GET" action="" class="row g-3 align-items-center">
                        <!-- Quick Filter Buttons -->
                        <div class="col-lg-6">
                            <label class="form-label fw-bold text-muted small d-block">Quick Filters:</label>
                            <div class="btn-group" role="group">
                                <a href="sales.php?filter=all" class="btn btn-outline-dark btn-sm <?php echo ($filter == 'all') ? 'active' : ''; ?>">All Time</a>
                                <a href="sales.php?filter=today" class="btn btn-outline-primary btn-sm <?php echo ($filter == 'today') ? 'active' : ''; ?>">Today</a>
                                <a href="sales.php?filter=7days" class="btn btn-outline-primary btn-sm <?php echo ($filter == '7days') ? 'active' : ''; ?>">Last 7 Days</a>
                                <a href="sales.php?filter=month" class="btn btn-outline-primary btn-sm <?php echo ($filter == 'month') ? 'active' : ''; ?>">This Month</a>
                            </div>
                        </div>

                        <!-- Custom Date Range Form -->
                        <div class="col-lg-6">
                            <label class="form-label fw-bold text-muted small d-block">Custom Date Range:</label>
                            <div class="input-group input-group-sm">
                                <input type="hidden" name="filter" value="custom">
                                <input type="date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>" required>
                                <span class="input-group-text bg-light">to</span>
                                <input type="date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>" required>
                                <button type="submit" class="btn btn-dark"><i class="fa-solid fa-filter me-1"></i> Filter</button>
                                <?php if ($filter != 'all'): ?>
                                    <a href="sales.php" class="btn btn-outline-secondary" title="Reset Filter"><i class="fa-solid fa-rotate-right"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Stats Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Filtered Invoices Count</h6>
                                <h3 class="fw-bold text-primary mb-0"><?php echo number_format($total_sales_count); ?></h3>
                            </div>
                            <div class="fs-1 text-primary opacity-50"><i class="fa-solid fa-receipt"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Revenue (Filtered)</h6>
                                <h3 class="fw-bold text-success mb-0">Taka <?php echo number_format($filtered_revenue, 2); ?></h3>
                            </div>
                            <div class="fs-1 text-success opacity-50"><i"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Table -->
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
                                        <td colspan="8" class="text-center text-muted py-4">No sales records found for the selected range.</td>
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