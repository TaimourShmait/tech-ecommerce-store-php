<?php
session_start();
require "../includes/db.php";
require "../api/cart/cart_functions.php";

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get user data from session
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : 'User';
$user_id = $_SESSION['user_id'];

// Get cart items and summary
$cart_items = getCartItems($pdo, $user_id);
$cart_summary = getCartSummary($pdo, $user_id);

// Calculate tax and shipping
$subtotal = $cart_summary['total_price'] ? $cart_summary['total_price'] : 0;
$tax_rate = 0.08; // 8% tax
$tax = $subtotal * $tax_rate;
$shipping = $subtotal > 50 ? 0 : 9.99; // Free shipping over $50
$total = $subtotal + $tax + $shipping;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Real Pixel Store</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/cart.css">
</head>

<body>
    <!-- Header -->
    <header class="bg-light border-bottom">
        <nav class="navbar navbar-expand-lg navbar-light">
            <div class="container">
                <div class="d-flex align-items-center">
                    <img src="../assets/images/RP-2.png" alt="RealPixel Logo" class="logo-img me-3">
                    <h2 class="brand-title mb-0">Real Pixel Store</h2>
                </div>

                <div class="d-none d-md-block me-auto ms-4">
                    <small class="text-muted">Welcome, <?php echo htmlspecialchars($user_first_name); ?></small>
                </div>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

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
                            <a class="nav-link active" href="cart.php">
                                <i class="fas fa-shopping-cart me-1"></i>Cart
                                <?php if ($cart_summary['total_quantity'] > 0): ?>
                                    <span class="badge bg-danger ms-1"><?= $cart_summary['total_quantity'] ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">
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
        <div class="container-fluid py-4">
            <!-- Success/Error Messages -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['success']) {
                        case 'item_added':
                            echo 'Item added to cart successfully!';
                            break;
                        case 'quantity_updated':
                            echo 'Cart updated successfully!';
                            break;
                        case 'item_removed':
                            echo 'Item removed from cart!';
                            break;
                        case 'cart_cleared':
                            echo 'Cart cleared successfully!';
                            break;
                        default:
                            echo 'Action completed successfully!';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                    switch ($_GET['error']) {
                        case 'product_not_found':
                            echo 'Product not found!';
                            break;
                        case 'database_error':
                            echo 'An error occurred. Please try again.';
                            break;
                        default:
                            echo 'An error occurred!';
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="page-header">
                        <h1 class="page-title">
                            <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
                        </h1>
                        <p class="text-muted">Review your items before checkout</p>
                    </div>
                </div>
            </div>

            <?php if (empty($cart_items)): ?>
                <!-- Empty Cart -->
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <div class="cart-card text-center py-5">
                            <i class="fas fa-shopping-cart empty-icon mb-3"></i>
                            <h4>Your cart is empty</h4>
                            <p class="text-muted">Start shopping to add items to your cart</p>
                            <a href="products.php" class="btn btn-primary">
                                <i class="fas fa-shopping-bag me-2"></i>Start Shopping
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <!-- Cart Items -->
                    <div class="col-lg-8">
                        <div class="cart-card">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="fas fa-list me-2"></i>Cart Items (<?= $cart_summary['item_count'] ?>)
                                </h5>
                                <form method="POST" action="../api/cart/cart_handler.php" class="d-inline">
                                    <input type="hidden" name="action" value="clear_cart">
                                    <button type="submit" class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Are you sure you want to clear your cart?')">
                                        <i class="fas fa-trash me-1"></i>Clear Cart
                                    </button>
                                </form>
                            </div>
                            <div class="card-body p-0">
                                <?php foreach ($cart_items as $item): ?>
                                    <div class="cart-item">
                                        <div class="item-image">
                                            <?php if ($item['cover_image']): ?>
                                                <img src="../assets/uploads/<?= htmlspecialchars($item['cover_image']) ?>"
                                                    alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid">
                                            <?php else: ?>
                                                <img src="../assets/placeholder-product.png"
                                                    alt="<?= htmlspecialchars($item['product_name']) ?>" class="img-fluid">
                                            <?php endif; ?>
                                        </div>
                                        <div class="item-details">
                                            <h6 class="item-name"><?= htmlspecialchars($item['product_name']) ?></h6>
                                            <p class="item-category"><?= htmlspecialchars($item['subcategory_name']) ?></p>
                                            <div class="item-actions">
                                                <form method="POST" action="../api/cart/cart_handler.php" class="d-inline">
                                                    <input type="hidden" name="action" value="remove_item">
                                                    <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="fas fa-trash me-1"></i>Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="item-quantity">
                                            <label class="form-label small">Quantity</label>
                                            <form method="POST" action="../api/cart/cart_handler.php" class="quantity-form">
                                                <input type="hidden" name="action" value="update_quantity">
                                                <input type="hidden" name="cart_item_id" value="<?= $item['cart_item_id'] ?>">
                                                <div class="quantity-controls">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-decrease">-</button>
                                                    <input type="number" name="quantity" class="form-control form-control-sm quantity-input"
                                                        value="<?= $item['quantity'] ?>" min="1" max="99">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm quantity-increase">+</button>
                                                </div>
                                                <button type="submit" class="btn btn-sm btn-primary mt-2">Update</button>
                                            </form>
                                        </div>
                                        <div class="item-price">
                                            <div class="price-display">$<?= number_format($item['price'] * $item['quantity'], 2) ?></div>
                                            <?php if ($item['quantity'] > 1): ?>
                                                <small>$<?= number_format($item['price'], 2) ?> each</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="products.php" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>

                    <!-- Order Summary -->
                    <div class="col-lg-4">
                        <div class="cart-card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-receipt me-2"></i>Order Summary
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="summary-row">
                                    <span>Subtotal (<?= $cart_summary['total_quantity'] ?> items)</span>
                                    <span>$<?= number_format($subtotal, 2) ?></span>
                                </div>
                                <div class="summary-row">
                                    <span>Shipping</span>
                                    <span class="<?= $shipping == 0 ? 'text-success' : '' ?>">
                                        <?= $shipping == 0 ? 'FREE' : '$' . number_format($shipping, 2) ?>
                                    </span>
                                </div>
                                <div class="summary-row">
                                    <span>Tax</span>
                                    <span>$<?= number_format($tax, 2) ?></span>
                                </div>
                                <hr>
                                <div class="summary-row total">
                                    <strong>
                                        <span>Total</span>
                                        <span>$<?= number_format($total, 2) ?></span>
                                    </strong>
                                </div>

                                <button class="btn btn-primary w-100 mt-3" disabled>
                                    <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                                    <br><small>(Coming Soon)</small>
                                </button>

                                <?php if ($subtotal < 50 && $subtotal > 0): ?>
                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-shipping-fast me-1"></i>
                                            Add $<?= number_format(50 - $subtotal, 2) ?> more for free shipping!
                                        </small>
                                    </div>
                                <?php endif; ?>

                                <div class="mt-3">
                                    <small class="text-muted">
                                        <i class="fas fa-shield-alt me-1"></i>
                                        Secure checkout with SSL encryption
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="../assets/images/RP-2.png" alt="RealPixel Logo" class="footer-logo me-2">
                        <h5 class="mb-0">Real Pixel Store</h5>
                    </div>
                    <p class="text-muted small">Crafting pixel-perfect digital experiences since 2025</p>
                </div>

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

                <div class="col-lg-4 mb-3">
                    <h6 class="text-uppercase mb-3">Follow Us</h6>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light fs-4" title="Facebook"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-light fs-4" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-light fs-4" title="LinkedIn"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>

            <hr class="border-secondary">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-muted small">&copy; 2025 Real Pixel Store. All rights reserved.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>

    <script>
        // Quantity control
        document.querySelectorAll('.quantity-controls').forEach(control => {
            const minusBtn = control.querySelector('.quantity-decrease');
            const plusBtn = control.querySelector('.quantity-increase');
            const input = control.querySelector('.quantity-input');

            minusBtn.addEventListener('click', () => {
                const val = parseInt(input.value);
                if (val > 1) input.value = val - 1;
            });

            plusBtn.addEventListener('click', () => {
                const val = parseInt(input.value);
                if (val < 99) input.value = val + 1;
            });
        });

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert').forEach(alert => {
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    </script>
</body>

</html>