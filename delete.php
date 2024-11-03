<?php
session_start();
include 'database.php';

// Check if consumerID is set and valid
if (isset($_GET['consumer_id']) && is_numeric($_GET['consumer_id'])) {
    $consumerID = (int)$_GET['consumer_id'];

    // SQL to delete from consumption_records
    $deleteConsumptionSQL = "DELETE FROM consumption_records WHERE consumer_id = ?";
    
    // Prepare the statement for deleting from consumption_records
    if ($stmt1 = mysqli_prepare($conn, $deleteConsumptionSQL)) {
        mysqli_stmt_bind_param($stmt1, "i", $consumerID);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);
    } else {
        echo "Error preparing statement for consumption_records: " . mysqli_error($conn);
        exit();
    }

    // SQL to delete from consumers
    $deleteConsumersSQL = "DELETE FROM consumers WHERE consumer_id = ?";

    // Prepare the statement for deleting from consumers
    if ($stmt2 = mysqli_prepare($conn, $deleteConsumersSQL)) {
        mysqli_stmt_bind_param($stmt2, "i", $consumerID);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);

        // Redirect to the index page after deletion
        header("Location: index.php?message=Consumer and associated records deleted successfully");
        exit();
    } else {
        echo "Error preparing statement for consumers: " . mysqli_error($conn);
        exit();
    }
} else {
    echo "Invalid request.";
}

// Close the connection
mysqli_close($conn);
?>
