<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Applicant</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <?php
    session_start();
    require_once 'core/dbConfig.php';
    require_once 'core/models.php';

    // Check if an ID is provided in the URL
    if (isset($_GET['id'])) {
        $id = htmlspecialchars($_GET['id']); // Sanitize the ID for security

        // Fetch applicant details from the database
        $query = $pdo->prepare("SELECT * FROM engineer_job_applications WHERE id = :id");
        $query->execute(['id' => $id]);
        $applicant = $query->fetch(PDO::FETCH_ASSOC);

        // If the user has confirmed the deletion
        if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes') {

            // First, delete related activity logs if needed
            $deleteLogsQuery = "DELETE FROM activity_logs WHERE log_id = :id";
            $deleteLogsStmt = $pdo->prepare($deleteLogsQuery);
            $deleteLogsStmt->bindParam(':id', $id);
            $deleteLogsStmt->execute();

            // Now delete the applicant
            $deleteApplicantQuery = "DELETE FROM engineer_job_applications WHERE id = :id";
            $deleteApplicantStmt = $pdo->prepare($deleteApplicantQuery);
            $deleteApplicantStmt->bindParam(':id', $id);

            if ($deleteApplicantStmt->execute()) {
                // Log the activity of the deletion
                logActivity($_SESSION['user_id'], $_SESSION['username'], 'delete', 'Deleted applicant with ID: ' . $id);

                // Set a success message
                $_SESSION['message'] = "Applicant deleted successfully.";

                // Redirect to the main page
                header('Location: index.php');
                exit();
            } else {
                // Set an error message
                $_SESSION['message'] = "Error deleting applicant.";
                header('Location: index.php');
                exit();
            }
        }

            // Display confirmation prompt with applicant details
            ?>
            <div class="conformation-container">
                <h1>Are you sure you want to delete the following applicant?</h1>
                <div class="applicant-details">
                    <p><strong>First Name:</strong> <?php echo htmlspecialchars($applicant['first_name']); ?></p>
                    <p><strong>Last Name:</strong> <?php echo htmlspecialchars($applicant['last_name']); ?></p>
                    <p><strong>Email:</strong> <?php echo htmlspecialchars($applicant['email']); ?></p>
                    <p><strong>Specialization:</strong> <?php echo htmlspecialchars($applicant['specialization']); ?></p>
                    <p><strong>Years of Experience:</strong> <?php echo htmlspecialchars($applicant['years_of_experience']); ?></p>
                    <p><strong>Current Employer:</strong> <?php echo htmlspecialchars($applicant['current_employer']); ?></p>
                    <p><strong>Phone Number:</strong> <?php echo htmlspecialchars($applicant['phone_number']); ?></p>
                    <p><strong>Application Status:</strong> <?php echo htmlspecialchars($applicant['application_status']); ?></p>
                    <p><strong>Date Applied:</strong> <?php echo htmlspecialchars($applicant['date_applied']); ?></p>
                </div>
                <div class="action-buttons">
                    <a href="index.php" class="btn clear-search">Back</a>
                    <a href="delete.php?id=<?php echo $id; ?>&confirm=yes" class="btn clear-search">Confirm</a>
                </div>
            </div>
            <?php
    } else {
         // If no ID is passed, show an error
        echo '<h1>No applicant ID provided.</h1>';
        echo '<a href="index.php">Back to Applicants</a>';
        exit();
}
?>
</body>
</html>
