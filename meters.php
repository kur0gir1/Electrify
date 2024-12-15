<?php
session_start();
include 'database.php';

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); 
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
      background-color: black;
      color: #FFD700;
    }
    .navbar {
      background-color: black;
    }
    .navbar-nav .nav-link {
      color: #FFD700; 
    }
    .navbar-nav .nav-link.active {
      font-weight: bold;
    }
    .navbar-nav .nav-link:hover {
      color: white;
    }
    .table {
      background-color: #000;
      color: white;
    }
    .table th, .table td {
      vertical-align: middle;
    }

    /* Buttons Styling */
    .btn-info.view-consumption {
        background-color: #FFD700; /* Bright Yellow */
        color: black;
        border: 1px solid #FFD700;
    }

    .btn-info.view-consumption:hover {
        background-color: #e0a800; /* Slightly darker yellow */
        color: white;
        border-color: #e0a800;
    }

    .btn-info.print-monthly-pay {
        background-color: #FFD700; /* Bright Yellow */
        color: black;
        border: 1px solid #FFD700;
    }

    .btn-info.print-monthly-pay:hover {
        background-color: #e0a800; /* Slightly darker yellow */
        color: white;
        border-color: #e0a800;
    }

    /* Table Row Styling */
    .table tbody tr:hover {
        background-color: #333; /* Dark gray */
        transition: background-color 0.3s ease;
    }

    .modal-content {
      background-color: #000; 
      color: white; 
    }
    .modal-header, .modal-body {
      border-color: #FFD700; 
    }
    .modal-header .btn-close {
      filter: brightness(0) invert(1);
    }
  </style>
</head>
<body>
<div class="container mt-5">
  <div class="row mb-4 align-items-center">
    <div class="col-md-6">
      <h2 class="mb-0" style="font-weight: 600">Electrify</h2>
    </div>
    <div class="col-md-6 text-end">
      <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
      <a href="login.php" class="btn btn-outline-light">Logout</a>
    </div>
  </div>

  <h1 class="text-center mb-4">Meters Table</h1>

  <nav class="navbar navbar-expand-lg navbar-dark justify-content-center mb-4">
    <ul class="navbar-nav">
      <li class="nav-item"><a href="index.php" class="nav-link">Consumers Table</a></li>
      <li class="nav-item"><a href="meters.php" class="nav-link active">Meters Table</a></li>
      <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-dark">Add Consumer</a></li>
    </ul>
  </nav>

  <h2 class="text-center mb-4">Electricity Meters</h2>

  <?php
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
      consumers c ON em.consumer_id = c.consumer_id
  ";

  $result = mysqli_query($conn, $meterSql);

  if ($result && mysqli_num_rows($result) > 0) {
      echo "<table class='table table-responsive mt-4'>";
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
            echo "<td><button class='btn btn-info print-monthly-pay' data-consumer-id='" . htmlspecialchars($row['consumer_id']) . "'>Print Monthly Pay</button></td>";
            
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

<div class="modal fade" id="consumptionModal" tabindex="-1" aria-labelledby="consumptionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="consumptionModalLabel" style="font-weight:600">Consumption Records</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-responsive" id="consumptionTable">
          <thead>
            <tr>
              <th>Date</th>
              <th>Daily Consumption (kWh)</th>
              <th>Weekly Consumption (kWh)</th>
              <th>Monthly Consumption (kWh)</th>
              <th>Price to Pay</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
        <div class="mb-3">
          <label for="energyConsumed" class="form-label">Energy Consumed (kWh):</label>
          <input type="number" class="form-control" id="energyConsumed" placeholder="Enter daily consumption" required>
        </div>
        <div class="mb-3">
          <label for="date" class="form-label">Date:</label>
          <input type="date" class="form-control" id="date" required>
        </div>
        <button id="addRecord" class="btn btn-info">Add Record</button>
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

            $.ajax({
                url: 'fetch_consumption_records.php',
                type: 'POST',
                data: { consumer_id: consumerId },
                success: function(response) {

                    $('#consumptionTable tbody').html(response);
                    $('#consumptionModal').modal('show');

                    calculateConsumption();
                },
                error: function() {
                    alert('Error fetching consumption records.');
                }
            });

            $('#addRecord').off('click').on('click', function() {
                var energyConsumed = $('#energyConsumed').val();
                var date = $('#date').val();

                if (!energyConsumed || !date) {
                    alert('Please enter both the energy consumed and the date.');
                    return;
                }

                $.ajax({
                    url: 'add_consumption_record.php',
                    type: 'POST',
                    data: {
                        consumer_id: consumerId,
                        energy_consumed: energyConsumed,
                        date: date
                    },
                    success: function(response) {
                        $('#consumptionTable tbody').html(response); 
                        $('#energyConsumed').val(''); 
                        $('#date').val('');
                        calculateConsumption();
                    },
                    error: function() {
                        alert('Error adding consumption record.');
                    }
                });
            });
        });

        function calculateConsumption() {
        $('#consumptionTable tbody tr').each(function() {
        var dailyConsumption = parseFloat($(this).find('td:nth-child(2)').text()) || 0;
        var weeklyConsumption = dailyConsumption * 7;
        var monthlyConsumption = dailyConsumption * 30; 
        var pricePerKwh = 6;
        var priceToPay = monthlyConsumption * pricePerKwh;

        $(this).find('td:nth-child(3)').text(weeklyConsumption.toFixed(2));
        $(this).find('td:nth-child(4)').text(monthlyConsumption.toFixed(2));
        $(this).find('td:nth-child(5)').text('₱' + priceToPay.toFixed(2));
        });
        }

    });

    $(document).ready(function () {
    $('.print-monthly-pay').on('click', function () {
        var consumerId = $(this).data('consumer-id');

        $.ajax({
            url: 'fetch_monthly_pay.php',
            type: 'POST',
            data: { consumer_id: consumerId },
            success: function (response) {
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write('<html><head><title>Monthly Pay Receipt</title>');
                printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">');
                printWindow.document.write('<style>');
                printWindow.document.write(`
                    body {
                        font-family: 'Courier New', Courier, monospace;
                        background-color: #f9f9f9;
                        padding: 20px;
                    }
                    .receipt-container {
                        max-width: 400px;
                        margin: 20px auto;
                        padding: 20px;
                        border: 1px dashed #333;
                        background-color: #fff;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                    }
                    .receipt-header {
                        text-align: center;
                        font-size: 18px;
                        font-weight: bold;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #333;
                        padding-bottom: 10px;
                    }
                    .receipt-details p {
                        margin: 5px 0;
                        font-size: 14px;
                    }
                    .receipt-footer {
                        text-align: center;
                        margin-top: 20px;
                        font-size: 12px;
                        color: #666;
                    }
                `);
                printWindow.document.write('</style>');
                printWindow.document.write('</head><body>');
                printWindow.document.write('<div class="container d-flex justify-content-center align-items-center">');
                printWindow.document.write('<div class="receipt-container">');
                printWindow.document.write('<div class="receipt-header">Monthly Pay Receipt</div>');
                printWindow.document.write('<div class="receipt-details">' + response + '</div>');
                printWindow.document.write('<div class="receipt-footer">Thank you for your payment!</div>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');
                printWindow.document.write('</body></html>');
                printWindow.document.close();
                printWindow.print();
            },
            error: function () {
                alert('Error fetching monthly pay details.');
            }
        });
    });
});

</script>
</body>
</html>
