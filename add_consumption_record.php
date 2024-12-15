<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consumerId = $_POST['consumer_id'];
    $energyConsumed = $_POST['energy_consumed'];
    $date = $_POST['date']; 
    $stmt = $conn->prepare("INSERT INTO consumption_records (consumer_id, energy_consumed, date) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $consumerId, $energyConsumed, $date);

    if ($stmt->execute()) {
        $sql = "SELECT date, energy_consumed FROM consumption_records WHERE consumer_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $consumerId);
        $stmt->execute();
        $result = $stmt->get_result();

        $output = "";
        while ($row = $result->fetch_assoc()) {
            $output .= "<tr>";
            $output .= "<td>" . htmlspecialchars($row['date']) . "</td>";
            $output .= "<td>" . htmlspecialchars($row['energy_consumed']) . " kWh</td>";
            $output .= "</tr>";
        }
        echo $output; 
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
