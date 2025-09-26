<?php
session_start();
require "../../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $password = trim($_POST["password"]);

    if (empty($username) || empty($password)) {
        header("Location: ../../admin/pages/login.php");
        exit();
    }

    try {
        $sql = "SELECT * FROM admins WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$admin || !password_verify($password, $admin["password"])) {
            header("Location: ../../admin/pages/login.php?error=invalid");
            exit();
        }

        $_SESSION["admin_id"] = $admin["id"];
        $_SESSION["admin_username"] = $admin["username"];
        $_SESSION["loggedIn"] = true;

        header("Location: ../../admin/index.php");
        exit();
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }
}
