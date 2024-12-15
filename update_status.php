<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve the posted values
    $record_id = $_POST['record_id'];
    $new_status = $_POST['status'];

    // Prepare the SQL query to update the status
    $query = "UPDATE consumption_records SET status = ? WHERE record_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $new_status, $record_id);

    if ($stmt->execute()) {
        echo "Status updated successfully.";
    } else {
        echo "Error updating status: " . $stmt->error;
    }
    $stmt->close();
    $conn->close();
}
?>
