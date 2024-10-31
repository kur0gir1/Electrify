<?php
session_start();
include 'database.php'; 

// At the top of your PHP section
if (isset($_POST["submit"])) {
  // Collect form data
  $username = $_POST["username"];
  $email = $_POST["email"];
  $password = $_POST["password"];
  $verifypassword = $_POST["verifypassword"];
  $terms = isset($_POST["terms"]) ? 1 : 0;

  $errors = array();

  // Validation checks
  if (empty($username) || empty($email) || empty($password) || empty($verifypassword)) {
      array_push($errors, "All fields are required");
  }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      array_push($errors, "Email is not valid");
  }
  if (strlen($password) < 8) {
      array_push($errors, "Password must be at least 8 characters long");
  }
  if ($password !== $verifypassword) {
      array_push($errors, "Passwords do not match");
  }
  if ($terms == 0) {
      array_push($errors, "You must accept the terms and conditions");
  }

  require_once "database.php";
  $sql = "SELECT * FROM users WHERE email = ?";
  $stmt = mysqli_stmt_init($conn);
  if (mysqli_stmt_prepare($stmt, $sql)) {
      mysqli_stmt_bind_param($stmt, "s", $email);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);
      $rowCount = mysqli_num_rows($result);
      if ($rowCount > 0) {
          array_push($errors, "Email already exists!");
      }
  }

  if (count($errors) == 0) {

      $sql = "INSERT INTO users (username, email, password, terms) VALUES (?, ?, ?, ?)";
      $stmt = mysqli_stmt_init($conn);
      if (mysqli_stmt_prepare($stmt, $sql)) {
          mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $password, $terms);
          mysqli_stmt_execute($stmt);
          echo "<div class='alert alert-success'>You are registered successfully.</div>";
          header("Location: login.php");
      } else {
          die("Something went wrong");
      }
  } else {
      foreach ($errors as $error) {
          echo "<div class='alert alert-danger'>$error</div>";
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
  <link rel="stylesheet" href="css/style.css">
  <title>Register</title>
</head>

<body class="bg-black fade-out">
  <div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row border rounded-5 p-3 bg-white shadow box-area">
      <div class="col-md-6 right-box">
        <div class="row align-items-center">
          <div class="header-text mb-4">
            <h2>Welcome!</h2>
            <p>Create your account to get started.</p>
          </div>

          <form action="register.php" method="POST">
            <div class="input-group mb-3">
              <input type="text" class="form-control form-control-lg bg-light fs-6" placeholder="Username" name="username" required>
            </div>
            <div class="input-group mb-3">
              <input type="email" class="form-control form-control-lg bg-light fs-6" placeholder="Email Address" name="email" required>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Password" name="password" required>
            </div>
            <div class="input-group mb-3">
              <input type="password" class="form-control form-control-lg bg-light fs-6" placeholder="Confirm Password" name="verifypassword" required>
            </div>
            <div class="input-group mb-5">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="formCheck" name="terms" required>
                <label for="formCheck" class="form-check-label" style="color: #06031f;"><small>Accept Terms & Conditions</small></label>
              </div>
            </div>
            <div class="input-group mb-3">
              <button type="submit" name="submit" class="btn btn-lg btn-primary w-100 fs-6 custom-btn">Register</button>
            </div>
            <div class="row">
              <small>Already have an account? <a href="login.php" style="color: #06031f;">Log In</a></small>
            </div>
          </form>
        </div>
      </div>

      <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box" style="background: #06031f;">
        <p class="text-white fs-2" style="font-family: 'Courier New', Courier, monospace; font-weight: 600;">BlockForge Labs</p>
        <small class="text-white text-wrap text-center" style="width: 17rem; font-family: 'Courier New', Courier, monospace;">
          Forging the Future of Blockchain Innovation!
        </small>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {

      document.body.classList.add('fade-in'); 

      setTimeout(function () {
        document.body.classList.remove('fade-out');
      }, 10); 

      const form = document.querySelector('form');
      form.addEventListener('submit', function (event) {
        document.body.classList.add('fade-out', 'active');  

        setTimeout(function () {
          form.submit();
        }, 500);
      });

      const links = document.querySelectorAll('a');
      links.forEach(function (link) {
        link.addEventListener('click', function (event){
          event.preventDefault(); 
          const href = link.getAttribute('href');  
            document.body.classList.add('fade-out', 'active');  

            setTimeout(function () {
              window.location.href = href;
            }, 500);
          });
        });
      });
  </script>
</body>

</html>
