<?php
session_start(); 
$title = 'Happy Pot Dashboard';

global $dbc; 
if (!isset($dbc)) {
    if (file_exists('connect.php')) {
        require_once('connect.php');
    } else if (file_exists('../connect.php')) { 
        require_once('../connect.php');
    } else {
        // Handle no connect.php found
    }
}
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
        html, body.dashboard-page-body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body.dashboard-page-body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #ccf1ff; 
            font-family: "Montserrat", sans-serif;
        }

        .dashboard-site-header { 
            display: flex;
            align-items: center;
            justify-content: space-between; 
            padding: 15px 30px; 
            background-color: #fff; 
            border-bottom: 1px solid #cccccc; 
            width: 100%;
            box-sizing: border-box;
            flex-shrink: 0; 
        }

        .dashboard-site-header .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .dashboard-site-header .logo-image {
            height: 50px;
            width: auto;
        }

        .dashboard-site-header .logo-text {
            padding-left: 10px;
            color: #4dc9f7;
            font-size: 38px;
            font-weight: bold;
            margin: 0;
        }
        
        .dashboard-header-nav { 
            display: flex;
            align-items: center;
        }
        /* --- PERUBAHAN TEMAN ANDA DIMULAI DI SINI --- */
        .dashboard-header-nav .usermenu-greeting {
            font-weight:bold;
            color:#555;
            margin-right: 15px; /* Tambahkan margin kanan untuk usermenu-greeting */
        }
        .dashboard-header-nav .username {
            color:#4dc9f7;
        }
        
        .dashboard-header-nav .btn, .dashboard-header-nav .logoutbtn {
            padding: 8px 15px;
            font-size:0.9em;
            border-radius:8px;
            text-decoration:none;
            cursor:pointer;
            border: 1px solid #ccc;
            background-color: #e9e9e9;
            color: #333;
            /* Hapus margin-left umum di sini jika Anda ingin mengontrol setiap tombol secara individu */
            /* Margin-left yang sebelumnya di sini dipindahkan ke .dashboard-header-nav .btn */
        }

        .dashboard-header-nav .btn { /* Aturan untuk semua tombol 'btn' */
            margin-left: 15px; /* Jarak antara tombol umum */
        }

        /* Aturan khusus untuk tombol 'Post a recipe' untuk memberikan jarak ke logout */
        /* Anda mungkin perlu menambahkan kelas pada tombol ini jika Anda punya banyak tombol 'btn' */
        /* Atau bisa juga pakai selector yang lebih spesifik seperti ini */
        .dashboard-header-nav button[onClick*='recipe.php'] {
            margin-right: 10px; /* Atau sesuai keinginan Anda untuk jarak tambahan */
        }

        .dashboard-header-nav .logoutbtn {
            /* Tidak perlu margin-left tambahan di sini karena margin-right pada tombol sebelumnya sudah bekerja */
        }
        /* --- PERUBAHAN TEMAN ANDA BERAKHIR DI SINI --- */


        .dashboard-header-nav .btn.btnhov:hover, .dashboard-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }


        .dashboard-main-content-wrapper {
            flex-grow: 1; 
            width: 100%;
            display: flex; 
            justify-content: center;
            align-items: flex-start; 
            padding: 30px 15px; 
            box-sizing: border-box;
        }

        .dashboard-content-box { 
            background-color: #fff;
            padding: 30px 25px; 
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 95%; 
            max-width: 1400px; 
            box-sizing: border-box;
        }

        .dashboard-content-box .title { 
            text-align: center;
            color: #4dc9f7;
            font-size: 2.2em; 
            margin-top: 0;
            margin-bottom: 35px; 
        }

        .dashboard-content-box main { 
        }
        .dashboard-content-box table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* Penting untuk lebar kolom yang konsisten */
        }
        .dashboard-content-box td {
            text-align: center; 
            padding: 15px 10px; 
            vertical-align: top;
            width: 25%; /* Untuk 4 kolom */
            box-sizing: border-box; 
        }

        /* --- RASIO GAMBAR: Gaya untuk membuat gambar seragam dengan rasio aspek tetap --- */
        .image-wrapper {
            position: relative; /* Penting untuk posisi absolut gambar */
            width: 100%;
            padding-bottom: 75%; /* Rasio aspek 4:3 (height is 75% of width). Sesuaikan ini! */
                                 /* Untuk rasio 16:9, gunakan 56.25% */
                                 /* Untuk rasio 1:1, gunakan 100% */
            overflow: hidden; /* Sembunyikan bagian gambar yang terpotong */
            border: 2px solid #f0f0f0; 
            border-radius: 10px;
            margin: 0 auto 12px auto; /* Pusatkan wrapper dan beri margin bawah */
        }

        .dashboard-content-box .postimg { 
            position: absolute; /* Gambar diposisikan absolut di dalam wrapper */
            top: 0;
            left: 0;
            width: 100%; 
            height: 100%; /* Gambar mengisi seluruh ruang wrapper */
            object-fit: cover; /* Ini yang membuat gambar di-crop agar muat */
            display: block; 
        }
        /* --- AKHIR RASIO GAMBAR: Gaya untuk membuat gambar seragam --- */


        .dashboard-content-box .postitle { 
            font-size: 1.15em;
            font-weight: bold;
            color: #4dc9f7; 
            text-decoration: none;
            display: block;
            margin-bottom: 6px;
            line-height: 1.3; 
        }
        .dashboard-content-box .postitle strong { 
            font-weight: 600; 
        }
        .dashboard-content-box .postitle:hover {
            color: #4dc9f7; 
            text-decoration: none;
        }
        .dashboard-content-box .fa-clock {
            margin-right: 5px;
            color: #777; 
        }
        .dashboard-content-box td .recipe-detail { 
            display: block; 
            font-size: 0.9em;
            color: #666; 
            margin-bottom: 4px;
            line-height: 1.4;
        }

        .dashboard-content-box .norec { 
            text-align: center;
            font-size: 1.1em;
            color: #777;
            padding: 30px 0;
        }

        .swal-modal {border-radius: 10px;}

        /* Media queries for responsiveness (untuk 4 kolom per baris) */
        @media (max-width: 1200px) {
            .dashboard-content-box td {
                width: 33.33%; /* 3 kolom per baris */
            }
        }

        @media (max-width: 992px) {
            .dashboard-content-box td {
                width: 50%; /* 2 kolom per baris */
            }
            /* Tidak perlu menyesuaikan height image-container lagi karena pakai padding-bottom */
        }

        @media (max-width: 768px) {
            .dashboard-content-box td {
                width: 100%; /* 1 kolom per baris */
            }
            /* Tidak perlu menyesuaikan height image-container lagi karena pakai padding-bottom */
        }
    </style>
