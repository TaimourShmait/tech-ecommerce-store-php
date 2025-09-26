<?php

session_start();
$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';
// Get current page to set active navigation
$current_page = basename($_SERVER['PHP_SELF']);


require "../includes/db.php";
require "../api/cart/cart_functions.php";

$is_logged_in = isset($_SESSION['user_id']) && isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] === true;
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : '';

if ($is_logged_in) {
    $user_id = $_SESSION['user_id'];
    $cart_items = getCartItems($pdo, $user_id);
    $cart_summary = getCartSummary($pdo, $user_id);
    $subtotal = $cart_summary['total_price'] ? $cart_summary['total_price'] : 0;
}


// Calculate tax and shipping

// Get filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_filter = isset($_GET['category']) ? $_GET['category'] : '';
$subcategory_filter = isset($_GET['subcategory']) ? $_GET['subcategory'] : '';
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'name_asc';

// Build the main query
$sql = "SELECT 
    products.id,
    products.name,
    products.description,
    products.price,
    products.cover_image,
    subcategories.name as subcategory_name,
    subcategories.id as subcategory_id,
    categories.name as category_name,
    categories.id as category_id,
    COUNT(pi.image_id) as gallery_count
FROM products
INNER JOIN product_subcategories ps ON products.id = ps.product_id
INNER JOIN subcategories ON ps.subcategory_id = subcategories.id
INNER JOIN category_subcategories cs ON subcategories.id = cs.subcategory_id
INNER JOIN categories ON cs.category_id = categories.id
LEFT JOIN product_images pi ON products.id = pi.product_id
";

// Add WHERE conditions
$conditions = [];
$params = [];

if (!empty($search)) {
    $conditions[] = "(products.name LIKE :search OR products.description LIKE :search)";
    $params[':search'] = '%' . $search . '%';
}

if (!empty($category_filter)) {
    $conditions[] = "categories.id = :category_id";
    $params[':category_id'] = $category_filter;
}

