<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// Fetch Suppliers and Medicines for form dropdowns
$suppliers = $conn->query("SELECT * FROM supplier_list ORDER BY supplier_name ASC");
$medicines = $conn->query("SELECT * FROM product_list ORDER BY name ASC");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_receiving'])) {
    $supplier_id = intval($_POST['supplier_id'] ?? 0);
    $product_ids = $_POST['product_id'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $prices = $_POST['price'] ?? [];

    if ($supplier_id > 0 && !empty($product_ids)) {
        $total_amount = 0;
        
        // Calculate total amount first
        for ($i = 0; $i < count($product_ids); $i++) {
            $q = floatval($qtys[$i]);
            $p = floatval($prices[$i]);
            $total_amount += ($q * $p);
        }

        // Insert into receiving_list
        $sql_rec = "INSERT INTO receiving_list (supplier_id, total_amount) VALUES ($supplier_id, $total_amount)";
        if ($conn->query($sql_rec) === TRUE) {
            $receiving_id = $conn->insert_id;

            // Insert items and update product quantity
            for ($i = 0; $i < count($product_ids); $i++) {
                $p_id = intval($product_ids[$i]);
                $q = intval($qtys[$i]);
                $p = floatval($prices[$i]);
                $total = $q * $p;

                if ($p_id > 0 && $q > 0) {
                    // Insert receiving item
                    $conn->query("INSERT INTO receiving_items (receiving_id, product_id, qty, price, total) VALUES ($receiving_id, $p_id, $q, $p, $total)");

                    // Update main product stock quantity
                    $conn->query("UPDATE product_list SET quantity = quantity + $q WHERE id = $p_id");
                }
            }

            header("Location: receivings.php?msg=success");
            exit();
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Please select a supplier and at least one medicine item!";
    }
}
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-cart-plus me-2 text-primary"></i> Add Stock Receiving</h3>
                    <p class="text-muted mb-0">Record new medicines purchased from a supplier</p>
                </div>
                <a href="receivings.php" class="btn btn-secondary fw-bold">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to List
                </a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Select Supplier</label>
                                <select name="supplier_id" class="form-control" required>
                                    <option value="">-- Choose Supplier --</option>
                                    <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                                        <?php while ($sup = $suppliers->fetch_assoc()): ?>
                                            <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['name']); ?></option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Table -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-dark text-white fw-bold">
                        <i class="fa-solid fa-pills me-1"></i> Purchase Items
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered align-middle" id="itemTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="45%">Medicine Name</th>
                                    <th width="20%">Quantity</th>
                                    <th width="25%">Purchase Price (৳)</th>
                                    <th width="10%">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemRows">
                                <tr>
                                    <td>
                                        <select name="product_id[]" class="form-control" required>
                                            <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
                                            <?php 
                                            // Reset pointer for medicines
                                            if ($medicines) { $medicines->data_seek(0); }
                                            while ($med = $medicines->fetch_assoc()): 
                                            ?>
                                                <option value="<?php echo $med['id']; ?>"><?php echo htmlspecialchars($med['name']); ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="qty[]" class="form-control" placeholder="Qty" min="1" required>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" name="price[]" class="form-control" placeholder="Unit Price" min="0" required>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm removeRow"><i class="fa-solid fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" id="addRow" class="btn btn-sm btn-success fw-bold">
                            <i class="fa-solid fa-plus me-1"></i> Add Another Item
                        </button>
                    </div>
                </div>

                <button type="submit" name="save_receiving" class="btn btn-primary btn-lg fw-bold w-100">
                    <i class="fa-solid fa-save me-1"></i> Save Receiving & Update Stock
                </button>
            </form>

        </div>
    </div>
</div>

<!-- Simple JavaScript to Add/Remove Rows dynamically -->
<script>
document.getElementById('addRow').addEventListener('click', function() {
    let tbody = document.getElementById('itemRows');
    let firstRow = tbody.querySelector('tr');
    let newRow = firstRow.cloneNode(true);
    
    // Clear input values in the cloned row
    newRow.querySelectorAll('input').forEach(input => input.value = '');
    newRow.querySelectorAll('select').forEach(select => select.value = '');
    
    tbody.appendChild(newRow);
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
        let tbody = document.getElementById('itemRows');
        if (tbody.rows.length > 1) {
            e.target.closest('tr').remove();
        } else {
            alert("At least one item is required!");
        }
    }
});
</script>

<?php include '../includes/footer.php'; ?>