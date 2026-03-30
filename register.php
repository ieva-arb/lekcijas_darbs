<?php
session_start();
include '../includes/FUNCTIONS.php';
include '../includes/CONFIG.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = test_input($_POST["username"] ?? '');
    $email = test_input($_POST["email"] ?? '');
    $password = $_POST["password"] ?? '';
    $confirmpassword = $_POST["checkpassword"] ?? '';

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Username validation
    if (empty($username)) {
        $errors[] = "Username is required.";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters long.";
    }

    // Email validation
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!preg_match("/^(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}$/", $password)) {
        $errors[] = "Password must include at least 8 characters, one uppercase letter, and one number.";
    } elseif ($password !== $confirmpassword) {
        $errors[] = "Passwords do not match.";
    }

    // If no errors, check DB
    if (empty($errors)) {

        // Check if username already exists
        $stmt = $con->prepare("SELECT user_id FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = "This username is already taken.";
        }
        $stmt->close();

        // Insert new user
        if (empty($errors)) {
            $stmt = $con->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $email, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION["username"] = $username;
                $_SESSION["email"] = $email;

                header("Location: index.php");
                exit();
            } else {
                $errors[] = "Database error: " . $stmt->error;
            }

            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register</title>

    <link rel="stylesheet" href="css/style_register.css">
</head>
<body>
    <div class="container">
        <h2>Register</h2> 

        <div class="topnav">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
            <a class="active" href="register.php">Register</a>
        </div>

        <form method="post" action="" novalidate>
            <input type="text" name="username" placeholder="Username" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
            <input type="email" name="email" placeholder="E-mail" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="password" name="checkpassword" placeholder="Confirm password" required>
            <input type="submit" value="Register">
        </form>

        <?php if (!empty($errors)): ?>
            <div class="error">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <p>Already have an account? <a href="login.php">Login</a></p>
    </div>
</body>
</html>
