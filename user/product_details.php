<?php
session_start();

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';
// Get current page to set active navigation
$current_page = basename($_SERVER['PHP_SELF']);

require "../includes/db.php";
require "../api/cart/cart_functions.php";

$user_id = $_SESSION['user_id'];

$cart_items = getCartItems($pdo, $user_id);
$cart_summary = getCartSummary($pdo, $user_id);


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: products.php");
    exit();
}

$product_id = (int)$_GET['id'];

// Get product details
$product_sql = "SELECT 
    products.id,
    products.name,
    products.description,
    products.price,
    products.cover_image,
    subcategories.name as subcategory_name,
    categories.name as category_name
FROM products
INNER JOIN product_subcategories ps ON products.id = ps.product_id
INNER JOIN subcategories ON ps.subcategory_id = subcategories.id
INNER JOIN category_subcategories cs ON subcategories.id = cs.subcategory_id
INNER JOIN categories ON cs.category_id = categories.id
WHERE products.id = :product_id";

$stmt = $pdo->prepare($product_sql);
$stmt->bindParam(':product_id', $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header("Location: products.php");
    exit();
}

// Get gallery images
$images_sql = "SELECT 
    images.id as image_id,
    images.image_url,
    images.filename,
    images.alt_text
FROM product_images
INNER JOIN images ON product_images.image_id = images.id
WHERE product_images.product_id = :product_id
ORDER BY images.id ASC";

