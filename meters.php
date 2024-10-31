<?php
session_start();
include 'database.php';

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); // Get the username safely
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meters Table</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>
<body class="container-fluid bg-dark text-white">
  <div class="container mt-5 bg-dark text-light p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">BlockForge Labs</h2>
      <div>
        <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
        <a href="logout.php" class="btn btn-outline-light">Logout</a>
      </div>
    </div>

    <h1 class="text-center">Meters Table</h1>

    <nav class="navbar navbar-expand-lg navbar-dark bg-black justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a href="index.php" class="nav-link text-light">Consumers Table</a></li>
        <li class="nav-item"><a href="address.php" class="nav-link text-light active">Addresses Table</a></li>
        <li class="nav-item"><a href="meters.php" class="nav-link text-light">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link text-light active btn btn-success">Add Consumer</a></li>
      </ul>
    </nav>

    <h2 class="text-center">Consumption Details by Consumer</h2>

    <?php
    // Query to get total consumption by each consumer
    $consumptionSql = "
        SELECT 
            c.account_number, 
            c.meter_id, 
            SUM(dcr.energy_consumed) AS daily_energy_consumed, 
            SUM(wcr.energy_consumed) AS weekly_energy_consumed, 
            SUM(mcr.energy_consumed) AS monthly_energy_consumed 
        FROM 
            consumers c 
        LEFT JOIN 
            dailyconsumptionrecords dcr ON c.meter_id = dcr.meter_id 
        LEFT JOIN 
            weeklyconsumptionrecords wcr ON c.meter_id = wcr.meter_id 
        LEFT JOIN 
            monthlyconsumptionrecords mcr ON c.meter_id = mcr.meter_id 
        GROUP BY 
            c.account_number, c.meter_id
    ";

    $result = mysqli_query($conn, $consumptionSql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='table table-dark table-striped mt-4'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>Account Number</th>";
        echo "<th>Meter ID</th>";
        echo "<th>Daily Consumption (kWh)</th>";
        echo "<th>Weekly Consumption (kWh)</th>";
        echo "<th>Monthly Consumption (kWh)</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['account_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['meter_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['daily_energy_consumed']) . "</td>";
            echo "<td>" . htmlspecialchars($row['weekly_energy_consumed']) . "</td>";
            echo "<td>" . htmlspecialchars($row['monthly_energy_consumed']) . "</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-info mt-4'>No consumption records found for any consumers.</div>";
    }

    mysqli_close($conn);
    ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
