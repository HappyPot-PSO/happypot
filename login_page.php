<?php
session_start();
$title = 'Login - Happy Pot';

// Move all SweetAlert display logic from index.php to here
if (isset($_SESSION["login_error"]) && $_SESSION["login_error"]) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                swal("Sorry!", "Incorrect password.", "error");
            });
          </script>';
    $_SESSION["login_error"] = false; // Reset the flag
}
if (isset($_SESSION["login_errorl"]) && $_SESSION["login_errorl"]) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                swal("Sorry!", "User doesn\'t exist.", "error");
            });
          </script>';
    $_SESSION["login_errorl"] = false; // Reset the flag
}
if (isset($_SESSION["registration_success"]) && $_SESSION["registration_success"]) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                swal("Account created successfully!", "You can now log in.", "success");
            });
          </script>';
    $_SESSION["registration_success"] = false; // Reset the flag
}

// Get the email from a failed attempt to pre-fill the form
$email_prefill = isset($_SESSION['email_attempt']) ? htmlspecialchars($_SESSION['email_attempt']) : '';
unset($_SESSION['email_attempt']); // Clear it after use
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">

    <style>
        /* Your existing CSS styles */
        html, body.page-centered-container-login { 
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body.page-centered-container-login {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #ccf1ff;
            font-family: "Montserrat", sans-serif;
            padding: 20px;
            box-sizing: border-box;
        }
        .single-white-box-login {
            background: white;
            border: 1px solid #dddddd;
            border-radius: 18px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.1);
            padding: 35px 45px;
            width: 90%;
            max-width: 500px;
            box-sizing: border-box;
            text-align: center;
        }
        .internal-header-section-login {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .internal-header-section-login .logo-image {
            height: 50px;
            width: auto;
        }
        .internal-header-section-login .logo-text {
            padding-left: 10px;
            color: #4dc9f7;
            font-size: 38px;
            font-weight: bold;
            margin: 0;
        }
        .login-form-content .content-title {
            font-size: 1.6em;
            color: #4dc9f7;
            margin-top: 0;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .form-input-login {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 18px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.95em;
            font-family: "Montserrat", sans-serif;
        }

        .form-button-login {
            width: 100%;
            padding: 12px 0;
            margin-top: 10px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.1s ease;
        }
        .form-button-login.primary-button {
            width: 100%;
            margin-top: 15px;
            padding: 12px 0; 
            font-size: 1.1em; 
            background-color: #4dc9f7;
            color: white; 
            border: none; 
            border-radius: 8px; 
            cursor: pointer; 
            transition: background-color 0.3s ease; 
        }
        .form-button-login.primary-button:hover {
            background-color: #36a2c9;
        }

        .form-footer-link-login {
            margin-top: 25px;
            text-align: center;
            font-size: 0.9em;
            color: #555;
        }
        .form-footer-link-login a {
            color: #4dc9f7;
            text-decoration: none;
            font-weight: 600;
        }
        .form-footer-link-login a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body class="page-centered-container-login">

    <div class="single-white-box-login">

        <div class="internal-header-section-login">
            <a href="index.php" style="text-decoration:none; display:inline-flex; align-items:center;">
                   <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
                   <h1 class="logo-text">Happy Pot</h1>
            </a>
        </div>

        <div class="login-form-content">
            <h2 class="content-title">Login to Happy Pot</h2>
            <form id="loginform" method="POST" action="logindb.php"> 
                <input class="form-input-login" type="text" name="email" placeholder="E-mail" required 
                        value="<?php echo $email_prefill; ?>"> <input class="form-input-login" type="password" name="password" placeholder="Password" required>
                <input class="form-button-login primary-button" type="submit" name="login" value="Log-in">
            </form>
            <p class="form-footer-link-login">
                Don't have an account yet? <a href="register_page.php">Sign up here!</a>
            </p>
        </div>

    </div>

    <script src="script.js"></script> 
</body>
</html>
