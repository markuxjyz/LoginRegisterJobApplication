<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'core/dbConfig.php';
require_once 'core/models.php';

// Handle the form submission for inserting a new applicant
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect the form data
    $data = [
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'specialization' => $_POST['specialization'],
        'years_of_experience' => $_POST['years_of_experience'],
        'current_employer' => $_POST['current_employer'],
        'phone_number' => $_POST['phone_number'],
        'application_status' => $_POST['application_status']
    ];

    // Check if all fields are filled out
    foreach ($data as $key => $value) {
        if (empty($value)) {
            $error_message = "Please fill out all fields.";
            break;
        }
    }

    if (!isset($error_message)) {
        // Insert the new applicant using the insertApplicant function
        $insertResult = insertApplicant($pdo, $data);

        // Check if the insertion was successful
        if ($insertResult['statusCode'] == 200) {
            // Log the activity
            if (isset($_SESSION['user_id'], $_SESSION['username'])) {
                $user_id = $_SESSION['user_id'];
                $username = $_SESSION['username'];
                $action = "Insert Applicant";
                $action_details = "Inserted a new applicant: " . json_encode($data);

                logActivity($user_id, $username, $action, $action_details);
            }

            // Set a session message and redirect
            $_SESSION['message'] = $insertResult['message'];
            header("Location: index.php"); // Redirect to index.php after successful insertion
            exit();
        } else {
            $_SESSION['message'] = $insertResult['message'];
            $_SESSION['status'] = $insertResult['statusCode'];
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Applicant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Insert New Applicant</h1>
    <form action="insert.php" method="POST">
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" required>
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" required>
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" required>
        </p>
        <p>
            <label for="specialization">Specialization:</label>
            <input type="text" name="specialization" required>
        </p>
        <p>
            <label for="years_of_experience">Years of Experience:</label>
            <input type="number" name="years_of_experience" required>
        </p>
        <p>
            <label for="current_employer">Recent Company:</label>
            <input type="text" name="current_employer" required>
        </p>
        <p>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" required>
        </p>
        <p>
            <p><label for="application_status">Application Status</label>
                <select name="application_status" required>
                    <option value="Pending">Pending</option>
                    <option value="Reviewed">Reviewed</option>
                    <option value="Shortlisted">Shortlisted</option>
                    <option value="Rejected">Rejected</option>
                </select>
            </p>
        </p>
        <button type="submit">Add Applicant</button>
    </form>
</body>
</html>
