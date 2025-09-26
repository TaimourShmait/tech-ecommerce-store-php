<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../../includes/db.php";

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
                <li class="nav-item"><a class="nav-link sidebar-link" href="users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../admin_management/admins.php">Admins</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../product_management/products.php">Products</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../category_management/categories.php">Categories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../subcategory_management/subcategories.php">Subcategories</a></li>
                <li class="nav-item"><a class="nav-link sidebar-link" href="../../logout.php">Log Out</a></li>
            </ul>
        </nav>



        <div class="main-content d-flex flex-column w-100 p-2 gap-2 flex-grow-1">
            <div class="d-flex flex-row w-100 gap-2">
                <form class="d-flex flex-grow-1 gap-2">
                    <input class="form-control flex-grow-1 p-2" type="search" placeholder="Search users" name="search">
                    <input class="btn btn-outline-success" type="submit" value="Search">
                </form>

                <a class="btn btn-primary" href="create_user.php"><i class="bi bi-plus-circle"></i></a>
            </div>

            <table class="table table-hover">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Date of Birth</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Created At</th>
                        <th>Updated At</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    // $id = 0;

                    $sql = "SELECT * FROM users";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute();
                    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    foreach ($users as $user) {
                        echo "
                                <tr>
                                    <td>" . $user["id"] . "</td>
                                    <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/users.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'>
                                            <input class='form-control' type='text' name='first_name' value='" . htmlspecialchars($user["first_name"]) . "'>
                                            <button type='submit' class='btn btn-success update-user-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                    <td>
                                         <form class='update-user-form d-flex' method='POST' action='../../../api/admin/users.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'>
                                            <input class='form-control' type='text' name='last_name' value='" . htmlspecialchars($user["last_name"]) . "'>
                                            <button type='submit' class='btn btn-success update-user-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/users.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'>
                                            <input class='form-control' type='date' name='dob' value='" . htmlspecialchars($user["dob"]) . "'>
                                            <button type='submit' class='btn btn-success update-user-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/users.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'>
                                            <input class='form-control' type='email' name='email' value='" . htmlspecialchars($user["email"]) . "'>
                                            <button type='submit' class='btn btn-success update-user-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                    <td>
                                        <form class='update-user-form d-flex' method='POST' action='../../../api/admin/users.php'>
                                            <input type='hidden' name='action' value='update'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'>
                                            <input class='form-control' type='tel' name='phone_number' value='" . htmlspecialchars($user["phone_number"]) . "'>
                                            <button type='submit' class='btn btn-success update-user-btn'><i class='bi bi-pencil'></i></button>
                                        </form>
                                    </td>
                                    <td>" . $user["created_at"] . "</td>
                                    <td>" . $user["updated_at"] . "</td>
                                    <td>
                                        <form method='POST' action='../../../api/admin/users.php' data-id='" . $user["id"] . "'>
                                            <input type='hidden' name='id' value='" . $user["id"] . "'> 
                                            <input type='hidden' name='action' value='delete'>   
                                            <button class='btn btn-danger delete-btn' data-user='" . $user["first_name"] . "'><i class='bi bi-trash'></i></button> 
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