<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];

    if ($action == "create") {
        $name = trim($_POST["name"]);
        $description = trim($_POST["description"]);
        $price = trim($_POST["price"]);
        $file = $_FILES["cover_image"];
        $path = $file["tmp_name"];
        $fname = $file["name"];
        $size = $file["size"];
        $type = strtolower(pathinfo($fname, PATHINFO_EXTENSION));
        $subcategory_id = $_POST["subcategory"];

        if (empty($name) || empty($description) || empty($price)) {
            header("Location: ../../admin/pages/product_management/create_product.php");
            exit();
        }

        if ($type != "png" && $type != "jpg") {
            die("Invalid image type!");
        }

        if (!getimagesize($path)) {
            die("Invalid image!");
        }

        if ($size > 500000) {
            die("Image too large!");
        }

        $img_name = "COVERIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;

        $sql_name = "SELECT * FROM products WHERE name = :name"; // MAKE INTO FUNCTION!
        $stmt_name = $pdo->prepare($sql_name);
        $stmt_name->bindParam(":name", $name);
        $stmt_name->execute();
        $product = $stmt_name->fetch(PDO::FETCH_ASSOC);

        if ($product) {
            header("Location: ../../admin/pages/product_management/create_product.php");
            exit();
        }

        while (file_exists("../../assets/uploads/$img_name")) {
            $img_name = "COVERIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;
        }

        $upload_path = "../../assets/uploads/" . $img_name;

        if (!move_uploaded_file($path, $upload_path)) {
            die("Failed to move uploaded image.");
        }

        try {
            $sql = "INSERT INTO products (name, description, price, cover_image) VALUES (:name, :description, :price, :cover_image)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":description", $description);
            $stmt->bindParam(":price", $price);
            $stmt->bindParam(":cover_image", $img_name);
            $stmt->execute();

            $product_id = $pdo->lastInsertId();

            $sql_subcategory = "INSERT INTO product_subcategories (product_id, subcategory_id) VALUES (:product_id, :subcategory_id)";
            $stmt_subcategory = $pdo->prepare($sql_subcategory);
            $stmt_subcategory->bindParam(":product_id", $product_id);
            $stmt_subcategory->bindParam(":subcategory_id", $subcategory_id);
            $stmt_subcategory->execute();

            // Handle product images

            if (isset($_FILES["product_images"]) && !empty($_FILES["product_images"]["tmp_name"][0])) {
                $sql_product = "SELECT name FROM products WHERE id = :id";
                $stmt_product = $pdo->prepare($sql_product);
                $stmt_product->bindParam(":id", $product_id);
                $stmt_product->execute();
                $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

                foreach ($_FILES["product_images"]["tmp_name"] as $key => $path) {
                    if ($_FILES["product_images"]["error"][$key] == UPLOAD_ERR_OK) {
                        $fname = $_FILES["product_images"]["name"][$key];
                        $size = $_FILES["product_images"]["size"][$key];
                        $type = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

                        if ($type != "png" && $type != "jpg") {
                            continue;
                        }

                        if (!getimagesize($path)) {
                            continue;
                        }

                        if ($size > 500000) {
                            continue;
                        }

                        $img_name = "PRODUCTIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;

                        while (file_exists("../../assets/uploads/$img_name")) {
                            $img_name = "PRODUCTIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;
                        }

                        $upload_path = "../../assets/uploads/" . $img_name;

                        if (move_uploaded_file($path, $upload_path)) {

                            // Auto-generate alt text from product name
                            $alt_text = $product["name"] . " - Product Image";

                            // Insert into images table (your bindParam style)
                            $sql_image = "INSERT INTO images (filename, image_url, alt_text) VALUES (:filename, :image_url, :alt_text)";
                            $stmt_image = $pdo->prepare($sql_image);
                            $stmt_image->bindParam(":filename", $fname);
                            $stmt_image->bindParam(":image_url", $img_name);  // Store just filename like cover_image
                            $stmt_image->bindParam(":alt_text", $alt_text);
                            $stmt_image->execute();

                            $image_id = $pdo->lastInsertId();

                            // Link image to product via bridge table (your bindParam style)
                            $sql_link = "INSERT INTO product_images (product_id, image_id) VALUES (:product_id, :image_id)";
                            $stmt_link = $pdo->prepare($sql_link);
                            $stmt_link->bindParam(":product_id", $product_id);
                            $stmt_link->bindParam(":image_id", $image_id);
                            $stmt_link->execute();
                        }
                    }
                }
            }


            header("Location: ../../admin/pages/product_management/products.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "update") {
        $id = $_POST["id"];
        if (!$id) {
            die("Product ID is required");
        }

        if (isset($_POST["name"])) {
            $name = trim($_POST["name"]);
            if ($name === "") {
                die("Name cannot be empty");
            }
            $sql = "UPDATE products SET name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":name" => $name, ":id" => $id]);
        }

        if (isset($_POST["description"])) {
            $description = trim($_POST["description"]);
            if ($description === "") {
                die("Last name cannot be empty");
            }
            $sql = "UPDATE products SET description = :description WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":description" => $description, ":id" => $id]);
        }

        if (isset($_POST["price"])) {
            $price = $_POST["price"];
            $sql = "UPDATE products SET price = :price WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":price" => $price, ":id" => $id]);
        }

        if (isset($_FILES["cover_image"])) {
            $file = $_FILES["cover_image"];

            $path = $file["tmp_name"];
            $name = $file["name"];
            $size = $file["size"];
            $type = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            if ($type != "png" && $type != "jpg") {
                die("Invalid image type!");
            }

            if (!getimagesize($path)) {
                die("Invalid image!");
            }

            if ($size > 500000) {
                die("Image too large!");
            }

            $img_name = "COVERIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;

            while (file_exists("../../assets/uploads/$img_name")) {
                $img_name = "COVERIMAGE_" . bin2hex(random_bytes(10)) . "." . $type;
            }

            $upload_path = "../../assets/uploads/" . $img_name;

            if (!move_uploaded_file($path, $upload_path)) {
                die("Failed to move uploaded image.");
            }

            $sql = "UPDATE products SET cover_image = :cover_image WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":cover_image", $img_name);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        if (isset($_POST["subcategory"])) {
            $subcategory_id = $_POST["subcategory"];
            $sql = "UPDATE product_subcategories SET subcategory_id = :subcategory_id WHERE product_id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":subcategory_id", $subcategory_id);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        header("Location: ../../admin/pages/product_management/products.php");
        exit();
    } else if ($action == "delete") { // make sure to delete entry in product_subcategorires and product cover image / other images
        $id = $_POST["id"];

        try {
            $sql = "DELETE FROM products WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            header("Location: ../../admin/pages/product_management/products.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
