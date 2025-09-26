<?php
session_start();

require "includes/db.php";
require "api/cart/cart_functions.php";

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $cart_items = getCartItems($pdo, $user_id);
    $cart_summary = getCartSummary($pdo, $user_id);
}

// Get current page to set active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RealPixel Store</title>

    <!-- Bootstrap CSS -->

    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/indexStyles.css">
</head>

<body>
    <!-- Header -->
    <header class="bg-light border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <!-- Logo and Brand -->
                <div class="d-flex align-items-center">
                    <img src="assets/images/RP-2.png" alt="RealPixel Logo" class="logo-img me-3">
                    <h2 class="brand-title mb-0">Real Pixel Store</h2>
                </div>

                <!-- User Welcome (only show if logged in) -->
                <?php if ($is_logged_in): ?>
                    <div class="d-none d-md-block me-auto ms-4">
                        <small class="text-muted">Welcome, <?php echo htmlspecialchars($user_first_name); ?></small>
                    </div>
                <?php endif; ?>

                <!-- Mobile Menu Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="index.php">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="user/products.php">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                        </li>

                        <?php if ($is_logged_in): ?>
                            <!-- Show Cart and Profile for logged-in users -->
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'cart.php' ? 'active' : '' ?>" href="user/cart.php">
                                    <i class="fas fa-shopping-cart me-1"></i>Cart
                                    <?php if ($cart_summary['total_quantity'] > 0): ?>
                                        <span class="badge bg-danger ms-1"><?= $cart_summary['total_quantity'] ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'profile.php' ? 'active' : '' ?>" href="user/profile.php">
                                    <i class="fas fa-user-circle me-1"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="user/logout.php">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Show Contact and Login for guests -->
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="user/contact.php">
                                    <i class="fas fa-envelope me-1"></i>Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= in_array($current_page, ['login.php', 'signup.php']) ? 'active' : '' ?>" href="user/login.php">
                                    <i class="fas fa-user me-1"></i>Login
                                </a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow-1">
        <!-- Hero Section -->
        <section class="hero-section py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <h1 class="hero-title mb-4">Welcome to Real Pixel Store<br>
                            <span class="text-primary">Your Tech Destination</span>
                        </h1>
                        <p class="hero-description mb-4">
                            Discover the latest technology products at Real Pixel Store. From cutting-edge computers
                            and laptops to the newest smartphones and accessories - we offer premium tech products
                            with guaranteed authenticity and reliable service.
                        </p>
                        <div class="hero-cta">
                            <a href="user/products.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-shopping-cart me-2"></i>Shop Now
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="hero-visual text-center">
                            <div class="display-1 text-primary mb-3">
                                <i class="fas fa-laptop"></i>
                            </div>
                            <p class="fs-5 text-muted">Premium Tech Products</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Product Categories -->
        <section class="categories-section py-5 bg-light">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="section-title">Shop by Category</h2>
                        <p class="section-subtitle">Find the perfect tech product for your needs</p>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Computers -->
                    <div class="col-md-6 col-lg-3">
                        <a href="user/products.php" class="category-card-link">
                            <div class="card category-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="category-icon mb-3">
                                        <i class="fas fa-desktop display-4 text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Computers</h5>
                                    <p class="card-text text-muted">Desktop PCs, workstations, and gaming rigs</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Laptops -->
                    <div class="col-md-6 col-lg-3">
                        <a href="user/products.php" class="category-card-link">
                            <div class="card category-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="category-icon mb-3">
                                        <i class="fas fa-laptop display-4 text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Laptops</h5>
                                    <p class="card-text text-muted">Ultrabooks, gaming laptops, and notebooks</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Phones -->
                    <div class="col-md-6 col-lg-3">
                        <a href="user/products.php" class="category-card-link">
                            <div class="card category-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="category-icon mb-3">
                                        <i class="fas fa-mobile-alt display-4 text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Smartphones</h5>
                                    <p class="card-text text-muted">Latest phones and mobile devices</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Accessories -->
                    <div class="col-md-6 col-lg-3">
                        <a href="user/products.php" class="category-card-link">
                            <div class="card category-card h-100 border-0 shadow-sm">
                                <div class="card-body text-center p-4">
                                    <div class="category-icon mb-3">
                                        <i class="fas fa-headphones display-4 text-primary"></i>
                                    </div>
                                    <h5 class="card-title">Accessories</h5>
                                    <p class="card-text text-muted">Headphones, keyboards, mice, and more</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Store Perks -->
        <section class="perks-section py-5">
            <div class="container">
                <div class="row text-center mb-5">
                    <div class="col-12">
                        <h2 class="section-title">Why Choose Real Pixel Store</h2>
                        <p class="section-subtitle">Your trusted tech partner</p>
                    </div>
                </div>
                <div class="row g-4">
                    <!-- Authentic Products -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card perk-card h-100 border-0">
                            <div class="card-body text-center p-4">
                                <div class="perk-icon mb-3">
                                    <i class="fas fa-shield-alt display-4 text-success"></i>
                                </div>
                                <h5 class="card-title">100% Authentic</h5>
                                <p class="card-text text-muted">All products are genuine with manufacturer warranty</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fast Shipping -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card perk-card h-100 border-0">
                            <div class="card-body text-center p-4">
                                <div class="perk-icon mb-3">
                                    <i class="fas fa-shipping-fast display-4 text-info"></i>
                                </div>
                                <h5 class="card-title">Fast Delivery</h5>
                                <p class="card-text text-muted">Quick and secure shipping to your doorstep</p>
                            </div>
                        </div>
                    </div>

                    <!-- Customer Support -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card perk-card h-100 border-0">
                            <div class="card-body text-center p-4">
                                <div class="perk-icon mb-3">
                                    <i class="fas fa-headset display-4 text-warning"></i>
                                </div>
                                <h5 class="card-title">24/7 Support</h5>
                                <p class="card-text text-muted">Expert customer service whenever you need help</p>
                            </div>
                        </div>
                    </div>

                    <!-- Money Back -->
                    <div class="col-md-6 col-lg-3">
                        <div class="card perk-card h-100 border-0">
                            <div class="card-body text-center p-4">
                                <div class="perk-icon mb-3">
                                    <i class="fas fa-money-bill-wave display-4 text-danger"></i>
                                </div>
                                <h5 class="card-title">Money Back Guarantee</h5>
                                <p class="card-text text-muted">30-day return policy for your peace of mind</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <!-- Brand Section -->
                <div class="col-lg-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="assets/images/RP-2.png" alt="RealPixel Logo" class="footer-logo me-2">
                        <h5 class="mb-0">Real Pixel Store</h5>
                    </div>
                    <p class="text-muted small">Crafting pixel-perfect digital experiences since 2025</p>
                </div>

                <!-- Navigation -->
                <div class="col-lg-4 mb-3">
                    <h6 class="text-uppercase mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li class="mb-1">
                            <a href="index.php" class="text-light text-decoration-none">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="user/products.php" class="text-light text-decoration-none">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="user/contact.php" class="text-light text-decoration-none">
                                <i class="fas fa-envelope me-1"></i>Contact
                            </a>
                        </li>
                        <li class="mb-1">
                            <a href="user/login.php" class="text-light text-decoration-none">
                                <i class="fas fa-user me-1"></i>Login
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