<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $username = $_POST['username'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Check if the username already exists
        $query = "SELECT * FROM user_accounts WHERE username = :username";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $existing_user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_user) {
            $error_message = "Username already taken.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user into the database
            $insert_query = "INSERT INTO user_accounts (username, first_name, last_name, password) 
                             VALUES (:username, :first_name, :last_name, :password)";
            $insert_stmt = $pdo->prepare($insert_query);
            $insert_stmt->bindParam(':username', $username);
            $insert_stmt->bindParam(':first_name', $first_name);
            $insert_stmt->bindParam(':last_name', $last_name);
            $insert_stmt->bindParam(':password', $hashed_password);
            $insert_stmt->execute();

            // Get the last inserted user ID from the PDO instance
            $user_id = $pdo->lastInsertId();

            // Log the registration action
            logActivity($user_id, $username, 'register', 'User registered successfully.');

            // Redirect to login page after successful registration
            header("Location: login.php?message=Registration successful, please login.");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <header>BuildBright Engineering Recruitment Agency</header>

        <h1>Register New Account</h1>

        <?php if (isset($error_message)) : ?>
            <p class="error"><?= $error_message; ?></p>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <label for="username">Username:</label>
            <input type="text" name="username" required><br>

            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required><br>

            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required><br>

            <label for="password">Password:</label>
            <input type="password" name="password" required><br>

            <label for="confirm_password">Confirm Password:</label>
            <input type="password" name="confirm_password" required><br>

            <input type="submit" value="Register">
        </form>

            <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>

</html>