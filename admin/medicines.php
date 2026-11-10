<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="fw-bold">Medicine List</h3>
                <a href="add_medicine.php" class="btn btn-primary"><i class="fa-solid fa-plus me-1"></i> Add New Medicine</a>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>SKU</th>
                                    <th>Medicine Name</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <th>Price (BDT)</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.*, c.name as cat_name, t.name as type_name 
                                        FROM product_list p 
                                        LEFT JOIN category_list c ON p.category_id = c.id 
                                        LEFT JOIN type_list t ON p.type_id = t.id 
                                        ORDER BY p.id DESC";
                                $result = $conn->query($sql);

                                if ($result->num_rows > 0) {
                                    $i = 1;
                                    while ($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $i++ . "</td>";
                                        echo "<td>" . htmlspecialchars($row['sku']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['name']) . " <small class='text-muted'>(" . htmlspecialchars($row['measurement']) . ")</small></td>";
                                        echo "<td>" . htmlspecialchars($row['cat_name']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['type_name']) . "</td>";
                                        echo "<td>" . number_format($row['price'], 2) . "</td>";
                                        echo "<td>
                                                <a href='edit_medicine.php?id=" . $row['id'] . "' class='btn btn-sm btn-warning text-white me-1'><i class='fa-solid fa-pen-to-square'></i></a>
                                                <a href='delete_medicine.php?id=" . $row['id'] . "' class='btn btn-sm btn-danger' onclick='return confirm(\"Are you sure you want to delete this medicine?\");'><i class='fa-solid fa-trash'></i></a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>No medicines found in the inventory.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>