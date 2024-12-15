<?php
session_start();
include 'database.php';

$errors = array(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"] ?? '';
    $email = $_POST["email"] ?? '';
    $password = $_POST["password"] ?? '';
    $confirm_password = $_POST["confirm_password"] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        array_push($errors, "All fields are required");
    }

    if ($password !== $confirm_password) {
        array_push($errors, "Passwords do not match");
    }

    if (count($errors) == 0) {
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        $stmt = mysqli_stmt_init($conn);
        
        if (mysqli_stmt_prepare($stmt, $sql)) {
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: login.php");
                exit();
            } else {
                array_push($errors, "Registration failed");
            }
        } else {
            array_push($errors, "Database error");
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
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
            background-color: #111;
            color: #FFD700;
        }
        .right-box {
            background-color: #FFD700;
            color: black;
            border-top-right-radius: 20px;
            border-bottom-right-radius: 20px;
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
        .btn-fixed-width {
            width: 60px;
        }
    </style>
    <title>Register</title>
</head>
<body class="fade-in">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <div class="row rounded-5 p-3 box-area">

        <div class="col-md-6 left-box">
            <div class="row align-items-center">
                <div class="header-text mb-4">
                    <h2 style="font-weight: 600">Create Account</h2>
                    <p>Join us in powering your world!</p>
                </div>

                <form action="register.php" method="POST">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control form-control-lg fs-6" placeholder="Username" name="username" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="email" class="form-control form-control-lg fs-6" placeholder="Email Address" name="email" required>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-control-lg fs-6" placeholder="Password" name="password" required id="password">
                        <button type="button" class="btn btn-outline-light btn-fixed-width" id="togglePassword">Show</button>
                    </div>
                    <div class="input-group mb-3">
                        <input type="password" class="form-control form-control-lg fs-6" placeholder="Confirm Password" name="confirm_password" required id="confirm_password">
                        <button type="button" class="btn btn-outline-light btn-fixed-width" id="toggleConfirmPassword">Show</button>
                    </div>
                    <div class="input-group mb-3">
                        <button type="submit" class="btn btn-lg btn-primary fs-6 w-100">Register</button>
                    </div>
                    <div class="row">
                        <small>Already have an account? <a href="login.php" style="color: #FFD700;">Login</a></small>
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

        <div class="col-md-6 rounded-4 d-flex justify-content-center align-items-center flex-column right-box">
            <p class="fs-1 fw-bold">Electrify</p>
            <small class="text-wrap text-center" style="width: 17rem;">
                Powering Your World, One Connection at a Time!
            </small>
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

        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });

        const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
        const confirmPasswordInput = document.getElementById('confirm_password');

        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPasswordInput.setAttribute('type', type);
            this.textContent = type === 'password' ? 'Show' : 'Hide';
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
