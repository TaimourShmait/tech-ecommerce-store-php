<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $product_id = $_POST["product_id"];

    if (!$product_id) {
        die("Product ID is required");
    }

    if ($action == "add") {
        // Get product name for alt text generation
        $sql_product = "SELECT name FROM products WHERE id = :id";
        $stmt_product = $pdo->prepare($sql_product);
        $stmt_product->bindParam(":id", $product_id);
        $stmt_product->execute();
        $product = $stmt_product->fetch(PDO::FETCH_ASSOC);

        if (!$product) {
            die("Product not found");
        }

        // Handle multiple product images
        if (isset($_FILES["product_images"]) && !empty($_FILES["product_images"]["tmp_name"][0])) {

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

                        // Insert into images table
                        $sql_image = "INSERT INTO images (image_url, filename, alt_text) VALUES (:image_url, :filename, :alt_text)";
                        $stmt_image = $pdo->prepare($sql_image);
                        $stmt_image->bindParam(":image_url", $img_name);
                        $stmt_image->bindParam(":filename", $fname);
                        $stmt_image->bindParam(":alt_text", $alt_text);
                        $stmt_image->execute();

                        $image_id = $pdo->lastInsertId();

                        // Link image to product via bridge table
                        $sql_link = "INSERT INTO product_images (product_id, image_id) VALUES (:product_id, :image_id)";
                        $stmt_link = $pdo->prepare($sql_link);
                        $stmt_link->bindParam(":product_id", $product_id);
                        $stmt_link->bindParam(":image_id", $image_id);
                        $stmt_link->execute();
                    }
                }
            }
        }

        header("Location: ../../admin/pages/product_management/manage_images.php?product_id=" . $product_id);
        exit();
    } else if ($action == "update_cover") {
        if (!isset($_FILES["cover_image"]) || $_FILES["cover_image"]["error"] !== UPLOAD_ERR_OK) {
            die("Cover image is required");
        }

        $file = $_FILES["cover_image"];
        $path = $file["tmp_name"];
        $fname = $file["name"];
        $size = $file["size"];
        $type = strtolower(pathinfo($fname, PATHINFO_EXTENSION));

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

        // Update cover image in products table
        $sql = "UPDATE products SET cover_image = :cover_image WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":cover_image", $img_name);
        $stmt->bindParam(":id", $product_id);
        $stmt->execute();

        header("Location: ../../admin/pages/product_management/manage_images.php?product_id=" . $product_id);
        exit();
    } else if ($action == "delete") {
        $image_id = $_POST["image_id"];

        if (!$image_id) {
            die("Image ID is required");
        }

        try {
            $pdo->beginTransaction();

            // Get image info before deletion
            $sql_get_image = "SELECT image_url FROM images WHERE id = :image_id";
            $stmt_get_image = $pdo->prepare($sql_get_image);
            $stmt_get_image->bindParam(":image_id", $image_id);
            $stmt_get_image->execute();
            $image = $stmt_get_image->fetch(PDO::FETCH_ASSOC);

            if (!$image) {
                throw new Exception("Image not found");
            }

            // Remove from bridge table first
            $sql_bridge = "DELETE FROM product_images WHERE product_id = :product_id AND image_id = :image_id";
            $stmt_bridge = $pdo->prepare($sql_bridge);
            $stmt_bridge->bindParam(":product_id", $product_id);
            $stmt_bridge->bindParam(":image_id", $image_id);
            $stmt_bridge->execute();

            // Check if image is used elsewhere
            $sql_usage = "
                    SELECT COUNT(*) as usage_count FROM (
                        SELECT image_id FROM product_images WHERE image_id = :image_id
                        UNION ALL
                        SELECT image_id FROM category_images WHERE image_id = :image_id
                        UNION ALL
                        SELECT image_id FROM subcategory_images WHERE image_id = :image_id
                    ) as usage
                ";
            $stmt_usage = $pdo->prepare($sql_usage);
            $stmt_usage->bindParam(":image_id", $image_id);
            $stmt_usage->execute();
            $usage = $stmt_usage->fetch(PDO::FETCH_ASSOC);

            // If not used elsewhere, delete from images table and filesystem
            if ($usage["usage_count"] == 0) {
                // Delete file from filesystem
                $file_path = "../../assets/uploads/" . $image["image_url"];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }

                // Delete from images table
                $sql_delete_image = "DELETE FROM images WHERE id = :image_id";
                $stmt_delete_image = $pdo->prepare($sql_delete_image);
                $stmt_delete_image->bindParam(":image_id", $image_id);
                $stmt_delete_image->execute();
            }

            $pdo->commit();

            header("Location: ../../admin/pages/product_management/manage_images.php?product_id=" . $product_id);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            die("Error deleting image: " . $e->getMessage());
        }
    }
}
