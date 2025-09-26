<?php
    session_start();
    require "../../includes/db.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $action = $_POST["action"]; // register or login

        if ($action == "register") {
            $first_name = trim($_POST["first_name"]);
            $last_name = trim($_POST["last_name"]);
            $dob = trim($_POST["dob"]);
            $phone_number = str_replace(' ', '', $_POST["phone_number"]);
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);
            $confirm_password = trim($_POST["confirm_password"]);

            if ((empty($first_name) || empty($last_name) || empty($email) || empty($password)) || strlen($password) < 8 || $password !== $confirm_password || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                header("Location: ../../user/Signup.php");
                exit();
            }

            try {
                $sql_email = "SELECT * FROM users WHERE email = :email";
                $stmt_email = $pdo->prepare($sql_email);
                $stmt_email->bindParam(":email", $email);
                $stmt_email->execute();
                $user = $stmt_email->fetch(PDO::FETCH_ASSOC);

                if ($user) {
                    header("Location: ../../user/Signup.php"); 
                    exit();
                } else {

                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                    $sql = "INSERT INTO users (first_name, last_name, dob, phone_number, email, password) VALUES (:first_name, :last_name, :dob, :phone_number, :email, :password)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(":first_name", $first_name);
                    $stmt->bindParam(":last_name", $last_name);
                    $stmt->bindParam(":dob", $dob);
                    $stmt->bindParam(":phone_number", $phone_number);
                    $stmt->bindParam(":email", $email);
                    $stmt->bindParam(":password", $hashed_password);
                    $stmt->execute();

                    $_SESSION["user_id"] = $pdo->lastInsertId();
                    $_SESSION["user_first_name"] = $first_name;
                    $_SESSION["user_full_name"] = $first_name . " " . $last_name;                
                    $_SESSION["user_email"] = $email;
                    $_SESSION["loggedIn"] = true;
                    
                    header("Location: ../../index.php");
                    exit();
                }
                

            } catch (PDOException $ex) {
                die($ex->getMessage());
            }
        } else {
            $email = trim($_POST["email"]);
            $password = trim($_POST["password"]);

            if (empty($email) || empty($password)) {
                header("Location: ../../user/login.php");
                exit();
            }

            try {
                $sql = "SELECT * FROM users WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(":email", $email);
                $stmt->execute();

                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if (!$user || !password_verify($password, $user["password"])) {
                    header("Location: ../../user/login.php?error=invalid");
                    exit();
                }

                $_SESSION["user_id"] = $user["id"];
                $_SESSION["user_first_name"] = $user["first_name"];
                $_SESSION["user_full_name"] = $user["first_name"] . " " . $user["last_name"];                
                $_SESSION["user_email"] = $email;
                $_SESSION["loggedIn"] = true;
                
                header("Location: ../../index.php");
                exit();

            } catch(PDOException $ex) {
                die($ex->getMessage());
            }
        }
    }

?>