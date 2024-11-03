<?php
session_start();
include 'database.php'; // Include your database connection file

if (isset($_POST['consumer_id'])) {
    $consumerId = intval($_POST['consumer_id']);

    // Query to fetch consumption records for the specified consumer
    $sql = "SELECT date, energy_consumed FROM consumption_records WHERE consumer_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $consumerId); // Bind the consumer ID parameter
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if any records were found
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Output each row as a table row
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['energy_consumed']) . "</td>"; // Daily Energy Consumed
                echo "<td></td>"; // Placeholder for Weekly Consumption
                echo "<td></td>"; // Placeholder for Monthly Consumption
                echo "<td></td>"; // Placeholder for Price to Pay
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='5' class='text-center'>No consumption records found.</td></tr>";
        }

        $stmt->close();
    } else {
        echo "<tr><td colspan='5' class='text-center'>Error preparing the SQL statement.</td></tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center'>Invalid consumer ID.</td></tr>";
}

$conn->close(); // Close the database connection
?>
