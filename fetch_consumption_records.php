<?php
include 'database.php';

if (isset($_POST['consumer_id'])) {
    $consumer_id = intval($_POST['consumer_id']);
    
    // Query to retrieve consumption records for the specified consumer
    $consumptionSql = "
    SELECT 
        date, 
        energy_consumed, 
        payment_period 
    FROM 
        consumption_records 
    WHERE 
        consumer_id = ?
    ";
    
    $stmt = $conn->prepare($consumptionSql);
    $stmt->bind_param('i', $consumer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['energy_consumed']) . "</td>";
            echo "<td>" . htmlspecialchars($row['payment_period']) . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='3' class='text-center'>No records found.</td></tr>";
    }

    $stmt->close();
    mysqli_close($conn);
}
?>
