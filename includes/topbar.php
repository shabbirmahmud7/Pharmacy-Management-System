<?php
// Top Navigation Header
?>
<nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom px-4">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1 fw-bold text-secondary">
            Pharmacy Sales & Inventory Management System
        </span>
        <div class="d-flex align-items-center">
            <div class="dropdown">
                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user-circle me-1"></i> Administrator
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fa-solid fa-id-badge me-2"></i>Profile</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="logout.php"><i class="fa-solid fa-right-from-bracket me-2"></i>Logout</a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>