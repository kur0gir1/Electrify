<?php
session_start();
include 'database.php';

// Check if employee ID is provided in the URL
if (isset($_GET['employeeID'])) {
    $employeeID = $_GET['employeeID'];

    // Fetch the employee details from the database
    $sql = "SELECT e.*, p.GrossPay, p.Deductions, p.NetPay FROM employees e 
            JOIN payroll p ON e.EmployeeID = p.EmployeeID 
            WHERE e.EmployeeID = '$employeeID'";
    $result = mysqli_query($conn, $sql);
    $employee = mysqli_fetch_assoc($result);

    // Check if employee exists
    if (!$employee) {
        echo "<div class='alert alert-danger'>Employee not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger'>No employee ID provided.</div>";
    exit;
}

$username = '';
if (isset($_SESSION['username'])) {
    $username = htmlspecialchars($_SESSION['username']); // Get the username safely
}


// Handle form submission for updating employee details
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = mysqli_real_escape_string($conn, $_POST['first_name']);
    $lastName = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phoneNumber = mysqli_real_escape_string($conn, $_POST['phone_number']);
    $jobTitle = mysqli_real_escape_string($conn, $_POST['job_title']);
    $departmentID = mysqli_real_escape_string($conn, $_POST['department']);
    $grossPay = mysqli_real_escape_string($conn, $_POST['salary']);
    $deductions = $_POST['deductions']; // Deductions array

    // Calculate total deductions
    $totalDeductions = 0;
    foreach ($deductions as $deduction) {
        if ($deduction === 'tax') {
            $totalDeductions += $grossPay * 0.12; // 12% tax
        } elseif ($deduction === 'insurance') {
            $totalDeductions += 500; // Fixed insurance
        } elseif ($deduction === 'sss') {
            $totalDeductions += 800; // Fixed SSS
        } elseif ($deduction === 'other') {
            // Custom deduction, assuming it's sent as an array with the same index
            $customDeduction = $_POST['custom_deductions'][$totalDeductions]; // Use an additional input to capture custom values
            $totalDeductions += (float) $customDeduction; // Add custom deduction value
        }
    }

    // Calculate net pay
    $netPay = $grossPay - $totalDeductions;

    // Update the employee details in the employees table
    $sql = "UPDATE employees SET FirstName='$firstName', LastName='$lastName', Email='$email', PhoneNumber='$phoneNumber', JobTitle='$jobTitle', DepartmentID='$departmentID' WHERE EmployeeID='$employeeID'";

    if (mysqli_query($conn, $sql)) {
        // Update the payroll table
        $sqlPayroll = "UPDATE payroll SET GrossPay='$grossPay', Deductions='$totalDeductions', NetPay='$netPay' WHERE EmployeeID='$employeeID'";
        mysqli_query($conn, $sqlPayroll);

        echo "<div class='alert alert-success'>Employee details updated successfully!</div>";
    } else {
        echo "<div class='alert alert-danger'>Error updating employee: " . mysqli_error($conn) . "</div>";
    }

    mysqli_close($conn);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Employee</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <script>
    let deductionIndex = 0;

    // Function to calculate net pay
    function calculateNetPay() {
      const grossPay = parseFloat(document.getElementById('salary').value) || 0;
      const deductionElements = document.getElementsByClassName('deduction');
      let totalDeductions = 0;

      for (let i = 0; i < deductionElements.length; i++) {
        const deductionType = deductionElements[i].querySelector('select').value;
        let deductionAmount = 0;

        if (deductionType === 'tax') {
          deductionAmount = grossPay * 0.12; // 12% tax
        } else if (deductionType === 'insurance') {
          deductionAmount = 500; // Fixed insurance
        } else if (deductionType === 'sss') {
          deductionAmount = 800; // Fixed SSS
        } else if (deductionType === 'other') {
          const customDeductionInput = deductionElements[i].querySelector('.custom_deduction');
          deductionAmount = parseFloat(customDeductionInput.value) || 0; // Custom input
        }

        totalDeductions += deductionAmount;
      }

      const netPay = grossPay - totalDeductions;
      document.getElementById('netpay').value = netPay.toFixed(2); // Format to 2 decimal places
    }

    // Show/hide custom deduction input based on selection
    function toggleCustomDeduction(deductionSelect) {
      const customDeductionInput = deductionSelect.parentNode.querySelector('.custom_deduction');
      if (deductionSelect.value === 'other') {
        customDeductionInput.style.display = 'block';
        customDeductionInput.value = ''; // Clear the input field
      } else {
        customDeductionInput.style.display = 'none';
        customDeductionInput.value = ''; // Clear the input field if not "other"
      }
      calculateNetPay(); // Update net pay on change
    }

    // Function to add a new deduction field
    function addDeduction() {
      const deductionContainer = document.getElementById('deductionContainer');
      const newDeduction = document.createElement('div');
      newDeduction.classList.add('deduction', 'mb-2');

      newDeduction.innerHTML = `
        <select name="deductions[]" class="form-control bg-light border border-secondary" onchange="toggleCustomDeduction(this);" required>
          <option value="">Select Deduction</option>
          <option value="tax">Tax (12%)</option>
          <option value="insurance">Insurance (-$500)</option>
          <option value="sss">SSS (-$800)</option>
          <option value="other">Other</option>
        </select>
        <input type="number" name="custom_deductions[]" class="custom_deduction form-control mt-2" placeholder="Enter custom deduction" style="display:none;" oninput="calculateNetPay()">
      `;

      deductionContainer.appendChild(newDeduction);
      deductionIndex++;
    }
  </script>
</head>
<body class="container-fluid bg-dark text-white">
  <div class="container mt-5 bg-dark text-light p-4 rounded">
  <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="mb-0">BlockForge Labs</h2>
      <div>
        <span class="me-3">Welcome, <?php echo $username ?: 'Guest'; ?>!</span>
        <a href="login.php" class="btn btn-outline-light">Logout</a> <!-- Change to logout.php -->
      </div>
    </div>
    <h1 class="text-center">Edit Employee</h1>

    <nav class="navbar navbar-expand-lg navbar-dark bg-black justify-content-center mb-4">
      <ul class="navbar-nav">
      <li class="nav-item"><a href="index.php" class="nav-link text-light active">Employees Table</a></li>
        <li class="nav-item"><a href="departments.php" class="nav-link text-light">Departments Table</a></li>
        <li class="nav-item"><a href="payroll.php" class="nav-link text-light">Payroll Table</a></li>
        <li class="nav-item"><a href="addemployee.php" class="nav-link text-light active btn btn-success">Add Employees</a></li>
      </ul>
    </nav>

    <!-- Form to edit employee -->
    <form action="edit.php?employeeID=<?php echo htmlspecialchars($employeeID); ?>" method="post" class="mt-4">
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="first_name">First Name:</label>
          <input type="text" name="first_name" id="first_name" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['FirstName']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="last_name">Last Name:</label>
          <input type="text" name="last_name" id="last_name" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['LastName']); ?>" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="email">Email:</label>
          <input type="email" name="email" id="email" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['Email']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="phone_number">Phone Number:</label>
          <input type="text" name="phone_number" id="phone_number" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['PhoneNumber']); ?>" required>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="job_title">Job Title:</label>
          <input type="text" name="job_title" id="job_title" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['JobTitle']); ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label for="salary">Gross Pay:</label>
          <select name="salary" id="salary" class="form-control bg-light border border-secondary" onchange="calculateNetPay()" required>
            <option value="">Select Salary</option>
            <?php
            for ($i = 5000; $i <= 100000; $i += 500) {
                $selected = $employee['GrossPay'] == $i ? "selected" : ""; // Mark current salary as selected
                echo "<option value='$i' $selected>$" . number_format($i) . "</option>";
            }
            ?>
          </select>
        </div>
      </div>
      <div id="deductionContainer" class="mb-3">
        <label>Deductions:</label>
        <div class="deduction mb-2">
          <select name="deductions[]" class="form-control bg-light border border-secondary" onchange="toggleCustomDeduction(this);" required>
            <option value="">Select Deduction</option>
            <option value="tax">Tax (12%)</option>
            <option value="insurance">Insurance (-$500)</option>
            <option value="sss">SSS (-$800)</option>
            <option value="other">Other</option>
          </select>
          <input type="number" name="custom_deductions[]" class="custom_deduction form-control mt-2" placeholder="Enter custom deduction" style="display:none;" oninput="calculateNetPay()">
        </div>
      </div>
      <button type="button" class="btn btn-primary mb-3" onclick="addDeduction()">Add Another Deduction</button>
      <div class="row">
        <div class="col-md-6 mb-3">
          <label for="netpay">Net Pay:</label>
          <input type="text" name="netpay" id="netpay" class="form-control bg-light border border-secondary" value="<?php echo htmlspecialchars($employee['NetPay']); ?>" readonly>
        </div>
      </div>
      <div class="form-group mt-3">
        <label for="department">Department:</label>
        <select name="department" id="department" class="form-control bg-light border border-secondary" required>
          <option value="">Select a Department</option>
          <?php
          // Fetch all departments for the dropdown
          $sql = "SELECT DepartmentID, DepartmentName FROM departments";
          $result = mysqli_query($conn, $sql);
          while ($row = mysqli_fetch_assoc($result)) {
              $selected = $employee['DepartmentID'] == $row['DepartmentID'] ? "selected" : ""; // Mark current department as selected
              echo "<option value='" . htmlspecialchars($row['DepartmentID']) . "' $selected>" . htmlspecialchars($row['DepartmentName']) . "</option>";
          }
          ?>
        </select>
      </div>
      <button type="submit" class="btn btn-success mt-4">Update Employee</button>
    </form>

  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.min.js"></script>
</body>
</html>
