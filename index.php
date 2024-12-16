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
  <title>Electrify</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
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
          text-align: center;
      }
      .table tbody tr:hover {
          background-color: #333; /* Dark gray */
          transition: background-color 0.3s ease;
      }
      .btn-edit:hover {
          background-color: #FFD700;
          color: black;
      }
      .btn-delete:hover {
          background-color: #FF0000;
          color: white;
      }

  </style>
</head>
<body class=" my-5">
  <div class="container mx-auto mt-5">
    <div class="flex justify-between items-center mb-4">
      <h2 class="mb-0 font-semibold text-3xl text-yellow-500 flex items-center">
        <i class="fas fa-bolt mr-2"></i> Electrify
      </h2>
      <div class="flex items-center">
        <span class="mr-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
        <a href="login.php" class="bg-yellow-500 text-black py-2 px-4 rounded hover:bg-yellow-600 flex items-center">
          <i class="fas fa-sign-out-alt mr-2"></i> Logout
        </a>
      </div>
    </div>

    <nav class="bg-black mb-4">
      <div class="container mx-auto flex justify-center items-center py-4">
        <div class="hidden md:flex md:items-center w-full md:w-auto" id="navbar-menu">
          <ul class="flex flex-col md:flex-row md:space-x-6">
            <li><a href="index.php" class="text-yellow-500 hover:text-white font-bold"><i class="fas fa-users"></i> Consumers Table</a></li>
            <li><a href="meters.php" class="text-yellow-500 hover:text-white"><i class="fas fa-tachometer-alt"></i> Meters Table</a></li>
            <li><a href="addconsumer.php" class="text-yellow-500 hover:text-white"><i class="fas fa-user-plus"></i> Add Consumer</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <?php
    $sql = "SELECT c.consumer_id, CONCAT(c.first_name, ' ', c.last_name) AS name, c.account_number, c.contact_details, c.address, 
                  e.meter_id 
            FROM Consumers c 
            LEFT JOIN electricitymeters e ON c.consumer_id = e.consumer_id
            ORDER BY c.consumer_id";

    if (isset($_GET['sort'])) {
        $sort = htmlspecialchars($_GET['sort']);
        $order = isset($_GET['order']) && $_GET['order'] == 'asc' ? 'desc' : 'asc';
        $sql = "SELECT c.consumer_id, CONCAT(c.first_name, ' ', c.last_name) AS name, c.account_number, c.contact_details, c.address, 
                      e.meter_id 
                FROM Consumers c 
                LEFT JOIN electricitymeters e ON c.consumer_id = e.consumer_id 
                ORDER BY $sort $order";
    } else {
        $order = 'asc';
    }

    $result = mysqli_query($conn, $sql);

    if ($result && mysqli_num_rows($result) > 0) {
        echo "<table class='min-w-full bg-black text-white'>";
        echo "<thead>";
        echo "<tr>";
        echo "<th class='py-2 px-4 border-b'><a href='?sort=consumer_id&order=$order' class='text-yellow-500'>Consumer ID</a></th>";
        echo "<th class='py-2 px-4 border-b'><a href='?sort=name&order=$order' class='text-yellow-500'>Name</a></th>";
        echo "<th class='py-2 px-4 border-b'><a href='?sort=account_number&order=$order' class='text-yellow-500'>Account Number</a></th>";
        echo "<th class='py-2 px-4 border-b'><a href='?sort=contact_details&order=$order' class='text-yellow-500'>Contact Details</a></th>";
        echo "<th class='py-2 px-4 border-b'><a href='?sort=address&order=$order' class='text-yellow-500'>Address</a></th>";
        echo "<th class='py-2 px-4 border-b text-yellow-500'>Meter ID</th>";
        echo "<th class='py-2 px-4 border-b'>Actions</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";

        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr class='hover:bg-gray-700 transition duration-300'>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['consumer_id']) . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['account_number']) . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['contact_details']) . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['address']) . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>" . htmlspecialchars($row['meter_id'] ?? 'N/A') . "</td>";
            echo "<td class='py-2 px-4 border-b text-center'>
                    <div class='flex flex-col items-center space-y-2'>
                      <a href='edit.php?consumer_id=" . htmlspecialchars($row['consumer_id']) . "' class='bg-yellow-500 text-black py-2 px-4 w-24 rounded btn-edit hover:bg-yellow-600 flex items-center justify-center'>
                        <i class='fas fa-edit mr-2'></i> Edit
                      </a>
                      <a href='delete.php?consumer_id=" . htmlspecialchars($row['consumer_id']) . "' class='bg-red-500 text-white py-2 px-4 w-24 rounded btn-delete hover:bg-red-600 flex items-center justify-center' onclick='return confirm(\"Are you sure you want to delete this record?\");'>
                        <i class='fas fa-trash-alt mr-2'></i> Delete
                      </a>
                    </div>
                  </td>";
            echo "</tr>";
        }
        echo "</tbody>";
        echo "</table>";
    } else {
        echo "<p class='text-center text-yellow-500'>No records found.</p>";
    }
    ?>

  </div>
</body>
</html>
