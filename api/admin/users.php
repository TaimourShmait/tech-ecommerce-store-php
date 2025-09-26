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
        $first_name = trim($_POST["first_name"]);
        $last_name = trim($_POST["last_name"]);
        $dob = $_POST["dob"];
        $phone_number = str_replace(' ', '', $_POST["phone_number"]);
        $email = trim($_POST["email"]);
        $password = trim($_POST["password"]);
        $confirm_password =  trim($_POST["confirm_password"]);

        if (empty($first_name) || empty($last_name) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL) || empty($password) || ($password !== $confirm_password) || (strlen($password) < 8)) {
            header("Location: ../../admin/pages/user_management/create_user.php");
            exit();
        }

        $sql_email = "SELECT * FROM users WHERE email = :email"; // MAKE INTO FUNCTION!
        $stmt_email = $pdo->prepare($sql_email);
        $stmt_email->bindParam(":email", $email);
        $stmt_email->execute();
        $user = $stmt_email->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            header("Location: ../../admin/pages/user_management/create_user.php");
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        try {
            $sql = "INSERT INTO users (first_name, last_name, dob, phone_number, email, password) VALUES (:first_name, :last_name, :dob, :phone_number, :email, :password)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":first_name", $first_name);
            $stmt->bindParam(":last_name", $last_name);
            $stmt->bindParam(":dob", $dob);
            $stmt->bindParam(":phone_number", $phone_number);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed_password);
            $stmt->execute();
            header("Location: ../../admin/pages/user_management/users.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    } else if ($action == "update") {
        if ($action == "update") {
            $id = $_POST["id"];
            if (!$id) {
                die("User ID is required");
            }

            if (isset($_POST["first_name"])) {
                $first_name = trim($_POST["first_name"]);
                if ($first_name === "") {
                    die("First name cannot be empty");
                }
                $sql = "UPDATE users SET first_name = :first_name WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":first_name" => $first_name, ":id" => $id]);
            }

            if (isset($_POST["last_name"])) {
                $last_name = trim($_POST["last_name"]);
                if ($last_name === "") {
                    die("Last name cannot be empty");
                }
                $sql = "UPDATE users SET last_name = :last_name WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":last_name" => $last_name, ":id" => $id]);
            }

            if (isset($_POST["dob"])) {
                $dob = $_POST["dob"];
                $sql = "UPDATE users SET dob = :dob WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":dob" => $dob, ":id" => $id]);
            }

            if (isset($_POST["phone_number"])) {
                $phone_number = str_replace(' ', '', $_POST["phone_number"]);
                $sql = "UPDATE users SET phone_number = :phone_number WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":phone_number" => $phone_number, ":id" => $id]);
            }

            if (isset($_POST["email"])) {
                $email = trim($_POST["email"]);
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    die("Invalid email.");
                }

                $sql_email = "SELECT id FROM users WHERE email = :email AND id != :id";
                $stmt_email = $pdo->prepare($sql_email);
                $stmt_email->execute([":email" => $email, ":id" => $id]);
                if ($stmt_email->fetch()) {
                    die("Email already in use.");
                }

                $sql = "UPDATE users SET email = :email WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([":email" => $email, ":id" => $id]);
            }

            header("Location: ../../admin/pages/user_management/users.php");
            exit();
        }
    } else if ($action == "delete") {
        $id = $_POST["id"];

        try {
            $sql = "DELETE FROM users WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();

            header("Location: ../../admin/pages/user_management/users.php");
            exit();
        } catch (PDOException $ex) {
            die($ex->getMessage());
        }
    }
}
