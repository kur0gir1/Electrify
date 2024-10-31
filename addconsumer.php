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
</head>
<body class="container-fluid bg-dark text-white">
  <div class="container mt-5 bg-dark text-light p-4 rounded">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">BlockForge Labs</h2>
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
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        
        // Insert consumer data
        $sql = "INSERT INTO consumers (account_number, address) VALUES ('$accountNumber', '$address')";

        if (mysqli_query($conn, $sql)) {
            echo "<div class='alert alert-success mt-4'>Consumer added successfully!</div>";
        } else {
            echo "<div class='alert alert-danger mt-4'>Error adding consumer: " . mysqli_error($conn) . "</div>";
        }

        mysqli_close($conn);
    }
    ?>

    <!-- Form to add consumer -->
    <form action="addconsumer.php" method="post" class="mt-4">
      <div class="form-group mb-3">
        <label for="address">Address:</label>
        <input type="text" name="address" id="address" class="form-control bg-light border border-secondary" required>
      </div>
      <button type="submit" class="btn btn-success mt-4">Add Consumer</button>
    </form>

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
