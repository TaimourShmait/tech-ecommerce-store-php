<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../../includes/db.php";

$product_id = $_GET["product_id"];

// Get product details
$sql = "SELECT * FROM products WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(":id", $product_id);
$stmt->execute();
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Get all images for this product
$sql_images = "SELECT products.id as product_id, images.id as image_id, images.image_url, images.filename, images.alt_text 
                   FROM product_images 
                   INNER JOIN products ON product_images.product_id = products.id 
                   INNER JOIN images ON product_images.image_id = images.id 
                   WHERE products.id = :product_id 
                   ORDER BY images.id ASC";
$stmt_images = $pdo->prepare($sql_images);
$stmt_images->bindParam(":product_id", $product_id);
$stmt_images->execute();
$product_images = $stmt_images->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Images - <?= htmlspecialchars($product["name"]) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="../../../assets/css/admin.css" rel="stylesheet">
</head>

<body class="d-flex flex-column">

    <header class="w-100 d-flex flex-row justify-content-between align-items-center p-3">
        <p>Real Pixel Store</p>
        <p>Admin Panel</p>
    </header>

    <main class="d-flex flex-row w-100 p-2 gap-2 flex-grow-1 align-items-stretch">

        <nav class="admin-sidebar d-flex flex-column p-3 border rounded h-100">
            <ul class="nav d-flex flex-column sidebar-nav gap-2">
                <li class="nav-item"><a class="nav-link sidebar-link" href="../../index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../user_management/users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../admin_management/admins.php">Admins</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link active" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../category_management/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../subcategory_management/subcategories.php">Subcategories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../../logout.php">Log Out</a></li>
            </ul>
        </nav>

        <div class="main-content d-flex flex-column w-100 p-2 gap-3 flex-grow-1">

            <!-- Page Header -->
            <div class="d-flex flex-column gap-1">
                <h4 class="mb-0">Manage Images</h4>
                <p class="text-muted mb-0"><?= htmlspecialchars($product["name"]) ?></p>
            </div>

            <!-- Cover Image Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Cover Image</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3">
                        <?php if ($product["cover_image"]): ?>
                            <img class="image-management-product-cover-image"
                                src="../../../assets/uploads/<?= htmlspecialchars($product["cover_image"]) ?>"
                                alt="Cover Image">
                            <div class="d-flex flex-column gap-2">
                                <p class="mb-1"><strong>Current Cover Image</strong></p>
                                <p class="text-muted small mb-2"><?= htmlspecialchars($product["cover_image"]) ?></p>

                                <!-- Update Cover Image Form -->
                                <form method="POST" action="../../../api/admin/product_images.php" enctype="multipart/form-data" class="d-flex flex-column gap-2">
                                    <input type="hidden" name="action" value="update_cover">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    <input type="file" name="cover_image" accept="image/*" class="form-control form-control-sm" required>
                                    <button type="submit" class="btn btn-outline-primary btn-sm">
                                        <i class="bi bi-pencil"></i> Update Cover
                                    </button>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                No cover image set for this product.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Gallery Images Section -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Gallery Images (<?= count($product_images) ?>)</h5>
                </div>
                <div class="card-body">

                    <!-- Current Images Grid -->
                    <?php if (count($product_images) > 0): ?>
                        <div class="row g-3 mb-4">
                            <?php foreach ($product_images as $index => $image): ?>
                                <div class="col-md-4 col-lg-3">
                                    <div class="card">
                                        <img src="../../../assets/uploads/<?= htmlspecialchars($image["image_url"]) ?>"
                                            class="card-img-top"
                                            style="height: 200px; object-fit: cover;"
                                            alt="<?= htmlspecialchars($image["alt_text"]) ?>">
                                        <div class="card-body p-2">
                                            <p class="card-text small mb-2">
                                                <strong>Image <?= $index + 1 ?></strong><br>
                                                <span class="text-muted"><?= htmlspecialchars($image["filename"]) ?></span>
                                            </p>

                                            <!-- Delete Image Form -->
                                            <form method="POST" action="../../../api/admin/product_images.php" class="d-inline">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                                <input type="hidden" name="image_id" value="<?= $image["image_id"] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm w-100"
                                                    onclick="return confirm('Are you sure you want to delete this image?')">
                                                    <i class="bi bi-trash"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No gallery images uploaded yet. Use the form below to add some!
                        </div>
                    <?php endif; ?>

                    <!-- Add New Images Section -->
                    <div class="border-top pt-3">
                        <h6 class="mb-3">Add New Images</h6>
                        <form method="POST" action="../../../api/admin/product_images.php" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="add">
                            <input type="hidden" name="product_id" value="<?= $product_id ?>">

                            <div class="row g-3">
                                <div class="col-md-8">
                                    <input required type="file" name="product_images[]" multiple accept="image/*"
                                        class="form-control" id="newImages">
                                    <div class="form-text">
                                        <i class="bi bi-info-circle"></i>
                                        Select multiple images. Hold Ctrl/Cmd to select multiple files.
                                        Supported formats: JPG, PNG. Max 500KB each.
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-plus-circle"></i> Add Images
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <div class="d-flex gap-2">
                <a class="btn btn-outline-secondary" href="products.php">
                    <i class="bi bi-arrow-left"></i> Back to Products
                </a>
            </div>
        </div>
    </main>

    <footer class="w-100 d-flex flex-row justify-content-between align-items-center p-3">Footer!</footer>

    <script>
        // Image preview functionality
        document.getElementById('newImages').addEventListener('change', function(e) {
            const files = e.target.files;
            if (files.length > 0) {
                let fileNames = [];
                for (let i = 0; i < Math.min(files.length, 3); i++) {
                    fileNames.push(files[i].name);
                }
                const preview = fileNames.join(', ') + (files.length > 3 ? ` and ${files.length - 3} more...` : '');
                console.log('Selected files:', preview);
            }
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                if (alert.classList.contains('alert-success') || alert.classList.contains('alert-danger')) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
    </script>

</body>

</html>