<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../../includes/db.php";

$sql_subcategories = "SELECT * FROM subcategories";
$stmt_subcategories = $pdo->prepare($sql_subcategories);
$stmt_subcategories->execute();
$subcategories = $stmt_subcategories->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins</title>
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
                <li class="nav-item"><a class="nav-link sidebar-link" href="products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../category_management/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../subcategory_management/subcategories.php">Subcategories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../../logout.php">Log Out</a></li>
            </ul>
        </nav>


        <div class="main-content d-flex flex-column w-100 p-2 gap-2 flex-grow-1">
            <p>Create New Product</p>

            <form class="d-flex flex-column gap-2 w-100" method="POST" action="../../../api/admin/products.php" enctype="multipart/form-data">
                <input type="hidden" value="create" name="action">
                <input class="form-control" id="name" type="text" placeholder="Product Name" name="name" required>
                <input class="form-control" id="description" type="text" placeholder="Description" name="description" required>
                <input class="form-control" id="price" type="number" step="0.01" placeholder="Price in USD" name="price" required>

                <div class="d-flex gap-2 flex-column">
                    <div class="d-flex flex-column gap-2">
                        <label class="form-label mb-0 text-nowrap" for="cover_image">Cover Image</label>
                        <input class="form-control flex-grow-1" id="cover_image" type="file" name="cover_image" required>
                    </div>

                    <div class="d-flex flex-column gap-2">
                        <label class="form-label mb-0 text-nowrap" for="images">Images</label>
                        <input class="form-control flex-grow-1" id="images" type="file" multiple name="product_images[]" required>
                    </div>
                </div>

                <select class="form-select" name="subcategory" id="subcategory-select">
                    <option value="">Select Subcategory</option>
                    <?php
                    foreach ($subcategories as $subcategory) {
                        echo "<option value='" . $subcategory["id"] . "'>" . $subcategory["name"] . "</option>";
                    }
                    ?>
                </select>

                <input class="btn btn-success w-25" type="submit" value="Create Product">
            </form>

            <a class="btn btn-outline-primary w-25" href="products.php">Back</a>
        </div>
    </main>

    <footer></footer>

</body>

</html>