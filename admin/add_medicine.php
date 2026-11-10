<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sku = trim($_POST['sku']);
    $name = trim($_POST['name']);
    $category_id = $_POST['category_id'];
    $type_id = $_POST['type_id'];
    $price = $_POST['price'];
    $measurement = trim($_POST['measurement']);
    $description = trim($_POST['description']);

    if (!empty($sku) && !empty($name) && !empty($price)) {
        $sql = "INSERT INTO product_list (sku, name, category_id, type_id, price, measurement, description) 
                VALUES ('$sku', '$name', '$category_id', '$type_id', '$price', '$measurement', '$description')";
        
        if ($conn->query($sql) === TRUE) {
            header("Location: medicines.php");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Please fill up all required fields!";
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

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SKU / Barcode</label>
                                <input type="text" name="sku" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medicine Name</label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Category</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php
                                    $cat_result = $conn->query("SELECT * FROM category_list");
                                    while ($cat = $cat_result->fetch_assoc()) {
                                        echo "<option value='" . $cat['id'] . "'>" . $cat['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Medicine Type</label>
                                <select name="type_id" class="form-control" required>
                                    <option value="">Select Type</option>
                                    <?php
                                    $type_result = $conn->query("SELECT * FROM type_list");
                                    while ($type = $type_result->fetch_assoc()) {
                                        echo "<option value='" . $type['id'] . "'>" . $type['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Price (BDT)</label>
                                <input type="number" step="0.01" name="price" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Measurement (e.g., 500mg)</label>
                                <input type="text" name="measurement" class="form-control">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary"><i class="fa-solid fa-save me-1"></i> Save Medicine</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>