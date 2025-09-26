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

        if (empty($name)) {
            header("Location: ../../admin/pages/category_management/create_category.php");
            exit();
        }

        try {
            $sql = "INSERT INTO categories (name) VALUES (:name)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name", $name);
            $stmt->execute();

            header("Location: ../../admin/pages/category_management/categories.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "update") {
        $id = $_POST["id"];
        $name = trim($_POST["name"]);

        if (empty($name)) {
            header("Location: ../../admin/pages/category_management/categories.php");
            exit();
        }

        try {
            $sql = "UPDATE categories SET name = :name WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":name", $name);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            header("Location: ../../admin/pages/category_management/categories.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "delete") {
        $id = $_POST["id"];

        try {
            $sql = "DELETE FROM categories WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            header("Location: ../../admin/pages/category_management/categories.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
