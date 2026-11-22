<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

// --- ADDED: Pure PHP GET Search for Inventory Medicines (No AJAX) ---
$med_search = trim($_GET['med_search'] ?? '');
$search_results = null;
if ($med_search !== '') {
    $safe_search = $conn->real_escape_string($med_search);
    $sql_search = "SELECT p.*, c.name as category_name 
                   FROM product_list p 
                   LEFT JOIN category_list c ON p.category_id = c.id 
                   WHERE p.name LIKE '%$safe_search%' 
                   ORDER BY p.name ASC";
    $search_results = $conn->query($sql_search);
}
// ------------------------------------------------------------------

$med_res = $conn->query("SELECT COUNT(*) as count FROM product_list");
$total_medicines = ($med_res) ? $med_res->fetch_assoc()['count'] : 0;


$low_res = $conn->query("SELECT COUNT(*) as count FROM product_list WHERE quantity <= 10");
$low_stock = ($low_res) ? $low_res->fetch_assoc()['count'] : 0;


$sales_res = $conn->query("SELECT COUNT(*) as total_sales, SUM(total_amount) as total_revenue FROM sales_list");
$sales_data = ($sales_res) ? $sales_res->fetch_assoc() : ['total_sales' => 0, 'total_revenue' => 0];
$total_sales = $sales_data['total_sales'] ?? 0;
$total_revenue = $sales_data['total_revenue'] ?? 0;


$cust_res = $conn->query("SELECT COUNT(*) as count FROM customer_list");
$total_customers = ($cust_res) ? $cust_res->fetch_assoc()['count'] : 0;


$chart_labels = [];
$chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $day_name = date('D, M j', strtotime("-$i days"));
    
    $q = $conn->query("SELECT SUM(total_amount) as daily_total FROM sales_list WHERE DATE(date_created) = '$date'");
    $row = $q ? $q->fetch_assoc() : ['daily_total' => 0];
    $total = $row['daily_total'] ?? 0;
    
    $chart_labels[] = $day_name;
    $chart_data[] = floatval($total);
}


$medicines_preview = $conn->query("SELECT p.*, c.name as category_name FROM product_list p LEFT JOIN category_list c ON p.category_id = c.id ORDER BY p.id DESC LIMIT 5");


$recent_sales = $conn->query("SELECT s.*, c.name as customer_name FROM sales_list s LEFT JOIN customer_list c ON s.customer_id = c.id ORDER BY s.id DESC LIMIT 5");
?>

<?php include '../includes/header.php'; ?>