$images_stmt = $pdo->prepare($images_sql);
$images_stmt->bindParam(':product_id', $product_id);
$images_stmt->execute();
$images = $images_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($product['name']) ?> - Real Pixel Store</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/product_details.css">
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
                                    <?php if ($cart_summary['total_quantity'] > 0): ?>
                                        <span class="badge bg-danger ms-1"><?= $cart_summary['total_quantity'] ?></span>
                                    <?php endif; ?>
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
    <main class="flex-grow-1">
        <div class="container py-4">
            <!-- Breadcrumb -->
            <nav class="breadcrumb-nav mb-4">
                <div class="breadcrumb-container">
                    <a href="../index.php" class="breadcrumb-link">
                        <i class="fas fa-home me-1"></i>Home
                    </a>
                    <span class="breadcrumb-separator">/</span>
                    <a href="products.php" class="breadcrumb-link">Products</a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-category"><?= htmlspecialchars($product['category_name']) ?></span>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current"><?= htmlspecialchars($product['name']) ?></span>
                </div>
            </nav>

            <!-- Product Details -->
            <div class="row g-4">
                <!-- Product Images -->
                <div class="col-lg-6">
                    <div class="product-images-section">
                        <!-- Main Image -->
                        <div class="main-image-container mb-3">
                            <?php if ($product['cover_image']): ?>
                                <img id="mainImage"
                                    src="../assets/uploads/<?= htmlspecialchars($product['cover_image']) ?>"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                    class="main-product-image">
                            <?php else: ?>
                                <img id="mainImage"
                                    src="../assets/placeholder-product.png"
                                    alt="<?= htmlspecialchars($product['name']) ?>"
                                    class="main-product-image">
                            <?php endif; ?>
                        </div>

                        <!-- Gallery Thumbnails -->
                        <?php if (!empty($images)): ?>
                            <div class="image-gallery">
                                <div class="d-flex flex-wrap gap-2">
                                    <?php foreach ($images as $index => $image): ?>
                                        <img src="../assets/uploads/<?= htmlspecialchars($image['image_url']) ?>"
                                            alt="<?= htmlspecialchars($image['alt_text']) ?>"
                                            class="gallery-thumbnail <?= $index === 0 ? 'active' : '' ?>"
                                            onclick="changeMainImage('<?= htmlspecialchars($image['image_url']) ?>', this)">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Product Information -->
                <div class="col-lg-6">
                    <div class="product-info-section">
                        <!-- Product Title -->
                        <h1 class="product-title mb-2"><?= htmlspecialchars($product['name']) ?></h1>

                        <!-- Category -->
                        <p class="product-category mb-3">
                            <i class="fas fa-tag me-1"></i>
                            <?= htmlspecialchars($product['category_name']) ?> > <?= htmlspecialchars($product['subcategory_name']) ?>
                        </p>

                        <!-- Price -->
                        <div class="product-price mb-4">$<?= number_format($product['price'], 2) ?></div>

                        <!-- Description -->
                        <div class="product-description mb-4">
                            <h5 class="description-title">
                                <i class="fas fa-info-circle me-1"></i>Description
                            </h5>
                            <p class="description-text">
                                <?= nl2br(htmlspecialchars($product['description'])) ?>
                            </p>
                        </div>

                        <!-- Gallery Info -->
                        <?php if (!empty($images)): ?>
                            <div class="gallery-info mb-4">
                                <small class="text-muted">
                                    <i class="fas fa-images me-1"></i>
                                    This product has <?= count($images) ?> additional image<?= count($images) > 1 ? 's' : '' ?>
                                </small>
                            </div>
                        <?php endif; ?>

                        <!-- Action Buttons -->
                        <div class="product-actions">
                            <div class="d-grid gap-2 d-md-flex">
                                <button class="btn btn-success btn-lg flex-md-fill" onclick="addToCart(<?= $product['id'] ?>)">
                                    <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                </button>
                                <a href="products.php" class="btn btn-outline-secondary btn-lg">
                                    <i class="fas fa-arrow-left me-2"></i>Back to Products
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

    <script>
        function changeMainImage(imageSrc, thumb) {
            document.getElementById('mainImage').src = '../assets/uploads/' + imageSrc;

            // Update active thumbnail
            document.querySelectorAll('.gallery-thumbnail').forEach(img => {
                img.classList.remove('active');
            });
            thumb.classList.add('active');
        }

        // Replace the existing addToCart function in your product pages with this

        function addToCart(productId, quantity = 1) {
            // Create form data
            const formData = new FormData();
            formData.append('action', 'add_to_cart');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);

            // Show loading state
            const button = event.target;
            const originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Adding...';
            button.disabled = true;

            // Submit to cart API
            fetch('../api/cart/cart_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (response.redirected) {
                        // If redirected to login, follow redirect
                        window.location.href = response.url;
                    } else {
                        // Success - redirect to cart page
                        window.location.href = 'cart.php?success=item_added';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Restore button state
                    button.innerHTML = originalText;
                    button.disabled = false;
                    alert('Error adding item to cart. Please try again.');
                });
        }

        // Alternative version using form submission (more reliable for your pattern)
        function addToCart(productId, quantity = 1) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '../api/cart/cart_handler.php';

            // Add AJAX flag
            const ajaxField = document.createElement('input');
            ajaxField.type = 'hidden';
            ajaxField.name = 'ajax';
            ajaxField.value = '1';
            form.appendChild(ajaxField);

            const actionField = document.createElement('input');
            actionField.type = 'hidden';
            actionField.name = 'action';
            actionField.value = 'add_to_cart';
            form.appendChild(actionField);

            const productField = document.createElement('input');
            productField.type = 'hidden';
            productField.name = 'product_id';
            productField.value = productId;
            form.appendChild(productField);

            const quantityField = document.createElement('input');
            quantityField.type = 'hidden';
            quantityField.name = 'quantity';
            quantityField.value = quantity;
            form.appendChild(quantityField);

            // Submit via fetch instead of form.submit()
            const formData = new FormData(form);

            fetch('../api/cart/cart_handler.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success feedback
                        showCartNotification('Item added to cart!');
                    } else {
                        alert('Error adding item to cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding item to cart');
                });
        }

        // Add this notification function
        function showCartNotification(message) {
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'alert alert-success position-fixed';
            toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
            toast.innerHTML = `
        <i class="fas fa-check-circle me-2"></i>${message}
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;

            document.body.appendChild(toast);

            // Auto-remove after 3 seconds
            setTimeout(() => {
                if (toast.parentElement) {
                    toast.remove();
                }
            }, 3000);
        }
    </script>
</body>

</html>