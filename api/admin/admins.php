<?php

session_start();

if (!isset($_SESSION["admin_id"])) {
    header("Location: ../../index.php");
    exit();
}

require "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") { # CHECK IF USERNAME IS TAKEN!!

    $action = $_POST["action"];

    if ($action == "create") {
        $username = trim($_POST["username"]);
        $password = trim($_POST["password"]);
        $confirm_password = trim($_POST["confirm_password"]);

        if ((empty($username) || empty($password)) || (strlen($password) < 8) || ($password !== $confirm_password)) {
            header("Location: ../../admin/pages/admin_management/create_admin.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO admins (username, password) VALUES (:username, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->execute();

            header("Location: ../../admin/pages/admin_management/admins.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "update") { # CHECK IF USERNAME IS TAKEN!!

        $id = $_POST["id"];

        $username = $_POST["username"];

        if (empty($username)) {
            header("Location: ../../admin/pages/admin_management/edit_admin.php");
            exit();
        }

        try {
            $sql = "UPDATE admins SET username = :username WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            header("Location: ../../admin/pages/admin_management/admins.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "delete") {
        $id = $_POST["id"];

        try {
            $sql = "DELETE FROM admins WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            header("Location: ../../admin/pages/admin_management/admins.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
