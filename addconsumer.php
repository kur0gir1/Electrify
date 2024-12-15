<?php
session_start();
include 'database.php';

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); 
}

function generateAccountNumber() {
  $randomNumber = random_int(0, 99999999);
  $formattedNumber = str_pad($randomNumber, 8, '0', STR_PAD_LEFT); 
  return 'AC' . $formattedNumber; 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $accountNumber = generateAccountNumber();
    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $contact_details = mysqli_real_escape_string($conn, $_POST['contact_details']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);  // Email field
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $manufactureDate = date('Y-m-d'); 
    $installationDate = date('Y-m-d');  

    // Combine first name and last name for full name
    $name = $firstName . ' ' . $lastName;

    // Generate username and password
    $username = mysqli_real_escape_string($conn, strtolower(str_replace(' ', '', $firstName)) . substr($accountNumber, 2, 4));
    $password = $accountNumber; // Default password is the account number

    // Insert consumer data into consumers table, including email
    $sql = "INSERT INTO consumers (account_number, first_name, last_name, contact_details, email, address, username, password) 
    VALUES ('$accountNumber', '$firstName', '$lastName', '$contact_details', '$email', '$address', '$username', '$password')";

    if (mysqli_query($conn, $sql)) {
        $consumerId = mysqli_insert_id($conn);

        // Insert meter data into electricitymeters table
        $meterSql = "INSERT INTO electricitymeters (consumer_id, manufacture_date, installation_date) 
                    VALUES ('$consumerId', '$manufactureDate', '$installationDate')";

        if (mysqli_query($conn, $meterSql)) {
            echo "<div class='alert alert-success mt-4'>Consumer and Meter added successfully!</div>";

            // Retrieve and display new consumer and meter details
            $displaySql = "SELECT em.meter_id, em.manufacture_date, em.installation_date, c.account_number, c.username, c.password, c.email
                          FROM electricitymeters em
                          JOIN consumers c ON em.consumer_id = c.consumer_id
                          WHERE c.consumer_id = '$consumerId'";

            $result = mysqli_query($conn, $displaySql);
            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                echo "<div class='mt-4'><strong>New Consumer Details:</strong></div>";
                echo "<p><strong>Meter ID: </strong>" . $row['meter_id'] . "</p>";
                echo "<p><strong>Manufacture Date: </strong>" . $row['manufacture_date'] . "</p>";
                echo "<p><strong>Installation Date: </strong>" . $row['installation_date'] . "</p>";
                echo "<p><strong>Account Number: </strong>" . $row['account_number'] . "</p>";
                echo "<p><strong>Username:</strong> " . $row['username'] . "</p>";
                echo "<p><strong>Default Password:</strong> " . $row['password'] . "</p>";
                echo "<p><strong>Email:</strong> " . $row['email'] . "</p>";  // Display the email
            } else {
                echo "<div class='alert alert-warning mt-4'>Unable to retrieve consumer details.</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-4'>Error adding meter: " . mysqli_error($conn) . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger mt-4'>Error adding consumer: " . mysqli_error($conn) . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Consumer</title>
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
    .navbar-nav .nav-link:hover {
      color: white;
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">Electrify</h2>
      <div>
        <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
        <a href="login.php" class="btn btn-outline-light">Logout</a>
      </div>
    </div>
    <h1 class="text-center">Add Consumer</h1>

    <nav class="navbar navbar-expand-lg navbar-dark bg-black justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a href="index.php" class="nav-link">Consumers Table</a></li>
        <li class="nav-item"><a href="meters.php" class="nav-link">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-dark active">Add Consumer</a></li>
      </ul>
    </nav>

    <form action="addconsumer.php" method="post" class="mt-4">
    <div class="mb-3 row">
      <div class="col-md-6">
        <label for="first_name" class="form-label">First Name:</label>
          <input type="text" name="first_name" id="first_name" class="form-control bg-light border border-secondary" placeholder="Enter consumer's first name" required>
      </div>
      <div class="col-md-6">
        <label for="last_name" class="form-label">Last Name:</label>
          <input type="text" name="last_name" id="last_name" class="form-control bg-light border border-secondary" placeholder="Enter consumer's last name" required>
      </div>
    </div>

    <div class="mb-3 row">
      <div class="col-md-6">
        <label for="contact_details" class="form-label">Contact Number:</label>
        <input type="text" name="contact_details" id="contact_details" class="form-control bg-light border border-secondary" placeholder="Enter consumer's contact number" required>
      </div>

      <div class="col-md-6">
        <label for="email" class="form-label">Email:</label>
        <input type="email" name="email" id="email" class="form-control bg-light border border-secondary" placeholder="Enter consumer's email" required>
      </div>
    </div>

      <div class="mb-3">
        <label for="address" class="form-label">Address:</label>
        <input type="text" name="address" id="address" class="form-control bg-light border border-secondary" placeholder="Enter consumer's address" required>
      </div>
      <button type="submit" class="btn btn-success mt-4">Add Consumer</button>
    </form>

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
