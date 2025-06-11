<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php"); 
    exit();
}

$title = 'Welcome to Happy Pot';

// SweetAlert display logic is correctly removed from here.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">
    <style>
        /* Your existing CSS for landing page */
        html, body.landing-page {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body.landing-page {
            display: flex;
            flex-direction: column;
            background-color: #ccf1ff;
            font-family: "Montserrat", sans-serif;
        }
        .landing-content-wrapper {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .landing-container {
            text-align: center;
            background-color: #ffffff;
            padding: 40px 50px; 
            border-radius: 18px; 
            box-shadow: 0 6px 20px rgba(0,0,0,0.1); 
            min-width: 380px; 
            width: auto;      
            max-width: 550px; 
        }
        .landing-logo {
            width: 80px; 
            height: auto;
            margin-bottom: 15px;
        }
        .landing-title {
            font-size: 50px; 
            color: #4dc9f7;
            margin-top: 0;
            margin-bottom: 35px; 
            font-weight: bold;
            white-space: nowrap;
        }
        .landing-buttons { 
            display: flex;
            justify-content: center; 
            gap: 15px; 
        }
        .landing-buttons .btn {
            display: inline-block; 
            padding: 12px 28px; 
            text-decoration: none;
            font-size: 16px;
            font-weight: bold;
            border-radius: 12px;
            background-color: #a3e6ff;
            color: #333333;
            border: 2px solid #cccccc;
            transition: background-color 0.3s ease, transform 0.1s ease; 
            white-space: nowrap; 
        }
        .landing-buttons .btn:hover {
            background-color: #4dc9f7;
            cursor: pointer;
            transform: translateY(-1px); 
        }
    </style>
</head>
<body class="landing-page">
    <div class="landing-content-wrapper">
        <div class="landing-container">
            <img src="images/logo.png" alt="Happy Pot Logo" class="landing-logo">
            <h1 class="landing-title">HAPPY POT</h1>
            <div class="landing-buttons">
                <a href="register_page.php" class="btn">Sign Up</a>
                <a href="login_page.php" class="btn">Login</a>
            </div>
        </div>
    </div>
</body>
</html>