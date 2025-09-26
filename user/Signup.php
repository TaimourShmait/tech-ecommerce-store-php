<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';
// Get current page to set active navigation
$current_page = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Real Pixel Store</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/authStyles.css">
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
                            <a class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>" href="../index.php">
                                <i class="fas fa-home me-1"></i>Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?= $current_page == 'products.php' ? 'active' : '' ?>" href="products.php">
                                <i class="fas fa-box me-1"></i>Products
                            </a>
                        </li>

                        <?php if ($is_logged_in): ?>
                            <!-- Show Cart and Profile for logged-in users -->
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'cart.php' ? 'active' : '' ?>" href="cart.php">
                                    <i class="fas fa-shopping-cart me-1"></i>Cart
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'profile.php' ? 'active' : '' ?>" href="profile.php">
                                    <i class="fas fa-user-circle me-1"></i>Profile
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="logout.php">
                                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                                </a>
                            </li>
                        <?php else: ?>
                            <!-- Show Contact and Login for guests -->
                            <li class="nav-item">
                                <a class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>" href="contact.php">
                                    <i class="fas fa-envelope me-1"></i>Contact
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link <?= in_array($current_page, ['login.php', 'signup.php']) ? 'active' : '' ?>" href="login.php">
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
    <main class="auth-main">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6">
                    <div class="auth-card">
                        <div class="text-center mb-4">
                            <i class="fas fa-user-plus auth-icon"></i>
                            <h1 class="auth-title">Sign Up</h1>
                            <p class="text-muted">Join Real Pixel Store today</p>
                        </div>

                        <form action="../api/user/user_auth.php" method="POST">
                            <input type="hidden" value="register" name="action">

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user me-1"></i>First Name
                                        </label>
                                        <input type="text" name="first_name" class="form-control" placeholder="Enter first name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-user me-1"></i>Last Name
                                        </label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Enter last name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label class="form-label">
                                    <i class="fas fa-calendar me-1"></i>Date of Birth
                                </label>
                                <input type="date" name="dob" class="form-control">
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-phone me-1"></i>Phone Number
                                        </label>
                                        <input type="tel" name="phone_number" class="form-control" placeholder="Enter phone number" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-envelope me-1"></i>Email Address
                                        </label>
                                        <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">
                                            <i class="fas fa-lock me-1"></i>Password
                                        </label>
                                        <input type="password" name="password" class="form-control" placeholder="Enter password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-lock me-1"></i>Confirm Password
                                        </label>
                                        <input type="password" name="confirm_password" class="form-control" placeholder="Confirm password" required>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                <i class="fas fa-user-plus me-2"></i>Sign Up
                            </button>

                            <div class="text-center">
                                <p class="mb-0">
                                    Already have an account?
                                    <a href="login.php" class="register-link">Login here</a>
                                </p>
                            </div>
                        </form>
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
                        <li class="mb-1">
                            <a href="login.php" class="text-light text-decoration-none">
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