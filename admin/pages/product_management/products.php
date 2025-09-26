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


        <div class="main-content d-flex flex-column w-100 p-3 gap-2 flex-grow-1">
            <div class="d-flex flex-row w-100 gap-2">
                <form class="d-flex flex-grow-1 gap-2">
                    <input class="form-control flex-grow-1 p-2" type="search" placeholder="Search products" name="search">
                    <input class="btn btn-outline-success" type="submit" value="Search">
                </form>

                <a class="btn btn-primary" href="create_product.php"><i class="bi bi-plus-circle"></i></a>
            </div>

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Subcategory</th>
                        <th>Category</th>
                        <th>Images</th>
                        <th colspan="2">Cover Image</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    // $id = 0;

                    $sql = "SELECT
                            products.id,
                            products.name,
                            products.description,
                            products.price,
                            products.cover_image,
                            products.created_at,
                            products.updated_at,
                            subcategories.name as subcategory,
                            subcategories.id as subcategory_id, 
                            categories.name as category
                            from product_subcategories
                                inner join products on product_subcategories.product_id = products.id
                                inner join subcategories on product_subcategories.subcategory_id = subcategories.id
                                inner join category_subcategories on subcategories.id = category_subcategories.subcategory_id
                                inner join categories on category_subcategories.category_id = categories.id
                        ";

                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($products as $product) {
                        echo "
                                <tr>
                                    <td>" . $product["id"] . "</td>
                                    <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/products.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $product["id"] . "'>
                                            <input class='form-control' type='text' name='name' value='" . htmlspecialchars($product["name"]) . "'>
                                            <button type='submit' class='btn btn-success update-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                     <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/products.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $product["id"] . "'>
                                            <input class='form-control' type='text' name='description' value='" . htmlspecialchars($product["description"]) . "'>
                                            <button type='submit' class='btn btn-success update-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                     <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/products.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $product["id"] . "'>
                                            <input class='form-control' type='number' step='0.01' name='price' value='" . htmlspecialchars($product["price"]) . "'>
                                            <button type='submit' class='btn btn-success update-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>

                                        <td>
                                            <form class='update-user-form d-flex' method='POST' action='../../../api/admin/products.php'>
                                                <input type='hidden' name='action' value='update'>
                                                <input type='hidden' name='id' value='" . $product["id"] . "'>
                                                <select class='form-select' name='subcategory' id='subcategory-select'>
                                                    <option value='" . $product['subcategory_id'] . "' selected>" . $product['subcategory'] . "</option>";

                        foreach ($subcategories as $subcategory) {
                            if ($subcategory["id"] != $product['subcategory_id']) {
                                echo "<option value='" . $subcategory["id"] . "'>" . $subcategory["name"] . "</option>";
                            }
                        }

                        echo "</select>
                                                <button type='submit' class='btn btn-success update-btn'><i class='bi bi-pencil'></i></button>
                                            </form>
                                        </td>

                                    <td>
                                        <p>" . htmlspecialchars($product["category"]) . "</p>
                                    </td>

                                    <td>
                                        <a class='btn btn-outline-secondary' href='product_image_management.php?product_id=" . $product["id"] . "'><i class='bi bi-images'></i></a>
                                    </td>

                                          
                                    <td>
                                        <img class='product-image' src='../../../assets/uploads/" . $product["cover_image"] . "'>
                                    </td>

                                    <td>
                                        <form class='update-user-form d-flex' method='POST' enctype='multipart/form-data' action='../../../api/admin/products.php'>
                                                <input type='hidden' name='action' value='update'>
                                                <input type='hidden' name='id' value='" . $product["id"] . "'>
                                                <input class='form-control' type='file' name='cover_image'>
                                                <button type='submit' class='btn btn-success update-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>

                                    <td>
                                        <form method='POST' action='../../../api/admin/products.php' data-id='" . $product["id"] . "'>
                                            <input type='hidden' name='id' value='" . $product["id"] . "'> 
                                            <input type='hidden' name='action' value='delete'>   
                                            <button class='btn btn-danger delete-btn' data-user='" . $product["name"] . "'><i class='bi bi-trash'></i></button> 
                                        </form>
                                    </td>
                                </tr>
                            ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </main>


    <footer></footer>

    <script src="../../js/delete-form.js"></script>

</body>

</html>