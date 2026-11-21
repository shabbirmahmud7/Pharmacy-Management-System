<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// 1. Add New Supplier
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $supplier_name = $conn->real_escape_string(trim($_POST['supplier_name'] ?? ''));
    $contact_person = $conn->real_escape_string(trim($_POST['contact_person'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $address = $conn->real_escape_string(trim($_POST['address'] ?? ''));

    if (!empty($supplier_name) && !empty($phone)) {
        $sql = "INSERT INTO supplier_list (supplier_name, contact_person, phone, email, address) 
                VALUES ('$supplier_name', '$contact_person', '$phone', '$email', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "New supplier added successfully!";
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Supplier Name and Phone are required!";
    }
}

// 2. Update Supplier Info
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_supplier'])) {
    $id = intval($_POST['supplier_id'] ?? 0);
    $supplier_name = $conn->real_escape_string(trim($_POST['supplier_name'] ?? ''));
    $contact_person = $conn->real_escape_string(trim($_POST['contact_person'] ?? ''));
    $phone = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $email = $conn->real_escape_string(trim($_POST['email'] ?? ''));
    $address = $conn->real_escape_string(trim($_POST['address'] ?? ''));

    if ($id > 0 && !empty($supplier_name) && !empty($phone)) {
        $sql = "UPDATE supplier_list SET 
                supplier_name = '$supplier_name', 
                contact_person = '$contact_person', 
                phone = '$phone', 
                email = '$email', 
                address = '$address' 
                WHERE id = $id";

        if ($conn->query($sql) === TRUE) {
            $success = "Supplier updated successfully!";
        } else {
            $error = "Update Failed: " . $conn->error;
        }
    } else {
        $error = "Please provide valid Supplier Name and Phone!";
    }
}

// 3. Delete Supplier
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    if ($del_id > 0) {
        $conn->query("DELETE FROM supplier_list WHERE id = $del_id");
        $success = "Supplier deleted successfully!";
    }
}

// Fetch All Suppliers
$suppliers = $conn->query("SELECT * FROM supplier_list ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h3 class="fw-bold mb-0"><i class="fa-solid fa-truck-field me-2 text-primary"></i> Supplier Management</h3>
                    <p class="text-muted mb-0">Add, update, and manage pharmaceutical suppliers</p>
                </div>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Left Column: Add Supplier Form -->
                <div class="col-md-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="fa-solid fa-plus-circle me-1"></i> Add New Supplier
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Company / Supplier Name *</label>
                                    <input type="text" name="supplier_name" class="form-control" placeholder="e.g. Square Pharmaceuticals" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control" placeholder="e.g. Tanvir Hasan">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Phone Number *</label>
                                    <input type="text" name="phone" class="form-control" placeholder="e.g. 01711-000000" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Email Address</label>
                                    <input type="email" name="email" class="form-control" placeholder="e.g. info@supplier.com">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">Office / Depot Address</label>
                                    <textarea name="address" class="form-control" rows="2" placeholder="Enter address..."></textarea>
                                </div>
                                <button type="submit" name="add_supplier" class="btn btn-success w-100 fw-bold">
                                    <i class="fa-solid fa-save me-1"></i> Save Supplier
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Suppliers Table -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white fw-bold">
                            <i class="fa-solid fa-list me-1"></i> Registered Suppliers List
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Supplier Info</th>
                                            <th>Contact Details</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($suppliers && $suppliers->num_rows > 0): ?>
                                            <?php $i = 1; while ($row = $suppliers->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td>
                                                        <div class="fw-bold text-primary"><?php echo htmlspecialchars($row['supplier_name']); ?></div>
                                                        <small class="text-muted"><i class="fa-solid fa-user-tie me-1"></i> <?php echo htmlspecialchars($row['contact_person'] ?: 'N/A'); ?></small>
                                                    </td>
                                                    <td>
                                                        <div><i class="fa-solid fa-phone me-1 text-success"></i> <?php echo htmlspecialchars($row['phone']); ?></div>
                                                        <?php if (!empty($row['email'])): ?>
                                                            <small class="text-muted"><i class="fa-solid fa-envelope me-1"></i> <?php echo htmlspecialchars($row['email']); ?></small>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="small"><?php echo htmlspecialchars($row['address'] ?: 'N/A'); ?></td>
                                                    <td>
                                                        <!-- Edit Modal Button -->
                                                        <button type="button" class="btn btn-sm btn-warning text-dark" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#editModal<?php echo $row['id']; ?>">
                                                            <i class="fa-solid fa-pen-to-square"></i>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <a href="suppliers.php?delete_id=<?php echo $row['id']; ?>" 
                                                           class="btn btn-sm btn-danger" 
                                                           onclick="return confirm('Are you sure you want to delete this supplier?');">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </a>
                                                    </td>
                                                </tr>

                                                <!-- Edit Supplier Modal -->
                                                <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <form method="POST" action="">
                                                                <div class="modal-header bg-warning">
                                                                    <h6 class="modal-title fw-bold text-dark"><i class="fa-solid fa-pen-to-square me-1"></i> Edit Supplier</h6>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="supplier_id" value="<?php echo $row['id']; ?>">
                                                                    
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Supplier Name *</label>
                                                                        <input type="text" name="supplier_name" class="form-control form-control-sm" value="<?php echo htmlspecialchars($row['supplier_name']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Contact Person</label>
                                                                        <input type="text" name="contact_person" class="form-control form-control-sm" value="<?php echo htmlspecialchars($row['contact_person']); ?>">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Phone Number *</label>
                                                                        <input type="text" name="phone" class="form-control form-control-sm" value="<?php echo htmlspecialchars($row['phone']); ?>" required>
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Email</label>
                                                                        <input type="email" name="email" class="form-control form-control-sm" value="<?php echo htmlspecialchars($row['email']); ?>">
                                                                    </div>
                                                                    <div class="mb-2">
                                                                        <label class="form-label small fw-bold">Address</label>
                                                                        <textarea name="address" class="form-control form-control-sm" rows="2"><?php echo htmlspecialchars($row['address']); ?></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer py-2">
                                                                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                                    <button type="submit" name="update_supplier" class="btn btn-sm btn-primary fw-bold">Update Changes</button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-4">No suppliers found. Add one from the left form!</td>
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

<?php include '../includes/footer.php'; ?>