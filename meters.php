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
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'DM Sans', sans-serif;
      background-color: black; /* Black background for the entire body */
      color: #FFD700; /* Yellow text color */
    }
    .navbar {
      background-color: black; /* Black background for navbar */
    }
    .navbar-nav .nav-link {
      color: #FFD700; /* Yellow color for nav links */
    }

    .navbar-nav .nav-link.active {
          font-weight: bold;
      }

    .navbar-nav .nav-link:hover {
      color: white; /* White color on hover */
    }
    .table {
      background-color: #000; /* Black background for the table */
      color: white; /* White text color in the table */
    }

    .table th, .table td {
      vertical-align: middle; /* Center align table cells */
    }
    .btn-info {
      background-color: #FFD700; /* Yellow background for info button */
      color: black; /* Black text for button */
      outline: none;
    }
    .btn-info:hover {
      background-color: #e0a800; /* Darker yellow on hover */
      color: white; /* White text on hover */
    }
    .modal-content {
      background-color: #000; /* Black background for modal */
      color: white; /* White text for modal */
    }
    .modal-header, .modal-body {
      border-color: #FFD700; /* Yellow border for modal */
    }
    .modal-header .btn-close {
      filter: brightness(0) invert(1); /* Change close button color to white */
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0" style="font-weight: 600">Electrify</h2>
      <div>
        <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
        <a href="login.php" class="btn btn-outline-light">Logout</a>
      </div>
    </div>

    <h1 class="text-center">Meters Table</h1>

    <nav class="navbar navbar-expand-lg navbar-dark justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a href="index.php" class="nav-link">Consumers Table</a></li>
        <li class="nav-item"><a href="address.php" class="nav-link">Addresses Table</a></li>
        <li class="nav-item"><a href="meters.php" class="nav-link text-light active">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-dark">Add Consumer</a></li>
      </ul>
    </nav>

    <h2 class="text-center">Consumption Details by Consumer</h2>

    <?php
    // Query to get total consumption and fees for each consumer
    $consumptionSql = "
    SELECT 
        c.account_number, 
        c.meter_id, 
        SUM(dcr.energy_consumed) AS daily_energy_consumed, 
        SUM(wcr.energy_consumed) AS weekly_energy_consumed, 
        SUM(mcr.energy_consumed) AS monthly_energy_consumed, 
        mp.installation_fee, 
        mp.taxes, 
        mp.miscellaneous_fees 
    FROM 
        consumers c 
    LEFT JOIN 
        dailyconsumptionrecords dcr ON c.meter_id = dcr.meter_id 
    LEFT JOIN 
        weeklyconsumptionrecords wcr ON c.meter_id = wcr.meter_id 
    LEFT JOIN 
        monthlyconsumptionrecords mcr ON c.meter_id = mcr.meter_id 
    LEFT JOIN 
        monthlypay mp ON c.consumer_id = mp.consumer_id
    GROUP BY 
        c.account_number, c.meter_id
";

    $result = mysqli_query($conn, $consumptionSql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='table mt-4'>"; // Striped table
        echo "<thead>";
        echo "<tr>";
        echo "<th>Account Number</th>";
        echo "<th>Meter ID</th>";
        echo "<th>Daily Consumption (kWh)</th>";
        echo "<th>Weekly Consumption (kWh)</th>";
        echo "<th>Monthly Consumption (kWh)</th>";
        echo "<th>Action</th>";
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
            echo "<td><button class='btn btn-info view-monthly-pay' data-monthly-pay='" . htmlspecialchars($row['monthly_energy_consumed']) . "' data-installation-fee='" . htmlspecialchars($row['installation_fee']) . "' data-taxes='" . htmlspecialchars($row['taxes']) . "' data-misc-fees='" . htmlspecialchars($row['miscellaneous_fees']) . "'>View Monthly Pay</button></td>";
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

  <!-- Modal for Monthly Pay Details -->
  <div class="modal fade" id="monthlyPayModal" tabindex="-1" aria-labelledby="monthlyPayModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="monthlyPayModalLabel" style="font-weight:600">Monthly Pay Details</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p><strong>Monthly Consumption (kWh):</strong> <span id="monthlyConsumption"></span></p>
          <p><strong>Base Monthly Pay (PHP):</strong> <span id="baseMonthlyPay"></span></p>
          <p><strong>Installation Fee (PHP):</strong> <span id="installationFee"></span></p>
          <p><strong>Taxes (PHP):</strong> <span id="taxes"></span></p>
          <p><strong>Miscellaneous Fees (PHP):</strong> <span id="miscFees"></span></p>
          <hr>
          <p><strong>Total Monthly Pay (PHP):</strong> <span id="totalMonthlyPay"></span></p>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
        $('.view-monthly-pay').on('click', function() {
            var monthlyConsumption = $(this).data('monthly-pay');
            var installationFee = parseFloat($(this).data('installation-fee'));
            var taxes = parseFloat($(this).data('taxes'));
            var miscFees = parseFloat($(this).data('misc-fees'));
            
            var baseMonthlyPay = (monthlyConsumption * 8).toFixed(2); // Change rate as needed, e.g., 8 PHP per kWh
            var totalMonthlyPay = (parseFloat(baseMonthlyPay) + installationFee + taxes + miscFees).toFixed(2);

            $('#monthlyConsumption').text(monthlyConsumption);
            $('#baseMonthlyPay').text('₱' + baseMonthlyPay);
            $('#installationFee').text('₱' + installationFee.toFixed(2));
            $('#taxes').text('₱' + taxes.toFixed(2));
            $('#miscFees').text('₱' + miscFees.toFixed(2));
            $('#totalMonthlyPay').text('₱' + totalMonthlyPay);
            
            $('#monthlyPayModal').modal('show');
        });
    });
  </script>
</body>
</html>
