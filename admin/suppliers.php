<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// Add Supplier Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_supplier'])) {
    $supplier_name = trim($_POST['supplier_name']);
    $contact_person = trim($_POST['contact_person']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);

    if (!empty($supplier_name) && !empty($phone)) {
        $sql = "INSERT INTO supplier_list (supplier_name, contact_person, phone, email, address) 
                VALUES ('$supplier_name', '$contact_person', '$phone', '$email', '$address')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Supplier added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Supplier Name and Phone number are required!";
    }
}

// Fetch Suppliers
$result = $conn->query("SELECT * FROM supplier_list ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4">Supplier Management</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Add Supplier Form -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Add New Supplier</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Supplier/Company Name</label>
                                    <input type="text" name="supplier_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Contact Person</label>
                                    <input type="text" name="contact_person" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone Number</label>
                                    <input type="text" name="phone" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control" rows="2"></textarea>
                                </div>
                                <button type="submit" name="add_supplier" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i> Add Supplier</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Supplier Table List -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Supplier List</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Supplier Name</th>
                                            <th>Contact Person</th>
                                            <th>Phone</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result->num_rows > 0): ?>
                                            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['supplier_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['contact_person'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td>
                                                    <td>
                                                        <a href="delete_supplier.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this supplier?');"><i class="fa-solid fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No suppliers found.</td>
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