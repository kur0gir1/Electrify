<?php
session_start();
include 'database.php';

// Check if both employeeID and payrollID are set and valid
if (isset($_GET['employeeID']) && is_numeric($_GET['employeeID']) && 
    isset($_GET['payrollID']) && is_numeric($_GET['payrollID'])) {

    $EmployeeID = (int)$_GET['employeeID'];
    $PayrollID = (int)$_GET['payrollID'];

    // SQL to delete from employees
    $deleteEmployeesSQL = "DELETE FROM employees WHERE EmployeeID = ?";
    
    // SQL to delete from payroll
    $deletePayrollSQL = "DELETE FROM payroll WHERE PayrollID = ?";

    // Prepare the statement for deleting from employees
    if ($stmt1 = mysqli_prepare($conn, $deleteEmployeesSQL)) {
        mysqli_stmt_bind_param($stmt1, "i", $EmployeeID);
        mysqli_stmt_execute($stmt1);
        mysqli_stmt_close($stmt1);
    } else {
        echo "Error preparing statement for employees: " . mysqli_error($conn);
        exit();
    }

    // Prepare the statement for deleting from payroll
    if ($stmt2 = mysqli_prepare($conn, $deletePayrollSQL)) {
        mysqli_stmt_bind_param($stmt2, "i", $PayrollID);
        mysqli_stmt_execute($stmt2);
        mysqli_stmt_close($stmt2);
    } else {
        echo "Error preparing statement for payroll: " . mysqli_error($conn);
        exit();
    }

    // Redirect to the employee list page after deletion
    header("Location: index.php?message=Employee and Payroll deleted successfully");
    exit();
} else {
    echo "Invalid request.";
}

// Close the connection
mysqli_close($conn);
?>
