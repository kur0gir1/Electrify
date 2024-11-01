<?php
session_start();
include 'database.php';

$errors = array(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loginIdentifier = $_POST["loginIdentifier"] ?? ''; 
    $password = $_POST["password"] ?? '';

    if (empty($loginIdentifier) || empty($password)) {
        array_push($errors, "Email or Username and Password are required");
    }

    if (count($errors) == 0) {
        $sql = "SELECT * FROM users WHERE email = ? OR username = ?";
        $stmt = mysqli_stmt_init($conn);
        
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "ss", $loginIdentifier, $loginIdentifier);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $user = mysqli_fetch_assoc($result);

                if ($password === $user['password']) { 
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['username'] = $user['username']; 
                    header("Location: index.php");
                    exit();
                } else {
                    array_push($errors, "Incorrect password");
                }
            } else {
                array_push($errors, "You are not registered yet");
            }
        } else {
            array_push($errors, "Database error");
        }
    }

    foreach ($errors as $error) {
        echo "<div class='alert alert-danger'>$error</div>";
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
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: black;
            color: #FFD700;
            transition: opacity 0.5s ease;
            opacity: 1;
        }
        .fade-in {
            opacity: 1;
        }
        .fade-out {
            opacity: 0;
        }
        .container .box-area {
            background-color: #111;
            border-color: #FFD700;
        }
        .left-box {
            background-color: #FFD700;
            color: black;
        }
        .header-text h2, .header-text p {
            color: #FFD700;
        }
        .form-control {
            background-color: #222;
            color: #FFD700;
            border: none;
        }
        .form-check-label, .forgot a, .row a {
            color: #FFD700;
        }
        .form-check-input, .btn-primary {
            background-color: #FFD700;
            color: black;
            border-color: #FFD700;
        }
        .btn-primary:hover {
            background-color: #FFC107;
            border-color: #FFC107;
        }
    </style>
    <title>Login</title>
</head>
<body class="fade-in">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row rounded-5 p-3 box-area">

        <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column left-box">
            <p class="fs-1 fw-bold">Electrify</p>
            <small class="fs-8 text-wrap text-center" style="width: 17rem;">
                Powering Your World, One Connection at a Time!
            </small>
        </div>

        <div class="col-md-6 right-box">
            <div class="row align-items-center">
                <div class="header-text mb-4">
                    <h2 style="font-weight: 600">Hello, Again</h2>
                    <p>We are happy to have you back.</p>
                </div>

                <form action="login.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg fs-6" placeholder="Email Address or Username" name="loginIdentifier" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-control-lg fs-6" placeholder="Password" name="password" required id="password">
                    </div>
                    <div class="input-group mb-5 d-flex justify-content-between">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="formCheck">
                            <label for="formCheck" class="form-check-label"><small>Remember Me</small></label>
                        </div>
                        <div class="forgot">
                            <small><a href="#">Forgot Password?</a></small>
                        </div>
                    </div>
                    <div class="input-group mb-3">
                        <button type="submit" class="btn btn-lg btn-primary w-100 fs-6">Login</button>
                    </div>
                    <div class="row">
                        <small>Don't have an account? <a href="register.php" style="color: #FFD700;">Sign Up</a></small>
                    </div>
                </form>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.body.classList.add('fade-in'); 
        setTimeout(function() {
            document.body.classList.remove('fade-out');
        }, 10); 

        const form = document.querySelector('form');
        form.addEventListener('submit', function (event) {
            event.preventDefault();  
            document.body.classList.add('fade-out', 'active'); 
            setTimeout(function () {
                form.submit();
            }, 500);
        });

        const links = document.querySelectorAll('a');
        links.forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault(); 
                const href = link.getAttribute('href');  
                if (href) {
                    document.body.classList.add('fade-out', 'active'); 
                    setTimeout(function () {
                        window.location.href = href;
                    }, 500);
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-3Z0EXGJl8H0C1A9uNa90MfwzF1oEovb7ZRX1L7qKuxp9t+6gT6uCq3EVp8SNiJ1X" crossorigin="anonymous"></script>
</body>
</html>
