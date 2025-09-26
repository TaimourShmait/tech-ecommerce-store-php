<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../index.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link href="../assets/css/admin.css" rel="stylesheet">
</head>

<body class="d-flex flex-column">

    <header class="w-100 d-flex flex-row justify-content-between align-items-center p-3">
        <p>Real Pixel Store</p>
        <p>Admin Panel</p>
    </header>

    <main class="d-flex flex-row w-100 p-2 gap-2 flex-grow-1 align-items-stretch">
        <nav class="admin-sidebar d-flex flex-column p-3 border rounded h-100">
            <ul class="nav d-flex flex-column sidebar-nav gap-2">
                <li class="nav-item"><a class="nav-link sidebar-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="pages/user_management/users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="pages/admin_management/admins.php">Admins</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="pages/product_management/products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="pages/category_management/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="pages/subcategory_management/subcategories.php">Subcategories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="logout.php">Log Out</a></li>
            </ul>
        </nav>


        <div class="main-content d-flex flex-column w-100 p-2 gap-2 flex-grow-1">
            <h1>Welcome back!</h1>
        </div>

    </main>

    <footer></footer>

</body>

</html>