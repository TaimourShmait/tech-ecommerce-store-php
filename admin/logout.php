<?php
    session_start();

    if (!isset($_SESSION["admin_id"])) {
        header("Location: ../index.php");
        exit();
    }

    $_SESSION = [];
    session_destroy();
    header("Location: ../index.php");
    exit();
?>