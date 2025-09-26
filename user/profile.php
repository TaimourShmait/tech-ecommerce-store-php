<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from session - with better fallback handling
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : 'User';
$user_full_name = isset($_SESSION['user_full_name']) ? $_SESSION['user_full_name'] : $user_first_name;
$user_email = isset($_SESSION['email']) ? $_SESSION['email'] : '';
$user_id = $_SESSION['user_id'];

require "../includes/db.php";
require "../api/cart/cart_functions.php";
$cart_items = getCartItems($pdo, $user_id);
$cart_summary = getCartSummary($pdo, $user_id);


// Debug: If full name is empty, construct it from first name
if (empty($user_full_name)) {
    $user_full_name = $user_first_name;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Real Pixel Store</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/profile.css">
</head>

<body>
    <!-- Header -->
    <header class="bg-light border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo and Brand -->
                <div class="d-flex align-items-center">
                    <img src="../assets/images/RP-2.png" alt="RealPixel Logo" class="logo-img me-3">
                    <h2 class="brand-title mb-0">Real Pixel Store</h2>
                </div>

                <!-- User Welcome -->
                <div class="d-none d-md-block me-auto ms-4">
                    <small class="text-muted">Welcome, <?php echo htmlspecialchars($user_first_name); ?></small>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="../index.php">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products.php">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                                <?php if ($cart_summary['total_quantity'] > 0): ?>
                                    <span class="badge bg-danger ms-1"><?= $cart_summary['total_quantity'] ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="profile.php">
                                <i class="fas fa-user-circle me-1"></i>Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">
                                <i class="fas fa-sign-out-alt me-1"></i>Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <div class="container py-4">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-user-circle me-2"></i>My Profile
                        </h1>
                        <p class="text-muted">Manage your account and track your orders</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions Grid -->
            <div class="row g-3 mb-4 justify-content-around">
                <!-- Account Information Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="quick-action-card account-card">
                        <div class="action-icon">
                            <i class="fas fa-user"></i>
                        </div>
                        <h6>Account Details</h6>
                        <div class="account-info">
                            <p class="account-name"><?php echo htmlspecialchars($user_full_name); ?></p>
                            <p class="account-email"><?php echo htmlspecialchars($user_email); ?></p>
                            <p class="account-id">ID: #<?php echo str_pad($user_id, 6, '0', STR_PAD_LEFT); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Shopping Cart Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="quick-action-card">
                        <div class="action-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h6>Shopping Cart</h6>
                        <p class="text-muted small">View your saved items</p>
                        <a href="cart.php" class="btn btn-sm btn-primary">View Cart</a>
                    </div>
                </div>

                <!-- My Orders Card -->
                <div class="col-lg-3 col-md-6">
                    <div class="quick-action-card">
                        <div class="action-icon">
                            <i class="fas fa-box"></i>
                        </div>
                        <h6>My Orders</h6>
                        <p class="text-muted small">Track your purchases</p>
                        <button class="btn btn-sm btn-outline-primary">View Orders</button>
                    </div>
                </div>

                <!-- Wishlist Card -->
                <!-- <div class="col-lg-3 col-md-6">
                   <div class="quick-action-card">
                       <div class="action-icon">
                           <i class="fas fa-heart"></i>
                       </div>
                       <h6>Wishlist</h6>
                       <p class="text-muted small">Your favorite items</p>
                       <button class="btn btn-sm btn-outline-primary">View Wishlist</button>
                   </div>
               </div> -->
            </div>

            <!-- Recent Orders -->
            <div class="row">
                <div class="col-12">
                    <div class="profile-card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-history me-2"></i>Recent Orders
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="empty-state">
                                <i class="fas fa-shopping-bag empty-icon"></i>
                                <h6>No orders yet</h6>
                                <p class="text-muted">Start shopping to see your orders here</p>
                                <a href="products.php" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart me-1"></i>Start Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <!-- Brand Section -->
                <div class="col-lg-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../assets/images/RP-2.png" alt="RealPixel Logo" class="footer-logo me-2">
                        <h5 class="mb-0">Real Pixel Store</h5>
                    </div>
                    <p class="text-muted small">Crafting pixel-perfect digital experiences since 2025</p>
                </div>

                <!-- Navigation -->
                <div class="col-lg-4 mb-3">
                    <h6 class="text-uppercase mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-1">
                            <a href="../index.php" class="text-light text-decoration-none">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="products.php" class="text-light text-decoration-none">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="contact.php" class="text-light text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>Contact
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Social Media -->
                <div class="col-lg-4 mb-3">
                    <h6 class="text-uppercase mb-3">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4" title="Facebook">
                            <i class="fab fa-facebook"></i>
                        </a>
                        <a href="#" class="text-light fs-4" title="Instagram">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="text-light fs-4" title="LinkedIn">
                            <i class="fab fa-linkedin"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <hr class="border-secondary">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-muted small">
                        &copy; 2025 Real Pixel Store. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
</body>

</html>