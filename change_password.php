<?php
session_start();
include 'database.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

// Check if form is submitted and required fields are present
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['current_password'], $_POST['new_password'], $_POST['confirm_password'])) {
    $email = $_SESSION['email'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Check if new password matches confirm password
    if ($new_password !== $confirm_password) {
        echo "New passwords do not match.";
        exit();
    }

    // Fetch the current password from the database
    $sql = "SELECT password FROM consumers WHERE email = ?";
    $stmt = mysqli_stmt_init($conn);
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            // Check if the current password matches the one in the database
            if ($current_password === $row['password']) {
                // Prepare the SQL query to update the password
                $sql_update = "UPDATE consumers SET password = ? WHERE email = ?";
                $stmt_update = mysqli_stmt_init($conn);
                if (mysqli_stmt_prepare($stmt_update, $sql_update)) {
                    // Bind the new password and email to the query
                    mysqli_stmt_bind_param($stmt_update, "ss", $new_password, $email);

                    // Execute the update query
                    if (mysqli_stmt_execute($stmt_update)) {
                        echo "Password successfully updated.";
                        header("Refresh: 1; url=consumer.php");
                    } else {
                        echo "Error updating password.";
                    }
                } else {
                    echo "Error preparing SQL query for password update.";
                }
            } else {
                echo "Current password is incorrect.";
            }
        } else {
            echo "User not found.";
        }
    } else {
        echo "Database query failed.";
    }

    mysqli_close($conn);
} else {
    echo "Please fill in all the fields.";
}
?>
