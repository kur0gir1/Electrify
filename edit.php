<?php
session_start();
include 'database.php';

if (isset($_GET['consumer_id'])) {
    $consumerID = $_GET['consumer_id'];

    $sql = "SELECT consumer_id, first_name, last_name, account_number, contact_details, address FROM consumers WHERE consumer_id = '$consumerID'";
    $result = mysqli_query($conn, $sql);
    $consumer = mysqli_fetch_assoc($result);

    if (!$consumer) {
        echo "<div class='alert alert-danger'>Consumer not found.</div>";
        exit;
    }

    $meterSql = "SELECT meter_id FROM electricitymeters LIMIT 1"; 
    $meterResult = mysqli_query($conn, $meterSql);
    $meter = mysqli_fetch_assoc($meterResult);

    if (!$meter) {
        echo "<div class='alert alert-danger'>No meter found.</div>";
        exit;
    }

    $meterID = $meter['meter_id'];
} else {
    echo "<div class='alert alert-danger'>No consumer ID provided.</div>";
    exit;
}

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); 
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $accountNumber = mysqli_real_escape_string($conn, $_POST['account_number']);
    $contactDetails = mysqli_real_escape_string($conn, $_POST['contact_details']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    
    $sql = "UPDATE consumers SET first_name='$firstName', last_name='$lastName', account_number='$accountNumber', 
            contact_details='$contactDetails', address='$address' WHERE consumer_id='$consumerID'";

    if (mysqli_query($conn, $sql)) {
        echo "<div class='alert alert-success'>Consumer details updated successfully!</div>";
        echo "<meta http-equiv='refresh' content='5;url=index.php'>";
    } else {
        echo "<div class='alert alert-danger'>Error updating consumer: " . mysqli_error($conn) . "</div>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Consumer</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: black;
            color: #FFD700;
        }
    </style>
</head>
<body class="container-fluid">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">BlockForge Labs</h2>
            <div>
                <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
                <a href="login.php" class="btn btn-outline-light">Logout</a>
            </div>
        </div>
        <h1 class="text-center">Edit Consumer</h1>

        <nav class="navbar navbar-expand-lg navbar-dark bg-black justify-content-center mb-4">
            <ul class="navbar-nav">
                <li class="nav-item"><a href="index.php" class="nav-link">Consumers Table</a></li>
                <li class="nav-item"><a href="meters.php" class="nav-link">Meters Table</a></li>
                <li class="nav-item"><a href="addconsumer.php" class="nav-link btn btn-success">Add Consumer</a></li>
            </ul>
        </nav>

        <form action="edit.php?consumer_id=<?php echo htmlspecialchars($consumerID); ?>" method="post" class="mt-4">
            <div class="mb-3">
                <label for="consumer_id">Consumer ID:</label>
                <input type="text" name="consumer_id" id="consumer_id" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['consumer_id']); ?>" readonly required>
            </div>
            <div class="mb-3">
                <label for="meter_id">Meter ID:</label>
                <input type="text" name="meter_id" id="meter_id" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($meterID); ?>" readonly required>
            </div>
            <div class="mb-3">
                <label for="first_name">First Name:</label>
                <input type="text" name="first_name" id="first_name" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['first_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="last_name">Last Name:</label>
                <input type="text" name="last_name" id="last_name" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['last_name']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="account_number">Account Number:</label>
                <input type="text" name="account_number" id="account_number" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['account_number']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="contact_details">Contact Details:</label>
                <input type="text" name="contact_details" id="contact_details" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['contact_details']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="address">Address:</label>
                <input type="text" name="address" id="address" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($consumer['address']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Consumer</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
