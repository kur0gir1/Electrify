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
        <li class="nav-item"><a href="meters.php" class="nav-link text-light active">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-dark">Add Consumer</a></li>
      </ul>
    </nav>

    <h2 class="text-center">Electricity Meters</h2>

    <?php
    // Query to retrieve meter details along with the associated consumers
    $meterSql = "
    SELECT 
        em.meter_id, 
        em.manufacture_date, 
        em.installation_date, 
        c.consumer_id,
        c.account_number 
    FROM 
        electricitymeters em 
    LEFT JOIN 
        consumers c ON em.meter_id = c.meter_id
    ";

    $result = mysqli_query($conn, $meterSql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='table mt-4'>"; // Striped table
        echo "<thead>";
        echo "<tr>";
        echo "<th>Meter ID</th>";
        echo "<th>Manufacture Date</th>";
        echo "<th>Installation Date</th>";
        echo "<th>Account Number</th>";
        echo "<th>Action</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['meter_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['manufacture_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['installation_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['account_number']) . "</td>";
            echo "<td><button class='btn btn-info view-consumption' data-consumer-id='" . htmlspecialchars($row['consumer_id']) . "'>View Consumption Records</button></td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-info mt-4'>No meter records found.</div>";
    }

    mysqli_close($conn);
    ?>
  </div>

  <!-- Modal for Consumption Records -->
  <div class="modal fade" id="consumptionModal" tabindex="-1" aria-labelledby="consumptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="consumptionModalLabel" style="font-weight:600">Consumption Records</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <table class="table" id="consumptionTable">
            <thead>
              <tr>
                <th>Date</th>
                <th>Energy Consumed (kWh)</th>
                <th>Payment Period</th>
              </tr>
            </thead>
            <tbody>
              <!-- Consumption records will be populated here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
  <script>
    $(document).ready(function() {
        $('.view-consumption').on('click', function() {
            var consumerId = $(this).data('consumer-id');

            // Fetch consumption records for the selected consumer
            $.ajax({
                url: 'fetch_consumption_records.php', // URL of the PHP file to fetch data
                type: 'POST',
                data: { consumer_id: consumerId },
                success: function(response) {
                    // Populate the modal table with data
                    $('#consumptionTable tbody').html(response);
                    $('#consumptionModal').modal('show'); // Show the modal
                },
                error: function() {
                    alert('Error fetching consumption records.');
                }
            });
        });
    });
  </script>
</body>
</html>
