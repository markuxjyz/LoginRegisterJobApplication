<?php
session_start();
require_once 'core/dbConfig.php'; // Ensure the database connection is included
require_once 'core/models.php'; // Include models.php for logActivity()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists in the database
    $query = "SELECT * FROM user_accounts WHERE username = :username";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        // If credentials are correct, start the session and store user data
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['first_name'] = $user['first_name'];
        $_SESSION['last_name'] = $user['last_name'];

        // Log the login action
        logActivity($user['user_id'], $user['username'], 'login', 'User logged in successfully.');

        // Redirect to the main page (index.php)
        header("Location: index.php");
        exit();
    } else {
        $error_message = "Invalid username or password.";
        // Log the failed login attempt
        logActivity(0, $username, 'login', 'Failed login attempt.');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>BuildBright Engineering Recruitment Agency </header>

        <?php if (isset($error_message)) : ?>
            <p class="message error"><?= $error_message; ?></p>
        <?php endif; ?>

        <h1>Log-in</h1>

        <form action="login.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <input type="submit" value="Login">
        </form>
            <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>

</html>