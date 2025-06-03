<!-- Database Connection-->
<?php include('connect.php'); ?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <!-- Meta -->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS -->
        <link rel="stylesheet" href="style.css">   

        <!-- Javascript-->
        <script src="script.js"></script>
        <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" ></script>
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js" ></script>

        <!-- Title/Favicon -->
        <link rel="icon" type="image/ico" href="images/favi.ico">
        <title><?php echo $title;?></title>
    </head>

    <body>
		<div id="wrapper">
			<header> 
				<!-- Logo -->
                <div>
                    <a href="index.php">
                        <img class="logo" src="images/logo.png" alt="logo">
                        <h1 class="logotxt">Happy Pot</h1>
                    </a>
                </div>