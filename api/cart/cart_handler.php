<?php
session_start();
require "../../includes/db.php";

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../user/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST["action"];
    $user_id = $_SESSION['user_id'];

    try {
        if ($action == "add_to_cart") {
            $product_id = (int)$_POST["product_id"];
            $quantity = isset($_POST["quantity"]) ? (int)$_POST["quantity"] : 1;

            // Validate quantity
            if ($quantity < 1) {
                $quantity = 1;
            }

            // Check if product exists
            $product_check_sql = "SELECT id FROM products WHERE id = :product_id";
            $product_check_stmt = $pdo->prepare($product_check_sql);
            $product_check_stmt->bindParam(":product_id", $product_id);
            $product_check_stmt->execute();

            if (!$product_check_stmt->fetch()) {
                header("Location: ../../user/products.php?error=product_not_found");
                exit();
            }

            $sql = "INSERT INTO cart_items (user_id, product_id, quantity) 
                    VALUES (:user_id, :product_id, :quantity)
                    ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)";

            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":product_id", $product_id);
            $stmt->bindParam(":quantity", $quantity);
            $stmt->execute();

            if ($action == "add_to_cart") {

                // Instead of redirect, check if it's an AJAX request
                if (isset($_POST['ajax']) && $_POST['ajax'] == '1') {
                    // Return JSON for AJAX
                    header('Content-Type: application/json');
                    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
                    exit();
                } else {
                    // Regular form submission - redirect to current page
                    $referer = $_SERVER['HTTP_REFERER'] ?? '../../user/products.php';
                    header("Location: " . $referer . "?success=item_added");
                    exit();
                }
            }
        } elseif ($action == "update_quantity") {
            $cart_item_id = (int)$_POST["cart_item_id"];
            $quantity = (int)$_POST["quantity"];

            if ($quantity < 1) {
                // If quantity is 0 or negative, remove the item
                $sql = "DELETE FROM cart_items WHERE id = :cart_item_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":cart_item_id", $cart_item_id);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();
            } else {
                // Update quantity
                $sql = "UPDATE cart_items SET quantity = :quantity WHERE id = :cart_item_id AND user_id = :user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":quantity", $quantity);
                $stmt->bindParam(":cart_item_id", $cart_item_id);
                $stmt->bindParam(":user_id", $user_id);
                $stmt->execute();
            }

            header("Location: ../../user/cart.php?success=quantity_updated");
            exit();
        } elseif ($action == "remove_item") {
            $cart_item_id = (int)$_POST["cart_item_id"];

            $sql = "DELETE FROM cart_items WHERE id = :cart_item_id AND user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":cart_item_id", $cart_item_id);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            header("Location: ../../user/cart.php?success=item_removed");
            exit();
        } elseif ($action == "clear_cart") {
            $sql = "DELETE FROM cart_items WHERE user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":user_id", $user_id);
            $stmt->execute();

            header("Location: ../../user/cart.php?success=cart_cleared");
            exit();
        }
    } catch (PDOException $ex) {
        error_log("Cart error: " . $ex->getMessage());
        header("Location: ../../user/cart.php?error=database_error");
        exit();
    }
} else {
    // If not POST request, redirect to cart
    header("Location: ../../user/cart.php");
    exit();
}
