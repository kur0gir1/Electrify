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
  <title>Consumers Table</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
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
      .table {
          color: white;
      }
      .table th, .table td {
          border-color: #fff;
      }

      .table tbody tr:hover {
        background-color: #333; /* Dark gray */
        transition: background-color 0.3s ease;
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

    <h1 class="text-center">Consumers Table</h1>

    <nav class="navbar navbar-expand-lg navbar-dark justify-content-center">
      <ul class="navbar-nav">
        <li class="nav-item"><a href="index.php" class="nav-link active">Consumers Table</a></li>
        <li class="nav-item"><a href="meters.php" class="nav-link">Meters Table</a></li>
        <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-dark">Add Consumer</a></li>
      </ul>
    </nav>

    <?php
    $sql = "SELECT c.consumer_id, c.name, c.account_number, c.contact_details, c.address, 
                  e.meter_id 
            FROM Consumers c 
            LEFT JOIN electricitymeters e ON c.consumer_id = e.consumer_id
            ORDER BY c.consumer_id";

    if (isset($_GET['sort'])) {
        $sort = htmlspecialchars($_GET['sort']);
        $order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'desc' : 'asc';
        $sql = "SELECT c.consumer_id, c.name, c.account_number, c.contact_details, c.address, 
                      e.meter_id 
                FROM Consumers c 
                LEFT JOIN electricitymeters e ON c.consumer_id = e.consumer_id 
                ORDER BY $sort $order";
    } else {
        $order = 'asc';
    }

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='table mt-4'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th><a href='?sort=consumer_id&order=$order' class='text-light'>Consumer ID</a></th>";
        echo "<th><a href='?sort=name&order=$order' class='text-light'>Name</a></th>";
        echo "<th><a href='?sort=account_number&order=$order' class='text-light'>Account Number</a></th>";
        echo "<th><a href='?sort=contact_details&order=$order' class='text-light'>Contact Details</a></th>";
        echo "<th><a href='?sort=address&order=$order' class='text-light'>Address</a></th>";
        echo "<th>Meter ID</th>";
        echo "<th>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['consumer_id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['account_number']) . "</td>";
            echo "<td>" . htmlspecialchars($row['contact_details']) . "</td>";
            echo "<td>" . htmlspecialchars($row['address']) . "</td>";
            echo "<td>" . htmlspecialchars($row['meter_id'] ?? 'N/A') . "</td>";
            echo "<td>
                    <a href='edit.php?consumer_id=" . htmlspecialchars($row['consumer_id']) . "' class='btn btn-warning btn-sm'>Edit</a>
                    <a href='delete.php?consumer_id=" . htmlspecialchars($row['consumer_id']) . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this record?\");'>Delete</a>
                  </td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<div class='alert alert-info mt-4'>No records found.</div>";
    }

    mysqli_close($conn);
    ?>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
