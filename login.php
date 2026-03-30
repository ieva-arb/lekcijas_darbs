<?php

session_start();
include '../includes/CONFIG.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = mysqli_real_escape_string($con, $_POST["username"]);
    $password = $_POST["password"];

    // Look for user in DB
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if user exists
    if ($result && $result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user["password"])) {

            // Save session
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];

            // Redirect to homepage
            header("Location: index.php");
            exit();

        } else {
            $error = "Wrong password.";
        }
    } else {
        $error = "User not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="css/style_login.css">
</head>
<body>

<div class="container">
    <h2>Log in</h2>

    <div class="topnav">
        <a href="index.php">Home</a>
        <a class="active" href="login.php">Login</a>
        <a href="register.php">Register</a>
    </div> 

    <?php if (isset($error)): ?>
        <div class="error">
            <p><?= htmlspecialchars($error) ?></p>
        </div>
    <?php endif; ?>
    
    <form method="post" action="login.php" novalidate>
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Login"> 
        <a href="index.php" class="button_link">Back</a>
    </form>
</div>

</body>
</html>