</head>
<body class="dashboard-page-body">

    <header class="dashboard-site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="dashboard-header-nav">
            <?php
            if (isset($_SESSION['username'])) {
                // Perubahan HTML: Menambahkan elemen div untuk usermenu-greeting jika belum ada
                echo '<div class="usermenu-greeting">Welcome, <span class="username">' . htmlspecialchars($_SESSION['username']) . '!</span></div>';
            }
            // Tambahkan kelas spesifik atau selector yang konsisten jika perlu untuk tombol "Post a recipe"
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'profile.php\'">Profile</button>';
            echo '<button class="btn btnhov post-recipe-btn" type="button" onClick="location.href=\'recipe.php\'">Post a recipe</button>'; /* Added class for specific styling */
            echo '<form method="POST" action="logout.php" style="display:inline;">';
            echo '<button class="logoutbtn btnhovel" type="submit" name="logout">Log-out</button>';
            echo '</form>';
            ?>
        </div>
    </header>

    <div class="dashboard-main-content-wrapper">
        <div class="dashboard-content-box">
            
            <h1 class="title">Recipes</h1>

            <main>
                <?php
                if (isset($_SESSION["post_success"]) && $_SESSION["post_success"]) {
                    echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    if(typeof swal === "function") {
                                        swal("Post successful", "You can now view your post or make another one!", "success");
                                    }
                                });
                            </script>';
                    $_SESSION["post_success"] = false;
                }

                if (isset($dbc)) {
                    $query = "SELECT r.idrec, r.title, r.img, r.time, u.fname, u.lname FROM recipe r JOIN user u ON r.user_id = u.id ORDER BY r.idrec DESC"; 
                    $result = mysqli_query($dbc, $query);

                    if ($result) {
                        if (mysqli_num_rows($result) == 0) {
                            echo '<p class="norec">No recipes posted yet. Be the first one to post!</p>';
                        } else {
                            echo '<table>';
                            $count = 0;
                            while ($row = mysqli_fetch_assoc($result)) {
                                // PHP untuk menentukan kapan memulai baris baru
                                if ($count % 4 == 0) { 
                                    if ($count > 0) echo '</tr>'; // Tutup baris sebelumnya jika bukan yang pertama
                                    echo '<tr>'; // Mulai baris baru
                                }
                                echo '<td>';
                                // Struktur HTML baru untuk gambar dengan container
                                echo '<div class="image-wrapper">'; // Ubah nama kelas menjadi image-wrapper
                                echo '<a href="display.php?id=' . $row['idrec'] . '"><img class="postimg" src="' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['title']) . '"></a>';
                                echo '</div>'; // Tutup image-wrapper
                                echo '<a href="display.php?id=' . $row['idrec'] . '" class="postitle">' . htmlspecialchars($row['title']) . '</a>'; 
                                echo '<span class="recipe-detail"><i class="fa-regular fa-clock"></i> ' . htmlspecialchars($row['time']) . ' mins</span>'; 
                                echo '<span class="recipe-detail">By ' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</span>'; 
                                echo '</td>';

                                $count++;
                            }
                            // Isi sel kosong di baris terakhir jika tidak genap 4
                            if ($count % 4 != 0) {
                                while ($count % 4 != 0) {
                                    echo '<td></td>'; 
                                    $count++;
                                }
                            }
                            echo '</tr>'; // Tutup baris terakhir
                            echo '</table>';
                        }
                    } else {
                        echo '<p class="norec">Error fetching recipes: ' . mysqli_error($dbc) . '</p>';
                    }
                } else {
                     echo "<p class='norec'>Database connection not available.</p>";
                }
                ?>
            </main>

        </div> 
    </div>
    
</body>
</html>
<?php
if(isset($dbc)) mysqli_close($dbc); 
?>