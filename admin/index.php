<?php
session_start();
include '../config/db_connect.php';

$error = "";

// Handle Login Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
  
        $safe_user = $conn->real_escape_string($username);
        $sql = "SELECT * FROM users WHERE username = '$safe_user'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Check password (supports both hashed password and plain text)
            if (password_verify($password, $user['password']) || $password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Incorrect password! Please try again.";
            }
        } else {
            $error = "Username not found in the system!";
        }
    } else {
        $error = "Please fill in all required fields!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Pharmacy Management System</title>
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
        }
        .login-header {
            background-color: #fff;
            border-bottom: none;
            padding-top: 30px;
        }
    </style>
</head>
<body>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                
                <div class="card login-card p-4 bg-white">
                    <div class="text-center mb-4 login-header">
                        <div class="mb-3 text-primary fs-1">
                            <i class="fa-solid fa-prescription-bottle-medical"></i>
                        </div>
                        <h3 class="fw-bold text-dark">Pharmacy Admin</h3>
                        <p class="text-muted small">Sign in to start your session</p>
                    </div>

                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger py-2 small text-center" role="alert">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i> <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-user"></i></span>
                                <input type="text" name="username" class="form-control" placeholder="Enter username" required autofocus>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-muted"><i class="fa-solid fa-lock"></i></span>
                                <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                            </div>
                        </div>

                        <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold shadow-sm">
                            <i class="fa-solid fa-right-to-bracket me-2"></i> Login to Dashboard
                        </button>
                    </form>

                    <div class="text-center mt-4">
                        <small class="text-muted">&copy; <?php echo date('Y'); ?> Pharmacy Management System</small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>