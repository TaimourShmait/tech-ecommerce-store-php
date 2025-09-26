<?php
session_start();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Real Pixel Store</title>
    <link rel="stylesheet" href="css/loginStyles.css" />

</head>

<body>
    <main>
        <div class="wrapper">
            <form method="POST" action="../api/admin/admin_auth.php">
                <h1>Admin Login</h1>
                <div class="input-box">
                    <input type="text" placeholder="username" required name="username">
                </div>
                <div class="input-box">
                    <input type="password" placeholder="password" required name="password">
                </div>
                <button type="submit" id="login-btn" class="btn">Login</button>
                <div class="register-link">
                </div>
            </form>
        </div>
    </main>
    <footer>
    </footer>

</body>

</html>