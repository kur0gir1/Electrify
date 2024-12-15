<?php
session_start();
include 'database.php';

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email']; // Get the logged-in user's email

// Fetch consumer's details based on email
$sql = "SELECT * FROM consumers WHERE email = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        $consumer = mysqli_fetch_assoc($result);
        $consumer_id = $consumer['consumer_id']; // Get the consumer's ID
        $consumerName = htmlspecialchars($consumer['first_name'] . ' ' . $consumer['last_name']);
        $accountNumber = htmlspecialchars($consumer['account_number']);
    } else {
        echo "Consumer not found.";
        exit();
    }
} else {
    echo "Database query failed.";
    exit();
}

// SQL to fetch the current month's bill
$sql = "
SELECT 
    c.account_number, 
    c.first_name, 
    c.last_name, 
    em.meter_id,
    dc.energy_consumed AS recent_consumption,
    (dc.energy_consumed * 7) AS monthly_consumption,
    (dc.energy_consumed * 30 * 7) AS total_pay,
    dc.date AS bill_date,
    dc.status AS consumption_status  -- Add status from consumption_records
FROM 
    consumers c
LEFT JOIN 
    electricitymeters em ON c.consumer_id = em.consumer_id
LEFT JOIN 
    consumption_records dc ON c.consumer_id = dc.consumer_id
WHERE 
    c.consumer_id = $consumer_id 
AND 
    MONTH(dc.date) = MONTH(CURDATE()) 
AND 
    YEAR(dc.date) = YEAR(CURDATE())
ORDER BY 
    dc.date DESC
LIMIT 1
";

$result = mysqli_query($conn, $sql);

if ($result && $row = mysqli_fetch_assoc($result)) {
    $consumerName = htmlspecialchars($row['first_name'] . ' ' . $row['last_name']);
    $meterId = htmlspecialchars($row['meter_id']);
    $billDate = date('F j, Y', strtotime($row['bill_date']));
    $monthlyConsumption = $row['monthly_consumption'];
    $totalPay = $row['total_pay'];
    $consumptionStatus = $row['consumption_status']; // Get the status from the bill record
} else {
    echo "<p>No records found for this consumer in the current month.</p>";
    exit();
}

// SQL to fetch previous month's bills for the consumer
$sql_previous_bills = "
SELECT 
    dc.energy_consumed, 
    (dc.energy_consumed * 7) AS monthly_consumption,
    (dc.energy_consumed * 30 * 7) AS total_pay,
    dc.date AS bill_date,
    dc.status AS consumption_status  -- Add status from consumption_records
FROM 
    consumption_records dc
JOIN 
    consumers c ON dc.consumer_id = c.consumer_id
WHERE 
    c.consumer_id = $consumer_id
AND 
    YEAR(dc.date) = YEAR(CURDATE())
ORDER BY 
    dc.date DESC
";

