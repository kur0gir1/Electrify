<?php
session_start();
include 'database.php';

if (isset($_POST['consumer_id'])) {
    $consumerId = intval($_POST['consumer_id']);

    // Modify the SQL to order by date
    $sql = "SELECT date, energy_consumed FROM consumption_records WHERE consumer_id = ? ORDER BY date ASC";  // Or use DESC for reverse order

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $consumerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($row['date']) . "</td>";
                echo "<td>" . htmlspecialchars($row['energy_consumed']) . "</td>"; 
                echo "<td></td>";
                echo "<td></td>";
                echo "<td></td>";
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

$conn->close(); 
?>