<!-- Chart.js CDN for Bar Chart -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <!-- Welcome Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-1">Admin Dashboard</h3>
                    <p class="text-muted mb-0">Welcome back! Here is a quick overview of your pharmacy system.</p>
                </div>
                <div>
                    <a href="pos.php" class="btn btn-success fw-bold me-2"><i class="fa-solid fa-cash-register me-1"></i> Open POS</a>
                    <a href="add_medicine.php" class="btn btn-primary fw-bold"><i class="fa-solid fa-plus me-1"></i> Add Medicine</a>
                </div>
            </div>

            <!-- ADDED: Top Inventory Medicine Search Bar (PHP + Bootstrap, No AJAX) -->
            <div class="card shadow-sm border-0 bg-white mb-4">
                <div class="card-body">
                    <form method="GET" action="dashboard.php" class="row g-2 align-items-center">
                        <div class="col-md-9 col-sm-8">
                            <div class="input-group">
                                <span class="input-group-text bg-white">
                                    <i class="fa-solid fa-magnifying-glass text-primary"></i>
                                </span>
                                <input type="text"
                                       name="med_search"
                                       class="form-control"
                                       placeholder="Search medicine in inventory by name..."
                                       value="<?php echo htmlspecialchars($med_search); ?>">
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-4 d-flex gap-2">
                            <button type="submit" class="btn btn-primary fw-bold w-100">
                                <i class="fa-solid fa-search me-1"></i> Search
                            </button>
                            <?php if ($med_search !== ''): ?>
                                <a href="dashboard.php" class="btn btn-outline-secondary fw-bold">Reset</a>
                            <?php endif; ?>
                        </div>
                    </form>

                    <?php if ($med_search !== ''): ?>
                        <div class="mt-3">
                            <div class="small text-muted mb-2">
                                Search results for <strong><?php echo htmlspecialchars($med_search); ?></strong>
                                (<?php echo $search_results ? $search_results->num_rows : 0; ?> found)
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($search_results && $search_results->num_rows > 0): ?>
                                            <?php while ($med = $search_results->fetch_assoc()): 
                                                $stock = intval($med['quantity'] ?? 0);
                                            ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($med['name']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($med['category_name'] ?? 'N/A'); ?></td>
                                                    <td>৳ <?php echo number_format($med['price'], 2); ?></td>
                                                    <td class="fw-bold"><?php echo $stock; ?></td>
                                                    <td>
                                                        <?php if ($stock > 10): ?>
                                                            <span class="badge bg-success">In Stock</span>
                                                        <?php elseif ($stock > 0 && $stock <= 10): ?>
                                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">
                                                    No medicine found matching "<strong><?php echo htmlspecialchars($med_search); ?></strong>".
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <!-- ------------------------------------------------------------------ -->

            <!-- Analytics Cards Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Medicines</h6>
                                <h3 class="fw-bold text-primary mb-0"><?php echo number_format($total_medicines); ?></h3>
                            </div>
                            <div class="fs-1 text-primary opacity-50"><i class="fa-solid fa-pills"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Low Stock Alerts</h6>
                                <h3 class="fw-bold text-danger mb-0"><?php echo number_format($low_stock); ?></h3>
                            </div>
                            <div class="fs-1 text-danger opacity-50"><i class="fa-solid fa-triangle-exclamation"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Revenue</h6>
                                <h3 class="fw-bold text-success mb-0">৳ <?php echo number_format($total_revenue, 2); ?></h3>
                            </div>
                            <div class="fs-1 text-success opacity-50"><i class="fa-solid fa-bangladeshi-taka-sign"></i></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card shadow-sm border-0 bg-white p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1">Total Customers</h6>
                                <h3 class="fw-bold text-info mb-0"><?php echo number_format($total_customers); ?></h3>
                            </div>
                            <div class="fs-1 text-info opacity-50"><i class="fa-solid fa-users"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Bar Chart Section -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white py-3">
                            <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chart-column me-2 text-primary"></i> Sales Overview (Last 7 Days)</h5>
                        </div>
                        <div class="card-body">
                            <canvas id="salesBarChart" height="85"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Two Columns Row: Medicines Snapshot & Recent Sales -->
            <div class="row g-4">
                <!-- Left: Medicines Stock Preview -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="fw-bold text-dark"><i class="fa-solid fa-boxes-stacked me-2 text-primary"></i> Medicine Stock Snapshot</span>
                            <a href="medicines.php" class="btn btn-sm btn-outline-primary fw-bold">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Name</th>
                                            <th>Category</th>
                                            <th>Price</th>
                                            <th>Stock</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($medicines_preview && $medicines_preview->num_rows > 0): ?>
                                            <?php while ($med = $medicines_preview->fetch_assoc()): 
                                                $stock = intval($med['quantity'] ?? 0);
                                            ?>
                                                <tr>
                                                    <td><strong><?php echo htmlspecialchars($med['name']); ?></strong></td>
                                                    <td><?php echo htmlspecialchars($med['category_name'] ?? 'N/A'); ?></td>
                                                    <td>৳ <?php echo number_format($med['price'], 2); ?></td>
                                                    <td class="fw-bold"><?php echo $stock; ?></td>
                                                    <td>
                                                        <?php if ($stock > 10): ?>
                                                            <span class="badge bg-success">In Stock</span>
                                                        <?php elseif ($stock > 0 && $stock <= 10): ?>
                                                            <span class="badge bg-warning text-dark">Low Stock</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">Out of Stock</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No medicines found.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Recent Sales Transactions -->
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                            <span class="fw-bold text-dark"><i class="fa-solid fa-clock-rotate-left me-2 text-success"></i> Recent Sales</span>
                            <a href="sales.php" class="btn btn-sm btn-outline-success fw-bold">View All</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Ref No</th>
                                            <th>Customer</th>
                                            <th>Amount</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($recent_sales && $recent_sales->num_rows > 0): ?>
                                            <?php while ($sale = $recent_sales->fetch_assoc()): ?>
                                                <tr>
                                                    <td><span class="badge bg-secondary"><?php echo htmlspecialchars($sale['ref_no'] ?? 'N/A'); ?></span></td>
                                                    <td><?php echo htmlspecialchars($sale['customer_name'] ?? 'Walk-in'); ?></td>
                                                    <td class="fw-bold text-success">taka <?php echo number_format($sale['total_amount'], 2); ?></td>
                                                    <td><small><?php echo date('M j, g:i A', strtotime($sale['date_created'])); ?></small></td>
                                                    <td>
                                                        <a href="view_sale.php?id=<?php echo $sale['id']; ?>" class="btn btn-sm btn-outline-info py-0 px-2"><i class="fa-solid fa-eye"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">No recent sales found.</td>
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
    </div>
</div>

<!-- Chart.js Initialization Script -->
<script>
const ctx = document.getElementById('salesBarChart').getContext('2d');
const salesBarChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($chart_labels); ?>,
        datasets: [{
            label: 'Daily Sales (taka)',
            data: <?php echo json_encode($chart_data); ?>,
            backgroundColor: 'rgba(13, 110, 253, 0.7)',
            borderColor: 'rgba(13, 110, 253, 1)',
            borderWidth: 1,
            borderRadius: 4
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) { return 'taka ' + value; }
                }
            }
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>