$previous_bills_result = mysqli_query($conn, $sql_previous_bills);
$previous_bills = mysqli_fetch_all($previous_bills_result, MYSQLI_ASSOC);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consumer Records</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #111;
            color: #fff;
        }
        .container {
            max-width: 900px;
            margin-top: 50px;
        }
        .card {
            background-color: #222;
            border-color: #FFD700;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-primary {
            background-color: #FFD700;
            color: black;
            border: none;
        }
        .btn-primary:hover {
            background-color: #FFC107;
        }
        .btn-logout {
            background-color: #FF6347;
            color: white;
            position: absolute;
            top: 20px;
            right: 20px;
            border: none;
        }
        .btn-logout:hover {
            background-color: #FF4500;
        }
        .btn-change-password {
            background-color: #4CAF50;
            color: white;
            position: absolute;
            top: 20px;
            right: 120px;
            border: none;
        }
        .btn-change-password:hover {
            background-color: #45a049;
        }
        .table {
            background-color: #333;
            color: #FFD700;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #444;
        }
        .table th, .table td {
            text-align: center;
        }
        h2, h4 {
            color: #FFD700;
        }
        .modal-content {
            background-color: #222;
            border: 1px solid #FFD700;
        }
        .modal-header, .modal-body {
            color: #FFD700;
        }
        .modal-footer {
            border-top: 1px solid #FFD700;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card position-relative">
        <!-- Log Out and Change Password Buttons -->
        <form action="logout.php" method="post" style="display:inline;">
            <button type="submit" class="btn btn-logout">Log Out</button>
        </form>

        <!-- Button to trigger change password modal -->
        <button class="btn btn-change-password" data-bs-toggle="modal" data-bs-target="#changePasswordModal">Change Password</button>

        <h2>Welcome, <?php echo $consumerName; ?>!</h2>
        <p><strong>Account Number:</strong> <?php echo $accountNumber; ?></p>

        <h4>Current Month's Bill:</h4>
<table class="table table-striped table-dark">
    <tbody>
        <tr>
            <td><strong>Date:</strong></td>
            <td><?php echo $billDate; ?></td>
        </tr>
        <tr>
            <td><strong>Energy Consumption:</strong></td>
            <td><?php echo $monthlyConsumption; ?> kWh</td>
        </tr>
        <tr>
            <td><strong>Total Amount Due:</strong></td>
            <td>₱<?php echo number_format($totalPay, 2); ?></td>
        </tr>
        <tr>
            <td><strong>Consumption Status:</strong></td>
            <td><?php echo $consumptionStatus; ?></td>
        </tr>
    </tbody>
</table>

        <h4>Previous Bills:</h4>
        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Monthly Consumption</th>
                    <th>Total Pay</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($previous_bills as $bill): ?>
                    <tr>
                        <td><?php echo date('F j, Y', strtotime($bill['bill_date'])); ?></td>
                        <td><?php echo $bill['monthly_consumption']; ?> kWh</td>
                        <td><?php echo number_format($bill['total_pay'], 2); ?></td>
                        <td><?php echo $bill['consumption_status']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal for Change Password -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changePasswordModalLabel">Change Password</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form action="change_password.php" method="POST">
            <div class="mb-3">
                <label for="current_password" class="form-label">Current Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleCurrentPassword"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleNewPassword"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm New Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    <button type="button" class="btn btn-outline-secondary" id="toggleConfirmPassword"><i class="bi bi-eye"></i></button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<script>
$(document).ready(function () {
    $('.print-monthly-pay').on('click', function () {
        var consumerId = $(this).data('consumer-id'); // Get the consumer ID from the button

        $.ajax({
            url: 'fetch_monthly_pay.php', // URL to fetch the monthly pay details
            type: 'POST',
            data: { consumer_id: consumerId }, // Send the consumer ID via POST
            success: function (response) {
                // Open a new window for the print layout
                var printWindow = window.open('', '_blank', 'width=800,height=600');
                printWindow.document.open();
                printWindow.document.write('<html><head><title>Monthly Pay Receipt</title>');

                // Include Bootstrap for default styling and custom styles for the receipt
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
                
                // Add the response (monthly pay details) into the receipt container
                printWindow.document.write('<div class="container d-flex justify-content-center align-items-center">');
                printWindow.document.write('<div class="receipt-container">');
                printWindow.document.write('<div class="receipt-header">Monthly Pay Receipt</div>');
                printWindow.document.write('<div class="receipt-details">' + response + '</div>');
                printWindow.document.write('<div class="receipt-footer">Thank you for your payment!</div>');
                printWindow.document.write('</div>');
                printWindow.document.write('</div>');
                
                printWindow.document.write('</body></html>');
                printWindow.document.close(); // Finalize the document

                // Trigger the print dialog
                printWindow.print();
            },
            error: function () {
                alert('Error fetching monthly pay details.');
            }
        });
    });
});

function togglePassword(fieldId) {
        var passwordField = document.getElementById(fieldId);
        var button = passwordField.nextElementSibling;

        // Toggle password visibility
        if (passwordField.type === "password") {
            passwordField.type = "text";
            button.textContent = "Hide";
        } else {
            passwordField.type = "password";
            button.textContent = "Show";
        }
    }


    // Toggle show/hide password for each field
    document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
        var passwordField = document.getElementById('current_password');
        var icon = this.querySelector('i');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    document.getElementById('toggleNewPassword').addEventListener('click', function() {
        var passwordField = document.getElementById('new_password');
        var icon = this.querySelector('i');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        var passwordField = document.getElementById('confirm_password');
        var icon = this.querySelector('i');
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.classList.remove('bi-eye');
            icon.classList.add('bi-eye-slash');
        } else {
            passwordField.type = "password";
            icon.classList.remove('bi-eye-slash');
            icon.classList.add('bi-eye');
        }
    });
</script>

</body>
</html>
