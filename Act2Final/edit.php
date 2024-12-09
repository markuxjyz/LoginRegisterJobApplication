<?php
require_once 'core/dbConfig.php';
require_once 'core/models.php';

session_start(); // Ensure session data is available

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Retrieve logged-in user details from the session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Initialize $applicant and $applicant_id
$applicant = null;
$applicant_id = null;

// Handle GET request to fetch applicant details
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $applicant_id = $_GET['id'];

    // Fetch the applicant details from the database
    $query = "SELECT * FROM engineer_job_applications WHERE id = :applicant_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':applicant_id', $applicant_id, PDO::PARAM_INT);
    $stmt->execute();
    $applicant = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$applicant) {
        echo "Invalid applicant ID.";
        exit();
    }

    // Store the current values for comparison later
    $old_first_name = $applicant['first_name'];
    $old_last_name = $applicant['last_name'];
    $old_email = $applicant['email'];
    $old_specialization = $applicant['specialization'];
    $old_years_of_experience = $applicant['years_of_experience'];
    $old_current_employer = $applicant['current_employer'];
    $old_phone_number = $applicant['phone_number'];
    $old_application_status = $applicant['application_status'];
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    // Handle POST request to update applicant details
    $applicant_id = $_POST['id'];

    // Get the updated data from the form
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $specialization = $_POST['specialization'];
    $years_of_experience = $_POST['years_of_experience'];
    $current_employer = $_POST['current_employer'];
    $phone_number = $_POST['phone_number'];
    $application_status = $_POST['application_status'];

    // Update the applicant in the database
    $update_query = "UPDATE engineer_job_applications SET first_name = :first_name, last_name = :last_name, 
                     email = :email, specialization = :specialization, years_of_experience = :years_of_experience, 
                     current_employer = :current_employer, phone_number = :phone_number, application_status = :application_status WHERE id = :id";
    $update_stmt = $pdo->prepare($update_query);
    $update_stmt->bindParam(':first_name', $first_name);
    $update_stmt->bindParam(':last_name', $last_name);
    $update_stmt->bindParam(':email', $email);
    $update_stmt->bindParam(':specialization', $specialization);
    $update_stmt->bindParam(':years_of_experience', $years_of_experience);
    $update_stmt->bindParam(':current_employer', $current_employer);
    $update_stmt->bindParam(':phone_number', $phone_number);
    $update_stmt->bindParam(':application_status', $application_status);
    $update_stmt->bindParam(':id', $applicant_id, PDO::PARAM_INT);
    $update_stmt->execute();

    // Log the changes made to the applicant details
    $action_details = "Updated applicant ID #$applicant_id with new details: ";

    $changes = [];
    if ($old_first_name !== $first_name) $changes[] = "First Name: $first_name";
    if ($old_last_name !== $last_name) $changes[] = "Last Name: $last_name";
    if ($old_email !== $email) $changes[] = "Email: $email";
    if ($old_specialization !== $specialization) $changes[] = "Specialization: $specialization";
    if ($old_years_of_experience !== $years_of_experience) $changes[] = "Years of Experience: $years_of_experience";
    if ($old_current_employer !== $current_employer) $changes[] = "Recent Company: $current_employer";
    if ($old_phone_number !== $phone_number) $changes[] = "Phone Number: $phone_number";
    if ($old_application_status !== $application_status) $changes[] = "Application Status: $application_status";

    $action_details .= implode(", ", $changes);

    logActivity($user_id, $username, 'edit', $action_details);

    // Redirect back to the index page after updating
    header("Location: index.php?message=Applicant updated successfully.");
    exit();
} else {
    echo "No applicant ID provided.";
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Applicant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Edit Applicant</h1>
    <?php if (!empty($_SESSION['message'])) { echo "<p class='message'>{$_SESSION['message']}</p>"; unset($_SESSION['message']); } ?>
    <form action="edit.php" method="POST">
        <input type="hidden" name="id" value="<?php echo $applicant['id']; ?>">
        <p>
            <label for="first_name">First Name:</label>
            <input type="text" name="first_name" value="<?php echo $applicant['first_name']; ?>" required>
        </p>
        <p>
            <label for="last_name">Last Name:</label>
            <input type="text" name="last_name" value="<?php echo $applicant['last_name']; ?>" required>
        </p>
        <p>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo $applicant['email']; ?>" required>
        </p>
        <p>
            <label for="specialization">Specialization:</label>
            <input type="text" name="specialization" value="<?php echo $applicant['specialization']; ?>" required>
        </p>
        <p>
            <label for="years_of_experience"> Years of Experience:</label>
            <input type="number" name="years_of_experience" value="<?php echo $applicant['years_of_experience']; ?>" required>
        </p>
        <p>
            <label for="current_employer">Recent Company:</label>
            <input type="text" name="current_employer" value="<?php echo $applicant['current_employer']; ?>" required>
        </p>
        <p>
            <label for="phone_number">Phone Number:</label>
            <input type="text" name="phone_number" value="<?php echo $applicant['phone_number']; ?>" required>
        </p>
        <p>
            <label for="application_status">Application Status:</label>
            <select name="application_status" required>
                <option value="Pending" <?php echo ($applicant['application_status'] == 'Pending') ? 'selected' : ''; ?>>Pending</option>
                <option value="Reviewed" <?php echo ($applicant['application_status'] == 'Reviewed') ? 'selected' : ''; ?>>Reviewed</option>
                <option value="Shortlisted" <?php echo ($applicant['application_status'] == 'Shortlisted') ? 'selected' : ''; ?>>Shortlisted</option>
                <option value="Rejected" <?php echo ($applicant['application_status'] == 'Rejected') ? 'selected' : ''; ?>>Rejected</option>
            </select>
        </p>
        <button type="submit">Update Applicant</button>
    </form>
</body>
</html>
