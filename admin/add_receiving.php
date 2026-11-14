<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// Fetch Suppliers for Dropdown
$suppliers = $conn->query("SELECT * FROM supplier_list ORDER BY supplier_name ASC");

// Fetch Medicines for Dropdown
$medicines = $conn->query("SELECT * FROM product_list ORDER BY name ASC");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_receiving'])) {
    $supplier_id = $_POST['supplier_id'];
    $product_id = $_POST['product_id'];
    $qty = intval($_POST['qty']);
    $price = floatval($_POST['price']);
    $expiry_date = $_POST['expiry_date'];
    
    $total_amount = $qty * $price;

    if (!empty($supplier_id) && !empty($product_id) && $qty > 0) {
        // Insert into receiving_list table
        $sql = "INSERT INTO receiving_list (supplier_id, total_items, total_amount, date_created) 
                VALUES ('$supplier_id', '$qty', '$total_amount', NOW())";
        if ($conn->query($sql) === TRUE) {
            // Update product stock quantity and expiry date in product_list table
            $conn->query("UPDATE product_list SET quantity = quantity + $qty, expiry_date = '$expiry_date', supplier_id = '$supplier_id' WHERE id = $product_id");
            
            $success = "Stock received and inventory updated successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Please fill in all required fields correctly!";
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
                <h3 class="fw-bold">Add Stock Receiving</h3>
                <a href="receivings.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6 mx-auto">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Stock Purchase Form</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Select Supplier</label>
                                    <select name="supplier_id" class="form-control" required>
                                        <option value="">-- Choose Supplier --</option>
                                        <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                                            <?php while ($sup = $suppliers->fetch_assoc()): ?>
                                                <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Select Medicine</label>
                                    <select name="product_id" class="form-control" required>
                                        <option value="">-- Choose Medicine --</option>
                                        <?php if ($medicines && $medicines->num_rows > 0): ?>
                                            <?php while ($med = $medicines->fetch_assoc()): ?>
                                                <option value="<?php echo $med['id']; ?>"><?php echo htmlspecialchars($med['name']); ?> (SKU: <?php echo htmlspecialchars($med['sku']); ?>)</s-option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Receive Quantity</label>
                                    <input type="number" name="qty" class="form-control" min="1" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Purchase Price (Per Unit)</label>
                                    <input type="number" step="0.01" name="price" class="form-control" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="date" name="expiry_date" class="form-control" required>
                                </div>

                                <button type="submit" name="save_receiving" class="btn btn-primary w-100"><i class="fa-solid fa-save me-1"></i> Save & Update Stock</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>