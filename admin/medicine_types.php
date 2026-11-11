<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

$error = "";
$success = "";

// Add Medicine Type Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_type'])) {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if (!empty($name)) {
        $sql = "INSERT INTO type_list (name, description) VALUES ('$name', '$description')";
        if ($conn->query($sql) === TRUE) {
            $success = "Medicine type added successfully!";
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Type name cannot be empty!";
    }
}

// Fetch Medicine Types
$result = $conn->query("SELECT * FROM type_list ORDER BY id DESC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4">Medicine Type Management</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Add Medicine Type Form -->
                <div class="col-md-4 mb-4">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Add New Type</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Type Name (e.g., Tablet, Syrup)</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Description</label>
                                    <textarea name="description" class="form-control" rows="3"></textarea>
                                </div>
                                <button type="submit" name="add_type" class="btn btn-primary w-100"><i class="fa-solid fa-plus me-1"></i> Add Type</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Medicine Types Table List -->
                <div class="col-md-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-white fw-bold">Medicine Type List</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Type Name</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
    <?php if ($result->num_rows > 0): ?>
        <?php $i = 1; while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $i++; ?></td>
                <td><?php echo htmlspecialchars($row['name']); ?></td>
                <td><?php echo htmlspecialchars($row['description'] ?? ''); ?></td>
                <td>
                    <a href="delete_type.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this medicine type?');"><i class="fa-solid fa-trash"></i></a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr>
            <td colspan="4" class="text-center text-muted py-4">No medicine types found.</td>
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