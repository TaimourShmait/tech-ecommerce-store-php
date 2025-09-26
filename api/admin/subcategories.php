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
        $category_id = trim($_POST["category"]);

        if (empty($name) || empty($category_id)) {
            header("Location: ../../admin/pages/subcategory_management/create_subcategory.php");
            exit();
        }

        try {
            $sql = "INSERT INTO subcategories (name) VALUES (:name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name", $name);
            $stmt->execute();

            $id = $pdo->lastInsertId();

            $sql_category = "INSERT INTO category_subcategories (category_id, subcategory_id) VALUES (:category_id, :id)";
            $stmt_category = $pdo->prepare($sql_category);
            $stmt_category->bindParam(":category_id", $category_id);
            $stmt_category->bindParam(":id", $id);
            $stmt_category->execute();

            header("Location: ../../admin/pages/subcategory_management/subcategories.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "update") {
        $id = $_POST["id"];

        if (isset($_POST["name"])) {
            $name = trim($_POST["name"]);

            if (empty($name)) {
                header("Location: ../../admin/pages/subcategory_management/subcategories.php");
                exit();
            }

            $sql = "UPDATE subcategories SET name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        if (isset($_POST["category"])) {
            $category_id = $_POST["category"];

            $sql = "UPDATE category_subcategories SET category_id = :category_id WHERE subcategory_id =:id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":category_id", $category_id);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
        }

        header("Location: ../../admin/pages/subcategory_management/subcategories.php");
        exit();
    } else if ($action == "delete") {
        $id = $_POST["id"];

        try {
            $sql = "DELETE FROM subcategories WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            header("Location: ../../admin/pages/subcategory_management/subcategories.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
