<?php
// Detect current file name to highlight active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="bg-dark text-white p-3 min-vh-100 sidebar" style="width: 250px;">
    <!-- Sidebar Header -->
    <h4 class="text-center py-2 border-bottom border-secondary mb-3">
        <i class="fa-solid fa-prescription-bottle-medical text-primary me-2"></i>Pharmacy
    </h4>

    <!-- Navigation Links -->
    <ul class="nav nav-pills flex-column">

        <li class="nav-item mb-1">
            <a href="dashboard.php" class="nav-link text-white <?php echo ($current_page == 'dashboard.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-gauge me-2"></i> Dashboard
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="pos.php" class="nav-link text-white <?php echo ($current_page == 'pos.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-cart-shopping me-2"></i> Sales (POS)
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="sales.php" class="nav-link text-white <?php echo in_array($current_page, ['sales.php', 'view_sale.php']) ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-file-invoice-dollar me-2"></i> Sales History
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="receivings.php" class="nav-link text-white <?php echo in_array($current_page, ['receivings.php', 'add_receiving.php', 'view_receiving.php']) ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-truck-ramp-box me-2"></i> Receivings
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="medicines.php" class="nav-link text-white <?php echo in_array($current_page, ['medicines.php', 'add_medicine.php', 'edit_medicine.php']) ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Medicine List
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="categories.php" class="nav-link text-white <?php echo ($current_page == 'categories.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-list me-2"></i> Categories
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="medicine_types.php" class="nav-link text-white <?php echo ($current_page == 'medicine_types.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-capsules me-2"></i> Medicine Types
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="expired_list.php" class="nav-link text-white <?php echo ($current_page == 'expired_list.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-calendar-xmark me-2"></i> Expired List
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="suppliers.php" class="nav-link text-white <?php echo ($current_page == 'suppliers.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-truck-field me-2"></i> Suppliers
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="customers.php" class="nav-link text-white <?php echo ($current_page == 'customers.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-users me-2"></i> Customers
            </a>
        </li>

        <li class="nav-item mb-1">
            <a href="users.php" class="nav-link text-white <?php echo ($current_page == 'users.php') ? 'active bg-primary' : ''; ?>">
                <i class="fa-solid fa-user-gear me-2"></i> User Management
            </a>
        </li>

    </ul>
</div>