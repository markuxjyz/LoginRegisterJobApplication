<?php
function getAllApplicants($pdo) {
    try {
        $query = $pdo->query("SELECT * FROM engineer_job_applications ORDER BY date_applied DESC");
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        return [
            'message' => 'Applicants retrieved successfully from database.',
            'statusCode' => 200,
            'querySet' => $result
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to retrieve applicants: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function searchApplicants($pdo, $search) {
    try {
        $sql = "SELECT * FROM engineer_job_applications WHERE 
                first_name LIKE :search OR 
                last_name LIKE :search OR 
                email LIKE :search OR 
                specialization LIKE :search OR 
                years_of_experience LIKE :search OR 
                current_employer LIKE :search OR 
                phone_number LIKE :search OR
                application_status LIKE :search";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':search' => '%' . $search . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return [
            'message' => 'Search results retrieved successfully.',
            'statusCode' => 200,
            'querySet' => $result
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Search failed: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function insertApplicant($pdo, $data) {
    try {
        $sql = "INSERT INTO engineer_job_applications (first_name, last_name, email, specialization, years_of_experience, current_employer, phone_number, application_status) 
                VALUES (:first_name, :last_name, :email, :specialization, :years_of_experience, :current_employer, :phone_number, :application_status)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
        return [
            'message' => 'Applicant inserted successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to insert applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function getApplicantByID($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM engineer_job_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'message' => $result ? 'Applicant retrieved successfully.' : 'Applicant not found.',
            'statusCode' => $result ? 200 : 400,
            'querySet' => $result
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to retrieve applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function updateApplicant($pdo, $data, $id) {
    try {
        $sql = "UPDATE engineer_job_applications SET 
                first_name = :first_name, 
                last_name = :last_name, 
                email = :email, 
                specialization = :specialization, 
                years_of_experience = :years_of_experience, 
                current_employer = :current_employer, 
                phone_number = :phone_number,  
                application_status = :application_status
                WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array_merge($data, ['id' => $id]));
        return [
            'message' => 'Applicant updated successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to update applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

function deleteApplicant($pdo, $id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM engineer_job_applications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return [
            'message' => 'Applicant deleted successfully.',
            'statusCode' => 200
        ];
    } catch (PDOException $e) {
        return [
            'message' => 'Failed to delete applicant: ' . $e->getMessage(),
            'statusCode' => 400
        ];
    }
}

// models.php
function logActivity($user_id, $username, $action, $action_details)
{
    global $pdo; // Ensure the database connection is accessible

    // If user_id is 0 (for failed login), set it to NULL
    if ($user_id == 0) {
        $user_id = NULL;  // Or you can leave it as 0, depending on your table structure.
    }

    // Prepare the query to log the activity
    $query = "INSERT INTO activity_logs (user_id, username, action, action_details) 
              VALUES (:user_id, :username, :action, :action_details)";
    $stmt = $pdo->prepare($query);

    // Bind parameters
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT); // Make sure it's treated as an integer
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':action', $action);
    $stmt->bindParam(':action_details', $action_details);

    // Execute the statement to insert the log entry
    $stmt->execute();
}