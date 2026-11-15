<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// Fetch Categories for Dropdown
$categories = $conn->query("SELECT * FROM category_list ORDER BY name ASC");

// Fetch Suppliers for Dropdown
$suppliers = $conn->query("SELECT * FROM supplier_list ORDER BY supplier_name ASC");

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_medicine'])) {
    // Safely retrieve POST data using null coalescing operator (??) to prevent warnings/deprecations
    $name = trim($_POST['name'] ?? '');
    $sku = trim($_POST['sku'] ?? '');
    $measurement = trim($_POST['measurement'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $category_id = intval($_POST['category_id'] ?? 0);
    $supplier_id = intval($_POST['supplier_id'] ?? 0);
    $price = floatval($_POST['price'] ?? 0);
    $quantity = intval($_POST['quantity'] ?? 0);
    $expiry_date = $_POST['expiry_date'] ?? date('Y-m-d');

    if (!empty($name) && $price >= 0 && $quantity >= 0) {
        
        // Insert medicine query including measurement and description safely
        $sql = "INSERT INTO product_list (name, sku, measurement, description, category_id, supplier_id, price, quantity, expiry_date) 
                VALUES ('$name', '$sku', '$measurement', '$description', '$category_id', '$supplier_id', '$price', '$quantity', '$expiry_date')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Medicine added successfully with initial stock of $quantity units!";
        } else {
            $error = "Database Error: " . $conn->error;
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
                <h3 class="fw-bold">Add New Medicine</h3>
                <a href="medicines.php" class="btn btn-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Back to List</a>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8 mx-auto">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white fw-bold">Medicine Information Form</div>
                        <div class="card-body p-4">
                            <form method="POST" action="">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Medicine Name</label>
                                        <input type="text" name="name" class="form-control" placeholder="e.g. Napa Extra" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Measurement / Strength</label>
                                        <input type="text" name="measurement" class="form-control" placeholder="e.g. 500mg, 10ml">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">SKU / Barcode</label>
                                        <input type="text" name="sku" class="form-control" placeholder="e.g. MED-1001">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Category</label>
                                        <select name="category_id" class="form-control" required>
                                            <option value="">-- Choose Category --</option>
                                            <?php if ($categories && $categories->num_rows > 0): ?>
                                                <?php while ($cat = $categories->fetch_assoc()): ?>
                                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Supplier</label>
                                        <select name="supplier_id" class="form-control" required>
                                            <option value="">-- Choose Supplier --</option>
                                            <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                                                <?php while ($sup = $suppliers->fetch_assoc()): ?>
                                                    <option value="<?php echo $sup['id']; ?>"><?php echo htmlspecialchars($sup['supplier_name']); ?></option>
                                                <?php endwhile; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Selling Price (৳)</label>
                                        <input type="number" step="0.01" name="price" class="form-control" placeholder="0.00" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Initial Stock Quantity</label>
                                        <input type="number" name="quantity" class="form-control" value="0" min="0" required>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Expiry Date</label>
                                        <input type="date" name="expiry_date" class="form-control" required>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Description / Details</label>
                                    <textarea name="description" class="form-control" rows="2" placeholder="Enter medicine description or usage instructions..."></textarea>
                                </div>

                                <button type="submit" name="save_medicine" class="btn btn-success w-100 mt-3 py-2 fw-bold">
                                    <i class="fa-solid fa-save me-1"></i> Save Medicine & Stock
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>