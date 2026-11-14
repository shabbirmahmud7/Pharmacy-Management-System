<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

include '../config/db_connect.php';

// Initialize Cart Session
if (!isset($_SESSION['pos_cart'])) {
    $_SESSION['pos_cart'] = [];
}

$success = "";
$error = "";

// 1. Add to Cart Logic
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $qty = intval($_POST['qty']);

    // Fetch product details & stock
    $prod_res = $conn->query("SELECT * FROM product_list WHERE id = $product_id");
    if ($prod_res && $prod_res->num_rows > 0) {
        $prod = $prod_res->fetch_assoc();
        
        if ($prod['quantity'] >= $qty) {
            if (isset($_SESSION['pos_cart'][$product_id])) {
                $_SESSION['pos_cart'][$product_id]['qty'] += $qty;
            } else {
                $_SESSION['pos_cart'][$product_id] = [
                    'id' => $prod['id'],
                    'name' => $prod['name'],
                    'price' => $prod['price'] ?? 0, // Ensure price column exists
                    'qty' => $qty,
                    'stock' => $prod['quantity']
                ];
            }
        } else {
            $error = "Insufficient stock available! Current stock: " . $prod['quantity'];
        }
    }
}

// 2. Remove Item from Cart
if (isset($_GET['remove'])) {
    $remove_id = intval($_GET['remove']);
    unset($_SESSION['pos_cart'][$remove_id]);
    header("Location: pos.php");
    exit();
}

// 3. Clear Cart
if (isset($_GET['clear'])) {
    $_SESSION['pos_cart'] = [];
    header("Location: pos.php");
    exit();
}

// 4. Checkout / Complete Sale Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['checkout'])) {
    $customer_id = intval($_POST['customer_id']);
    $tendered = floatval($_POST['tendered']);
    
    // Calculate total amount
    $grand_total = 0;
    foreach ($_SESSION['pos_cart'] as $item) {
        $grand_total += ($item['price'] * $item['qty']);
    }

    if ($grand_total > 0 && $tendered >= $grand_total) {
        $change = $tendered - $grand_total;

        // Insert into sales_list
        $sql_sale = "INSERT INTO sales_list (customer_id, total_amount, amount_tendered, amount_change, date_created) 
                     VALUES ('$customer_id', '$grand_total', '$tendered', '$change', NOW())";
        
        if ($conn->query($sql_sale) === TRUE) {
            $sale_id = $conn->insert_id;

            // Insert sale items & update product stock
            foreach ($_SESSION['pos_cart'] as $item) {
                $p_id = $item['id'];
                $p_price = $item['price'];
                $p_qty = $item['qty'];
                $p_total = $p_price * $p_qty;

                $conn->query("INSERT INTO sale_items (sale_id, product_id, price, qty, total) VALUES ('$sale_id', '$p_id', '$p_price', '$p_qty', '$p_total')");

                // Reduce inventory stock
                $conn->query("UPDATE product_list SET quantity = quantity - $p_qty WHERE id = $p_id");
            }

            // Clear cart after successful checkout
            $_SESSION['pos_cart'] = [];
            $success = "Checkout successful! Sale ID: #" . $sale_id;
        } else {
            $error = "Database Error: " . $conn->error;
        }
    } else {
        $error = "Invalid amount tendered or empty cart!";
    }
}

// Fetch Customers & Medicines for dropdowns
$customers = $conn->query("SELECT * FROM customer_list ORDER BY name ASC");
$medicines = $conn->query("SELECT * FROM product_list WHERE quantity > 0 ORDER BY name ASC");
?>

<?php include '../includes/header.php'; ?>

<div class="d-flex">
    <?php include '../includes/sidebar.php'; ?>
    
    <div class="w-100 bg-light min-vh-100">
        <?php include '../includes/topbar.php'; ?>
        
        <div class="container-fluid p-4">
            <h3 class="fw-bold mb-4"><i class="fa-solid fa-cash-register me-2"></i> Point of Sale (POS)</h3>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <!-- Left Column: Add Product & Product List -->
                <div class="col-md-5 mb-4">
                    <div class="card shadow-sm border-0 mb-4">
                        <div class="card-header bg-primary text-white fw-bold">Add Medicine to Cart</div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Select Medicine</label>
                                    <select name="product_id" class="form-control" required>
                                        <option value="">-- Choose Medicine --</option>
                                        <?php if ($medicines && $medicines->num_rows > 0): ?>
                                            <?php while ($med = $medicines->fetch_assoc()): ?>
                                                <option value="<?php echo $med['id']; ?>">
                                                    <?php echo htmlspecialchars($med['name']); ?> (Stock: <?php echo $med['quantity']; ?> | Price: ৳<?php echo $med['price'] ?? 0; ?>)
                                                </option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" name="qty" class="form-control" value="1" min="1" required>
                                </div>
                                <button type="submit" name="add_to_cart" class="btn btn-success w-100"><i class="fa-solid fa-cart-plus me-1"></i> Add to Cart</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Cart & Checkout Summary -->
                <div class="col-md-7">
                    <div class="card shadow-sm border-0">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Billing Cart</span>
                            <?php if (!empty($_SESSION['pos_cart'])): ?>
                                <a href="pos.php?clear=true" class="btn btn-sm btn-danger"><i class="fa-solid fa-trash me-1"></i> Clear Cart</a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive mb-3" style="max-height: 250px; overflow-y: auto;">
                                <table class="table table-bordered table-sm align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Medicine</th>
                                            <th>Price</th>
                                            <th>Qty</th>
                                            <th>Total</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $subtotal = 0;
                                        if (!empty($_SESSION['pos_cart'])): 
                                            foreach ($_SESSION['pos_cart'] as $item): 
                                                $total = $item['price'] * $item['qty'];
                                                $subtotal += $total;
                                        ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                                <td>৳ <?php echo number_format($item['price'], 2); ?></td>
                                                <td><?php echo $item['qty']; ?></td>
                                                <td>৳ <?php echo number_format($total, 2); ?></td>
                                                <td>
                                                    <a href="pos.php?remove=<?php echo $item['id']; ?>" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-xmark"></i></a>
                                                </td>
                                            </tr>
                                        <?php 
                                            endforeach; 
                                        else: 
                                        ?>
                                            <tr>
                                                <td colspan="5" class="text-center text-muted py-3">Cart is empty.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Checkout Form -->
                            <form method="POST" action="">
                                <div class="mb-3">
                                    <label class="form-label">Select Customer</label>
                                    <select name="customer_id" class="form-control" required>
                                        <option value="">-- Walk-in Customer --</option>
                                        <?php if ($customers && $customers->num_rows > 0): ?>
                                            <?php while ($cus = $customers->fetch_assoc()): ?>
                                                <option value="<?php echo $cus['id']; ?>"><?php echo htmlspecialchars($cus['name']); ?> (<?php echo $cus['phone']; ?>)</option>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="bg-light p-3 rounded mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fw-bold">Grand Total:</span>
                                        <span class="fw-bold text-success fs-5">৳ <?php echo number_format($subtotal, 2); ?></span>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">Amount Tendered (Cash Paid)</label>
                                        <input type="number" step="0.01" name="tendered" class="form-control" required>
                                    </div>
                                </div>

                                <button type="submit" name="checkout" class="btn btn-dark w-100 py-2 fw-bold" <?php echo empty($_SESSION['pos_cart']) ? 'disabled' : ''; ?>>
                                    <i class="fa-solid fa-print me-1"></i> Complete Checkout & Print Bill
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