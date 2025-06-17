<?php
session_start(); // Mulai session di paling atas
$title = 'Register - Happy Pot'; // Judul halaman

// Logika PHP untuk pesan session (jika ada redirect dengan pesan error/sukses)
if (isset($_SESSION["registration_success"]) && $_SESSION["registration_success"]) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                swal("Account created successfully!", "You can now log in.", "success");
            });
          </script>';
    $_SESSION["registration_success"] = false;
}
if (isset($_SESSION["register_error"]) && $_SESSION["register_error"]) {
    echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                swal("User already exists!", "Use a different email to sign up", "error");
            });
          </script>';
    $_SESSION["register_error"] = false;
}

// include('connect.php'); // Jika diperlukan untuk halaman ini
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
    <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">

    <style>
        body {
            display: flex;
            flex-direction: column;
            justify-content: center; /* Memusatkan .single-white-box secara vertikal */
            align-items: center;    /* Memusatkan .single-white-box secara horizontal */
            min-height: 100vh;
            margin: 0;
            background-color: #ccf1ff; /* Latar belakang biru muda */
            font-family: "Montserrat", sans-serif;
            padding: 20px; /* Memberi sedikit ruang jika kotak putih terlalu besar untuk viewport */
            box-sizing: border-box;
        }

        .single-white-box {
            background: white;
            border: 1px solid #cccccc;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            padding: 30px 40px;
            width: 90%;
            max-width: 1000px; /* Lebar maksimum untuk kotak putih utama, sesuaikan */
            box-sizing: border-box;
            /* Tidak perlu display:flex di sini jika konten di dalamnya diatur sendiri */
        }

        .internal-header-section { /* Untuk logo dan teks "Happy Pot" di dalam kotak putih */
            display: flex;
            align-items: center;
            margin-bottom: 25px; /* Jarak ke konten di bawahnya */
            padding-bottom: 20px; /* Jarak visual dengan garis bawah */
            border-bottom: 1px solid #eee; /* Garis pemisah tipis */
        }

        .internal-header-section .logo-image {
            height: 50px; /* Ukuran logo */
            width: auto;
        }

        .internal-header-section .logo-text {
            padding-left: 10px;
            color: #4dc9f7;
            font-size: 38px; /* Ukuran teks "Happy Pot" */
            font-weight: bold;
            margin: 0;
        }

        .content-columns { /* Untuk "About Us" dan form registrasi bersebelahan */
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap; /* Akan menumpuk jika tidak cukup ruang */
        }

        .content-column {
            flex-basis: 100%; /* Default untuk layar kecil, akan menumpuk */
            box-sizing: border-box;
        }
        
        /* Untuk layar yang lebih lebar, buat dua kolom */
        @media (min-width: 768px) { /* Sesuaikan breakpoint jika perlu */
            .content-column {
                flex-basis: 50%; /* Dua kolom dengan sedikit jarak */
            }
            .content-column.about-us-section {
                 padding-right: 35px; /* Jarak antara kolom "About Us" dan form */
            }
        }


        /* Style untuk elemen di dalam kolom (pastikan diambil dari style.css atau didefinisikan di sini) */
        .abtusH2, .regiH2 {
            text-align: left; /* Atau center, sesuai desain */
            margin-top: 0; /* Hapus margin atas jika ini elemen pertama di kolom */
            margin-bottom: 15px;
            /* font-size: ...; color: ...; */
        }
        .underline, .underlinel { /* Jika masih menggunakan class ini untuk garis bawah judul */
            /* border-bottom: 2px solid #4dc9f7; */
            /* padding-bottom: 5px; */
            /* display: inline-block; */
        }
        .abtusP {
            /* font-size: ...; line-height: ...; */
            text-align: justify;
        }

        .regibox {
            width: 100%;
            padding: 10px; /* Padding lebih nyaman */
            margin-bottom: 12px; /* Jarak antar input */
            box-sizing: border-box;
            border: 1px solid #ccc; /* Border lebih jelas */
            border-radius: 8px;
            font-size: 1em; /* Ukuran font input */
        }
         .registration-form-section br { /* Sembunyikan <br> antar input jika tidak diperlukan */
            display: none;
        }
        .regibtn {
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
        .regibtn.btnhov:hover {
            background-color: #36a2c9; 
        }
         #error {
            min-height: 20px;
            margin-bottom: 10px;
            color: red;
            text-align: left;
            font-size: 0.9em;
        }
        .login-link-text { /* Untuk teks "Sudah punya akun? Login di sini!" */
             margin-top: 20px;
             text-align: center;
             font-size: 0.95em;
        }
        .login-link-text a {
            color: #4dc9f7;
            text-decoration: none;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <div class="single-white-box"> 

        <div class="internal-header-section"> 
            <a href="index.php" style="text-decoration:none;"> 
                 <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            </a>
            <a href="index.php" style="text-decoration:none;">
                <h1 class="logo-text">Happy Pot</h1>
            </a>
        </div>

        <div class="content-columns"> 
            <div class="content-column about-us-section">
                <h2 class="abtusH2">About Us</h2> 
                <p class="abtusP">Welcome to Happy Pot, your go-to website for delicious and easy to follow recipes! We are a community of passionate foodies who believe that cooking should be fun and stress-free. Here you can read and learn new recipes, and even post your own recipes too!</p>
            </div>

            <div class="content-column registration-form-section">
                <h2 class="regiH2">Not a member? Register here!</h2> 
                <div id="error"></div>
                <form id="registerform" method="POST" action="registerdb.php">
                    <input class="regibox" type="text" id="fname" name="fname" placeholder="First Name" required>
                    <input class="regibox" type="text" id="lname" name="lname" placeholder="Last Name" required>
                    <input class="regibox" type="email" id="remail" name="remail" placeholder="E-mail" required> 
                    <input class="regibox" type="password" id="rpassword" name="rpassword" placeholder="Password" required>
                    <input class="regibox" type="password" id="repassword" name="repassword" placeholder="Repeat Password" required>
                    <input class="regibtn btnhov" type="submit" id="registerbtn" name="register" value="Register">
                </form>
                <p class="login-link-text">
                    Already have an account? <a href="login_page.php">Login here!</a>
                </p>
            </div>
        </div>

    </div> 

    <?php // Tidak ada footer ?>

    <script src="script.js"></script>
</body>
</html>