if (!empty($subcategory_filter)) {
    $conditions[] = "subcategories.id = :subcategory_id";
    $params[':subcategory_id'] = $subcategory_filter;
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " GROUP BY products.id";

// Add sorting
switch ($sort_by) {
    case 'name_asc':
        $sql .= " ORDER BY products.name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY products.name DESC";
        break;
    case 'price_asc':
        $sql .= " ORDER BY products.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY products.price DESC";
        break;
    default:
        $sql .= " ORDER BY products.name ASC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all categories for filter
$categories_sql = "SELECT DISTINCT categories.id, categories.name FROM categories 
                   INNER JOIN category_subcategories cs ON categories.id = cs.category_id
                   INNER JOIN subcategories s ON cs.subcategory_id = s.id
                   INNER JOIN product_subcategories ps ON s.id = ps.subcategory_id
                   ORDER BY categories.name ASC";
$categories_stmt = $pdo->prepare($categories_sql);
$categories_stmt->execute();
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);

// Get subcategories for filter
$subcategories_sql = "SELECT DISTINCT subcategories.id, subcategories.name, categories.name as category_name 
                      FROM subcategories 
                      INNER JOIN category_subcategories cs ON subcategories.id = cs.subcategory_id
                      INNER JOIN categories ON cs.category_id = categories.id
                      INNER JOIN product_subcategories ps ON subcategories.id = ps.subcategory_id
                      ORDER BY categories.name ASC, subcategories.name ASC";
$subcategories_stmt = $pdo->prepare($subcategories_sql);
$subcategories_stmt->execute();
$subcategories = $subcategories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Real Pixel Store</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../assets/css/productsStyles.css">
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
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Sidebar Filters -->
                <aside class="col-lg-3 col-md-4 mb-4">
                    <div class="filter-sidebar">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-filter me-2"></i>
                            <h4 class="mb-0">Filters</h4>
                        </div>

                        <form method="GET" action="products.php" id="filterForm">
                            <input type="hidden" name="sort" value="<?= htmlspecialchars($sort_by) ?>">

                            <!-- Search Filter -->
                            <div class="filter-group mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-search me-1"></i>Search
                                </label>
                                <input type="text" name="search" class="form-control"
                                    value="<?= htmlspecialchars($search) ?>"
                                    placeholder="Search products...">
                            </div>

                            <!-- Category Filter -->
                            <div class="filter-group mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-tags me-1"></i>Category
                                </label>
                                <select name="category" class="form-select">
                                    <option value="">All Categories</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>" <?= $category_filter == $category['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($category['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Subcategory Filter -->
                            <div class="filter-group mb-3">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-bookmark me-1"></i>Subcategory
                                </label>
                                <select name="subcategory" class="form-select">
                                    <option value="">All Subcategories</option>
                                    <?php foreach ($subcategories as $subcategory): ?>
                                        <option value="<?= $subcategory['id'] ?>" <?= $subcategory_filter == $subcategory['id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($subcategory['category_name']) ?> > <?= htmlspecialchars($subcategory['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-check me-1"></i>Apply Filters
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Products Content -->
                <div class="col-lg-9 col-md-8">
                    <!-- Sort Bar -->
                    <div class="sort-bar mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="results-info">
                                <i class="fas fa-th-large me-1"></i>
                                <span class="fw-bold"><?= count($products) ?></span> products found
                                <?php if ($search || $category_filter || $subcategory_filter): ?>
                                    <a href="products.php" class="btn btn-sm btn-outline-danger ms-2">
                                        <i class="fas fa-times me-1"></i>Clear Filters
                                    </a>
                                <?php endif; ?>
                            </div>

                            <div class="sort-controls d-flex align-items-center">
                                <label class="me-2 mb-0">Sort by:</label>
                                <select class="form-select form-select-sm" onchange="changeSort(this.value)" style="width: auto;">
                                    <option value="name_asc" <?= $sort_by == 'name_asc' ? 'selected' : '' ?>>Name (A-Z)</option>
                                    <option value="name_desc" <?= $sort_by == 'name_desc' ? 'selected' : '' ?>>Name (Z-A)</option>
                                    <option value="price_asc" <?= $sort_by == 'price_asc' ? 'selected' : '' ?>>Price (Low to High)</option>
                                    <option value="price_desc" <?= $sort_by == 'price_desc' ? 'selected' : '' ?>>Price (High to Low)</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div class="products-grid">
                        <?php if (empty($products)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-search display-1 text-muted mb-3"></i>
                                <h3 class="text-muted">No products found</h3>
                                <p class="text-muted">Try adjusting your filters or search terms</p>
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($products as $product): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
                                        <div class="product-card h-100">
                                            <?php if ($product['gallery_count'] > 0): ?>
                                                <div class="gallery-badge">
                                                    <i class="fas fa-images me-1"></i>
                                                    <?= $product['gallery_count'] ?>
                                                </div>
                                            <?php endif; ?>

                                            <div class="product-image">
                                                <?php if ($product['cover_image']): ?>
                                                    <img src="../assets/uploads/<?= htmlspecialchars($product['cover_image']) ?>"
                                                        alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" />
                                                <?php else: ?>
                                                    <img src="../assets/placeholder-product.png"
                                                        alt="<?= htmlspecialchars($product['name']) ?>" class="img-fluid" />
                                                <?php endif; ?>
                                            </div>

                                            <div class="product-info p-3">
                                                <h5 class="product-title"><?= htmlspecialchars($product['name']) ?></h5>
                                                <p class="product-category text-muted small mb-2">
                                                    <?= htmlspecialchars($product['subcategory_name']) ?>
                                                </p>
                                                <p class="product-price mb-3">$<?= number_format($product['price'], 2) ?></p>

                                                <div class="product-actions d-grid gap-2">
                                                    <button class="btn btn-outline-primary btn-sm"
                                                        onclick="window.location.href='product_details.php?id=<?= $product['id'] ?>'">
                                                        <i class="fas fa-eye me-1"></i>View Details
                                                    </button>
                                                    <button class="btn btn-primary btn-sm" onclick="addToCart(<?= $product['id'] ?>)">
                                                        <i class="fas fa-cart-plus me-1"></i>Add to Cart
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
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
        function changeSort(sortValue) {
            const url = new URL(window.location);
            url.searchParams.set('sort', sortValue);
            window.location.href = url.toString();
        }

        // Replace the existing addToCart function in your product pages with this

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


        // Auto-submit form when filters change
        document.querySelector('select[name="category"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });

        document.querySelector('select[name="subcategory"]').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    </script>
</body>

</html>