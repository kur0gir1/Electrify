<?php
session_start();
include 'database.php';

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); // Get the username safely
}

// Function to generate a random account number
function generateAccountNumber($length = 10) {
    return strtoupper(substr(bin2hex(random_bytes($length)), 0, $length));
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
      background-color: black; /* Black background for the body */
      color: #FFD700; /* Yellow text color */
    }
    .navbar {
      background-color: black; /* Black navbar */
    }
    .navbar-nav .nav-link {
      color: #FFD700; /* Yellow nav links */
    }
    .navbar-nav .nav-link:hover {
      color: white; /* White color on hover */
    }
    .container {
      background-color: #222; /* Darker background for the form container */
      padding: 20px;
      border-radius: 8px;
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
        <li class="nav-item"><a href="index.php" class="nav-link text-light">Consumers Table</a></li>
        <li class="nav-item"><a href="address.php" class="nav-link text-light">Addresses Table</a></li>
        <li class="nav-item"><a href="meters.php" class="nav-link text-light">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link text-light active btn btn-success">Add Consumer</a></li>
      </ul>
    </nav>

    <?php
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accountNumber = generateAccountNumber(); // Generate a random account number
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $contact_details = mysqli_real_escape_string($conn, $_POST['contact_details']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);

        // Insert address into the addresses table first
        $address_sql = "INSERT INTO addresses (address) VALUES ('$address')";
        if (mysqli_query($conn, $address_sql)) {
            // Get the ID of the newly inserted address
            $address_id = mysqli_insert_id($conn);

            // Insert consumer data into the consumers table
            $sql = "INSERT INTO consumers (account_number, name, contact_details) 
                    VALUES ('$accountNumber', '$name', '$contact_details')";

            if (mysqli_query($conn, $sql)) {
                echo "<div class='alert alert-success mt-4'>Consumer added successfully!</div>";
            } else {
                echo "<div class='alert alert-danger mt-4'>Error adding consumer: " . mysqli_error($conn) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger mt-4'>Error adding address: " . mysqli_error($conn) . "</div>";
        }
    }
    ?>

    <!-- Form to add consumer -->
    <form action="addconsumer.php" method="post" class="mt-4">
      <div class="mb-3">
        <label for="name" class="form-label">Name:</label>
        <input type="text" name="name" id="name" class="form-control bg-light border border-secondary" placeholder="Enter consumer's name" required>
      </div>
      <div class="mb-3">
        <label for="contact_details" class="form-label">Contact Number:</label>
        <input type="text" name="contact_details" id="contact_details" class="form-control bg-light border border-secondary" placeholder="Enter consumer's contact number" required>
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
