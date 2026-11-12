<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address']);

    if (!empty($name) && !empty($phone)) {
        // Here we insert into 'name', 'phone', 'address', and 'contact'
        $sql = "INSERT INTO customer_list (name, phone, contact, address) VALUES ('$name', '$phone', '$phone', '$address')";
        if ($conn->query($sql) === TRUE) {
            $success = "Customer added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Customer Name and Phone number are required!";
    }
}

$result = $conn->query("SELECT * FROM customer_list ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4">Customer Management</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Add Customer Form -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Add New Customer</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Customer Name</label>
                                    <input type="text" name="name" class="form-control" required>
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
                                <button type="submit" name="add_customer" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i> Add Customer</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Customer Table List -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Customer List</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if ($result && $result->num_rows > 0): ?>
                                            <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td><?php echo $i++; ?></td>
                                                    <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                                                    <td><?php echo htmlspecialchars($row['address'] ?? ''); ?></td>
                                                    <td>
                                                        <a href="delete_customer.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this customer?');"><i class="fa-solid fa-trash"></i></a>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">No customers found.</td>
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