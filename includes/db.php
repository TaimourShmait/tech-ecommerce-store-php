<?php
    $username = "root";
    $password = "";
    $db = "rps_db";
    $host = "localhost";

    $dns = "mysql:host=$host;dbname=$db";

    try {
        $pdo = new PDO($dns, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $ex) {
        die($ex->getMessage());
    }

?>